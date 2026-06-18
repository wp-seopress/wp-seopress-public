<?php // phpcs:ignore

namespace SEOPress\Services\Sitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sitemap Diagnostic
 *
 * Runs a set of health checks against the site XML sitemap and the local
 * environment, then returns a structured report.
 *
 * The report is split in two families of checks:
 *  - Remote (black-box) checks: only need the sitemap URL + HTTP response.
 *    They are kept environment-agnostic so the same engine can later power an
 *    external "test any sitemap" tool.
 *  - Environment (white-box) checks: inspect the local WordPress install
 *    (permalinks, visibility, conflicting plugins, server, caching...).
 *
 * @since 10.0.0
 */
class Diagnostic {
	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'SitemapDiagnostic';

	/**
	 * Status levels, ordered by severity (worst first).
	 */
	const STATUS_ERROR   = 'error';
	const STATUS_WARNING = 'warning';
	const STATUS_INFO    = 'info';
	const STATUS_PASS    = 'pass';

	/**
	 * Sub-sitemap probing limits.
	 *
	 * Probing runs extra loopback requests, so cap the count and total time to
	 * keep the diagnostic fast and bounded.
	 */
	const SUBSITEMAP_MAX         = 25;
	const SUBSITEMAP_TIMEOUT_SEC = 8;
	const SUBSITEMAP_BUDGET_SEC  = 25;

	/**
	 * Documentation links, lazily loaded.
	 *
	 * @var array
	 */
	private $docs = array();

	/**
	 * Run the full diagnostic and return the report.
	 *
	 * @return array {
	 *     @type string $url         The tested sitemap URL.
	 *     @type int    $tested_at   Unix timestamp.
	 *     @type int    $http_code   HTTP status code of the index sitemap (0 if unreachable).
	 *     @type int    $duration_ms Round-trip time of the index request in ms.
	 *     @type array  $summary     Counts per status + overall status.
	 *     @type array  $checks      The list of individual checks.
	 * }
	 */
	public function run() {
		$this->docs = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();

		$url    = $this->getSitemapUrl();
		$checks = array();

		// 1. Is the feature even on? If not, there is nothing meaningful to fetch.
		$feature  = $this->checkFeatureEnabled();
		$checks[] = $feature;

		$http_code   = 0;
		$duration_ms = 0;

		if ( self::STATUS_ERROR !== $feature['status'] ) {
			// 2. Fetch the index sitemap (loopback request to our own site).
			$fetch       = $this->fetch( $url );
			$http_code   = $fetch['code'];
			$duration_ms = $fetch['duration_ms'];

			// 3. Remote (black-box) checks.
			$checks = array_merge( $checks, $this->remoteChecks( $url, $fetch ) );
		}

		// 4. Environment (white-box) checks. Always relevant.
		$checks = array_merge( $checks, $this->environmentChecks( $url ) );

		$checks = array_values( array_filter( $checks ) );

		// TODO: wire this report into the PRO "SEO alerts" so failing checks can
		// raise an alert (email / dashboard) instead of only running on demand.
		return array(
			'url'         => $url,
			'tested_at'   => time(),
			'http_code'   => $http_code,
			'duration_ms' => $duration_ms,
			'summary'     => $this->buildSummary( $checks ),
			'checks'      => $checks,
		);
	}

	/**
	 * The sitemap index URL to test.
	 *
	 * @return string
	 */
	public function getSitemapUrl() {
		return home_url( '/sitemaps.xml' );
	}

	/**
	 * Loopback GET on a local URL.
	 *
	 * Uses wp_remote_get (not the "safe" variant) on purpose: the target is the
	 * site's own host, which wp_safe_remote_get would reject on local/staging
	 * setups resolving to a private IP.
	 *
	 * @param string $url     The URL to fetch.
	 * @param int    $timeout Request timeout in seconds.
	 *
	 * @return array {
	 *     @type bool        $ok           Whether a usable response came back.
	 *     @type int         $code         HTTP status code (0 on transport error).
	 *     @type string      $content_type Lower-cased Content-Type header.
	 *     @type string      $body         Response body.
	 *     @type int         $duration_ms  Round-trip time in ms.
	 *     @type string|null $error        Transport error message, if any.
	 * }
	 */
	public function fetch( $url, $timeout = 15 ) {
		$args = array(
			'timeout'     => $timeout,
			'redirection' => 5,
			'sslverify'   => false,
			'user-agent'  => 'Mozilla/5.0 (compatible; WP Sitemap Diagnostic; ' . home_url() . ')',
			'headers'     => array(
				'Accept' => 'application/xml,text/xml,*/*;q=0.8',
			),
		);

		$args = apply_filters( 'seopress_sitemap_diagnostic_request_args', $args, $url );

		$start    = microtime( true );
		$response = wp_remote_get( $url, $args ); // phpcs:ignore WordPress.WP.AlternativeFunctions.remote_get_remote_get -- loopback to own host, see docblock.
		$duration = (int) round( ( microtime( true ) - $start ) * 1000 );

		if ( is_wp_error( $response ) ) {
			return array(
				'ok'           => false,
				'code'         => 0,
				'content_type' => '',
				'body'         => '',
				'duration_ms'  => $duration,
				'error'        => $response->get_error_message(),
			);
		}

		return array(
			'ok'           => true,
			'code'         => (int) wp_remote_retrieve_response_code( $response ),
			'content_type' => strtolower( (string) wp_remote_retrieve_header( $response, 'content-type' ) ),
			'body'         => (string) wp_remote_retrieve_body( $response ),
			'duration_ms'  => $duration,
			'error'        => null,
		);
	}

	/**
	 * Remote (black-box) checks: only depend on the URL and the HTTP response.
	 *
	 * @param string $url   The tested URL.
	 * @param array  $fetch The fetch() result.
	 *
	 * @return array List of checks.
	 */
	public function remoteChecks( $url, $fetch ) {
		$checks = array();

		// Reachability / HTTP status.
		$status   = $this->checkHttpStatus( $fetch );
		$checks[] = $status;

		// Stop here if we have nothing usable to parse.
		if ( ! $fetch['ok'] || $fetch['code'] >= 400 || '' === $fetch['body'] ) {
			return $checks;
		}

		$checks[] = $this->checkContentType( $fetch );

		$xml      = $this->parseXml( $fetch['body'] );
		$checks[] = $this->checkWellFormed( $fetch['body'], $xml );

		if ( $xml['valid'] ) {
			$checks[] = $this->checkIsSitemap( $xml );
			$checks[] = $this->checkContents( $xml );

			// Probe the first sub-sitemap of each post type / taxonomy, plus the
			// special author / news / video sitemaps (whichever the index lists).
			if ( 'sitemapindex' === $xml['root'] ) {
				foreach ( $this->subSitemapChecks( $xml['child_locs'], $url ) as $sub ) {
					$checks[] = $sub;
				}
			}
		}

		$checks[] = $this->checkStylesheet( $fetch['body'], $url );

		return array_filter( $checks );
	}

	/**
	 * Environment (white-box) checks.
	 *
	 * @param string $url The tested URL.
	 *
	 * @return array List of checks (nulls filtered out by caller).
	 */
	public function environmentChecks( $url ) {
		return array(
			$this->checkPermalinks(),
			$this->checkCoverage(),
			$this->checkVisibility(),
			$this->checkRobotsTxt( $url ),
			$this->checkSeoPluginConflict(),
			$this->checkCachingPlugins(),
			$this->checkServer(),
		);
	}

	/*
	 * Individual checks.
	 */

	/**
	 * Is the XML sitemap feature enabled?
	 *
	 * @return array
	 */
	private function checkFeatureEnabled() {
		$options = get_option( 'seopress_xml_sitemap_option_name' );
		$xml_on  = isset( $options['seopress_xml_sitemap_general_enable'] ) && '1' === $options['seopress_xml_sitemap_general_enable'];
		$toggle  = function_exists( 'seopress_get_toggle_option' ) ? '1' === seopress_get_toggle_option( 'xml-sitemap' ) : true;

		if ( $xml_on && $toggle ) {
			return $this->check(
				'feature_enabled',
				__( 'XML sitemap enabled', 'wp-seopress' ),
				self::STATUS_PASS,
				__( 'The XML sitemap feature is active.', 'wp-seopress' )
			);
		}

		return $this->check(
			'feature_enabled',
			__( 'XML sitemap enabled', 'wp-seopress' ),
			self::STATUS_ERROR,
			__( 'The XML sitemap is disabled. Enable it above so search engines can discover your content, then run the test again.', 'wp-seopress' ),
			$this->doc( array( 'sitemaps', 'xml' ) )
		);
	}

	/**
	 * HTTP reachability and status code.
	 *
	 * @param array $fetch The fetch() result.
	 *
	 * @return array
	 */
	private function checkHttpStatus( $fetch ) {
		$label = __( 'Sitemap reachable', 'wp-seopress' );

		if ( ! $fetch['ok'] ) {
			return $this->check(
				'http_status',
				$label,
				self::STATUS_ERROR,
				sprintf(
					/* translators: %s: error message returned by the HTTP request. */
					__( 'The sitemap could not be reached: %s', 'wp-seopress' ),
					$fetch['error']
				)
			);
		}

		$code = $fetch['code'];

		if ( 200 === $code ) {
			return $this->check(
				'http_status',
				$label,
				self::STATUS_PASS,
				sprintf(
					/* translators: %d: response time in milliseconds. */
					__( 'The sitemap responded with HTTP 200 in %d ms.', 'wp-seopress' ),
					$fetch['duration_ms']
				)
			);
		}

		if ( 404 === $code ) {
			return $this->check(
				'http_status',
				$label,
				self::STATUS_ERROR,
				__( 'The sitemap returns a 404 error. Flushing your permalinks usually fixes it; make sure pretty permalinks are enabled.', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', '404' ) ),
				'flush_permalinks'
			);
		}

		if ( $code >= 500 ) {
			return $this->check(
				'http_status',
				$label,
				self::STATUS_ERROR,
				sprintf(
					/* translators: %d: HTTP status code. */
					__( 'The sitemap returns a server error (HTTP %d). Enable WP_DEBUG to inspect the PHP error behind it.', 'wp-seopress' ),
					$code
				),
				$this->doc( array( 'sitemaps', 'error', 'blank' ) )
			);
		}

		if ( 401 === $code || 403 === $code ) {
			return $this->check(
				'http_status',
				$label,
				self::STATUS_WARNING,
				sprintf(
					/* translators: %d: HTTP status code. */
					__( 'The sitemap is protected (HTTP %d). Search engines will not be able to read it while access is restricted.', 'wp-seopress' ),
					$code
				)
			);
		}

		return $this->check(
			'http_status',
			$label,
			self::STATUS_WARNING,
			sprintf(
				/* translators: %d: HTTP status code. */
				__( 'Unexpected HTTP status: %d.', 'wp-seopress' ),
				$code
			)
		);
	}

	/**
	 * Content-Type header check. The #1 support cause ("appears to be an HTML page").
	 *
	 * @param array $fetch The fetch() result.
	 *
	 * @return array
	 */
	private function checkContentType( $fetch ) {
		$label        = __( 'Content type', 'wp-seopress' );
		$content_type = $fetch['content_type'];

		if ( false !== strpos( $content_type, 'xml' ) ) {
			return $this->check(
				'content_type',
				$label,
				self::STATUS_PASS,
				sprintf(
					/* translators: %s: Content-Type header value. */
					__( 'Served as %s.', 'wp-seopress' ),
					$content_type
				)
			);
		}

		if ( false !== strpos( $content_type, 'html' ) ) {
			return $this->check(
				'content_type',
				$label,
				self::STATUS_ERROR,
				__( 'The sitemap is served as HTML instead of XML. A caching plugin, a CDN or a translation layer is most likely interfering. Exclude .xml and .xsl files from the cache.', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', 'html' ) )
			);
		}

		return $this->check(
			'content_type',
			$label,
			self::STATUS_WARNING,
			sprintf(
				/* translators: %s: Content-Type header value. */
				__( 'Unexpected content type: %s. Search engines expect an XML media type.', 'wp-seopress' ),
				'' !== $content_type ? $content_type : __( '(none)', 'wp-seopress' )
			),
			$this->doc( array( 'sitemaps', 'error', 'html' ) )
		);
	}

	/**
	 * XML well-formedness, including leading output / BOM detection.
	 *
	 * @param string $body The response body.
	 * @param array  $xml  The parseXml() result.
	 *
	 * @return array
	 */
	private function checkWellFormed( $body, $xml ) {
		$label = __( 'Valid XML', 'wp-seopress' );

		// Leading output before the document: the classic "blank page" cause.
		// Even a single blank line before <?xml makes the declaration invalid.
		$has_bom = 0 === strncmp( $body, "\xEF\xBB\xBF", 3 );
		$work    = $has_bom ? substr( $body, 3 ) : $body;
		$first   = strpos( $work, '<' );
		$prefix  = false === $first ? $work : substr( $work, 0, $first );

		if ( $has_bom || '' !== $prefix ) {
			if ( $has_bom ) {
				$message = __( 'A byte-order mark (BOM) is output before the XML declaration. Re-save your wp-config.php, functions.php and header.php as UTF-8 without BOM.', 'wp-seopress' );
			} elseif ( '' === trim( $prefix ) ) {
				$message = __( 'A blank line or whitespace is printed before the XML declaration, which breaks the document. Remove any blank lines after a closing PHP tag in wp-config.php, functions.php or header.php.', 'wp-seopress' );
			} else {
				$message = __( 'Unexpected content is printed before the XML opening tag. A plugin or theme is echoing output too early. Check wp-config.php, functions.php and header.php.', 'wp-seopress' );
			}

			return $this->check(
				'well_formed',
				$label,
				self::STATUS_ERROR,
				$message,
				$this->doc( array( 'sitemaps', 'error', 'blank' ) )
			);
		}

		if ( ! $xml['valid'] ) {
			return $this->check(
				'well_formed',
				$label,
				self::STATUS_ERROR,
				sprintf(
					/* translators: %s: XML parser error message. */
					__( 'The document is not well-formed XML: %s', 'wp-seopress' ),
					$xml['error']
				),
				$this->doc( array( 'sitemaps', 'error', 'blank' ) )
			);
		}

		return $this->check(
			'well_formed',
			$label,
			self::STATUS_PASS,
			__( 'The document is well-formed XML.', 'wp-seopress' )
		);
	}

	/**
	 * Does the document look like a sitemap (root element)?
	 *
	 * @param array $xml The parseXml() result.
	 *
	 * @return array
	 */
	private function checkIsSitemap( $xml ) {
		$label = __( 'Sitemap format', 'wp-seopress' );
		$root  = $xml['root'];

		if ( 'sitemapindex' === $root || 'urlset' === $root ) {
			return $this->check(
				'is_sitemap',
				$label,
				self::STATUS_PASS,
				'sitemapindex' === $root
					? __( 'Valid sitemap index (a list of child sitemaps).', 'wp-seopress' )
					: __( 'Valid URL set sitemap.', 'wp-seopress' )
			);
		}

		return $this->check(
			'is_sitemap',
			$label,
			self::STATUS_ERROR,
			sprintf(
				/* translators: %s: the XML root element name found. */
				__( 'The root element is <%s>, not a sitemap. The expected root is <sitemapindex> or <urlset>.', 'wp-seopress' ),
				$root
			)
		);
	}

	/**
	 * Count entries and flag size limits.
	 *
	 * @param array $xml The parseXml() result.
	 *
	 * @return array
	 */
	private function checkContents( $xml ) {
		$label = __( 'Sitemap contents', 'wp-seopress' );

		if ( 'sitemapindex' === $xml['root'] ) {
			$count = $xml['child_sitemaps'];

			if ( 0 === $count ) {
				return $this->check(
					'contents',
					$label,
					self::STATUS_WARNING,
					__( 'The sitemap index is empty. It lists no child sitemaps, so search engines will find no URLs.', 'wp-seopress' )
				);
			}

			return $this->check(
				'contents',
				$label,
				self::STATUS_PASS,
				sprintf(
					/* translators: %d: number of child sitemaps. */
					_n( 'The index references %d child sitemap.', 'The index references %d child sitemaps.', $count, 'wp-seopress' ),
					$count
				)
			);
		}

		$count = $xml['urls'];

		if ( 0 === $count ) {
			return $this->check(
				'contents',
				$label,
				self::STATUS_WARNING,
				__( 'This sitemap contains no URLs.', 'wp-seopress' )
			);
		}

		if ( $count > 50000 ) {
			return $this->check(
				'contents',
				$label,
				self::STATUS_WARNING,
				sprintf(
					/* translators: %s: number of URLs (formatted). */
					__( 'This sitemap holds %s URLs, above the 50,000 limit per sitemap. Split it into several files.', 'wp-seopress' ),
					number_format_i18n( $count )
				)
			);
		}

		return $this->check(
			'contents',
			$label,
			self::STATUS_PASS,
			sprintf(
				/* translators: %s: number of URLs (formatted). */
				_n( 'This sitemap lists %s URL.', 'This sitemap lists %s URLs.', $count, 'wp-seopress' ),
				number_format_i18n( $count )
			)
		);
	}

	/**
	 * The XSL stylesheet referenced by the sitemap (browser rendering).
	 *
	 * @param string $body The response body.
	 * @param string $url  The sitemap URL (for resolving relative hrefs).
	 *
	 * @return array|null
	 */
	private function checkStylesheet( $body, $url ) {
		$label = __( 'Stylesheet (XSL)', 'wp-seopress' );

		// The stylesheet processing instruction always sits at the top of the
		// document; only scan the head to keep this fast and bounded.
		$head = substr( $body, 0, 2048 );

		if ( ! preg_match( '/<\?xml-stylesheet[^>]*href=["\']([^"\']+)["\']/i', $head, $matches ) ) {
			// No stylesheet declared. Not an error: it only affects browser display.
			return null;
		}

		$xsl_url = $this->absoluteUrl( $matches[1], $url );

		// SSRF guard: the href comes from the response body, so only probe a
		// stylesheet served from the same host as the site. Anything else is
		// skipped rather than fetched.
		if ( ! $this->isSameHost( $xsl_url, $url ) ) {
			return null;
		}

		$xsl = $this->fetch( $xsl_url, 8 );

		if ( $xsl['ok'] && 200 === $xsl['code'] && false !== strpos( $xsl['content_type'], 'xml' ) ) {
			return $this->check(
				'stylesheet',
				$label,
				self::STATUS_PASS,
				__( 'The XSL stylesheet loads correctly.', 'wp-seopress' )
			);
		}

		return $this->check(
			'stylesheet',
			$label,
			self::STATUS_WARNING,
			__( 'The XSL stylesheet does not load, so the sitemap looks blank in a browser. Search engines still read it, but exclude .xsl files from your cache to restore the preview.', 'wp-seopress' ),
			$this->doc( array( 'sitemaps', 'error', 'blank' ) )
		);
	}

	/**
	 * Probe the first sub-sitemap of each group listed in the index.
	 *
	 * Each post type and taxonomy is a group ({name}-sitemap{N}.xml); the
	 * special author / news / video sitemaps each form their own group. We test
	 * the lowest-numbered (first) entry of every group, bounded by a count cap
	 * and an overall time budget.
	 *
	 * @param array  $locs      Child sitemap URLs from the index.
	 * @param string $index_url The index URL (for the same-host guard).
	 *
	 * @return array List of checks.
	 */
	private function subSitemapChecks( $locs, $index_url ) {
		$selected = $this->selectFirstPerGroup( $locs );
		if ( empty( $selected ) ) {
			return array();
		}

		$checks   = array();
		$deadline = microtime( true ) + self::SUBSITEMAP_BUDGET_SEC;
		$count    = 0;
		$skipped  = 0;

		foreach ( $selected as $group => $loc ) {
			if ( $count >= self::SUBSITEMAP_MAX || microtime( true ) > $deadline ) {
				$skipped = count( $selected ) - $count;
				break;
			}

			// Defense in depth: the index is ours, but a filter could alter it.
			if ( ! $this->isSameHost( $loc, $index_url ) ) {
				continue;
			}

			$checks[] = $this->checkSubSitemap( $group, $loc );
			++$count;
		}

		if ( $skipped > 0 ) {
			$checks[] = $this->check(
				'subsitemap_truncated',
				__( 'Sub-sitemaps', 'wp-seopress' ),
				self::STATUS_INFO,
				sprintf(
					/* translators: %d: number of sub-sitemaps left untested. */
					_n( '%d more sub-sitemap was not tested to keep the check fast.', '%d more sub-sitemaps were not tested to keep the check fast.', $skipped, 'wp-seopress' ),
					$skipped
				)
			);
		}

		return $checks;
	}

	/**
	 * Probe a single sub-sitemap and condense the result into one check.
	 *
	 * @param string $group The group name (post type, taxonomy, author...).
	 * @param string $url   The sub-sitemap URL.
	 *
	 * @return array
	 */
	private function checkSubSitemap( $group, $url ) {
		$id    = 'subsitemap_' . $group;
		$label = sprintf(
			/* translators: %s: sub-sitemap group name (post type, taxonomy, author...). */
			__( 'Sub-sitemap: %s', 'wp-seopress' ),
			$group
		);

		$fetch = $this->fetch( $url, self::SUBSITEMAP_TIMEOUT_SEC );

		if ( ! $fetch['ok'] || $fetch['code'] >= 400 ) {
			// A 404 on a sub-sitemap is almost always stale rewrite rules, so
			// offer the same one-click flush as the index. Other codes (5xx,
			// 403) or transport errors are not flush-fixable.
			$is_404 = $fetch['ok'] && 404 === $fetch['code'];

			return $this->check(
				$id,
				$label,
				self::STATUS_ERROR,
				$fetch['ok']
					? sprintf(
						/* translators: %d: HTTP status code. */
						__( 'Returns HTTP %d.', 'wp-seopress' ),
						$fetch['code']
					)
					: __( 'Could not be reached.', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', '404' ) ),
				$is_404 ? 'flush_permalinks' : ''
			);
		}

		if ( false === strpos( $fetch['content_type'], 'xml' ) ) {
			return $this->check(
				$id,
				$label,
				self::STATUS_ERROR,
				__( 'Served as HTML instead of XML (cache or CDN interference).', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', 'html' ) )
			);
		}

		$xml = $this->parseXml( $fetch['body'] );

		if ( ! $xml['valid'] ) {
			return $this->check(
				$id,
				$label,
				self::STATUS_ERROR,
				__( 'Does not return valid XML.', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', 'blank' ) )
			);
		}

		if ( 'urlset' !== $xml['root'] && 'sitemapindex' !== $xml['root'] ) {
			return $this->check(
				$id,
				$label,
				self::STATUS_WARNING,
				__( 'Does not look like a sitemap.', 'wp-seopress' )
			);
		}

		return $this->check(
			$id,
			$label,
			self::STATUS_PASS,
			sprintf(
				/* translators: %s: number of URLs (formatted). */
				_n( 'Valid, %s URL.', 'Valid, %s URLs.', $xml['urls'], 'wp-seopress' ),
				number_format_i18n( $xml['urls'] )
			)
		);
	}

	/**
	 * Keep the first (lowest-numbered) sub-sitemap of each group.
	 *
	 * @param array $locs Child sitemap URLs.
	 *
	 * @return array Map of group name => URL, in first-seen order.
	 */
	private function selectFirstPerGroup( $locs ) {
		$groups = array();

		foreach ( $locs as $loc ) {
			$path = (string) wp_parse_url( $loc, PHP_URL_PATH );
			$file = basename( $path );

			list( $group, $page ) = $this->classifyChild( $file );

			if ( '' === $group ) {
				continue;
			}

			if ( ! isset( $groups[ $group ] ) || $page < $groups[ $group ]['page'] ) {
				$groups[ $group ] = array(
					'page' => $page,
					'url'  => $loc,
				);
			}
		}

		$selected = array();
		foreach ( $groups as $group => $data ) {
			$selected[ $group ] = $data['url'];
		}

		return $selected;
	}

	/**
	 * Classify a sub-sitemap filename into a group and a page number.
	 *
	 * @param string $file The sitemap filename (e.g. "post-sitemap2.xml").
	 *
	 * @return array array( string $group, int $page ).
	 */
	private function classifyChild( $file ) {
		if ( 'author.xml' === $file ) {
			return array( 'author', 1 );
		}
		if ( 'news.xml' === $file ) {
			return array( 'news', 1 );
		}
		if ( preg_match( '/^video([0-9]*)\.xml$/i', $file, $m ) ) {
			return array( 'video', '' === $m[1] ? 1 : (int) $m[1] );
		}
		if ( preg_match( '/^(.+?)-sitemap([0-9]*)\.xml$/i', $file, $m ) ) {
			return array( $m[1], '' === $m[2] ? 1 : (int) $m[2] );
		}

		return array( '', 1 );
	}

	/**
	 * Pretty permalinks must be enabled for the rewrite to resolve.
	 *
	 * @return array
	 */
	private function checkPermalinks() {
		$label = __( 'Permalinks', 'wp-seopress' );

		if ( '' === (string) get_option( 'permalink_structure' ) ) {
			return $this->check(
				'permalinks',
				$label,
				self::STATUS_ERROR,
				__( 'Plain permalinks are enabled. The pretty sitemap URL will not resolve. Set permalinks to "Post name" under Settings → Permalinks.', 'wp-seopress' ),
				$this->doc( array( 'sitemaps', 'error', '404' ) )
			);
		}

		return $this->check(
			'permalinks',
			$label,
			self::STATUS_PASS,
			__( 'Pretty permalinks are enabled.', 'wp-seopress' )
		);
	}

	/**
	 * Search engine visibility (Settings → Reading).
	 *
	 * @return array|null
	 */
	private function checkVisibility() {
		if ( '0' !== (string) get_option( 'blog_public' ) ) {
			return null;
		}

		return $this->check(
			'visibility',
			__( 'Search engine visibility', 'wp-seopress' ),
			self::STATUS_WARNING,
			__( 'Your site discourages search engines (Settings → Reading). Submitting a sitemap has little effect while this option is on.', 'wp-seopress' )
		);
	}

	/**
	 * Optional content types coverage.
	 *
	 * The diagnostic can only probe sub-sitemaps that the index actually lists,
	 * so a content type turned off in the settings is otherwise invisible here.
	 * A user who expects, say, an author sitemap and disabled it by accident
	 * would get no signal at all. This check reports the on/off state of the
	 * optional types so that gap is explained rather than silent. It stays at
	 * info level: turning a type off is a legitimate choice, not a fault.
	 *
	 * @return array
	 */
	private function checkCoverage() {
		$label    = __( 'Optional content types', 'wp-seopress' );
		$options  = get_option( 'seopress_xml_sitemap_option_name' );
		$coverage = $this->coverageReport( is_array( $options ) ? $options : array() );

		$labels = array(
			'images'  => __( 'images', 'wp-seopress' ),
			'videos'  => __( 'videos', 'wp-seopress' ),
			'authors' => __( 'authors', 'wp-seopress' ),
		);

		$disabled = array();
		foreach ( $coverage as $type => $enabled ) {
			if ( ! $enabled ) {
				$disabled[] = $labels[ $type ];
			}
		}

		if ( empty( $disabled ) ) {
			return $this->check(
				'coverage',
				$label,
				self::STATUS_PASS,
				__( 'Images, videos and authors are all included in the sitemap.', 'wp-seopress' )
			);
		}

		return $this->check(
			'coverage',
			$label,
			self::STATUS_INFO,
			sprintf(
				/* translators: %s: comma-separated list of disabled content types (e.g. "videos, authors"). */
				__( 'These optional content types are turned off: %s. This is fine if intended; if you expected them in your sitemap, enable them in the XML / HTML Sitemap settings above.', 'wp-seopress' ),
				implode( ', ', $disabled )
			)
		);
	}

	/**
	 * On/off state of the optional sitemap content types.
	 *
	 * Pure helper (no WordPress runtime) so it can be unit-tested directly. Keys
	 * are stable identifiers; the caller maps them to translated labels.
	 *
	 * @param array $options The seopress_xml_sitemap_option_name option value.
	 *
	 * @return array Ordered map of type identifier => bool enabled.
	 */
	private function coverageReport( $options ) {
		$is_on = function ( $key ) use ( $options ) {
			return isset( $options[ $key ] ) && '1' === $options[ $key ];
		};

		return array(
			'images'  => $is_on( 'seopress_xml_sitemap_img_enable' ),
			'videos'  => $is_on( 'seopress_xml_sitemap_video_enable' ),
			'authors' => $is_on( 'seopress_xml_sitemap_author_enable' ),
		);
	}

	/**
	 * Is the sitemap advertised in robots.txt and not blocked there?
	 *
	 * @param string $url The sitemap URL.
	 *
	 * @return array|null
	 */
	private function checkRobotsTxt( $url ) {
		$label   = __( 'robots.txt', 'wp-seopress' );
		$robots  = $this->fetch( home_url( '/robots.txt' ), 8 );

		if ( ! $robots['ok'] || 200 !== $robots['code'] || '' === $robots['body'] ) {
			return null;
		}

		$body = $robots['body'];
		$path = wp_parse_url( $url, PHP_URL_PATH );

		// Blocked by a Disallow rule?
		if ( $path && preg_match( '/^\s*Disallow:\s*' . preg_quote( $path, '/' ) . '\s*$/im', $body ) ) {
			return $this->check(
				'robots_txt',
				$label,
				self::STATUS_ERROR,
				__( 'Your robots.txt blocks the sitemap path with a Disallow rule. Remove it so search engines can fetch the sitemap.', 'wp-seopress' )
			);
		}

		if ( false !== stripos( $body, 'sitemap' ) && preg_match( '/^\s*Sitemap:\s*\S+/im', $body ) ) {
			return $this->check(
				'robots_txt',
				$label,
				self::STATUS_PASS,
				__( 'The sitemap is advertised in robots.txt.', 'wp-seopress' )
			);
		}

		return $this->check(
			'robots_txt',
			$label,
			self::STATUS_INFO,
			__( 'No Sitemap directive was found in robots.txt. Adding one helps search engines discover the sitemap faster.', 'wp-seopress' )
		);
	}

	/**
	 * Other active SEO plugins may emit a competing sitemap.
	 *
	 * @return array|null
	 */
	private function checkSeoPluginConflict() {
		$conflicts = array();

		if ( defined( 'WPSEO_VERSION' ) ) {
			$conflicts[] = 'Yoast SEO';
		}
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ) ) {
			$conflicts[] = 'Rank Math';
		}
		if ( defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' ) ) {
			$conflicts[] = 'All in One SEO';
		}
		if ( defined( 'The_SEO_Framework\\THE_SEO_FRAMEWORK_VERSION' ) || function_exists( 'the_seo_framework' ) ) {
			$conflicts[] = 'The SEO Framework';
		}

		if ( empty( $conflicts ) ) {
			return null;
		}

		return $this->check(
			'seo_plugin_conflict',
			__( 'Conflicting SEO plugins', 'wp-seopress' ),
			self::STATUS_WARNING,
			sprintf(
				/* translators: %s: comma-separated list of plugin names. */
				__( 'Another SEO plugin is active (%s). Running two sitemap generators at once can confuse search engines. Keep only one enabled.', 'wp-seopress' ),
				implode( ', ', $conflicts )
			)
		);
	}

	/**
	 * Active caching/optimization plugins that commonly break sitemaps.
	 *
	 * @return array|null
	 */
	private function checkCachingPlugins() {
		$map = array(
			'WP_ROCKET_VERSION'          => 'WP Rocket',
			'W3TC'                       => 'W3 Total Cache',
			'LSCWP_V'                    => 'LiteSpeed Cache',
			'WPCACHEHOME'                => 'WP Super Cache',
			'WPFC_MAIN_PATH'             => 'WP Fastest Cache',
			'AUTOPTIMIZE_PLUGIN_VERSION' => 'Autoptimize',
			'WP_OPTIMIZE_VERSION'        => 'WP-Optimize',
		);

		$found = array();
		foreach ( $map as $constant => $name ) {
			if ( defined( $constant ) ) {
				$found[] = $name;
			}
		}
		if ( class_exists( 'WpFastestCache' ) && ! in_array( 'WP Fastest Cache', $found, true ) ) {
			$found[] = 'WP Fastest Cache';
		}

		if ( empty( $found ) ) {
			return null;
		}

		return $this->check(
			'caching_plugin',
			__( 'Caching plugins', 'wp-seopress' ),
			self::STATUS_INFO,
			sprintf(
				/* translators: %s: comma-separated list of plugin names. */
				__( 'A caching or optimization plugin is active (%s). Exclude .xml and .xsl files from its cache and minification to avoid a blank or HTML sitemap.', 'wp-seopress' ),
				implode( ', ', $found )
			),
			$this->doc( array( 'sitemaps', 'error', 'html' ) )
		);
	}

	/**
	 * NGINX needs manual rewrite rules.
	 *
	 * @return array|null
	 */
	private function checkServer() {
		$software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		if ( '' === $software || false === stripos( $software, 'nginx' ) ) {
			return null;
		}

		return $this->check(
			'server',
			__( 'Server (NGINX)', 'wp-seopress' ),
			self::STATUS_INFO,
			__( 'Your server runs NGINX. If sitemaps 404, the rewrite rules must be added to your server configuration. Your host can apply them.', 'wp-seopress' ),
			$this->doc( array( 'sitemaps', 'error', '404' ) )
		);
	}

	/*
	 * Helpers.
	 */

	/**
	 * Parse a sitemap body and extract the bits the checks need.
	 *
	 * @param string $body The XML body.
	 *
	 * @return array {
	 *     @type bool   $valid          Whether the body is well-formed XML.
	 *     @type string $error          Parser error message when invalid.
	 *     @type string $root           Root element local name.
	 *     @type int    $child_sitemaps Number of <sitemap> entries.
	 *     @type array  $child_locs     Child sitemap URLs (capped), for index documents.
	 *     @type int    $urls           Number of <url> entries.
	 * }
	 */
	public function parseXml( $body ) {
		$result = array(
			'valid'          => false,
			'error'          => '',
			'root'           => '',
			'child_sitemaps' => 0,
			'child_locs'     => array(),
			'urls'           => 0,
		);

		$previous = libxml_use_internal_errors( true );
		libxml_clear_errors();

		// LIBXML_NONET blocks network access; external entities are disabled by
		// default in libxml 2.9+, guarding against XXE.
		$xml = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NONET );

		if ( false === $xml ) {
			$error             = libxml_get_last_error();
			$result['error']   = $error ? trim( $error->message ) : __( 'Unknown parser error.', 'wp-seopress' );
			libxml_clear_errors();
			libxml_use_internal_errors( $previous );
			return $result;
		}

		$result['valid'] = true;
		$result['root']  = $xml->getName();

		if ( 'sitemapindex' === $result['root'] ) {
			$result['child_sitemaps'] = $xml->sitemap->count();

			$locs = array();
			foreach ( $xml->sitemap as $sitemap ) {
				$loc = trim( (string) $sitemap->loc );
				if ( '' !== $loc ) {
					$locs[] = $loc;
				}
				if ( count( $locs ) >= 5000 ) {
					break;
				}
			}
			$result['child_locs'] = $locs;
		} elseif ( 'urlset' === $result['root'] ) {
			$result['urls'] = $xml->url->count();
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		return $result;
	}

	/**
	 * Resolve a possibly-relative URL against a base.
	 *
	 * @param string $href The href found in the document.
	 * @param string $base The base URL.
	 *
	 * @return string
	 */
	private function absoluteUrl( $href, $base ) {
		if ( preg_match( '#^https?://#i', $href ) ) {
			return $href;
		}
		if ( 0 === strpos( $href, '/' ) ) {
			return home_url( $href );
		}
		return trailingslashit( dirname( $base ) ) . $href;
	}

	/**
	 * Whether two URLs share the same host (case-insensitive).
	 *
	 * @param string $url_a First URL.
	 * @param string $url_b Second URL.
	 *
	 * @return bool
	 */
	private function isSameHost( $url_a, $url_b ) {
		$host_a = wp_parse_url( $url_a, PHP_URL_HOST );
		$host_b = wp_parse_url( $url_b, PHP_URL_HOST );

		if ( empty( $host_a ) || empty( $host_b ) ) {
			return false;
		}

		return strtolower( $host_a ) === strtolower( $host_b );
	}

	/**
	 * Build a single check entry.
	 *
	 * @param string $id      Check id.
	 * @param string $label   Human label.
	 * @param string $status  One of the STATUS_* constants.
	 * @param string $message Human message.
	 * @param string $doc     Optional documentation URL.
	 * @param string $fix     Optional one-click fix id (see Actions\Api\Diagnostics\SitemapFix).
	 *
	 * @return array
	 */
	private function check( $id, $label, $status, $message, $doc = '', $fix = '' ) {
		return array(
			'id'      => $id,
			'label'   => $label,
			'status'  => $status,
			'message' => $message,
			'doc'     => $doc,
			'fix'     => $fix,
		);
	}

	/**
	 * Resolve a documentation URL from a path into seopress_get_docs_links().
	 *
	 * @param array $path Ordered keys, e.g. array( 'sitemaps', 'error', 'blank' ).
	 *
	 * @return string
	 */
	private function doc( $path ) {
		$node = $this->docs;
		foreach ( $path as $key ) {
			if ( ! is_array( $node ) || ! isset( $node[ $key ] ) ) {
				return '';
			}
			$node = $node[ $key ];
		}
		return is_string( $node ) ? $node : '';
	}

	/**
	 * Aggregate the per-status counts and overall status.
	 *
	 * @param array $checks The list of checks.
	 *
	 * @return array
	 */
	private function buildSummary( $checks ) {
		$counts = array(
			self::STATUS_ERROR   => 0,
			self::STATUS_WARNING => 0,
			self::STATUS_INFO    => 0,
			self::STATUS_PASS    => 0,
		);

		foreach ( $checks as $check ) {
			if ( isset( $counts[ $check['status'] ] ) ) {
				++$counts[ $check['status'] ];
			}
		}

		if ( $counts[ self::STATUS_ERROR ] > 0 ) {
			$overall = self::STATUS_ERROR;
		} elseif ( $counts[ self::STATUS_WARNING ] > 0 ) {
			$overall = self::STATUS_WARNING;
		} else {
			$overall = self::STATUS_PASS;
		}

		return array(
			'status'  => $overall,
			'error'   => $counts[ self::STATUS_ERROR ],
			'warning' => $counts[ self::STATUS_WARNING ],
			'info'    => $counts[ self::STATUS_INFO ],
			'pass'    => $counts[ self::STATUS_PASS ],
			'total'   => count( $checks ),
		);
	}
}

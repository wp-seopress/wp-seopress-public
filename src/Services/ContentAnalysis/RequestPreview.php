<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RequestPreview
 */
class RequestPreview {

	/**
	 * Sentinel stored in the loop-back transient to mean "no local origin is
	 * reachable", so the candidate probe is not repaid on every analysis.
	 *
	 * @var string
	 */
	const LOOPBACK_NONE = 'none';

	/**
	 * The getLinkRequest function.
	 *
	 * @param int    $id The id.
	 * @param string $taxname The taxname.
	 *
	 * @return string
	 */
	public function getLinkRequest( $id, $taxname = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$args = array( 'no_admin_bar' => 1 );

		// Useful for Page / Theme builders.
		$args = apply_filters( 'seopress_real_preview_custom_args', $args );

		// Post type.
		if ( empty( $taxname ) ) {
			$theme = wp_get_theme();
			// Oxygen / beTheme compatibility.
			$oxygen_metabox_enabled = get_option( 'oxygen_vsb_ignore_post_type_' . get_post_type( $id ) ) ? false : true;
			// Beaver Builder compatibility: it renders its module layout (including
			// the heading modules) on the public URL but not on the preview URL, so
			// the preview HTML reports zero headings. Analyse the permalink instead.
			$beaver_builder_enabled = ( class_exists( 'FLBuilderModel' ) || defined( 'FL_BUILDER_VERSION' ) )
				&& '1' === get_post_meta( (int) $id, '_fl_builder_enabled', true );
			if (
				( is_plugin_active( 'oxygen/functions.php' ) && function_exists( 'ct_template_output' ) && true === $oxygen_metabox_enabled )
				||
				( 'betheme' === $theme->template || 'Betheme' === $theme->parent_theme )
				||
				$beaver_builder_enabled
			) {
				$link = get_permalink( (int) $id );
			} else {
				$link = get_preview_post_link( (int) $id, $args );
			}
		} else {
			// Taxonomy.
			$link = get_term_link( (int) $id, $taxname );
		}

		// Each source fails differently when the object cannot be resolved (an
		// unknown id, a status that is not previewable...): get_permalink()
		// returns false, get_preview_post_link() returns null and
		// get_term_link() returns a WP_Error. Feeding that to add_query_arg()
		// emitted PHP 8.1+ "passing null" deprecations on every admin screen
		// and built a meaningless relative URL ("?no_admin_bar=1") that was
		// then used as the preview URL. Stop here instead.
		if ( ! is_string( $link ) || '' === $link ) {
			return '';
		}

		$link = add_query_arg( 'no_admin_bar', 1, $link );

		$link = apply_filters( 'seopress_get_dom_link', $link, $id );

		return $link;
	}

	/**
	 * The getDomById function.
	 *
	 * @param int    $id The id.
	 * @param string $taxname The taxname.
	 *
	 * @return string
	 */
	public function getDomById( $id, $taxname = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		// Loop-back HTTP request against the post's own preview URL. The
		// admin request that fires this already holds one PHP-FPM worker;
		// the loop-back holds a second one for the duration of the call.
		// A 30 s ceiling means two open editors can saturate a small
		// worker pool — capped at 10 s so the metabox can surface a
		// degraded state instead of letting the Suspense fallback hang.
		// `redirection` is 0 on purpose: the hops are followed by
		// followSafely(), which refuses to leave the site. Letting WP_Http
		// follow them would carry the administrator's cookies to wherever the
		// response points, and the redirect target is attacker-controlled (a
		// SEOPress redirection stored on a post the attacker owns).
		$args = array(
			'redirection' => 0,
			'timeout'     => 10,
			'sslverify'   => false,
		);

		// Present the loop-back as a real browser. The default WP_Http user
		// agent ("WordPress/x.y; https://...") is a frequent trigger for the
		// heuristic bot rules of host-level WAFs; a browser-like signature
		// makes the request far less likely to be challenged.
		$args['user-agent']        = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36';
		$args['headers']           = isset( $args['headers'] ) && is_array( $args['headers'] ) ? $args['headers'] : array();
		$args['headers']['Accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

		$link = $this->getLinkRequest( $id, $taxname );

		// Re-encode the URL path to handle non-Latin characters (Korean, Japanese, etc.)
		// that may be decoded to raw UTF-8 by get_permalink(), causing wp_remote_get() to fail.
		$parsed = wp_parse_url( $link );
		if ( isset( $parsed['path'] ) ) {
			$segments      = explode( '/', $parsed['path'] );
			$encoded_parts = array_map(
				function ( $segment ) {
					return rawurlencode( rawurldecode( $segment ) );
				},
				$segments
			);
			$encoded_path  = implode( '/', $encoded_parts );

			$link = str_replace( $parsed['path'], $encoded_path, $link );
		}

		$is_self = $this->isSelfHostedLink( $link );

		// Bound to the host being called, never domainless. WordPress Requests
		// treats a cookie with no domain as matching every host, so the
		// previous set followed any cross-host redirect out of the site.
		$cookies = $this->buildCookies( wp_parse_url( $link, PHP_URL_HOST ) );
		if ( ! empty( $cookies ) ) {
			$args['cookies'] = $cookies;
		}

		// Applied last, as it always was, so a page builder or a security
		// plugin can still inspect and change the whole argument set —
		// `cookies` included. Building the jar after it would silently
		// override any filter that removes or replaces the session.
		// `redirection` is the one exception: followSafely() forces it back to
		// 0 because a filter must not be able to hand the redirect chain back
		// to WP_Http, which cannot be restricted to the site.
		$args = apply_filters( 'seopress_real_preview_remote', $args );

		try {
			$response = $this->requestPreview( $link, $args, $is_self, $id, $taxname );

			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'code'    => 'unreachable',
				);
			}

			$code_response = (int) wp_remote_retrieve_response_code( $response );

			if ( in_array( $code_response, array( 404, 401 ), true ) ) {
				return array(
					'success' => false,
					'code'    => $code_response,
				);
			}

			// A redirect that is still a redirect here is one followSafely()
			// declined to follow: it left the site. Its body is the server's
			// own "301 Moved Permanently" stub, not the post — analyzing it
			// would score an empty page and persist that as the post's
			// analysis. Surface it as a failure instead.
			if ( $code_response >= 300 && $code_response < 400 ) {
				return array(
					'success' => false,
					'code'    => 'redirected',
				);
			}

			// Still blocked after the loop-back attempts (no local origin
			// reachable): surface an explicit message rather than analyzing a
			// challenge interstitial or an error page as if it were the post.
			if ( $this->looksBlocked( $response ) ) {
				return array(
					'success' => false,
					'code'    => 'blocked',
				);
			}

			return array(
				'success' => true,
				'body'    => wp_remote_retrieve_body( $response ),
			);
		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'code'    => '',
			);
		}
	}

	/**
	 * Fetch the preview HTML by loading the post's own public URL — exactly
	 * what the metabox has always done.
	 *
	 * Public-first: on the overwhelming majority of hosts the server-side
	 * loop-back to the public URL works, including behind a host-level WAF such
	 * as o2switch Tiger Protect (which challenges the REST path, not a normal
	 * front-end request). So a normal host gets its answer in a single request
	 * and never touches a local origin.
	 *
	 * The local-origin bypass below runs only when the public URL is actively
	 * challenged by an *edge* CDN (Cloudflare Bot Fight Mode and the like) that
	 * hangs or 403/503s the loop-back. Even then a pinned response is validated
	 * as this very post before it is trusted, so a wrong vhost — a 404, or a
	 * default-site 200 from connecting by IP — is never analyzed. A short
	 * "nothing reachable" memo avoids re-probing on every analysis.
	 *
	 * @param string $link    The preview URL.
	 * @param array  $args    The wp_remote_get arguments.
	 * @param bool   $is_self Whether $link points to this very site.
	 * @param int    $id      The post/term id being previewed.
	 * @param string $taxname The taxonomy name when previewing a term.
	 *
	 * @return array|\WP_Error
	 */
	private function requestPreview( $link, $args, $is_self, $id, $taxname = null ) {
		/**
		 * Force the preview fetch through a local origin (loop-back / internal
		 * IP) for the PRIMARY request, instead of only as a fallback when the
		 * public URL is challenged.
		 *
		 * On a VPS whose internal IP differs from its external IP, the public
		 * URL resolves to the external IP and the self-request hair-pins out and
		 * back (hairpin NAT): slow, and at Site-Audit scale an occasional hung
		 * request can stall the whole run. Returning true makes the preview try
		 * candidateIps() first (127.0.0.1, SERVER_ADDR, ::1, plus the
		 * seopress_real_preview_loopback_ips / _ip filters), validating the body
		 * is this very post before trusting it, and only falls back to the
		 * public URL when no local origin serves it.
		 *
		 * @since 10.1.0
		 *
		 * @param bool   $force   Whether to try the local origin first. Default false.
		 * @param string $link    The preview URL.
		 * @param bool   $is_self Whether the link points to this very site.
		 */
		if ( $is_self && (bool) apply_filters( 'seopress_real_preview_force_loopback', false, $link, $is_self ) ) {
			foreach ( $this->candidateIps() as $ip ) {
				$loopback = $this->requestOrigin( $link, $args, $ip );

				if ( ! $this->looksBlocked( $loopback ) && $this->bodyMatchesPost( $loopback, $id, $taxname ) ) {
					return $loopback;
				}
			}
			// No local origin served this post: fall through to the public URL
			// so a misconfigured filter degrades to today's behavior instead of
			// failing the preview.
		}

		// Public-first: load the post's own URL, the path that works on a
		// normal host and behind a host-level WAF alike. This is the only path
		// a non-CDN host ever takes, so its behavior is unchanged.
		$response = $this->followSafely( $link, $args );

		if ( ! $is_self || ! $this->looksBlocked( $response ) ) {
			return $response;
		}

		// The public URL is intercepted by an edge CDN. If we already learned
		// no local origin is reachable, don't re-probe — just surface the block.
		$transient = 'seopress_content_analysis_loopback';
		if ( self::LOOPBACK_NONE === get_transient( $transient ) ) {
			return $response;
		}

		// Try to reach the origin directly, validating the body really is this
		// post before trusting it. A wrong-vhost 404 or default-site 200 must
		// never be analyzed as this content.
		foreach ( $this->candidateIps() as $ip ) {
			$loopback = $this->requestOrigin( $link, $args, $ip );

			if ( ! $this->looksBlocked( $loopback ) && $this->bodyMatchesPost( $loopback, $id, $taxname ) ) {
				return $loopback;
			}
		}

		// No local origin served this post: remember briefly so we don't probe
		// on every analysis, then hand back the blocked public response so the
		// caller surfaces a clear message instead of wrong content.
		set_transient( $transient, self::LOOPBACK_NONE, 10 * MINUTE_IN_SECONDS );

		return $response;
	}

	/**
	 * Build the loop-back cookie jar, scoped to the host being called.
	 *
	 * The preview is fetched with the editor's own session so a draft, a
	 * password-protected post or a members-only page renders as they see it.
	 * That means the administrator's authentication cookies travel with the
	 * request, and where they are allowed to travel is the whole question.
	 *
	 * They used to be created without a domain. WordPress Requests treats a
	 * domainless cookie as matching every host, so any redirect the fetched
	 * page returned carried the administrator session with it — and the
	 * redirect target is attacker-controlled: a user who can edit their own
	 * post can store a SEOPress redirection on it, then wait for an
	 * administrator to open that post in the editor. Reported as #1937.
	 *
	 * Scoping each cookie to the host actually being called is what stops
	 * that: Requests then declines to send them anywhere else, whatever the
	 * response says. followSafely() refuses the cross-origin hop as well, so
	 * neither layer alone has to be perfect.
	 *
	 * @since 10.2.0
	 *
	 * @param string $host The host the request is being sent to.
	 *
	 * @return \WP_Http_Cookie[]
	 */
	private function buildCookies( $host ) {
		$host = is_string( $host ) ? strtolower( trim( $host ) ) : '';

		if ( '' === $host || empty( $_COOKIE ) ) {
			return array();
		}

		$cookies = array();

		foreach ( $_COOKIE as $name => $value ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Values are forwarded verbatim to our own host; sanitizing would corrupt session tokens.
			if ( 'PHPSESSID' === $name ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$value = implode( '|', $value );
			}

			$cookies[] = new \WP_Http_Cookie(
				array(
					'name'      => $name,
					'value'     => $value,
					'domain'    => $host,
					'path'      => '/',
					// Not optional. WP_Http::normalize_cookies() forwards
					// `host_only` verbatim into the Requests cookie, and
					// WP_Http_Cookie leaves the property null unless it is
					// given. array_merge() then overwrites Requests' own
					// `host-only => true` default with that null, and
					// Cookie::domain_matches() tests it with `=== true`, so
					// null falls through to suffix matching: a cookie scoped
					// to example.org would still be sent to any subdomain,
					// evil.example.org included.
					'host_only' => true,
				)
			);
		}

		return $cookies;
	}

	/**
	 * Perform the request, following redirects only while they stay on the
	 * site that was asked for.
	 *
	 * WP_Http follows redirects itself, but it cannot be told "only within
	 * this site", and the destination here is attacker-controlled. So the hops
	 * are walked here instead: isSameSiteHop() decides, and anything it
	 * refuses is handed back as the redirect response it is, so the caller can
	 * report an unusable preview rather than analyse the server's redirect
	 * stub.
	 *
	 * The cookie scoping in buildCookies() already prevents the session from
	 * leaving the site. This is the second half: it also stops the server
	 * being used to reach hosts it was never meant to reach, which is the
	 * server-side request forgery part of the same report.
	 *
	 * @since 10.2.0
	 *
	 * @param string $url  The URL to fetch.
	 * @param array  $args The wp_remote_get arguments.
	 *
	 * @return array|\WP_Error
	 */
	private function followSafely( $url, $args ) {
		$max      = 2;
		$args     = is_array( $args ) ? $args : array();
		$response = null;

		// Never let WP_Http follow on our behalf: the hops are checked here.
		// Forced after the seopress_real_preview_remote filter on purpose — a
		// filter must not be able to hand the chain back to WP_Http.
		$args['redirection'] = 0;

		for ( $hop = 0; $hop <= $max; $hop++ ) {
			$response = wp_remote_get( $url, $args );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$code = (int) wp_remote_retrieve_response_code( $response );

			if ( ! in_array( $code, array( 301, 302, 303, 307, 308 ), true ) ) {
				return $response;
			}

			$location = wp_remote_retrieve_header( $response, 'location' );

			if ( empty( $location ) || ! is_string( $location ) ) {
				return $response;
			}

			$next = $this->resolveLocation( $url, $location );

			if ( '' === $next || ! $this->isSameSiteHop( $url, $next ) ) {
				// Off-site: stop here and let the caller decide. The body of a
				// redirect is not the post, so it is never analysed.
				return $response;
			}

			$url = $next;

			// The cookies are host-only, so a hop that legitimately changes
			// host (the www canonicalisation) has to have them re-bound to the
			// new host: left on the old one they would simply not be sent, and
			// a draft would come back as a 404. Only hops isSameSiteHop() has
			// already accepted get here.
			if ( isset( $args['cookies'] ) ) {
				$args['cookies'] = $this->buildCookies( wp_parse_url( $url, PHP_URL_HOST ) );
			}
		}

		return $response;
	}

	/**
	 * Whether a redirect may be followed: does it stay on the site it started
	 * from?
	 *
	 * Strict origin equality was too strict to ship. The two most ordinary
	 * canonical redirects on the web both change the origin — http to https,
	 * and www to non-www (or back) — and refusing them broke the content
	 * analysis on every site whose home_url() is not already the canonical
	 * form, silently, by handing the server's "301 Moved Permanently" stub to
	 * the analyser.
	 *
	 * So the rule is: same host up to the `www.` prefix, no scheme downgrade,
	 * and a port that is either unchanged or the default of the target scheme.
	 * `www.` is the only prefix allowed — an arbitrary subdomain is not the
	 * same site, and this is the check that keeps the administrator session
	 * from walking to one.
	 *
	 * @since 10.2.0
	 *
	 * @param string $from The URL that returned the redirect.
	 * @param string $to   The absolute URL it points to.
	 *
	 * @return bool
	 */
	private function isSameSiteHop( $from, $to ) {
		$from_parsed = wp_parse_url( (string) $from );
		$to_parsed   = wp_parse_url( (string) $to );

		if ( empty( $from_parsed['host'] ) || empty( $to_parsed['host'] ) ) {
			return false;
		}

		$from_scheme = isset( $from_parsed['scheme'] ) ? strtolower( $from_parsed['scheme'] ) : 'http';
		$to_scheme   = isset( $to_parsed['scheme'] ) ? strtolower( $to_parsed['scheme'] ) : 'http';

		if ( ! in_array( $to_scheme, array( 'http', 'https' ), true ) ) {
			return false;
		}

		// http -> https is the ordinary upgrade. https -> http is a downgrade
		// and is not something we replay a session over.
		if ( $from_scheme !== $to_scheme && ! ( 'http' === $from_scheme && 'https' === $to_scheme ) ) {
			return false;
		}

		$from_port = isset( $from_parsed['port'] ) ? (int) $from_parsed['port'] : ( 'https' === $from_scheme ? 443 : 80 );
		$to_port   = isset( $to_parsed['port'] ) ? (int) $to_parsed['port'] : ( 'https' === $to_scheme ? 443 : 80 );

		// Unchanged, or the default of the target scheme (which is what the
		// 80 -> 443 upgrade looks like). Any other port is a different service
		// on the same machine, not this site.
		$to_default_port = 'https' === $to_scheme ? 443 : 80;

		if ( $to_port !== $from_port && $to_port !== $to_default_port ) {
			return false;
		}

		$from_host = $this->baseHost( $from_parsed['host'] );
		$to_host   = $this->baseHost( $to_parsed['host'] );

		if ( '' === $to_host ) {
			return false;
		}

		if ( $from_host === $to_host ) {
			return true;
		}

		/**
		 * Extra hosts the preview fetch is allowed to be redirected to.
		 *
		 * Only for a site that legitimately serves its own content from
		 * another domain — a multilingual install with one domain per
		 * language is the usual case. The editor's session travels to whatever
		 * is listed here, so list only hosts you own.
		 *
		 * @since 10.2.0
		 *
		 * @param array  $hosts Additional allowed redirect hosts. Default empty.
		 * @param string $from  The URL that returned the redirect.
		 * @param string $to    The absolute URL it points to.
		 */
		$allowed = apply_filters( 'seopress_real_preview_allowed_redirect_hosts', array(), $from, $to );

		foreach ( (array) $allowed as $host ) {
			if ( $this->baseHost( $host ) === $to_host ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * A host reduced to what identifies the site: lowercased, without its
	 * trailing root dot and without the `www.` prefix.
	 *
	 * @since 10.2.0
	 *
	 * @param string $host The host.
	 *
	 * @return string
	 */
	private function baseHost( $host ) {
		$host = strtolower( trim( (string) $host ) );
		$host = rtrim( $host, '.' );

		if ( 0 === strpos( $host, 'www.' ) ) {
			$host = substr( $host, 4 );
		}

		return $host;
	}

	/**
	 * Turn a Location header into an absolute URL against the URL it came
	 * from, so a relative hop is compared on the same footing as an absolute
	 * one.
	 *
	 * @since 10.2.0
	 *
	 * @param string $base     The URL that returned the redirect.
	 * @param string $location The raw Location header.
	 *
	 * @return string Empty when the location cannot be resolved.
	 */
	private function resolveLocation( $base, $location ) {
		$location = trim( $location );

		if ( '' === $location ) {
			return '';
		}

		$parsed = wp_parse_url( $base );

		if ( empty( $parsed['host'] ) ) {
			return '';
		}

		$scheme = isset( $parsed['scheme'] ) ? $parsed['scheme'] : 'http';
		$root   = $scheme . '://' . $parsed['host'];

		if ( isset( $parsed['port'] ) ) {
			$root .= ':' . (int) $parsed['port'];
		}

		// Protocol-relative: keep the scheme, take the rest as given. Checked
		// before the absolute test below, not after: wp_parse_url() reports a
		// host for "//host/path" too, so an absolute-looking but scheme-less
		// URL would be handed back as is, read as http, and a
		// "//same-host/path" hop on an https site would then look like a
		// downgrade and be refused.
		if ( 0 === strpos( $location, '//' ) ) {
			return $scheme . ':' . $location;
		}

		// Already absolute.
		if ( wp_parse_url( $location, PHP_URL_HOST ) ) {
			return $location;
		}

		if ( 0 === strpos( $location, '/' ) ) {
			return $root . $location;
		}

		$path = isset( $parsed['path'] ) ? $parsed['path'] : '/';
		$path = substr( $path, 0, strrpos( $path, '/' ) + 1 );

		return $root . $path . $location;
	}

	/**
	 * Local origin addresses to try, in order.
	 *
	 * 127.0.0.1 covers the common single-server setup and is what host-level
	 * WAFs exempt; SERVER_ADDR reaches the origin on hosts that do not listen on
	 * loopback; ::1 covers IPv6-only stacks. None require any per-host
	 * configuration; a split web/app tier matches none and falls back to the
	 * public URL.
	 *
	 * @return array
	 */
	private function candidateIps() {
		$ips = array( '127.0.0.1' );

		if ( ! empty( $_SERVER['SERVER_ADDR'] ) ) {
			// Keep only IP characters: this is both the validation and the
			// sanitization of the raw server value.
			$server_addr = preg_replace( '/[^0-9A-Fa-f:.]/', '', sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) );
			if ( ! empty( $server_addr ) ) {
				$ips[] = $server_addr;
			}
		}

		$ips[] = '::1';

		// Back-compat: the previous single-IP override still wins when set.
		$legacy = apply_filters( 'seopress_real_preview_loopback_ip', '' );
		if ( ! empty( $legacy ) ) {
			array_unshift( $ips, $legacy );
		}

		/**
		 * Filter the ordered list of local origins probed to bypass a proxy.
		 *
		 * @since 9.9.0
		 *
		 * @param array $ips The candidate IP addresses.
		 */
		$ips = apply_filters( 'seopress_real_preview_loopback_ips', $ips );

		return array_values( array_unique( array_filter( array_map( 'trim', (array) $ips ) ) ) );
	}

	/**
	 * Request the link forcing the connection to a local origin so it never
	 * transits a front proxy/CDN.
	 *
	 * The URL host is swapped for the loop-back IP while the original Host
	 * header is preserved, so the right vhost is still served. This is
	 * transport agnostic: it works the same with the cURL and the streams
	 * WP_Http transports (unlike CURLOPT_RESOLVE, which is cURL-only). The
	 * certificate is not validated since we connect by IP and the SNI no
	 * longer matches — acceptable because we are talking to our own server.
	 * The timeout is capped low so an origin that does not listen locally
	 * fails fast instead of burning the full public timeout a second time.
	 *
	 * @param string $link The preview URL.
	 * @param array  $args The wp_remote_get arguments.
	 * @param string $ip   The local IP to connect to.
	 *
	 * @return array|\WP_Error
	 */
	private function requestOrigin( $link, $args, $ip ) {
		if ( empty( $ip ) ) {
			return new \WP_Error( 'seopress_no_loopback_ip', 'No loop-back IP available.' );
		}

		$parsed = wp_parse_url( $link );

		if ( empty( $parsed['host'] ) ) {
			return new \WP_Error( 'seopress_no_host', 'No host in the preview link.' );
		}

		// Preserve the original Host so vhost routing still selects the site.
		// A non-default port is kept in the header, as a browser would.
		$host = $parsed['host'];
		if ( isset( $parsed['port'] ) ) {
			$host .= ':' . (int) $parsed['port'];
		}

		if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
			$args['headers'] = array();
		}
		$args['headers']['Host'] = $host;

		// We connect by IP: the SNI/certificate can no longer match the host.
		$args['sslverify'] = false;

		// An origin that is not reachable locally must fail fast.
		$args['timeout'] = min( isset( $args['timeout'] ) ? (int) $args['timeout'] : 10, 5 );

		$target = $this->replaceHost( $parsed, $ip );

		// The connection goes to the IP, so the cookies have to be scoped to
		// it: bound to the public host they would simply not be sent, and left
		// domainless they would follow a redirect anywhere.
		$cookies = $this->buildCookies( wp_parse_url( $target, PHP_URL_HOST ) );
		if ( ! empty( $cookies ) ) {
			$args['cookies'] = $cookies;
		}

		return $this->followSafely( $target, $args );
	}

	/**
	 * Rebuild a URL with its host swapped for the loop-back IP, keeping the
	 * scheme, port, path and query untouched.
	 *
	 * @param array  $parsed The wp_parse_url() result of the original link.
	 * @param string $ip     The loop-back IP to connect to.
	 *
	 * @return string
	 */
	private function replaceHost( $parsed, $ip ) {
		$scheme = isset( $parsed['scheme'] ) ? $parsed['scheme'] : 'http';

		// Bracket IPv6 literals so the resulting URL stays valid.
		if ( false !== strpos( $ip, ':' ) ) {
			$ip = '[' . $ip . ']';
		}

		$url = $scheme . '://' . $ip;

		if ( isset( $parsed['port'] ) ) {
			$url .= ':' . (int) $parsed['port'];
		}

		if ( isset( $parsed['path'] ) ) {
			$url .= $parsed['path'];
		}

		if ( isset( $parsed['query'] ) ) {
			$url .= '?' . $parsed['query'];
		}

		return $url;
	}

	/**
	 * Whether the preview link targets this very site (so a loop-back is
	 * safe) and the loop-back bypass is enabled.
	 *
	 * @param string $link The preview URL.
	 *
	 * @return bool
	 */
	private function isSelfHostedLink( $link ) {
		if ( false === apply_filters( 'seopress_real_preview_loopback_enabled', true ) ) {
			return false;
		}

		$link_host = wp_parse_url( $link, PHP_URL_HOST );

		if ( empty( $link_host ) ) {
			return false;
		}

		$site_hosts = array_filter(
			array(
				strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
				strtolower( (string) wp_parse_url( site_url(), PHP_URL_HOST ) ),
			)
		);

		return in_array( strtolower( $link_host ), $site_hosts, true );
	}

	/**
	 * Whether the response looks blocked by a CDN/WAF (so it is worth
	 * retrying through the loop-back).
	 *
	 * Covers the three ways a front proxy gets in the way: a transport error
	 * or timeout (WP_Error), a block status code, and a soft block where a
	 * 200 carries a JS-challenge interstitial rather than the page.
	 *
	 * @param array|\WP_Error $response The wp_remote_get response.
	 *
	 * @return bool
	 */
	private function looksBlocked( $response ) {
		if ( is_wp_error( $response ) ) {
			return true;
		}

		if ( $this->isBlockedStatus( (int) wp_remote_retrieve_response_code( $response ) ) ) {
			return true;
		}

		return $this->looksLikeChallenge( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Whether a 200 body is actually a CDN/WAF JS-challenge interstitial
	 * (Cloudflare "Under Attack" / Bot Fight Mode, Sucuri...) instead of the
	 * rendered page. Only the head of the document is scanned: these pages are
	 * tiny, so this avoids walking a full-size legitimate response.
	 *
	 * @param string $body The response body.
	 *
	 * @return bool
	 */
	private function looksLikeChallenge( $body ) {
		if ( empty( $body ) ) {
			return false;
		}

		$head = substr( $body, 0, 8000 );

		$signatures = array(
			'cf-browser-verification',
			'challenge-platform',
			'__cf_chl',
			'cf_chl_opt',
			'Just a moment...',
			'Checking your browser before',
			'Sucuri WebSite Firewall',
		);

		foreach ( $signatures as $signature ) {
			if ( false !== stripos( $head, $signature ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether a loop-back response really is the previewed post/term, so a
	 * wrong-vhost 200 (default site, another tenant...) is never analyzed as
	 * this content.
	 *
	 * Matches on the body classes WordPress prints for the singular/term view
	 * and on the permalink. These markers are absent from a foreign vhost's
	 * page, so a mismatch is rejected and the caller keeps the honest blocked
	 * message instead of scoring the wrong HTML.
	 *
	 * @param array|\WP_Error $response The loop-back response.
	 * @param int             $id       The post/term id being previewed.
	 * @param string          $taxname  The taxonomy name when previewing a term.
	 *
	 * @return bool
	 */
	private function bodyMatchesPost( $response, $id, $taxname = null ) {
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		// Too short to be a rendered page (origin error page, empty body...).
		if ( strlen( $body ) < 200 ) {
			return false;
		}

		$id      = (int) $id;
		$markers = array();

		if ( empty( $taxname ) ) {
			// Body classes printed by body_class() for the singular view.
			$markers[] = 'postid-' . $id;
			$markers[] = 'page-id-' . $id;

			$permalink = get_permalink( $id );
			if ( ! empty( $permalink ) ) {
				$markers[] = $permalink;
			}
		} else {
			$markers[] = 'term-' . $id;
			$markers[] = 'tax-' . $taxname;

			$term_link = get_term_link( $id, $taxname );
			if ( ! is_wp_error( $term_link ) && ! empty( $term_link ) ) {
				$markers[] = $term_link;
			}
		}

		/**
		 * Filter the markers proving a loop-back response is this very post.
		 * Themes that do not print the default body classes can add their own.
		 *
		 * @since 9.9.0
		 *
		 * @param array  $markers The identity markers to look for in the body.
		 * @param int    $id      The post/term id.
		 * @param string $taxname The taxonomy name when previewing a term.
		 */
		$markers = array_filter( apply_filters( 'seopress_real_preview_loopback_markers', $markers, $id, $taxname ) );

		foreach ( $markers as $marker ) {
			if ( false !== strpos( $body, $marker ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether an HTTP status code is a typical CDN/WAF block.
	 *
	 * 403/429/503: challenge, rate-limit or "under attack" interstitial.
	 * 520-526: Cloudflare-specific origin errors.
	 *
	 * @param int $code The HTTP status code.
	 *
	 * @return bool
	 */
	private function isBlockedStatus( $code ) {
		return in_array( $code, array( 403, 429, 503, 520, 521, 522, 523, 524, 525, 526 ), true );
	}
}

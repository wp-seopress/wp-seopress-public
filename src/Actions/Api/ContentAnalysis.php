<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

/**
 * Content Analysis
 */
class ContentAnalysis implements ExecuteHooks {

	/**
	 * Default number of content-analysis requests allowed per minute and
	 * per user before a 429 is returned.
	 *
	 * @since 9.9.0
	 */
	const RATE_LIMIT_MAX = 30;

	/**
	 * Transient prefix for the per-user rate-limit counter.
	 *
	 * @since 9.9.0
	 */
	const RATE_LIMIT_TRANSIENT = 'seopress_ca_rl_';

	/**
	 * The Content Analysis hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Content Analysis register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/content-analysis',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);

		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/content-analysis',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					$post_id = $request['id'];
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);

		// Analyze the rendered HTML captured by the browser. Same handler as the
		// GET route, but the page source travels in the POST body so it survives
		// hosts whose WAF challenges a server-side loop-back. Distinct path from
		// the POST above (which saves the score).
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/content-analysis/analyze',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);

		/**
		 * Headless / standalone content analysis.
		 *
		 * Analyzes an arbitrary HTML payload without requiring SEOPress to
		 * HTTP-fetch a rendered preview, which a decoupled frontend cannot
		 * provide. Useful for headless WordPress, CI pipelines and external
		 * tooling.
		 *
		 * @since 9.9.0
		 */
		register_rest_route(
			'seopress/v1',
			'/content-analysis',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'analyze' ),
				'args'                => array(
					'content'         => array(
						'required' => true,
						'type'     => 'string',
					),
					'title'           => array(
						'type' => 'string',
					),
					'description'     => array(
						'type' => 'string',
					),
					'target_keyword'  => array(
						'type' => 'string',
					),
					'target_keywords' => array(
						'type' => 'string',
					),
					'post_id'         => array(
						'type'              => 'integer',
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return '' === $param || null === $param || is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					$post_id = (int) $request->get_param( 'post_id' );
					if ( $post_id > 0 ) {
						return current_user_can( 'edit_post', $post_id );
					}
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Per-user, fixed-window (one minute) rate limit shared across the
	 * content-analysis endpoints: a single budget per authenticated user
	 * covers the `get`, `save` and headless paths combined. Site audits
	 * aside, content analysis is the cheapest of the SEOPress REST
	 * endpoints to spam, so we cap it per authenticated user rather than
	 * per IP (the endpoints are capability-gated anyway).
	 *
	 * @since 9.9.0
	 *
	 * @param string $bucket Logical bucket id (e.g. "get", "headless"),
	 *                        passed to the filter for context only.
	 * @return true|\WP_Error True when allowed, WP_Error 429 otherwise.
	 */
	private function check_rate_limit( $bucket ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return true;
		}

		/**
		 * Filter the maximum number of content-analysis requests allowed
		 * per user and per minute. Return 0 (or a negative value) to
		 * disable rate limiting entirely.
		 *
		 * @since 9.9.0
		 *
		 * @param int    $max    Default request cap.
		 * @param string $bucket Logical bucket id.
		 */
		$max = (int) apply_filters( 'seopress_content_analysis_rate_limit', self::RATE_LIMIT_MAX, $bucket );
		if ( $max <= 0 ) {
			return true;
		}

		$now  = time();
		$key  = self::RATE_LIMIT_TRANSIENT . $user_id;
		$data = get_transient( $key );

		// Anchor the window on the first request: when no live window
		// exists (or it has elapsed), open a fresh one-minute window.
		if ( ! is_array( $data ) || empty( $data['reset'] ) || $data['reset'] <= $now ) {
			$data = array(
				'count' => 0,
				'reset' => $now + MINUTE_IN_SECONDS,
			);
		}

		$remaining = $data['reset'] - $now;

		if ( $data['count'] >= $max ) {
			return new \WP_Error(
				'rate_limited',
				__( 'Too many content analysis requests. Please wait a moment before trying again.', 'wp-seopress' ),
				array(
					'status'      => 429,
					'retry_after' => $remaining,
				)
			);
		}

		// Bump the counter while preserving the original window TTL, so
		// the limit resets a fixed minute after the first request rather
		// than sliding forward on every hit.
		++$data['count'];
		set_transient( $key, $data, $remaining );

		return true;
	}

	/**
	 * The Content Analysis process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function get( \WP_REST_Request $request ) {
		$rate = $this->check_rate_limit( 'get' );
		if ( is_wp_error( $rate ) ) {
			return $rate;
		}

		$api_header = new ApiHeader();
		$api_header->hooks();

		$id = (int) $request->get_param( 'id' );

		$link_preview = seopress_get_service( 'RequestPreview' )->getLinkRequest( $id );

		// Prefer the rendered HTML captured by the browser: it sees exactly what
		// a crawler sees and passes any host-level WAF/CDN that would challenge a
		// server-side loop-back. The DOM is only parsed for read-only extraction
		// (never output or executed) and the caller already holds edit_post.
		$html = $request->get_param( 'html' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- parsed read-only by DOMDocument, never echoed.

		if ( ! empty( $html ) ) {
			$str = (string) $html;
		} else {
			// Fallback: server-side loop-back when the client cannot supply the
			// HTML (JS disabled, fetch failed...).
			$dom_result = seopress_get_service( 'RequestPreview' )->getDomById( $id );

			if ( ! $dom_result['success'] ) {
				$default_response = array(
					'title'     => '...',
					'meta_desc' => '...',
				);

				switch ( $dom_result['code'] ) {
					case 404:
						$default_response['title'] = __( 'To get your Google snippet preview, publish your post!', 'wp-seopress' );
						break;
					case 401:
						$default_response['title'] = __( 'Your site is protected by an authentication.', 'wp-seopress' );
						break;
					case 'blocked':
						$default_response['title'] = __( 'Content analysis was blocked (HTTP 403/503). A CDN, firewall or security plugin is preventing your server from loading the preview.', 'wp-seopress' );
						break;
					case 'unreachable':
						$default_response['title'] = __( 'Your site could not be reached for content analysis. Please check your server, DNS or firewall configuration.', 'wp-seopress' );
						break;
				}

				return new \WP_REST_Response( $default_response );
			}

			$str = $dom_result['body'];
		}

		$data                 = $this->analyze_html( $str, $id, $request->get_param( 'target_keywords' ) );
		$data['link_preview'] = $link_preview;

		// link_preview is set after analyze_html() ran getDataAnalyze /
		// saveData, so persist it again so the stored row stays in sync
		// with the response (matches the legacy double-save behavior).
		seopress_get_service( 'ContentAnalysisDatabase' )->saveData(
			$id,
			$data,
			$this->resolve_keywords( $id, $request->get_param( 'target_keywords' ) )
		);

		/**
		 * Filter the content-analysis REST response before it is returned
		 * to the editor. Pro hooks into this to inject the per-(post, check)
		 * seopress_seo_issues rows so the React panel can render its
		 * ignore controls without an extra round trip.
		 *
		 * @since 9.9.0
		 *
		 * @param array $data The analysis payload.
		 * @param int   $id   Post id being analyzed.
		 */
		$data = apply_filters( 'seopress_content_analysis_response', $data, $id );

		return new \WP_REST_Response( $data );
	}

	/**
	 * Headless content analysis.
	 *
	 * Accepts a raw HTML/content payload (optionally a meta title and
	 * description, optional target keyword) and returns the same analysis
	 * shape as the editor endpoint. When `post_id` is supplied the result
	 * is persisted against that post — the recommended path for a decoupled
	 * frontend that owns the rendered markup. Without `post_id` a throwaway
	 * draft anchors the post-meta-dependent checks and is removed before
	 * the response is returned, so nothing is persisted.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 9.9.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function analyze( \WP_REST_Request $request ) {
		$rate = $this->check_rate_limit( 'headless' );
		if ( is_wp_error( $rate ) ) {
			return $rate;
		}

		$content = (string) $request->get_param( 'content' );
		if ( '' === trim( $content ) ) {
			return new \WP_Error(
				'missing_content',
				__( 'The "content" parameter is required.', 'wp-seopress' ),
				array( 'status' => 400 )
			);
		}

		/**
		 * Cap the payload size before any DOM parsing or temp-post creation.
		 * The endpoint is only gated by `edit_posts` (Contributor+), so an
		 * unbounded payload would let a low-privileged user exhaust memory /
		 * CPU through DOMDocument. Filterable for sites with larger pages.
		 *
		 * @since 9.9.0
		 *
		 * @param int $max_bytes Maximum accepted content length, in bytes.
		 */
		$max_bytes = (int) apply_filters( 'seopress_content_analysis_max_payload_bytes', 2 * MB_IN_BYTES );
		if ( $max_bytes > 0 && strlen( $content ) > $max_bytes ) {
			return new \WP_Error(
				'content_too_large',
				sprintf(
					/* translators: %s: maximum size, e.g. "2 MB". */
					__( 'The "content" payload exceeds the maximum allowed size (%s).', 'wp-seopress' ),
					size_format( $max_bytes )
				),
				array( 'status' => 413 )
			);
		}

		$title       = sanitize_text_field( (string) $request->get_param( 'title' ) );
		$description = sanitize_text_field( (string) $request->get_param( 'description' ) );

		// target_keyword is the documented headless param; target_keywords
		// is accepted as an alias for parity with the editor endpoint.
		$target_keywords_param = $request->get_param( 'target_keyword' );
		if ( null === $target_keywords_param ) {
			$target_keywords_param = $request->get_param( 'target_keywords' );
		}

		// If the payload is not a full document, wrap it so the DOM parser
		// can resolve <title> / meta description the same way it would for
		// a rendered page.
		if ( false !== stripos( $content, '<html' ) ) {
			$html = $content;
		} else {
			$head = '';
			if ( '' !== $title ) {
				$head .= '<title>' . esc_html( $title ) . '</title>';
			}
			if ( '' !== $description ) {
				$head .= '<meta name="description" content="' . esc_attr( $description ) . '" />';
			}
			$html = '<!DOCTYPE html><html><head>' . $head . '</head><body>' . $content . '</body></html>';
		}

		$post_id       = (int) $request->get_param( 'post_id' );
		$is_persistent = $post_id > 0;
		$temp_post_id  = 0;

		if ( $is_persistent ) {
			if ( ! get_post( $post_id ) ) {
				return new \WP_Error(
					'invalid_post',
					__( 'The provided post_id does not exist.', 'wp-seopress' ),
					array( 'status' => 404 )
				);
			}
			$analysis_id = $post_id;
		} else {
			// Throwaway anchor: the analysis pipeline keys on a real post
			// (post meta, slug, modified date). Kept as a draft so it is
			// never publicly queryable, and force-deleted in the finally.
			$temp_post_id = wp_insert_post(
				array(
					'post_title'   => '' !== $title ? $title : __( 'SEOPress headless analysis', 'wp-seopress' ),
					'post_content' => wp_kses_post( $content ),
					'post_status'  => 'draft',
					'post_type'    => 'post',
				),
				true
			);

			if ( is_wp_error( $temp_post_id ) ) {
				return new \WP_Error(
					'analysis_failed',
					__( 'Could not initialize the content analysis.', 'wp-seopress' ),
					array( 'status' => 500 )
				);
			}

			$analysis_id = (int) $temp_post_id;
		}

		try {
			$data                 = $this->analyze_html( $html, $analysis_id, $target_keywords_param );
			$data['link_preview'] = get_permalink( $analysis_id );

			if ( $is_persistent ) {
				seopress_get_service( 'ContentAnalysisDatabase' )->saveData(
					$analysis_id,
					$data,
					$this->resolve_keywords( $analysis_id, $target_keywords_param )
				);

				/** This filter is documented in src/Actions/Api/ContentAnalysis.php */
				$data = apply_filters( 'seopress_content_analysis_response', $data, $analysis_id );
			}
		} finally {
			if ( ! $is_persistent && $temp_post_id ) {
				$this->cleanup_temp_post( (int) $temp_post_id );
			}
		}

		return new \WP_REST_Response( $data );
	}

	/**
	 * Run the DOM filter + analysis + score pipeline on an HTML string for
	 * a given anchor post id. Mirrors the legacy get() body so both the
	 * editor and headless endpoints stay behaviorally identical.
	 *
	 * @since 9.9.0
	 *
	 * @param string      $html                  HTML to analyze.
	 * @param int         $id                    Anchor post id.
	 * @param string|null $target_keywords_param Explicit keywords override, or null to use the saved ones.
	 *
	 * @return array
	 */
	private function analyze_html( $html, $id, $target_keywords_param ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$data = seopress_get_service( 'DomFilterContent' )->getData( $html, $id );
		$data = seopress_get_service( 'DomAnalysis' )->getDataAnalyze(
			$data,
			array(
				'id' => $id,
			)
		);

		/**
		 * We delete old values because we have a new structure
		 *
		 * @deprecated
		 * @since 7.3.0
		 */
		delete_post_meta( $id, '_seopress_content_analysis_api' );
		delete_post_meta( $id, '_seopress_analysis_data' );

		$keywords = $this->resolve_keywords( $id, $target_keywords_param );

		// Save analysis data first so the analysis below reads fresh values
		// from the database.
		seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

		$post = get_post( $id );

		// Run the analysis once and reuse it for both the score and the
		// AI content-quality cards below, instead of calling getScore()
		// (which internally runs getAnalyzes() and then throws everything
		// but the impacts away).
		$analyzes      = seopress_get_service( 'GetContentAnalysis' )->getAnalyzes( $post );
		$score         = array_unique( array_values( wp_list_pluck( $analyzes, 'impact' ) ) );
		$data['score'] = $score;

		// Surface the AI content-quality checks (content depth, heading
		// structure, media in content, content readability) so the editor
		// Content Analysis tab renders the exact same impact and
		// description as the Site Audit. These are computed server-side
		// but were never part of the REST payload, so the metabox could
		// not display them.
		foreach ( array( 'content_depth', 'heading_hierarchy', 'content_media', 'content_structure' ) as $check ) {
			if ( ! isset( $analyzes[ $check ] ) ) {
				continue;
			}
			$data[ $check ] = array(
				'impact' => isset( $analyzes[ $check ]['impact'] ) ? $analyzes[ $check ]['impact'] : 'good',
				'desc'   => isset( $analyzes[ $check ]['desc'] ) ? $analyzes[ $check ]['desc'] : '',
			);
		}

		seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

		return $data;
	}

	/**
	 * Resolve the target keywords for a request: an explicitly provided
	 * value (even empty, meaning "no keywords") overrides the saved ones;
	 * null falls back to the database.
	 *
	 * @since 9.9.0
	 *
	 * @param int         $id                    Post id.
	 * @param string|null $target_keywords_param Request value or null.
	 *
	 * @return array
	 */
	private function resolve_keywords( $id, $target_keywords_param ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$options = array( 'id' => $id );

		// null = parameter not provided (use DB).
		// ""   = parameter provided but empty (use no keywords).
		if ( null !== $target_keywords_param ) {
			$options['target_keywords'] = $target_keywords_param;
		}

		return seopress_get_service( 'DomAnalysis' )->getKeywords( $options );
	}

	/**
	 * Remove a throwaway analysis post and every row it produced so a
	 * headless request leaves no trace (post, meta, content-analysis row,
	 * SEO issues).
	 *
	 * @since 9.9.0
	 *
	 * @param int $temp_post_id The temporary post id.
	 *
	 * @return void
	 */
	private function cleanup_temp_post( $temp_post_id ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		global $wpdb;

		wp_delete_post( $temp_post_id, true );

		$content_analysis_table = $wpdb->prefix . seopress_get_service( 'TableList' )->getTableContentAnalysis()->getName();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $content_analysis_table, array( 'post_id' => $temp_post_id ), array( '%d' ) );
		$wpdb->delete( $wpdb->prefix . 'seopress_seo_issues', array( 'post_id' => $temp_post_id ), array( '%d' ) );
		// phpcs:enable
	}

	/**
	 * The Content Analysis process save.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function save( \WP_REST_Request $request ) {
		$rate = $this->check_rate_limit( 'save' );
		if ( is_wp_error( $rate ) ) {
			return $rate;
		}

		$id             = (int) $request->get_param( 'id' );
		$score          = sanitize_text_field( $request->get_param( 'score' ) );
		$internal_links = map_deep( $request->get_param( 'internal_links' ), 'sanitize_text_field' );
		$outbound_links = map_deep( $request->get_param( 'outbound_links' ), 'sanitize_text_field' );

		$data = array(
			'internal_links' => $internal_links,
			'outbound_links' => $outbound_links,
			'score'          => $score,
		);

		update_post_meta( $id, '_seopress_content_analysis_api', $data );
		delete_post_meta( $id, '_seopress_analysis_data' );

		return new \WP_REST_Response( array( 'success' => true ) );
	}
}

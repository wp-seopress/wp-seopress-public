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
	}

	/**
	 * The Content Analysis process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function get( \WP_REST_Request $request ) {
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

		$data = seopress_get_service( 'DomFilterContent' )->getData( $str, $id );
		$data = seopress_get_service( 'DomAnalysis' )->getDataAnalyze(
			$data,
			array(
				'id' => $id,
			)
		);

		$save_data = array(
			'internal_links' => null,
			'outbound_links' => null,
			'score'          => null,
		);

		if ( isset( $data['internal_links'] ) ) {
			$save_data['internal_links'] = count( $data['internal_links']['value'] );
		}

		if ( isset( $data['outbound_links'] ) ) {
			$save_data['outbound_links'] = count( $data['outbound_links']['value'] );
		}

		/**
		 * We delete old values because we have a new structure
		 *
		 * @deprecated
		 * @since 7.3.0
		 */
		delete_post_meta( $id, '_seopress_content_analysis_api' );
		delete_post_meta( $id, '_seopress_analysis_data' );

		$data['link_preview'] = $link_preview;

		// Check if target_keywords was passed in the request (from frontend).
		// If the parameter exists (even if empty), pass it to getKeywords to override DB lookup.
		// null = parameter not provided, "" = parameter provided but empty (user cleared keywords).
		$target_keywords_param = $request->get_param( 'target_keywords' );
		$keywords_options      = array( 'id' => $id );

		// Only add target_keywords to options if the parameter was explicitly provided in the request.
		// This distinguishes between "not provided" (use DB) and "provided but empty" (use no keywords).
		if ( null !== $target_keywords_param ) {
			$keywords_options['target_keywords'] = $target_keywords_param;
		}

		$keywords = seopress_get_service( 'DomAnalysis' )->getKeywords( $keywords_options );

		// Save analysis data first so getScore() reads fresh values from the database.
		seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

		$post = get_post( $id );

		// Run the analysis once and reuse it for both the score and the
		// AI content-quality cards below, instead of running getScore()
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
	 * The Content Analysis process save.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function save( \WP_REST_Request $request ) {
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

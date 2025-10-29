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
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Content Analysis hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
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
					$post_id      = $request['id'];
					$current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

					if ( ! user_can( $current_user, 'edit_post', $post_id ) ) {
						return false;
					}

					return true;
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
			}

			return new \WP_REST_Response( $default_response );
		}

		$str = $dom_result['body'];

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

		$keywords = seopress_get_service( 'DomAnalysis' )->getKeywords(
			array(
				'id' => $id,
			)
		);

		$post          = get_post( $id );
		$score         = seopress_get_service( 'DomAnalysis' )->getScore( $post );
		$data['score'] = $score;
		seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

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
		$score          = $request->get_param( 'score' );
		$internal_links = $request->get_param( 'internal_links' );
		$outbound_links = $request->get_param( 'outbound_links' );

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

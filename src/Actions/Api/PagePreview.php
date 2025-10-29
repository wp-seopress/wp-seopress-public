<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

/**
 * Page Preview
 */
class PagePreview implements ExecuteHooks {

	/**
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Page Preview hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Page Preview register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/page-preview',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'preview' ),
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
	}

	/**
	 * The Page Preview process preview.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function preview( \WP_REST_Request $request ) {
		$api_header = new ApiHeader();
		$api_header->hooks();

		$id         = (int) $request->get_param( 'id' );
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

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$data['analyzed_content_id'] = $id;
		}

		$data['analysis_target_kw'] = array(
			'value' => array_filter( explode( ',', strtolower( get_post_meta( $id, '_seopress_analysis_target_kw', true ) ) ) ),
		);

		return new \WP_REST_Response( $data );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

/**
 * Count Target Keywords Use
 */
class CountTargetKeywordsUse implements ExecuteHooks {

	/**
	 * The Count Target Keywords Use hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Count Target Keywords Use register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/count-target-keywords-use',
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
	}

	/**
	 * The Count Target Keywords Use process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function get( \WP_REST_Request $request ) {
		$api_header = new ApiHeader();
		$api_header->hooks();

		$id              = (int) $request->get_param( 'id' );
		$target_keywords = $request->get_param( 'keywords' );

		$data = seopress_get_service( 'CountTargetKeywordsUse' )->getCountByKeywords( $target_keywords, $id );

		$data = apply_filters( 'seopress_get_count_target_keywords', $data );

		return new \WP_REST_Response( $data );
	}
}

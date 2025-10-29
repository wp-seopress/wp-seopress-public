<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Target Keywords
 */
class TargetKeywords implements ExecuteHooks {

	/**
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Target Keywords hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Target Keywords register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/target-keywords',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
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
			'/posts/(?P<id>\d+)/target-keywords',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'processPut' ),
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
	 * The Target Keywords process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processGet( \WP_REST_Request $request ) {
		$id              = $request->get_param( 'id' );
		$target_keywords = array_filter( explode( ',', strtolower( get_post_meta( $id, '_seopress_analysis_target_kw', true ) ) ) );

		$data = seopress_get_service( 'CountTargetKeywordsUse' )->getCountByKeywords( $target_keywords, $id );

		return new \WP_REST_Response(
			array(
				'value' => $target_keywords,
				'usage' => $data,
			)
		);
	}

	/**
	 * The Target Keywords process put.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processPut( \WP_REST_Request $request ) {
		$id     = $request->get_param( 'id' );
		$params = $request->get_params();
		if ( ! isset( $params['_seopress_analysis_target_kw'] ) ) {
			return new \WP_REST_Response(
				array(
					'code'         => 'error',
					'code_message' => 'missed_parameters',
				),
				403
			);
		}

		try {
			$target_keywords = implode( ',', array_map( 'trim', explode( ',', $params['_seopress_analysis_target_kw'] ) ) );
			update_post_meta( $id, '_seopress_analysis_target_kw', sanitize_text_field( $target_keywords ) );

			return new \WP_REST_Response(
				array(
					'code' => 'success',
				)
			);
		} catch ( \Exception $e ) {
			return new \WP_REST_Response(
				array(
					'code'         => 'error',
					'code_message' => 'execution_failed',
				),
				403
			);
		}
	}
}

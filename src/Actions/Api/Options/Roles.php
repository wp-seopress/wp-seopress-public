<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Roles
 */
class Roles implements ExecuteHooks {
	/**
	 * The Roles hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Roles permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.9
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'advanced' ) );
	}

	/**
	 * The Roles register.
	 *
	 * @since 5.9
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/roles',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Roles process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.9
	 */
	public function processGet( \WP_REST_Request $request ) {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles(); // phpcs:ignore
		}

		if ( empty( $wp_roles ) ) {
			return;
		}

		$data = $wp_roles->get_names();

		return new \WP_REST_Response( $data );
	}
}

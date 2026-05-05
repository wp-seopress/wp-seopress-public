<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Dashboard Settings
 */
class DashboardSettings implements ExecuteHooks {
	/**
	 * The Dashboard Settings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Dashboard Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'dashboard' ) );
	}

	/**
	 * The Dashboard Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/dashboard-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Dashboard Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_dashboard_option_name' );
		$toggles = get_option( 'seopress_toggle' );
		$notices = get_option( 'seopress_notices' );

		if ( empty( $options ) && empty( $toggles ) && empty( $notices ) ) {
			return new \WP_REST_Response( array() );
		}

		$data = array();

		foreach ( $options as $key => $value ) {
			$data[ $key ] = $value;
		}

		foreach ( $toggles as $key => $value ) {
			$data[ $key ] = $value;
		}

		foreach ( $notices as $key => $value ) {
			$data[ $key ] = $value;
		}

		return new \WP_REST_Response( $data );
	}
}

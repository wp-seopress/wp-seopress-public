<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * License Settings
 */
class LicenseSettings implements ExecuteHooks {
	/**
	 * The License Settings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The License Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'license' ) );
	}

	/**
	 * The License Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/license-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The License Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_pro_license_key' );

		if ( empty( $options ) ) {
			return new \WP_REST_Response( array() );
		}

		$data = array( 'license_key' => $options );

		return new \WP_REST_Response( $data );
	}
}

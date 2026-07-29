<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Analytics Settings
 */
class AnalyticsSettings implements ExecuteHooks {

	/**
	 * The Analytics Settings hooks.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Analytics Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'analytics' ) );
	}

	/**
	 * The Analytics Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/analytics-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/options/analytics-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processPost' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Analytics Settings process post.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processPost( \WP_REST_Request $request ) {
		$new_options = $request->get_json_params();

		if ( empty( $new_options ) || ! is_array( $new_options ) ) {
			return new \WP_Error(
				'invalid_data',
				__( 'Invalid data provided.', 'wp-seopress' ),
				array( 'status' => 400 )
			);
		}

		// Sanitize using the same function as PHP form saves.
		$sanitized_options = seopress_sanitize_options_fields( $new_options );

		update_option( 'seopress_google_analytics_option_name', $sanitized_options );

		do_action( 'seopress_analytics_settings_updated', $sanitized_options );

		$response = array(
			'success' => true,
			'message' => __( 'Settings saved successfully.', 'wp-seopress' ),
			'data'    => $sanitized_options,
		);

		// The custom tracking script fields keep their raw markup, so the
		// sanitizer stores them only for users with the `unfiltered_html`
		// capability. On multisite with DISALLOW_UNFILTERED_HTML that capability
		// is removed from everyone, super admins included, and the submitted
		// scripts are dropped. Surface that instead of reporting a plain success,
		// so the save is not silently misleading.
		$warning = $this->trackingScriptsWarning( $new_options, $sanitized_options );
		if ( '' !== $warning ) {
			$response['warning'] = $warning;
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Build a warning when submitted custom tracking scripts were not stored
	 * because the current user lacks the `unfiltered_html` capability.
	 *
	 * @param array $submitted The values sent by the client.
	 * @param array $saved     The values actually persisted after sanitization.
	 *
	 * @since 10.1.0
	 *
	 * @return string The warning message, or an empty string when nothing was dropped.
	 */
	protected function trackingScriptsWarning( $submitted, $saved ) {
		if ( current_user_can( 'unfiltered_html' ) ) {
			return '';
		}

		$fields = array(
			'seopress_google_analytics_other_tracking',
			'seopress_google_analytics_other_tracking_body',
			'seopress_google_analytics_other_tracking_footer',
		);

		foreach ( $fields as $field ) {
			$sent   = isset( $submitted[ $field ] ) ? (string) $submitted[ $field ] : '';
			$stored = isset( $saved[ $field ] ) ? (string) $saved[ $field ] : '';

			if ( '' !== trim( $sent ) && $sent !== $stored ) {
				return __( 'Your custom tracking scripts could not be saved: this installation does not allow unfiltered HTML (the "unfiltered_html" capability is disabled, typically by DISALLOW_UNFILTERED_HTML on multisite). Every other setting on this page was saved.', 'wp-seopress' );
			}
		}

		return '';
	}

	/**
	 * The Analytics Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_google_analytics_option_name' );

		if ( empty( $options ) ) {
			return new \WP_REST_Response( array() );
		}

		$data = array();

		foreach ( $options as $key => $value ) {
			$data[ $key ] = $value;
		}

		return new \WP_REST_Response( $data );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Social Settings
 */
class SocialSettings implements ExecuteHooks {
	/**
	 * The Social Settings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Social Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'social_networks' ) );
	}

	/**
	 * The Social Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/social-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/options/social-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processPost' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Social Settings process post.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processPost( \WP_REST_Request $request ) {
		$new_options = $request->get_json_params();

		// An empty object is a valid payload: it is what a settings screen sends
		// when nothing has ever been saved for it and no field was touched.
		// Rejecting it told the user their data was invalid for pressing Save on
		// an untouched page. Only a body that is not an array is invalid.
		if ( ! is_array( $new_options ) ) {
			return new \WP_Error(
				'invalid_data',
				__( 'Invalid data provided.', 'wp-seopress' ),
				array( 'status' => 400 )
			);
		}

		// Sanitize using the same function as PHP form saves.
		$sanitized_options = seopress_sanitize_options_fields( $new_options );

		update_option( 'seopress_social_option_name', $sanitized_options );

		do_action( 'seopress_social_settings_updated', $sanitized_options );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Settings saved successfully.', 'wp-seopress' ),
				'data'    => $sanitized_options,
			),
			200
		);
	}

	/**
	 * The Social Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_social_option_name' );

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

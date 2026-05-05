<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Indexing Settings
 */
class IndexingSettings implements ExecuteHooks {
	/**
	 * The Indexing Settings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Indexing Settings permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'instant_indexing' ) );
	}

	/**
	 * The Indexing Settings register.
	 *
	 * @since 5.5
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/options/indexing-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/options/indexing-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processPost' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * The Indexing Settings process post.
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

		update_option( 'seopress_instant_indexing_option_name', $sanitized_options );

		do_action( 'seopress_indexing_settings_updated', $sanitized_options );

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
	 * The Indexing Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.5
	 */
	public function processGet( \WP_REST_Request $request ) {
		$options = get_option( 'seopress_instant_indexing_option_name' );
		$log     = get_option( 'seopress_instant_indexing_log_option_name' );

		if ( empty( $options ) && empty( $log ) ) {
			return new \WP_REST_Response( array() );
		}

		$data = array();

		if ( is_array( $options ) ) {
			foreach ( $options as $key => $value ) {
				$data[ $key ] = $value;
			}
		}

		if ( is_array( $log ) ) {
			foreach ( $log as $key => $value ) {
				$data[ $key ] = $value;
			}
		}

		return new \WP_REST_Response( $data );
	}
}

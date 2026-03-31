<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Tools Settings REST API endpoints.
 */
class ToolsSettings implements ExecuteHooks {
	/**
	 * Current user ID
	 *
	 * @var int
	 */
	private $current_user = '';

	/**
	 * The ToolsSettings hooks.
	 *
	 * @since 5.0.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return boolean
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		$current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;
		if ( ! user_can( $current_user, 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register() {
		// Export settings.
		register_rest_route(
			'seopress/v1',
			'/tools/export',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processExport' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		// Import settings.
		register_rest_route(
			'seopress/v1',
			'/tools/import',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processImport' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		// Clean content scans.
		register_rest_route(
			'seopress/v1',
			'/tools/clean-content-scans',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processCleanContentScans' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		// Reset notices.
		register_rest_route(
			'seopress/v1',
			'/tools/reset-notices',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processResetNotices' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		// Reset all settings.
		register_rest_route(
			'seopress/v1',
			'/tools/reset-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processResetSettings' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * Export all settings.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processExport( \WP_REST_Request $request ) {
		$data = seopress_get_service( 'ExportSettings' )->handle();

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Import settings from JSON data.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processImport( \WP_REST_Request $request ) {
		$data = $request->get_json_params();

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error(
				'invalid_data',
				__( 'Invalid data provided. Please upload a valid SEOPress settings file.', 'wp-seopress' ),
				array( 'status' => 400 )
			);
		}

		seopress_get_service( 'ImportSettings' )->handle( $data );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Settings imported successfully.', 'wp-seopress' ),
			),
			200
		);
	}

	/**
	 * Clean content scans.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processCleanContentScans( \WP_REST_Request $request ) {
		global $wpdb;

		// Delete cache option.
		delete_option( 'seopress_content_analysis_api_in_progress' );

		// Clean post metas.
		$wpdb->query(
			"DELETE FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` IN ( '_seopress_analysis_data', '_seopress_content_analysis_api', '_seopress_analysis_data_oxygen', '_seopress_content_analysis_api_in_progress')"
		);

		// Clean custom table if it exists.
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}seopress_content_analysis'" ) === $wpdb->prefix . 'seopress_content_analysis' ) {
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}seopress_content_analysis`" );
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Content scans have been successfully deleted.', 'wp-seopress' ),
			),
			200
		);
	}

	/**
	 * Reset notices.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processResetNotices( \WP_REST_Request $request ) {
		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_notices' " );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Notices have been successfully reset.', 'wp-seopress' ),
			),
			200
		);
	}

	/**
	 * Reset all settings.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processResetSettings( \WP_REST_Request $request ) {
		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_%' " );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'All settings have been successfully reset.', 'wp-seopress' ),
			),
			200
		);
	}
}

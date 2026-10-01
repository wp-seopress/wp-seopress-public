<?php
/**
 * Options import export
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Export SEOPress Settings to JSON
 *
 * @return void
 */
function seopress_export_settings() {
	if ( empty( $_POST['seopress_action'] ) || 'export_settings' !== $_POST['seopress_action'] ) {
		return;
	}
	if ( ! isset( $_POST['seopress_export_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_export_nonce'] ) ), 'seopress_export_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( seopress_capability( 'manage_options', 'export_settings' ) ) ) {
		return;
	}

	$settings = seopress_get_service( 'ExportSettings' )->handle();

	ignore_user_abort( true );
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=seopress-settings-export-' . gmdate( 'm-d-Y' ) . '.json' );
	header( 'Expires: 0' );
	echo wp_json_encode( $settings );
	exit;
}
add_action( 'admin_init', 'seopress_export_settings' );

/**
 * Import SEOPress Settings from JSON
 *
 * @return void
 */
function seopress_import_settings() {
	if ( empty( $_POST['seopress_action'] ) || 'import_settings' !== $_POST['seopress_action'] ) {
		return;
	}
	if ( ! isset( $_POST['seopress_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_import_nonce'] ) ), 'seopress_import_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( seopress_capability( 'manage_options', 'import_settings' ) ) ) {
		return;
	}

	$extension = pathinfo( $_FILES['import_file']['name'], PATHINFO_EXTENSION );

	if ( 'json' !== $extension ) {
		wp_die( esc_html__( 'Please upload a valid .json file', 'wp-seopress' ) );
	}
	$import_file = $_FILES['import_file']['tmp_name'];

	if ( empty( $import_file ) ) {
		wp_die( esc_html__( 'Please upload a file to import', 'wp-seopress' ) );
	}

	$file_contents = file_get_contents( $import_file );
	if ( false === $file_contents ) {
		wp_die( esc_html__( 'Unable to read the import file', 'wp-seopress' ) );
	}
	$settings = (array) json_decode( seopress_remove_utf8_bom( $file_contents ), true );

	seopress_get_service( 'ImportSettings' )->handle( $settings );

	wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export&success=true' ) );
	exit;
}
add_action( 'admin_init', 'seopress_import_settings' );

/**
 * Delete all content scans
 *
 * @return void
 */
function seopress_clean_content_scans() {
	if ( empty( $_POST['seopress_action'] ) || 'clean_content_scans' !== $_POST['seopress_action'] ) {
		return;
	}
	if ( ! isset( $_POST['seopress_clean_content_scans_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_clean_content_scans_nonce'] ) ), 'seopress_clean_content_scans_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( seopress_capability( 'manage_options', 'cleaning' ) ) ) {
		return;
	}

	// Delete cache option.
	delete_option( 'seopress_content_analysis_api_in_progress' );

	global $wpdb;

	// Clean our post metas. The meta keys are hardcoded, so there is nothing to
	// bind here: calling prepare() without a placeholder triggers a
	// _doing_it_wrong() notice in WordPress.
	$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-off cleanup of our own post metas, no cache to invalidate.
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ( '_seopress_analysis_data', '_seopress_content_analysis_api', '_seopress_analysis_data_oxygen', '_seopress_content_analysis_api_in_progress' )"
	);

	// Clean custom table if it exists.
	$table_analysis = $wpdb->prefix . 'seopress_content_analysis';

	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_analysis ) ) ) === $table_analysis ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Schema lookup on our own table.
		$wpdb->query( "DELETE FROM `{$table_analysis}`" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name built from $wpdb->prefix, nothing to bind.
	}

	wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export' ) );
	exit;
}
add_action( 'admin_init', 'seopress_clean_content_scans' );


/**
 * Reset SEOPress Notices Settings
 *
 * @return void
 */
function seopress_reset_notices_settings() {
	if ( empty( $_POST['seopress_action'] ) || 'reset_notices_settings' !== $_POST['seopress_action'] ) {
		return;
	}
	if ( ! isset( $_POST['seopress_reset_notices_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_reset_notices_nonce'] ) ), 'seopress_reset_notices_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( seopress_capability( 'manage_options', 'reset_settings' ) ) ) {
		return;
	}

	global $wpdb;

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_notices' " );

	wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export' ) );
	exit;
}
add_action( 'admin_init', 'seopress_reset_notices_settings' );

/**
 * Reset SEOPress Settings
 *
 * @return void
 */
function seopress_reset_settings() {
	if ( empty( $_POST['seopress_action'] ) || 'reset_settings' !== $_POST['seopress_action'] ) {
		return;
	}
	if ( ! isset( $_POST['seopress_reset_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_reset_nonce'] ) ), 'seopress_reset_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( seopress_capability( 'manage_options', 'reset_settings' ) ) ) {
		return;
	}

	global $wpdb;

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'seopress_%' " );

	wp_safe_redirect( admin_url( 'admin.php?page=seopress-import-export' ) );
	exit;
}
add_action( 'admin_init', 'seopress_reset_settings' );

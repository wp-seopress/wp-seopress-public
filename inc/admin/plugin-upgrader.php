<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/** --------------------------------------------------------------------------------------------- */
/** MIGRATE / UPGRADE =========================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( 'admin_init', 'seopress_upgrader' );
/**
 * Tell WP what to do when admin is loaded aka upgrader
 *
 * @since 3.8.2
 */
function seopress_upgrader() {
	$versions = get_option( 'seopress_versions' );
	$actual_version = isset( $versions['free'] ) ? $versions['free'] : 0;

	// You can hook the upgrader to trigger any action when seopress is upgraded.
	// First install.
	if ( ! $actual_version ) {
		/**
		 * Allow to prevent plugin first install hooks to fire.
		 *
		 * @since 3.8.2
		 *
		 * @param (bool) $prevent True to prevent triggering first install hooks. False otherwise.
		 */
		if ( ! apply_filters( 'seopress_prevent_first_install', false ) ) {
			/**
			 * Fires on the plugin first install.
			 *
			 * @since 3.8.2
			 *
			 */
			do_action( 'seopress_first_install' );
		}

	}

	if ( SEOPRESS_VERSION !== $actual_version ) {
			/**
			 * Fires when seopress Pro is upgraded.
			 *
			 * @since 3.8.2
			 *
			 * @param (string) $new_pro_version    The version being upgraded to.
			 * @param (string) $actual_pro_version The previous version.
			 */
			do_action( 'seopress_upgrade', SEOPRESS_VERSION, $actual_version );
	}

	// If any upgrade has been done, we flush and update version.
	if ( did_action( 'seopress_first_install' ) || did_action( 'seopress_upgrade' ) ) {

		// Do not use seopress_get_option() here.
		$options = get_option( 'seopress_versions' );
		$options = is_array( $options ) ? $options : [];

		$options['free'] = SEOPRESS_VERSION;

		update_option( 'seopress_versions', $options, false );
	}
}


add_action( 'seopress_upgrade', 'seopress_new_upgrade', 10, 2 );
/**
 * What to do when seopress is updated, depending on versions.
 *
 * @since 3.8.2
 *
 * @param (string) $seopress_version The version being upgraded to.
 * @param (string) $actual_version    The previous version.
 */
function seopress_new_upgrade( $seopress_version, $actual_version ) {
	global $wpdb;

	// < 3.8.2
	if ( version_compare( $actual_version, '3.8.2', '<' ) ) {
	}

}

/**
 * Try to delete an old plugin file removed in a particular version, if not, will empty the file, if not, will rename it, if still not well… ¯\_(ツ)_/¯.
 *
 * @since 3.8.2
 * @param (string) $file
 * @author Julio Potier
 **/
function seopress_remove_old_plugin_file( $file ) {
	// Is it a sym link ?
	if ( is_link( $file ) ) {
		$file = @readlink( $file );
	}
	// Try to delete.
	if ( file_exists( $file ) && ! @unlink( $file ) ) {
		// Or try to empty it.
		$fh = @fopen( $file, 'w' );
		$fw = @fwrite( $fh, '<?php // File removed by seopress' );
		@fclose( $fh );
		if ( ! $fw ) {
			// Or try to rename it.
			return @rename( $file, $file . '.old' );
		}
	}
	return true;
}

<?php // phpcs:ignore
/**
 * This file is used to sync SEOPress settings with MainWP.
 *
 * @package SEOPress
 * @subpackage Thirds
 * @since 5.4
 * @updated 9.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Skip MainWP functions if SEOPress PRO < 5.4 to prevent fatal errors.
if ( ! is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) || ! defined( 'SEOPRESS_PRO_VERSION' ) || version_compare( SEOPRESS_PRO_VERSION, '5.4', '>=' ) ) {
	/**
	 * Get list of SEOPress option names for MainWP sync
	 *
	 * @return array List of option names.
	 */
	function seopress_mainwp_get_option_names() {
		return array(
			'seopress_activated',
			'seopress_titles_option_name',
			'seopress_social_option_name',
			'seopress_google_analytics_option_name',
			'seopress_instant_indexing_option_name',
			'seopress_advanced_option_name',
			'seopress_xml_sitemap_option_name',
			'seopress_pro_option_name',
			'seopress_pro_mu_option_name',
			'seopress_pro_license_key',
			'seopress_pro_license_status',
			'seopress_bot_option_name',
			'seopress_toggle',
			'seopress_google_analytics_lock_option_name',
			'seopress_tools_option_name',
			'seopress_dashboard_option_name',
		);
	}

	/**
	 * Return settings
	 *
	 * @return array
	 */
	function seopress_return_settings() {
		$settings = array();
		foreach ( seopress_mainwp_get_option_names() as $option_name ) {
			$settings[ $option_name ] = get_option( $option_name );
		}

		return $settings;
	}

	/**
	 * Seopress do import settings
	 *
	 * @param  array $settings The settings to be saved.
	 *
	 * @return void
	 */
	function seopress_do_import_settings( $settings ) {
		// Validate and sanitize settings to prevent type errors.
		$settings = seopress_validate_import_settings( $settings );

		foreach ( seopress_mainwp_get_option_names() as $option_name ) {
			if ( isset( $settings[ $option_name ] ) && false !== $settings[ $option_name ] ) {
				update_option( $option_name, $settings[ $option_name ], false );
			}
		}
	}

	/**
	 * Validate and sanitize import settings to prevent type errors
	 *
	 * @param  array $settings The settings to validate.
	 *
	 * @return array Validated settings.
	 */
	function seopress_validate_import_settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			return array();
		}

		// Recursively fix double-encoded or malformed serialized data.
		$settings = array_map( 'seopress_fix_serialized_data', $settings );

		return $settings;
	}

	/**
	 * Fix serialized data that may have been double-encoded or corrupted during MainWP transmission
	 *
	 * @param  mixed $value The value to fix.
	 *
	 * @return mixed Fixed value.
	 */
	function seopress_fix_serialized_data( $value ) {
		// If it's not a string, return as-is.
		if ( ! is_string( $value ) ) {
			return $value;
		}

		// Check if the string is double-serialized (common MainWP issue).
		if ( is_serialized( $value ) ) {
			$unserialized = @unserialize( $value ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			if ( false !== $unserialized ) {
				return $unserialized;
			}
		}

		return $value;
	}

	/**
	 * Save settings for given option
	 *
	 * @param  array  $settings The settings to be saved.
	 * @param  string $option The option name.
	 *
	 * @return void
	 */
	function seopress_mainwp_save_settings( $settings, $option ) {
		update_option( $option, $settings );
	}

	/**
	 * Flush rewrite rules.
	 *
	 * @return void
	 */
	function seopress_flush_rewrite_rules() {
		flush_rewrite_rules( false );
	}
}

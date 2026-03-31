<?php // phpcs:ignore

namespace SEOPress\Services\Settings;

/**
 * ImportSettings
 */
class ImportSettings {

	/**
	 * Options that contain settings fields needing sanitization.
	 *
	 * @var array
	 */
	private $sanitizable_options = array(
		'seopress_titles_option_name',
		'seopress_social_option_name',
		'seopress_google_analytics_option_name',
		'seopress_advanced_option_name',
		'seopress_xml_sitemap_option_name',
		'seopress_instant_indexing_option_name',
	);

	/**
	 * The handle function.
	 *
	 * @param array $data The data.
	 */
	public function handle( $data = array() ) {
		// Simple scalar/non-settings options (no sanitization needed).
		$simple_options = array(
			'seopress_activated',
			'seopress_pro_option_name',
			'seopress_pro_mu_option_name',
			'seopress_pro_license_key',
			'seopress_pro_license_status',
			'seopress_bot_option_name',
			'seopress_toggle',
			'seopress_google_analytics_lock_option_name',
			'seopress_tools_option_name',
		);

		foreach ( $simple_options as $option_name ) {
			if ( isset( $data[ $option_name ] ) && false !== $data[ $option_name ] ) {
				update_option( $option_name, $data[ $option_name ], false );
			}
		}

		// Settings options that need field-level sanitization.
		foreach ( $this->sanitizable_options as $option_name ) {
			if ( isset( $data[ $option_name ] ) && false !== $data[ $option_name ] && is_array( $data[ $option_name ] ) ) {
				$sanitized = seopress_sanitize_options_fields( $data[ $option_name ] );
				update_option( $option_name, $sanitized, false );
			}
		}
	}
}

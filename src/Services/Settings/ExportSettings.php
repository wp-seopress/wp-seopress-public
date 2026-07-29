<?php // phpcs:ignore

namespace SEOPress\Services\Settings;

/**
 * ExportSettings
 */
class ExportSettings {

	/**
	 * The handle function.
	 *
	 * @param array $exclude List of site-specific category keys to strip from the export.
	 *                       See getSiteSpecificCategories() for the available keys.
	 *
	 * @return array
	 */
	public function handle( $exclude = array() ) {
		$data                                = array();
		$data['seopress_activated']          = get_option( 'seopress_activated' );
		$data['seopress_titles_option_name'] = get_option( 'seopress_titles_option_name' );
		$data['seopress_social_option_name'] = get_option( 'seopress_social_option_name' );
		$data['seopress_google_analytics_option_name']      = get_option( 'seopress_google_analytics_option_name' );
		$data['seopress_advanced_option_name']              = get_option( 'seopress_advanced_option_name' );
		$data['seopress_xml_sitemap_option_name']           = get_option( 'seopress_xml_sitemap_option_name' );
		$data['seopress_pro_option_name']                   = get_option( 'seopress_pro_option_name' );
		$data['seopress_pro_mu_option_name']                = get_option( 'seopress_pro_mu_option_name' );
		$data['seopress_pro_license_key']                   = get_option( 'seopress_pro_license_key' );
		$data['seopress_pro_license_status']                = get_option( 'seopress_pro_license_status' );
		$data['seopress_bot_option_name']                   = get_option( 'seopress_bot_option_name' );
		$data['seopress_toggle']                            = get_option( 'seopress_toggle' );
		$data['seopress_google_analytics_lock_option_name'] = get_option( 'seopress_google_analytics_lock_option_name' );
		$data['seopress_tools_option_name']                 = get_option( 'seopress_tools_option_name' );
		$data['seopress_dashboard_option_name']             = get_option( 'seopress_dashboard_option_name' );
		$data['seopress_instant_indexing_option_name']      = get_option( 'seopress_instant_indexing_option_name' );

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$data = $this->stripSiteSpecific( $data, $exclude );
		}

		return $data;
	}

	/**
	 * Map of site-specific categories that can be excluded from an export to
	 * build a reusable "master" configuration file.
	 *
	 * Each category defines:
	 *  - label:         human-readable label (used by the export UI).
	 *  - drop_options:  full option names to remove entirely from the export.
	 *  - fields:        map of option_name => list of matchers. A matcher is an
	 *                   exact field key, or a key ending with "*" to match by
	 *                   prefix. Matching keys are unset from that option array.
	 *
	 * Filterable via 'seopress_export_site_specific_categories' so third parties
	 * (and the PRO plugin) can register their own client-specific fields.
	 *
	 * @return array
	 */
	public function getSiteSpecificCategories() {
		$categories = array(
			'knowledge_graph'    => array(
				'label'  => __( 'Knowledge Graph & Organization schema', 'wp-seopress' ),
				'fields' => array(
					'seopress_social_option_name' => array(
						'seopress_social_knowledge_*',
					),
				),
			),
			'social_profiles'    => array(
				'label'  => __( 'Social profiles', 'wp-seopress' ),
				'fields' => array(
					'seopress_social_option_name' => array(
						'seopress_social_accounts_facebook',
						'seopress_social_accounts_twitter',
						'seopress_social_accounts_pinterest',
						'seopress_social_accounts_instagram',
						'seopress_social_accounts_youtube',
						'seopress_social_accounts_linkedin',
						'seopress_social_accounts_extra',
						'seopress_social_facebook_admin_id',
						'seopress_social_facebook_app_id',
						'seopress_social_facebook_link_ownership_id',
						'seopress_social_twitter',
					),
				),
			),
			'analytics_ids'      => array(
				'label'  => __( 'Analytics & tracking IDs', 'wp-seopress' ),
				'fields' => array(
					'seopress_google_analytics_option_name' => array(
						'seopress_google_analytics_ga4',
						'seopress_google_analytics_ads',
						'seopress_google_analytics_clarity_project_id',
						'seopress_google_analytics_matomo_id',
						'seopress_google_analytics_matomo_site_id',
						'seopress_google_analytics_matomo_site_domain',
					),
				),
			),
			'verification_codes' => array(
				'label'  => __( 'Search engine verification codes', 'wp-seopress' ),
				'fields' => array(
					'seopress_advanced_option_name' => array(
						'seopress_advanced_advanced_google',
						'seopress_advanced_advanced_bing',
						'seopress_advanced_advanced_pinterest',
						'seopress_advanced_advanced_yandex',
					),
				),
			),
			'license'            => array(
				'label'        => __( 'License key', 'wp-seopress' ),
				'drop_options' => array(
					'seopress_pro_license_key',
					'seopress_pro_license_status',
				),
			),
			'api_keys'           => array(
				'label'  => __( 'API keys & secrets', 'wp-seopress' ),
				'fields' => array(
					'seopress_pro_option_name' => array(
						'seopress_ai_openai_api_key',
						'seopress_ai_claude_api_key',
						'seopress_ai_gemini_api_key',
						'seopress_ai_mistral_api_key',
						'seopress_ai_deepseek_api_key',
						'seopress_ai_seopress_api_key',
					),
					'seopress_instant_indexing_option_name' => array(
						'seopress_instant_indexing_google_api_key',
						'seopress_instant_indexing_bing_api_key',
						'seopress_instant_indexing_api_key_txt',
					),
					'seopress_google_analytics_option_name' => array(
						'seopress_google_analytics_auth_secret_id',
					),
				),
			),
		);

		return apply_filters( 'seopress_export_site_specific_categories', $categories );
	}

	/**
	 * Remove the fields belonging to the requested site-specific categories.
	 *
	 * @param array $data    The full export data.
	 * @param array $exclude List of category keys to strip.
	 *
	 * @return array
	 */
	protected function stripSiteSpecific( $data, $exclude ) {
		$categories = $this->getSiteSpecificCategories();

		foreach ( $exclude as $category_key ) {
			if ( ! isset( $categories[ $category_key ] ) ) {
				continue;
			}

			$category = $categories[ $category_key ];

			// Drop whole options.
			if ( ! empty( $category['drop_options'] ) ) {
				foreach ( $category['drop_options'] as $option_name ) {
					unset( $data[ $option_name ] );
				}
			}

			// Strip individual fields inside option arrays.
			if ( ! empty( $category['fields'] ) ) {
				foreach ( $category['fields'] as $option_name => $matchers ) {
					if ( empty( $data[ $option_name ] ) || ! is_array( $data[ $option_name ] ) ) {
						continue;
					}

					$data[ $option_name ] = $this->stripFields( $data[ $option_name ], $matchers );
				}
			}
		}

		return $data;
	}

	/**
	 * Remove keys matching a list of exact keys or "prefix*" patterns.
	 *
	 * @param array $option   The option array.
	 * @param array $matchers List of exact keys or keys ending with "*".
	 *
	 * @return array
	 */
	protected function stripFields( $option, $matchers ) {
		foreach ( $matchers as $matcher ) {
			if ( '*' === substr( $matcher, -1 ) ) {
				$prefix = substr( $matcher, 0, -1 );
				foreach ( array_keys( $option ) as $key ) {
					if ( 0 === strpos( $key, $prefix ) ) {
						unset( $option[ $key ] );
					}
				}
				continue;
			}

			unset( $option[ $matcher ] );
		}

		return $option;
	}
}

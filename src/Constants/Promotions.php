<?php // phpcs:ignore

namespace SEOPress\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promotions Constants
 *
 * @since 9.6.0
 */
abstract class Promotions {
	/**
	 * The remote API URL for fetching promotions.
	 *
	 * @since 9.6.0
	 *
	 * @var string
	 */
	const API_URL = 'https://www.seopress.org/wp-json/seopress/v1/promotions';

	/**
	 * Get the API URL, allowing for override via constant or filter.
	 *
	 * @since 9.6.0
	 *
	 * @return string The API URL to use.
	 */
	public static function getApiUrl(): string {
		// Allow override via constant for local development/testing.
		if ( defined( 'SEOPRESS_PROMOTIONS_API_URL' ) && SEOPRESS_PROMOTIONS_API_URL ) {
			return SEOPRESS_PROMOTIONS_API_URL;
		}

		// Auto-detect local Ads Manager plugin for development.
		if ( is_plugin_active( 'seopress-ads-manager/seopress-ads-manager.php' ) ) {
			return rest_url( 'seopress/v1/promotions' );
		}

		// Allow override via filter.
		return apply_filters( 'seopress_promotions_api_url', self::API_URL );
	}

	/**
	 * The cache key for promotions data.
	 *
	 * @since 9.6.0
	 *
	 * @var string
	 */
	const CACHE_KEY = 'seopress_promotions_data';

	/**
	 * The fallback option key for promotions data (used when API is unreachable).
	 *
	 * @since 9.6.0
	 *
	 * @var string
	 */
	const FALLBACK_OPTION_KEY = 'seopress_promotions_fallback';

	/**
	 * The option key for promotions preferences.
	 *
	 * @since 9.6.0
	 *
	 * @var string
	 */
	const PREFERENCES_KEY = 'seopress_promotions_preferences';

	/**
	 * The default cache TTL in seconds (6 hours).
	 *
	 * @since 9.6.0
	 *
	 * @var int
	 */
	const DEFAULT_TTL = 21600;

	/**
	 * The extended cache TTL in seconds when API fails (1 hour).
	 *
	 * @since 9.6.0
	 *
	 * @var int
	 */
	const FALLBACK_TTL = 3600;

	/**
	 * The default dismiss duration in days.
	 *
	 * @since 9.6.0
	 *
	 * @var int
	 */
	const DEFAULT_DISMISS_DAYS = 30;

	/**
	 * Available promotion types.
	 *
	 * @since 9.6.0
	 *
	 * @var array
	 */
	const TYPES = array(
		'banner',
		'block',
		'contextual',
		'metabox',
		'modal',
	);

	/**
	 * Available promotion locations.
	 *
	 * @since 9.6.0
	 *
	 * @var array
	 */
	const LOCATIONS = array(
		'top_banner',
		'dashboard',
		'settings_redirections',
		'settings_schemas',
		'settings_analytics',
		'metabox',
		'global',
	);
}

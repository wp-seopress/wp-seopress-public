<?php // phpcs:ignore

namespace SEOPress\Services\Admin\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Promotions;

/**
 * PromotionService
 *
 * Main service for fetching, caching, and filtering promotions from remote API.
 *
 * @since 9.6.0
 */
class PromotionService {

	/**
	 * The ConditionEvaluator instance.
	 *
	 * @var ConditionEvaluator|null
	 */
	protected $conditionEvaluator = null;

	/**
	 * In-memory cache to prevent multiple remote fetches per request.
	 *
	 * @var array|null
	 */
	private static $inMemoryCache = null;

	/**
	 * Get a single promotion by location.
	 *
	 * @since 9.6.0
	 *
	 * @param string $location The location (e.g., 'top_banner', 'dashboard').
	 *
	 * @return array|null The promotion data or null if none available.
	 */
	public function getPromotion( string $location ): ?array {
		$promotions = $this->getPromotions( $location );

		if ( empty( $promotions ) ) {
			return null;
		}

		// Return the highest priority promotion.
		return reset( $promotions );
	}

	/**
	 * Get promotions filtered by location.
	 *
	 * @since 9.6.0
	 *
	 * @param string|null $location The location filter, or null for all.
	 *
	 * @return array Array of promotions.
	 */
	public function getPromotions( ?string $location = null ): array {
		// Check if all promotions are disabled.
		if ( $this->arePromotionsDisabled() ) {
			return array();
		}

		$data = $this->getCachedData();

		if ( empty( $data ) || ! isset( $data['promotions'] ) ) {
			return array();
		}

		$promotions = $data['promotions'];

		// Filter by location if specified.
		if ( null !== $location ) {
			$promotions = array_filter(
				$promotions,
				function ( $promo ) use ( $location ) {
					return isset( $promo['location'] ) && $promo['location'] === $location;
				}
			);
		}

		// Filter by conditions and dismissal status.
		$promotions = array_filter( $promotions, array( $this, 'shouldShowPromotion' ) );

		// Sort by priority (higher priority first).
		usort(
			$promotions,
			function ( $a, $b ) {
				$priority_a = isset( $a['priority'] ) ? (int) $a['priority'] : 0;
				$priority_b = isset( $b['priority'] ) ? (int) $b['priority'] : 0;
				return $priority_b - $priority_a;
			}
		);

		// Resolve translations based on current locale.
		$promotions = array_map( array( $this, 'localizePromotion' ), $promotions );

		return array_values( $promotions );
	}

	/**
	 * Resolve translations for a promotion based on the current locale.
	 *
	 * The API returns English content as default with a 'translations' map.
	 * This method overrides content fields with the matching translation
	 * so the cached data stays language-neutral.
	 *
	 * @since 9.6.0
	 *
	 * @param array $promotion The promotion data.
	 *
	 * @return array The promotion with localized content.
	 */
	protected function localizePromotion( array $promotion ): array {
		if ( empty( $promotion['translations'] ) || ! is_array( $promotion['translations'] ) ) {
			return $promotion;
		}

		$locale = get_locale();

		// English is the default content, no need to translate.
		if ( 'en_US' === $locale || 'en_GB' === $locale ) {
			return $promotion;
		}

		$trans = null;

		// Try exact locale match first (e.g. fr_FR).
		if ( isset( $promotion['translations'][ $locale ] ) ) {
			$trans = $promotion['translations'][ $locale ];
		} else {
			// Try language-only match (e.g. 'fr' for 'fr_CA' matches 'fr_FR').
			$lang_code = substr( $locale, 0, 2 );
			foreach ( $promotion['translations'] as $trans_locale => $trans_data ) {
				if ( strpos( $trans_locale, $lang_code ) === 0 ) {
					$trans = $trans_data;
					break;
				}
			}
		}

		if ( ! $trans ) {
			return $promotion;
		}

		// Override content fields with translated values.
		if ( ! empty( $trans['title'] ) ) {
			$promotion['content']['title'] = $trans['title'];
		}
		if ( ! empty( $trans['body'] ) ) {
			$promotion['content']['body'] = $trans['body'];
		}
		if ( ! empty( $trans['cta_text'] ) ) {
			$promotion['content']['cta_text'] = $trans['cta_text'];
		}

		return $promotion;
	}

	/**
	 * Get affiliate partners filtered by conditions.
	 *
	 * @since 9.6.0
	 *
	 * @return array Array of affiliate partners.
	 */
	public function getAffiliatePartners(): array {
		// Check if all promotions are disabled.
		if ( $this->arePromotionsDisabled() ) {
			return array();
		}

		$data = $this->getCachedData();

		// Get partners from API data, or fall back to default mock partners.
		$partners = array();
		if ( ! empty( $data ) && isset( $data['affiliate_partners'] ) && ! empty( $data['affiliate_partners'] ) ) {
			$partners = $data['affiliate_partners'];
		} else {
			// Always use default affiliate partners if none from API.
			$mock_data = MockPromotionData::getData();
			$partners  = isset( $mock_data['affiliate_partners'] ) ? $mock_data['affiliate_partners'] : array();
		}

		if ( empty( $partners ) ) {
			return array();
		}

		// Filter by conditions.
		return array_filter(
			$partners,
			function ( $partner ) {
				$conditions = isset( $partner['conditions'] ) ? $partner['conditions'] : array();
				return $this->getConditionEvaluator()->evaluate( $conditions );
			}
		);
	}

	/**
	 * Check if a promotion should be shown.
	 *
	 * @since 9.6.0
	 *
	 * @param array $promotion The promotion data.
	 *
	 * @return bool True if should show, false otherwise.
	 */
	public function shouldShowPromotion( array $promotion ): bool {
		// Check if promotion ID exists.
		if ( empty( $promotion['id'] ) ) {
			return false;
		}

		// Check white-label settings.
		if ( $this->isWhiteLabelEnabled() ) {
			return false;
		}

		// Check if promotion is dismissed.
		if ( $this->isPromotionDismissed( $promotion['id'] ) ) {
			return false;
		}

		// Check max dismissals.
		if ( isset( $promotion['conditions']['max_dismissals'] ) ) {
			$dismissal_count = $this->getDismissalCount( $promotion['id'] );
			if ( $dismissal_count >= (int) $promotion['conditions']['max_dismissals'] ) {
				return false;
			}
		}

		// Check other conditions.
		$conditions = isset( $promotion['conditions'] ) ? $promotion['conditions'] : array();

		return $this->getConditionEvaluator()->evaluate( $conditions );
	}

	/**
	 * Get cached promotions data.
	 *
	 * @since 9.6.0
	 *
	 * @return array|null The cached data or null if not available.
	 */
	public function getCachedData(): ?array {
		// Return in-memory cache if already resolved in this request.
		if ( null !== self::$inMemoryCache ) {
			return self::$inMemoryCache;
		}

		// Try to get from transient first.
		$cached = get_transient( Promotions::CACHE_KEY );

		if ( false !== $cached && is_array( $cached ) ) {
			self::$inMemoryCache = $cached;
			return $cached;
		}

		// Try to fetch from remote API.
		$fresh_data = $this->fetchFromRemote();

		if ( null !== $fresh_data ) {
			self::$inMemoryCache = $fresh_data;
			return $fresh_data;
		}

		// Fall back to stored option if API fetch fails.
		$fallback = get_option( Promotions::FALLBACK_OPTION_KEY );

		if ( is_array( $fallback ) ) {
			// Extend the transient with a shorter TTL for retry.
			set_transient( Promotions::CACHE_KEY, $fallback, Promotions::FALLBACK_TTL );
			self::$inMemoryCache = $fallback;
			return $fallback;
		}

		// Fall back to default mock data if no API promotions available.
		$mock = MockPromotionData::getData();
		self::$inMemoryCache = $mock;
		return $mock;
	}

	/**
	 * Fetch promotions from remote API.
	 *
	 * @since 9.6.0
	 *
	 * @return array|null The fetched data or null on failure.
	 */
	public function fetchFromRemote(): ?array {
		$api_url = Promotions::getApiUrl();

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 10,
				'headers' => $this->getRequestHeaders(),
			)
		);

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status_code ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return null;
		}

		// Determine TTL from response or use default.
		$ttl = isset( $data['cache_ttl'] ) ? (int) $data['cache_ttl'] : Promotions::DEFAULT_TTL;

		// Cache the data.
		set_transient( Promotions::CACHE_KEY, $data, $ttl );

		// Store as fallback.
		update_option( Promotions::FALLBACK_OPTION_KEY, $data, false );

		return $data;
	}

	/**
	 * Get request headers for API calls.
	 *
	 * @since 9.6.0
	 *
	 * @return array Headers array.
	 */
	protected function getRequestHeaders(): array {
		return array(
			'X-SEOPress-Version'    => SEOPRESS_VERSION,
			'X-SEOPress-Pro-Active' => is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ? '1' : '0',
			'X-SEOPress-Locale'     => get_locale(),
			'Content-Type'          => 'application/json',
		);
	}

	/**
	 * Check if white-label mode is enabled.
	 *
	 * @since 9.6.0
	 *
	 * @return bool True if white-label is enabled.
	 */
	protected function isWhiteLabelEnabled(): bool {
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
				&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
				return true;
			}
		}

		if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if all promotions are disabled.
	 *
	 * @since 9.6.0
	 *
	 * @return bool True if disabled.
	 */
	protected function arePromotionsDisabled(): bool {
		$preferences = get_option( Promotions::PREFERENCES_KEY, array() );
		$preferences = is_array( $preferences ) ? $preferences : array();
		return ! empty( $preferences['disable_all'] );
	}

	/**
	 * Check if a specific promotion is dismissed.
	 *
	 * @since 9.6.0
	 *
	 * @param string $promo_id The promotion ID.
	 *
	 * @return bool True if dismissed.
	 */
	protected function isPromotionDismissed( string $promo_id ): bool {
		$notices = get_option( 'seopress_notices', array() );
		$notices = is_array( $notices ) ? $notices : array();
		$key     = 'promo-' . $promo_id;

		if ( ! isset( $notices[ $key ] ) ) {
			return false;
		}

		$dismissal = $notices[ $key ];

		// Check if dismiss_until has passed.
		if ( isset( $dismissal['dismiss_until'] ) && is_numeric( $dismissal['dismiss_until'] ) ) {
			return time() < (int) $dismissal['dismiss_until'];
		}

		// If no expiry, consider it permanently dismissed.
		return true;
	}

	/**
	 * Get the dismissal count for a promotion.
	 *
	 * @since 9.6.0
	 *
	 * @param string $promo_id The promotion ID.
	 *
	 * @return int The number of times dismissed.
	 */
	protected function getDismissalCount( string $promo_id ): int {
		$notices = get_option( 'seopress_notices', array() );
		$notices = is_array( $notices ) ? $notices : array();
		$key     = 'promo-' . $promo_id;

		if ( ! isset( $notices[ $key ] ) || ! isset( $notices[ $key ]['count'] ) ) {
			return 0;
		}

		return (int) $notices[ $key ]['count'];
	}

	/**
	 * Get the ConditionEvaluator instance.
	 *
	 * @since 9.6.0
	 *
	 * @return ConditionEvaluator
	 */
	protected function getConditionEvaluator(): ConditionEvaluator {
		if ( null === $this->conditionEvaluator ) {
			$this->conditionEvaluator = seopress_get_service( 'ConditionEvaluator' );
			if ( null === $this->conditionEvaluator ) {
				$this->conditionEvaluator = new ConditionEvaluator();
			}
		}

		return $this->conditionEvaluator;
	}

	/**
	 * Clear the promotions cache.
	 *
	 * @since 9.6.0
	 *
	 * @return void
	 */
	public function clearCache(): void {
		delete_transient( Promotions::CACHE_KEY );
		self::$inMemoryCache = null;
	}

	/**
	 * Dismiss a promotion.
	 *
	 * @since 9.6.0
	 *
	 * @param string $promo_id The promotion ID.
	 * @param int    $duration Duration in days (0 for permanent).
	 *
	 * @return bool True on success.
	 */
	public function dismissPromotion( string $promo_id, int $duration = 30 ): bool {
		$notices = get_option( 'seopress_notices', array() );
		$notices = is_array( $notices ) ? $notices : array();
		$key     = 'promo-' . $promo_id;

		// Get existing count or start at 0.
		$count = isset( $notices[ $key ]['count'] ) ? (int) $notices[ $key ]['count'] : 0;

		$notices[ $key ] = array(
			'dismissed_at'  => time(),
			'dismiss_until' => $duration > 0 ? time() + ( $duration * DAY_IN_SECONDS ) : 0,
			'count'         => $count + 1,
		);

		return update_option( 'seopress_notices', $notices, false );
	}

	/**
	 * Set global promotions preference.
	 *
	 * @since 9.6.0
	 *
	 * @param string $key   The preference key.
	 * @param mixed  $value The preference value.
	 *
	 * @return bool True on success.
	 */
	public function setPreference( string $key, $value ): bool {
		$preferences         = get_option( Promotions::PREFERENCES_KEY, array() );
		$preferences         = is_array( $preferences ) ? $preferences : array();
		$preferences[ $key ] = $value;

		return update_option( Promotions::PREFERENCES_KEY, $preferences, false );
	}

	/**
	 * Get global promotions preference.
	 *
	 * @since 9.6.0
	 *
	 * @param string $key     The preference key.
	 * @param mixed  $default Default value if not set.
	 *
	 * @return mixed The preference value.
	 */
	public function getPreference( string $key, $default = null ) {
		$preferences = get_option( Promotions::PREFERENCES_KEY, array() );
		$preferences = is_array( $preferences ) ? $preferences : array();
		return isset( $preferences[ $key ] ) ? $preferences[ $key ] : $default;
	}
}

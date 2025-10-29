<?php // phpcs:ignore

namespace SEOPress\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;
use SEOPress\ManualHooks\Thirds\WooCommerce\WooCommerceAnalytics;

/**
 * Google Analytics
 */
class GoogleAnalytics implements ExecuteHooksFrontend {
	/**
	 * Registers hooks for Google Analytics.
	 *
	 * @since 4.4.0
	 * @return void
	 */
	public function hooks(): void {
		$ga_option_service   = seopress_get_service( 'GoogleAnalyticsOption' );
		$disable_option      = $ga_option_service->getDisable();
		$half_disable_option = $ga_option_service->getHalfDisable();

		// Check user consent or GA disable setting.
		if (
			'1' === $half_disable_option ||
			( $this->isUserConsentGiven() && '1' === $disable_option ) ||
			'1' !== $disable_option
		) {
			if ( ! $this->shouldExcludeCurrentUser( $ga_option_service ) ) {
				add_action( 'init', array( $this, 'analytics' ) );
			}
		}
	}

	/**
	 * Handles Google Analytics logic.
	 *
	 * @since 4.4.0
	 * @return void
	 */
	public function analytics(): void {
		$ga_option_service = seopress_get_service( 'GoogleAnalyticsOption' );

		// Ensure GA4 and GA option is enabled.
		if (
			$ga_option_service->getGA4() !== ''
			&& '1' === $ga_option_service->getEnableOption()
		) {
			if ( seopress_get_service( 'WooCommerceActivate' )->isActive() ) {
				( new WooCommerceAnalytics() )->hooks();
			}
		}
	}

	/**
	 * Checks if the current user should be excluded based on role.
	 *
	 * @since 4.4.0
	 * @param object $ga_option_service The Google Analytics option service.
	 * @return bool True if the user should be excluded, false otherwise.
	 */
	private function shouldExcludeCurrentUser( object $ga_option_service ): bool {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user             = wp_get_current_user();
		$roles_to_exclude = $ga_option_service->getRoles();

		if ( ! empty( $roles_to_exclude ) && isset( $user->roles[0] ) ) {
			return array_key_exists( $user->roles[0], $roles_to_exclude );
		}

		return false;
	}

	/**
	 * Checks if user consent has been given.
	 *
	 * @since 4.4.0
	 * @return bool True if consent is given, false otherwise.
	 */
	private function isUserConsentGiven(): bool {
		return isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'];
	}
}

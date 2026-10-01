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
		$ga_option_service = seopress_get_service( 'GoogleAnalyticsOption' );

		/*
		 * Deliberately not gated on the consent cookie.
		 *
		 * These hooks print gtag('event', ...) calls and nothing else. They
		 * queue in window.dataLayer and reach Google no earlier than the
		 * tracking library itself, which stays behind the consent gate. Gating
		 * them here would instead make the rendered page differ per visitor,
		 * and a full-page cache would serve one visitor's answer to everyone
		 * -- the bug this whole consent path was rebuilt to close.
		 */
		if ( ! $this->shouldExcludeCurrentUser( $ga_option_service ) ) {
			add_action( 'init', array( $this, 'analytics' ) );
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
}

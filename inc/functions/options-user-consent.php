<?php
/**
 * User consent.
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/*
 * Which trackers get printed into the page, and the consent cookie plays no
 * part in it.
 *
 * This file only runs while rendering a front-end page, and that page is very
 * often stored by a full-page cache and handed to every later visitor. Deciding
 * here from one visitor's cookie made their answer everyone's: a cache primed
 * by someone who accepted printed the trackers for people who never answered.
 *
 * When tracking waits for consent, the trackers are held back and embedded
 * inert by seopress_cookies_user_consent_payload_html() instead, and the
 * browser starts them from its own cookie.
 */
$seopress_ga_options     = seopress_get_service( 'GoogleAnalyticsOption' );
$seopress_ga_disable     = $seopress_ga_options->getDisable();
$seopress_ga_auto_accept = $seopress_ga_options->getHalfDisable();

if ( '1' === $seopress_ga_auto_accept || '1' !== $seopress_ga_disable ) {

	/**
	 * Control where custom head tracking markup is printed.
	 *
	 * @param int $priority WordPress head priority. Default 980, after Analytics.
	 */
	$custom_tracking_head_priority = (int) apply_filters( 'seopress_custom_tracking_head_priority', 980 );

	$add_to_cart_option      = seopress_get_service( 'GoogleAnalyticsOption' )->getAddToCart();
	$remove_from_cart_option = seopress_get_service( 'GoogleAnalyticsOption' )->getRemoveFromCart();
	$get_view_items_details  = seopress_get_service( 'GoogleAnalyticsOption' )->getViewItemsDetails();

	if ( is_user_logged_in() ) {
		global $wp_roles;

		// Get current user role.
		if ( isset( wp_get_current_user()->roles[0] ) ) {
			$seopress_user_role = wp_get_current_user()->roles[0];
			// If current user role matchs values from SEOPress GA settings then apply.
			if ( ! empty( seopress_get_service( 'GoogleAnalyticsOption' )->getRoles() ) ) {
				if ( array_key_exists( $seopress_user_role, seopress_get_service( 'GoogleAnalyticsOption' )->getRoles() ) ) {
					// Do nothing.
				} else {
					if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getEnableOption() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
						add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 929, 1 );
						add_action( 'wp_head', 'seopress_custom_tracking_hook', 900, 1 );
					}
					if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) {
						add_action( 'wp_head', 'seopress_matomo_js_arguments', 960, 1 );
						add_action( 'wp_body_open', 'seopress_matomo_nojs', 960, 1 );
					}
					if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getClarityEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() ) {
						add_action( 'wp_head', 'seopress_clarity_js', 970 );
					}
					add_action( 'wp_head', 'seopress_custom_tracking_head_hook', $custom_tracking_head_priority, 1 );
					add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
					add_action( 'wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1 );

					// Ecommerce.
					$purchases_options = seopress_get_service( 'GoogleAnalyticsOption' )->getPurchases();
					if ( '1' === $purchases_options || '1' === $add_to_cart_option || '1' === $remove_from_cart_option || '1' === $get_view_items_details ) {
						add_action( 'wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1 );
					}
				}
			} else {
				if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getEnableOption() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
					add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 929, 1 );
					add_action( 'wp_head', 'seopress_custom_tracking_hook', 900, 1 );
				}
				if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) {
					add_action( 'wp_head', 'seopress_matomo_js_arguments', 960, 1 );
					add_action( 'wp_body_open', 'seopress_matomo_nojs', 960, 1 );
				}
				if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getClarityEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() ) {
					add_action( 'wp_head', 'seopress_clarity_js', 970 );
				}
				add_action( 'wp_head', 'seopress_custom_tracking_head_hook', $custom_tracking_head_priority, 1 ); // Oxygen: if prioriry >= 990, nothing will be outputed.
				add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
				add_action( 'wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1 );

				// Ecommerce.
				$purchases_options = seopress_get_service( 'GoogleAnalyticsOption' )->getPurchases();
				if ( '1' === $purchases_options || '1' === $add_to_cart_option || '1' === $remove_from_cart_option || '1' === $get_view_items_details ) {
					add_action( 'wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1 );
				}
			}
		}
	} else {
		if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getEnableOption() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getGA4() ) {
			add_action( 'wp_head', 'seopress_google_analytics_js_arguments', 929, 1 );
			add_action( 'wp_head', 'seopress_custom_tracking_hook', 900, 1 );
		}
		if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoId() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getMatomoSiteId() ) {
			add_action( 'wp_head', 'seopress_matomo_js_arguments', 960, 1 );
			add_action( 'wp_body_open', 'seopress_matomo_nojs', 960, 1 );
		}
		if ( '1' === seopress_get_service( 'GoogleAnalyticsOption' )->getClarityEnable() && '' !== seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() ) {
			add_action( 'wp_head', 'seopress_clarity_js', 970 );
		}
		add_action( 'wp_head', 'seopress_custom_tracking_head_hook', $custom_tracking_head_priority, 1 );
		add_action( 'wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1 );
		add_action( 'wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1 );

		// Ecommerce.
		$purchases_options = seopress_get_service( 'GoogleAnalyticsOption' )->getPurchases();
		if ( '1' === $purchases_options || '1' === $add_to_cart_option || '1' === $remove_from_cart_option || '1' === $get_view_items_details ) {
			add_action( 'wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1 );
		}
	}
} elseif ( ! seopress_user_consent_role_is_excluded() ) {
	/*
	 * Waiting for consent: the trackers above are deferred into the payload,
	 * but the cart listener is not part of it. Like the WooCommerce hooks, it
	 * only queues gtag('event', ...) calls in window.dataLayer, which reach
	 * Google no earlier than the deferred library, so it stays on the page for
	 * every visitor. Without it, AJAX cart events are lost for everyone who
	 * accepts.
	 */
	$seopress_ga_ecommerce = array(
		$seopress_ga_options->getPurchases(),
		$seopress_ga_options->getAddToCart(),
		$seopress_ga_options->getRemoveFromCart(),
		$seopress_ga_options->getViewItemsDetails(),
	);

	if ( in_array( '1', $seopress_ga_ecommerce, true ) ) {
		add_action( 'wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1 );
	}
}

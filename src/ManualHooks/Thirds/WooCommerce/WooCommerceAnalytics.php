<?php // phpcs:ignore

namespace SEOPress\ManualHooks\Thirds\WooCommerce;

use SEOPress\Thirds\WooCommerce\WooCommerceAnalyticsService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerceAnalytics
 */
class WooCommerceAnalytics {

	/**
	 * The woocommerce_analytics property.
	 *
	 * @var WooCommerceAnalyticsService
	 */
	protected $woocommerce_analytics;

	/**
	 * The WooCommerceAnalytics constructor.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function __construct() {
		/**
		 * The WooCommerceAnalyticsService instance.
		 *
		 * @var WooCommerceAnalyticsService
		 */
		$this->woocommerce_analytics = seopress_get_service( 'WooCommerceAnalyticsService' );
	}

	/**
	 * Registers hooks for WooCommerce Analytics.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! seopress_get_service( 'WooCommerceActivate' )->isActive() ) {
			return;
		}

		if ( '1' !== seopress_get_toggle_option( 'google-analytics' ) ) {
			return;
		}

		$add_to_cart_option = seopress_get_service( 'GoogleAnalyticsOption' )->getAddToCart();

		if ( $add_to_cart_option ) {
			// Listing page.
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'addToCart' ) );

			// Single.
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'singleAddToCart' ) );
		}

		$remove_from_cart_option = seopress_get_service( 'GoogleAnalyticsOption' )->getRemoveFromCart();

		if ( $remove_from_cart_option ) {
			// Cart page.
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'removeFromCart' ), 10, 2 );
		}

		if ( $add_to_cart_option && $remove_from_cart_option ) {
			// Before update.
			add_action( 'woocommerce_cart_actions', array( $this, 'updateCartOrCheckout' ) );
		}

		$get_view_items_details = seopress_get_service( 'GoogleAnalyticsOption' )->getViewItemsDetails();

		if ( $get_view_items_details ) {
			add_action( 'wp_head', array( $this, 'singleViewItemsDetails' ) );
		}
	}

	/**
	 * Handles the add to cart event.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function addToCart() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->woocommerce_analytics->addToCart();
	}

	/**
	 * Handles the single add to cart event.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function singleAddToCart() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->woocommerce_analytics->singleAddToCart();
	}

	/**
	 * Handles the remove from cart event.
	 *
	 * @since 4.4.0
	 *
	 * @param string $sprintf  The sprintf.
	 * @param string $cart_key The cart key.
	 *
	 * @return string
	 */
	public function removeFromCart( $sprintf, $cart_key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->woocommerce_analytics->removeFromCart( $sprintf, $cart_key );
	}

	/**
	 * Handles the update cart or checkout event.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function updateCartOrCheckout() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->woocommerce_analytics->updateCartOrCheckout();
	}

	/**
	 * Handles the single view items details event.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	public function singleViewItemsDetails() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! is_singular( 'product' ) ) {
			return;
		}
		$this->woocommerce_analytics->singleViewItemsDetails();
	}
}

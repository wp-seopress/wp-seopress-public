<?php

namespace SEOPress\ManualHooks\Thirds\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

class WooCommerceAnalytics {
    public function __construct() {
        $this->wooCommerceAnalytics = seopress_get_service('WooCommerceAnalyticsService');
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        if ( ! seopress_get_service('WooCommerceActivate')->isActive()) {
            return;
        }

        if (seopress_google_analytics_add_to_cart_option()) {
            // Listing page
            add_action('woocommerce_after_shop_loop_item', [$this, 'addToCart']);

            //Single
            add_action('woocommerce_after_add_to_cart_button', [$this, 'singleAddToCart']);
        }

        if (seopress_google_analytics_remove_from_cart_option()) {
            // Cart page
            add_filter('woocommerce_cart_item_remove_link', [$this, 'removeFromCart'], 10, 2);
        }

        if (seopress_google_analytics_add_to_cart_option() && seopress_google_analytics_remove_from_cart_option()) {
            // Before update
            add_action('woocommerce_cart_actions', [$this, 'updateCartOrCheckout']);
        }
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function addToCart() {
        if (apply_filters('seopress_fallback_woocommerce_analytics', false)) {
            return;
        }
        $this->wooCommerceAnalytics->addToCart();
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function singleAddToCart() {
        if (apply_filters('seopress_fallback_woocommerce_analytics', false)) {
            return;
        }
        $this->wooCommerceAnalytics->singleAddToCart();
    }

    /**
     * @since 4.4.0
     *
     * @param string $sprintf
     * @param string $cartKey
     *
     * @return void
     */
    public function removeFromCart($sprintf, $cartKey) {
        if (apply_filters('seopress_fallback_woocommerce_analytics', false)) {
            return;
        }

        return $this->wooCommerceAnalytics->removeFromCart($sprintf, $cartKey);
    }

    /**
     * @since 4.4.0
     *
     * @param string $sprintf
     * @param string $cartKey
     *
     * @return void
     */
    public function updateCartOrCheckout() {
        if (apply_filters('seopress_fallback_woocommerce_analytics', false)) {
            return;
        }
        $this->wooCommerceAnalytics->updateCartOrCheckout();
    }
}

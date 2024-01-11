<?php

namespace SEOPress\Thirds\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

class WooCommerceActivate {
    /**
     * WooCommerce is active ?
     *
     * @since 4.4.0
     *
     * @return bool
     */
    public function isActive() {
        if ( ! function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( ! is_plugin_active('woocommerce/woocommerce.php')) {
            return false;
        }

        return true;
    }
}

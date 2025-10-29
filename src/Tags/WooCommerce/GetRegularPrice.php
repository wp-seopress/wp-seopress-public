<?php

namespace SEOPress\Tags\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Get WooCommerce product regular price tag
 *
 * @since 7.9.0
 * Used by SEOPress PRO for sale price support in Product schemas
 */
class GetRegularPrice implements GetTagValue {
    const NAME = 'wc_get_regular_price';

    public static function getDescription() {
        return __('Product Regular Price', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        if ( ! seopress_get_service('WooCommerceActivate')->isActive()) {
            return '';
        }

        $value = '';

        if ( ! $context) {
            return $value;
        }

        if ((is_singular(['product']) || $context['is_product']) && isset($context['post']->ID)) {
            $product    = wc_get_product($context['post']->ID);
            $value      = $product->get_regular_price();
        }

        return apply_filters('seopress_get_tag_wc_get_regular_price_value', $value, $context);
    }
}

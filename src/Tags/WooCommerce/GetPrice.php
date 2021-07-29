<?php

namespace SEOPress\Tags\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class GetPrice implements GetTagValue {
    const NAME = 'wc_get_price';

    public static function getDescription() {
        return __('Product Price', 'wp-seopress');
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
            $value      = $product->get_price();
        }

        return apply_filters('seopress_get_tag_wc_get_price_value', $value, $context);
    }
}

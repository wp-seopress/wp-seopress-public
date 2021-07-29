<?php

namespace SEOPress\Tags\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SinglePriceExcludeTax implements GetTagValue {
    const NAME = 'wc_single_price_exc_tax';

    public static function getDescription() {
        return __('Product Price Taxes Excluded', 'wp-seopress');
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

        if (is_singular(['product']) || $context['is_product']) {
            $product          = wc_get_product($context['post']->ID);
            $value            = wc_get_price_excluding_tax($product);
        }

        return apply_filters('seopress_get_tag_wc_single_price_exc_tax_value', $value, $context);
    }
}

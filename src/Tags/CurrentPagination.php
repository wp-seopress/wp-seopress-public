<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentPagination implements GetTagValue {
    const NAME = 'current_pagination';

    public static function getDescription() {
        return __('Current Number Page', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if ($context['paged'] > '1') {
            $value = $context['paged'];
        }

        /**
         * @deprecated 4.4.0
         * Please use seopress_get_tag_current_pagination_value
         */
        $value = apply_filters('seopress_paged', $value);

        return apply_filters('seopress_get_tag_current_pagination_value', $value, $context);
    }
}

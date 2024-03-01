<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SearchKeywords implements GetTagValue {
    const NAME = 'search_keywords';

    public static function getDescription() {
        return __('Search Keywords', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = get_search_query();

        if ( ! empty($value)) {
            $value = esc_attr('"' . $value . '"');
        } else {
            $value = esc_attr('" "');
        }

        /**
         * @deprecated 4.4.0
         * Please use seopress_get_tag_search_keywords_value
         */
        $value = apply_filters('seopress_get_search_query', $value);

        return apply_filters('seopress_get_tag_search_keywords_value', $value, $context);
    }
}

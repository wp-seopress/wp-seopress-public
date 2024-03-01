<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class Page implements GetTagValue {
    const NAME = 'page';

    public static function getDescription() {
        return __('Page number with context', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        global $wp_query;

        $value = '';

        if ( ! $context) {
            return $value;
        }

        if (isset($wp_query->max_num_pages)) {
            if ($context['paged'] > 1) {
                $currentPage = get_query_var('paged');
            } else {
                $currentPage = 1;
            }

            $value = sprintf(__('Page %d of %2$d', 'wp-seopress'), $currentPage, $wp_query->max_num_pages);
            /**
             * @deprecated 4.4.0
             * Please use seopress_context_paged
             */
            $value = apply_filters('seopress_context_paged', $value);
        }

        return apply_filters('seopress_get_tag_page_value', $value, $context);
    }
}

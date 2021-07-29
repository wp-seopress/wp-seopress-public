<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class TermTitle implements GetTagValue {
    const NAME = 'term_title';

    public static function getDescription() {
        return __('Term Title', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if (null !== $context['term_id']) {
            $value = get_term_field('name', $context['term_id']);
            if (is_wp_error($value)) {
                $value = '';
            }
        } else {
            $value   = single_term_title('', false);
        }

        return apply_filters('seopress_get_tag_term_title_value', $value, $context);
    }
}

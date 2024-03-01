<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostTag implements GetTagValue {
    const NAME = 'post_tag';

    public static function getDescription() {
        return __('Post Tag', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if ($context['is_single'] && $context['has_tag'] && isset($context['post']->ID)) {
            $terms          = get_the_terms($context['post']->ID, 'post_tag');
            $value          = $terms[0]->name;
            /**
             * @deprecated 4.4.0
             * Please use seopress_get_tag_post_tag_value
             */
            $value               = apply_filters('seopress_titles_tag', $value);
        }

        return apply_filters('seopress_get_tag_post_tag_value', $value, $context);
    }
}

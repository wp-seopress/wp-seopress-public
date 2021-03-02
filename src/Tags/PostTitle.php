<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostTitle implements GetTagValue {
    const NAME = 'post_title';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if (
            ($context['is_home'] || $context['is_single'])
            && isset($context['post']) && $context['post']) {
            $value = esc_attr(strip_tags(get_post_field('post_title', $context['post']->ID)));
        }

        return apply_filters('seopress_get_tag_post_title_value', $value, $context);
    }
}

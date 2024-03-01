<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostThumbnailUrlWidth implements GetTagValue {
    const NAME = 'post_thumbnail_url_width';

    public static function getDescription() {
        return __('Post Thumbnail Width', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if (isset($context['is_single'], $context['post']) && $context['is_single'] && ! empty($context['post'])) {
            $size = wp_get_attachment_image_src(get_post_thumbnail_id($context['post']->ID), 'large');
            if (isset($size[1])) {
                $value = $size[1];
            }
        }

        return apply_filters('seopress_get_tag_post_thumbnail_url_width_value', $value, $context);
    }
}

<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostThumbnailUrlHeight implements GetTagValue {
    const NAME = 'post_thumbnail_url_height';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if ($context['is_single'] && ! empty($context['post']->ID)) {
            $size = wp_get_attachment_image_src(get_post_thumbnail_id($context['post']->ID), 'large');
            if (isset($size[1])) {
                $value = $size[2];
            }
        }

        return apply_filters('seopress_get_tag_post_thumbnail_url_height_value', $value, $context);
    }
}

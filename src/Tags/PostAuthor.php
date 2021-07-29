<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostAuthor implements GetTagValue {
    const NAME = 'post_author';

    public static function getDescription() {
        return __('Post Author', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if (isset($context['is_single']) && $context['is_single'] && isset($context['post']) && isset($context['post']->post_author)) {
            $value = esc_attr(get_the_author_meta('display_name', $context['post']->post_author));
        }

        if (isset($context['is_author']) && $context['is_author'] && is_int(get_queried_object_id())) {
            $user_info = get_userdata(get_queried_object_id());

            if (isset($user_info)) {
                $value = esc_attr($user_info->display_name);
            }
        }

        return apply_filters('seopress_get_tag_post_author_value', $value, $context);
    }
}

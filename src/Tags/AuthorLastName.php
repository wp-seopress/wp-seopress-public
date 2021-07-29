<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class AuthorLastName implements GetTagValue {
    const NAME = 'author_last_name';

    public static function getDescription() {
        return __('Author Last Name', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if ($context['is_single'] && isset($context['post']->post_author)) {
            $value      = get_the_author_meta('last_name', $context['post']->post_author);
        }

        if ($context['is_author'] && is_int(get_queried_object_id())) {
            $user_info = get_userdata(get_queried_object_id());

            if (isset($user_info)) {
                $value = $user_info->last_name;
            }
        }

        $value = esc_attr($value);

        return apply_filters('seopress_get_tag_author_last_name_value', $value, $context);
    }
}

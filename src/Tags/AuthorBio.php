<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class AuthorBio implements GetTagValue {
    const NAME = 'author_bio';

    public static function getDescription() {
        return __('Author Bio', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }

        if ($context['is_single'] && isset($context['post']->post_author)) {
            $value      = get_the_author_meta('description', $context['post']->post_author);
        }

        if ($context['is_author'] && is_int(get_queried_object_id())) {
            $user_info = get_userdata(get_queried_object_id());

            if (isset($user_info)) {
                $value = $user_info->description;
            }
        }

        $value = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($value)))));

        return apply_filters('seopress_get_tag_author_bio_value', $value, $context);
    }
}

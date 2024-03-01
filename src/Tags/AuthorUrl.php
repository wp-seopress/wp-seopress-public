<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class AuthorUrl implements GetTagValue {
    const NAME = 'author_url';

    public static function getDescription() {
        return __('Author URL', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if ( ! $context) {
            return $value;
        }


        if(isset($context['post']->post_author)){
            $value = get_the_author_meta('user_url', $context['post']->post_author);
        }

        if ($context['is_single'] && isset($context['post']->post_author) && empty($value)) {
            $value      = get_author_posts_url($context['post']->post_author);
        }

        if ($context['is_author'] && is_int(get_queried_object_id()) && empty($value)) {
            $value = get_author_posts_url(get_queried_object_id());
        }

        return apply_filters('seopress_get_tag_author_url_value', $value, $context);
    }
}

<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class PostDate implements GetTagValue {
    const NAME = 'post_date';

    const ALIAS = ['date'];

    public static function getDescription() {
        return __('Post Date', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = '';

        if (isset($context['post'])) {
            $value = get_the_date(get_option('date_format'), $context['post']->ID);
        }

        return apply_filters('seopress_get_tag_post_date_value', $value, $context);
    }
}

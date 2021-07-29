<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class TagDescription implements GetTagValue {
    const NAME = 'tag_description';

    public static function getDescription() {
        return __('Tag Description', 'wp-seopress');
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
            $value   = tag_description();
        }

        $value   = wp_trim_words(
            stripslashes_deep(
                wp_filter_nohtml_kses($value)
            ), seopress_get_service('TagsToString')->getExcerptLengthForTags()
        );

        return apply_filters('seopress_get_tag_tag_description_value', $value, $context);
    }
}

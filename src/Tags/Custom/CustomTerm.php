<?php

namespace SEOPress\Tags\Custom;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

class CustomTerm extends AbstractCustomTagValue implements GetTagValue {
    const CUSTOM_FORMAT = '_ct_';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $tag     = isset($args[1]) ? $args[1] : null;
        $value   = '';
        if (null === $tag || ! $context) {
            return $value;
        }

        if ( ! $context['post']) {
            return $value;
        }

        $regex = $this->buildRegex(self::CUSTOM_FORMAT);

        preg_match($regex, $tag, $matches);

        if (empty($matches) || ! array_key_exists('field', $matches)) {
            return $value;
        }

        $field = $matches['field'];

        $terms = wp_get_post_terms($context['post']->ID, $field);
        if (is_wp_error($terms)) {
            return $value;
        }
        $value   = esc_attr($terms[0]->name);
        /**
         * @deprecated 4.4.0
         * Use seopress_get_tag' . $tag . '_value
         */
        $value  = apply_filters('seopress_titles_custom_tax', $value, $field);

        return apply_filters('seopress_get_tag' . $tag . '_value', $value, $context);
    }
}

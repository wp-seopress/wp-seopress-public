<?php

namespace SEOPress\Tags\Custom;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

class CustomPostMeta extends AbstractCustomTagValue implements GetTagValue {
    const CUSTOM_FORMAT = '_cf_';
    const NAME          = '_cf_your_custom_field_name';

    public static function getDescription() {
        return __('Custom fields (replace your_custom_field_name by the name of your custom field)', 'wp-seopress');
    }

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

        $value = esc_attr(get_post_meta($context['post']->ID, $field, true));

        return apply_filters('seopress_get_tag_' . $tag . '_value', $value, $context);
    }
}

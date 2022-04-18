<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class Separator implements GetTagValue {
    const NAME = 'sep';

    const DEFAULT_SEPARATOR = '-';

    public static function getDescription() {
        return __('Separator', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context   = isset($args[0]) ? $args[0] : null;

        $separator = seopress_get_service('TitleOption')->getSeparator();
        if (empty($separator)) {
            $separator = self::DEFAULT_SEPARATOR;
        }

        return apply_filters('seopress_get_tag_separator_value', $separator, $context);
    }
}

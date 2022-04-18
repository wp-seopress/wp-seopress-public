<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class ArchiveDateDay implements GetTagValue {
    const NAME = 'archive_date_day';

    public static function getDescription() {
        return __('Day Archive Date', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = get_query_var('day');

        return apply_filters('seopress_get_tag_archive_date_day_value', $value, $context);
    }
}

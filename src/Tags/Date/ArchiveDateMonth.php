<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class ArchiveDateMonth implements GetTagValue {
    const NAME = 'archive_date_month';

    public static function getDescription() {
        return __('Month Archive Date', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = get_query_var('monthnum');

        return apply_filters('seopress_get_tag_archive_date_month_value', $value, $context);
    }
}

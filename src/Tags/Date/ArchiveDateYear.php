<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class ArchiveDateYear implements GetTagValue {
    const NAME = 'archive_date_year';

    public static function getDescription() {
        return __('Year Archive Date', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = get_query_var('year');

        return apply_filters('seopress_get_tag_archive_date_year_value', $value, $context);
    }
}

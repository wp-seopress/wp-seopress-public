<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class ArchiveTitle implements GetTagValue {
    const NAME = 'archive_title';

    public static function getDescription() {
        return __('Archive Title', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;
        $value   = get_the_archive_title();

        return apply_filters('seopress_get_tag_archive_title_value', $value, $context);
    }
}

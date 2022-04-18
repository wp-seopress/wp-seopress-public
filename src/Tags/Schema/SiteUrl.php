<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteUrl implements GetTagValue {
    const NAME = 'siteurl';

    public static function getDescription() {
        return __('Site URL', 'wp-seopress');
    }

    public function getValue($args = null) {
        $value = site_url();

        return apply_filters('seopress_get_tag_site_url_value', $value);
    }
}

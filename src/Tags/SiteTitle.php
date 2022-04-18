<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteTitle implements GetTagValue {
    const NAME = 'sitetitle';

    const ALIAS = ['sitename'];

    /**
     * 4.8.0.
     *
     * @return string
     */
    public static function getDescription() {
        return __('Site Title', 'wp-seopress');
    }

    public function getValue($args = null) {
        return get_bloginfo('name');
    }
}

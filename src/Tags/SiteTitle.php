<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteTitle implements GetTagValue {
    const NAME = 'sitetitle';

    const ALIAS = ['sitename'];

    public function getValue($args = null) {
        return get_bloginfo('name');
    }
}

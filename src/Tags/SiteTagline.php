<?php

namespace SEOPress\Tags;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteTagline implements GetTagValue {
    const NAME = 'tagline';

    const ALIAS = ['sitedesc'];

    public function getValue($args = null) {
        return get_bloginfo('description');
    }
}

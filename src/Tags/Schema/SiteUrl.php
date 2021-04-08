<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteUrl implements GetTagValue {
    const NAME = 'siteurl';

    public function getValue($args = null) {
        return site_url();
    }
}

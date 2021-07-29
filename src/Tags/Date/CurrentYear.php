<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentYear implements GetTagValue {
    const NAME = 'currentyear';

    public static function getDescription() {
        return __('Current Year', 'wp-seopress');
    }

    public function getValue($args = null) {
        return date('Y');
    }
}

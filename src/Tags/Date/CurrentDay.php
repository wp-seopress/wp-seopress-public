<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentDay implements GetTagValue {
    const NAME = 'currentday';

    public static function getDescription() {
        return __('Current Day', 'wp-seopress');
    }

    public function getValue($args = null) {
        return date_i18n('j');
    }
}

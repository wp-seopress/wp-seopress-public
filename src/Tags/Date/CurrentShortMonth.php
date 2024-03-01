<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentShortMonth implements GetTagValue {
    const NAME = 'currentmonth_short';

    public static function getDescription() {
        return __('Current Month in 3 letters', 'wp-seopress');
    }

    public function getValue($args = null) {
        return date_i18n('M');
    }
}

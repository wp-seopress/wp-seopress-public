<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentDate implements GetTagValue {
    const NAME = 'currentdate';

    public function getValue($args = null) {
        return date_i18n(get_option('date_format'));
    }
}

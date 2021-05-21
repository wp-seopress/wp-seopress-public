<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentMonth implements GetTagValue {
    const NAME = 'currentmonth';

    public function getValue($args = null) {
        return date_i18n('F');
    }
}

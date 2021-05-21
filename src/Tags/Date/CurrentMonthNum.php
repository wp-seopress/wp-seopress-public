<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentMonthNum implements GetTagValue {
    const NAME = 'currentmonth_num';

    public function getValue($args = null) {
        return date_i18n('n');
    }
}

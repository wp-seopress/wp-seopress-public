<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentTime implements GetTagValue {
    const NAME = 'currenttime';

    public function getValue($args = null) {
        return current_time(get_option('time_format'));
    }
}

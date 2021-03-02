<?php

namespace SEOPress\Tags\Date;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class CurrentYear implements GetTagValue {
    const NAME = 'currentyear';

    public function getValue($args = null) {
        return date('Y');
    }
}

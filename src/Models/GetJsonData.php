<?php

namespace SEOPress\Models;

if ( ! defined('ABSPATH')) {
    exit;
}

interface GetJsonData {
    public function getJsonData($context = null);
}

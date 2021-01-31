<?php

namespace SEOPress\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class TagCompose {
    const START_TAG = '%%';
    const END_TAG   = '%%';

    public static function getValueWithTag($tag) {
        return sprintf('%s%s%s', self::START_TAG, $tag, self::END_TAG);
    }
}

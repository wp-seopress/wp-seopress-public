<?php

namespace SEOPress\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class OpeningHoursHelper {
    public static function getDays() {
        return [
            __('Monday', 'wp-seopress'),
            __('Tuesday', 'wp-seopress'),
            __('Wednesday', 'wp-seopress'),
            __('Thursday', 'wp-seopress'),
            __('Friday', 'wp-seopress'),
            __('Saturday', 'wp-seopress'),
            __('Sunday', 'wp-seopress'),
        ];
    }

    public static function getHours() {
        return ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
    }

    public static function getMinutes() {
        return ['00', '15', '30', '45', '59'];
    }
}

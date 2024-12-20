<?php

namespace SEOPress\Helpers;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class Currencies {
    public static function getOptions() {
        return apply_filters('seopress_get_options_schema_currencies', [
            ['value' => 'none', 'label' => __('Select a Currency', 'wp-seopress')],
            ['value' => 'USD', 'label' => __('U.S. Dollar', 'wp-seopress')],
            ['value' => 'GBP', 'label' => __('Pound Sterling', 'wp-seopress')],
            ['value' => 'EUR', 'label' => __('Euro', 'wp-seopress')],
            ['value' => 'ARS', 'label' => __('Argentina Peso', 'wp-seopress')],
            ['value' => 'AUD', 'label' => __('Australian Dollar', 'wp-seopress')],
            ['value' => 'BRL', 'label' => __('Brazilian Real', 'wp-seopress')],
            ['value' => 'BGN', 'label' => __('Bulgarian lev', 'wp-seopress')],
            ['value' => 'CAD', 'label' => __('Canadian Dollar', 'wp-seopress')],
            ['value' => 'CLP', 'label' => __('Chilean Peso', 'wp-seopress')],
            ['value' => 'CZK', 'label' => __('Czech Koruna', 'wp-seopress')],
            ['value' => 'DKK', 'label' => __('Danish Krone', 'wp-seopress')],
            ['value' => 'HKD', 'label' => __('Hong Kong Dollar', 'wp-seopress')],
            ['value' => 'HUF', 'label' => __('Hungarian Forint', 'wp-seopress')],
            ['value' => 'INR', 'label' => __('Indian rupee', 'wp-seopress')],
            ['value' => 'ILS', 'label' => __('Israeli New Sheqel', 'wp-seopress')],
            ['value' => 'JPY', 'label' => __('Japanese Yen', 'wp-seopress')],
            ['value' => 'MYR', 'label' => __('Malaysian Ringgit', 'wp-seopress')],
            ['value' => 'MXN', 'label' => __('Mexican Peso', 'wp-seopress')],
            ['value' => 'NOK', 'label' => __('Norwegian Krone', 'wp-seopress')],
            ['value' => 'NZD', 'label' => __('New Zealand Dollar', 'wp-seopress')],
            ['value' => 'PHP', 'label' => __('Philippine Peso', 'wp-seopress')],
            ['value' => 'PLN', 'label' => __('Polish Zloty', 'wp-seopress')],
            ['value' => 'IDR', 'label' => __('Indonesian rupiah', 'wp-seopress')],
            ['value' => 'RUB', 'label' => __('Russian Ruble', 'wp-seopress')],
            ['value' => 'SGD', 'label' => __('Singapore Dollar', 'wp-seopress')],
            ['value' => 'PEN', 'label' => __('Sol', 'wp-seopress')],
            ['value' => 'ZAR', 'label' => __('South African Rand', 'wp-seopress')],
            ['value' => 'SEK', 'label' => __('Swedish Krona', 'wp-seopress')],
            ['value' => 'CHF', 'label' => __('Swiss Franc', 'wp-seopress')],
            ['value' => 'TWD', 'label' => __('Taiwan New Dollar', 'wp-seopress')],
            ['value' => 'THB', 'label' => __('Thai Baht', 'wp-seopress')],
            ['value' => 'UAH', 'label' => __('Ukrainian hryvnia', 'wp-seopress')],
            ['value' => 'VND', 'label' => __('Vietnamese đồng', 'wp-seopress')],
        ]);
    }
}

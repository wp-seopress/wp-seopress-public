<?php

namespace SEOPress\Helpers;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class Currencies {
    public static function getOptions() {
        return apply_filters('seopress_get_options_schema_currencies', [
            ['value' => 'none', 'label' => __('Select a Currency', 'wp-seopress-pro')],
            ['value' => 'USD', 'label' => __('U.S. Dollar', 'wp-seopress-pro')],
            ['value' => 'GBP', 'label' => __('Pound Sterling', 'wp-seopress-pro')],
            ['value' => 'EUR', 'label' => __('Euro', 'wp-seopress-pro')],
            ['value' => 'ARS', 'label' => __('Argentina Peso', 'wp-seopress-pro')],
            ['value' => 'AUD', 'label' => __('Australian Dollar', 'wp-seopress-pro')],
            ['value' => 'BRL', 'label' => __('Brazilian Real', 'wp-seopress-pro')],
            ['value' => 'BGN', 'label' => __('Bulgarian lev', 'wp-seopress-pro')],
            ['value' => 'CAD', 'label' => __('Canadian Dollar', 'wp-seopress-pro')],
            ['value' => 'CLP', 'label' => __('Chilean Peso', 'wp-seopress-pro')],
            ['value' => 'CZK', 'label' => __('Czech Koruna', 'wp-seopress-pro')],
            ['value' => 'DKK', 'label' => __('Danish Krone', 'wp-seopress-pro')],
            ['value' => 'HKD', 'label' => __('Hong Kong Dollar', 'wp-seopress-pro')],
            ['value' => 'HUF', 'label' => __('Hungarian Forint', 'wp-seopress-pro')],
            ['value' => 'INR', 'label' => __('Indian rupee', 'wp-seopress-pro')],
            ['value' => 'ILS', 'label' => __('Israeli New Sheqel', 'wp-seopress-pro')],
            ['value' => 'JPY', 'label' => __('Japanese Yen', 'wp-seopress-pro')],
            ['value' => 'MYR', 'label' => __('Malaysian Ringgit', 'wp-seopress-pro')],
            ['value' => 'MXN', 'label' => __('Mexican Peso', 'wp-seopress-pro')],
            ['value' => 'NOK', 'label' => __('Norwegian Krone', 'wp-seopress-pro')],
            ['value' => 'NZD', 'label' => __('New Zealand Dollar', 'wp-seopress-pro')],
            ['value' => 'PHP', 'label' => __('Philippine Peso', 'wp-seopress-pro')],
            ['value' => 'PLN', 'label' => __('Polish Zloty', 'wp-seopress-pro')],
            ['value' => 'IDR', 'label' => __('Indonesian rupiah', 'wp-seopress-pro')],
            ['value' => 'RUB', 'label' => __('Russian Ruble', 'wp-seopress-pro')],
            ['value' => 'SGD', 'label' => __('Singapore Dollar', 'wp-seopress-pro')],
            ['value' => 'PEN', 'label' => __('Sol', 'wp-seopress-pro')],
            ['value' => 'ZAR', 'label' => __('South African Rand', 'wp-seopress-pro')],
            ['value' => 'SEK', 'label' => __('Swedish Krona', 'wp-seopress-pro')],
            ['value' => 'CHF', 'label' => __('Swiss Franc', 'wp-seopress-pro')],
            ['value' => 'TWD', 'label' => __('Taiwan New Dollar', 'wp-seopress-pro')],
            ['value' => 'THB', 'label' => __('Thai Baht', 'wp-seopress-pro')],
            ['value' => 'UAH', 'label' => __('Ukrainian hryvnia', 'wp-seopress-pro')],
            ['value' => 'VND', 'label' => __('Vietnamese đồng', 'wp-seopress-pro')],
        ]);
    }
}

<?php

namespace SEOPress\Helpers\Schemas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class Course {
    public static function getCategories() {
        return apply_filters('seopress_get_options_schema_course_categories', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress-pro')],
            ['value' => 'Free', 'label' => __('Free', 'wp-seopress-pro')],
            ['value' => 'Partially Free', 'label' => __('Partially free', 'wp-seopress-pro')],
            ['value' => 'Subscription', 'label' => __('Subscription', 'wp-seopress-pro')],
            ['value' => 'Paid', 'label' => __('Paid', 'wp-seopress-pro')],
        ]);
    }

    public static function getCourseModes() {
        return apply_filters('seopress_get_options_schema_course_course_modes', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress-pro')],
            ['value' => 'Onsite', 'label' => __('Onsite', 'wp-seopress-pro')],
            ['value' => 'Online', 'label' => __('Online', 'wp-seopress-pro')],
        ]);
    }
    
    public static function getRepeatFrequencies() {
        return apply_filters('seopress_get_options_schema_course_repeat_frequencies', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress-pro')],
            ['value' => 'Daily', 'label' => __('Daily', 'wp-seopress-pro')],
            ['value' => 'Weekly', 'label' => __('Weekly', 'wp-seopress-pro')],
            ['value' => 'Monthly', 'label' => __('Monthly', 'wp-seopress-pro')],
            ['value' => 'Yearly', 'label' => __('Yearly', 'wp-seopress-pro')],
        ]);
    }
}
<?php

namespace SEOPress\Helpers\Schemas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class Course {
    public static function getCategories() {
        return apply_filters('seopress_get_options_schema_course_categories', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress')],
            ['value' => 'Free', 'label' => __('Free', 'wp-seopress')],
            ['value' => 'Partially Free', 'label' => __('Partially free', 'wp-seopress')],
            ['value' => 'Subscription', 'label' => __('Subscription', 'wp-seopress')],
            ['value' => 'Paid', 'label' => __('Paid', 'wp-seopress')],
        ]);
    }

    public static function getCourseModes() {
        return apply_filters('seopress_get_options_schema_course_course_modes', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress')],
            ['value' => 'Onsite', 'label' => __('Onsite', 'wp-seopress')],
            ['value' => 'Online', 'label' => __('Online', 'wp-seopress')],
        ]);
    }
    
    public static function getRepeatFrequencies() {
        return apply_filters('seopress_get_options_schema_course_repeat_frequencies', [
            ['value' => 'none', 'label' => __('Select a category', 'wp-seopress')],
            ['value' => 'Daily', 'label' => __('Daily', 'wp-seopress')],
            ['value' => 'Weekly', 'label' => __('Weekly', 'wp-seopress')],
            ['value' => 'Monthly', 'label' => __('Monthly', 'wp-seopress')],
            ['value' => 'Yearly', 'label' => __('Yearly', 'wp-seopress')],
        ]);
    }
}
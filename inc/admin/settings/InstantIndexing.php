<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Instant Indexing SECTION=========================================================================
add_settings_section(
    'seopress_setting_section_instant_indexing', // ID
    '',
    //__("Instant Indexing","wp-seopress"), // Title
    'seopress_print_section_instant_indexing_general', // Callback
    'seopress-settings-admin-instant-indexing' // Page
);

add_settings_field(
    'seopress_instant_indexing_google_engine', // ID
    __('Select search engines', 'wp-seopress'), // Title
    'seopress_instant_indexing_google_engine_callback', // Callback
    'seopress-settings-admin-instant-indexing', // Page
    'seopress_setting_section_instant_indexing' // Section
);

add_settings_field(
    'seopress_instant_indexing_google_action', // ID
    __('Which action to run for Google?', 'wp-seopress'), // Title
    'seopress_instant_indexing_google_action_callback', // Callback
    'seopress-settings-admin-instant-indexing', // Page
    'seopress_setting_section_instant_indexing' // Section
);

add_settings_field(
    'seopress_instant_indexing_manual_batch', // ID
    __('Submit URLs for indexing', 'wp-seopress'), // Title
    'seopress_instant_indexing_manual_batch_callback', // Callback
    'seopress-settings-admin-instant-indexing', // Page
    'seopress_setting_section_instant_indexing' // Section
);

add_settings_section(
    'seopress_setting_section_instant_indexing_settings', // ID
    '',
    //__("Settings","wp-seopress"), // Title
    'seopress_print_section_instant_indexing_settings', // Callback
    'seopress-settings-admin-instant-indexing-settings' // Page
);

add_settings_field(
    'seopress_instant_indexing_google_api_key', // ID
    __('Google Indexing API key', 'wp-seopress'), // Title
    'seopress_instant_indexing_google_api_key_callback', // Callback
    'seopress-settings-admin-instant-indexing-settings', // Page
    'seopress_setting_section_instant_indexing_settings' // Section
);

add_settings_field(
    'seopress_instant_indexing_bing_api_key', // ID
    __('Bing Indexing API key', 'wp-seopress'), // Title
    'seopress_instant_indexing_bing_api_key_callback', // Callback
    'seopress-settings-admin-instant-indexing-settings', // Page
    'seopress_setting_section_instant_indexing_settings' // Section
);

add_settings_field(
    'seopress_instant_indexing_automate_submission', // ID
    __('Automatically notify search engines', 'wp-seopress'), // Title
    'seopress_instant_indexing_automate_submission_callback', // Callback
    'seopress-settings-admin-instant-indexing-settings', // Page
    'seopress_setting_section_instant_indexing_settings' // Section
);

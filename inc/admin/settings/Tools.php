<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Tools SECTION=======================================================================
add_settings_section(
    'seopress_setting_section_tools_compatibility', // ID
    '',
    //__("Compatibility Center","wp-seopress"), // Title
    'print_section_info_tools_compatibility', // Callback
    'seopress-settings-admin-tools-compatibility' // Page
);

add_settings_field(
    'seopress_setting_section_tools_compatibility_oxygen', // ID
    __('Oxygen Builder compatibility', 'wp-seopress'), // Title
    'seopress_setting_section_tools_compatibility_oxygen_callback', // Callback
    'seopress-settings-admin-tools-compatibility', // Page
    'seopress_setting_section_tools_compatibility' // Section
);

add_settings_field(
    'seopress_setting_section_tools_compatibility_divi', // ID
    __('Divi Builder compatibility', 'wp-seopress'), // Title
    'seopress_setting_section_tools_compatibility_divi_callback', // Callback
    'seopress-settings-admin-tools-compatibility', // Page
    'seopress_setting_section_tools_compatibility' // Section
);

add_settings_field(
    'seopress_setting_section_tools_compatibility_bakery', // ID
    __('WP Bakery Builder compatibility', 'wp-seopress'), // Title
    'seopress_setting_section_tools_compatibility_bakery_callback', // Callback
    'seopress-settings-admin-tools-compatibility', // Page
    'seopress_setting_section_tools_compatibility' // Section
);

add_settings_field(
    'seopress_setting_section_tools_compatibility_avia', // ID
    __('Avia Layout Builder compatibility', 'wp-seopress'), // Title
    'seopress_setting_section_tools_compatibility_avia_callback', // Callback
    'seopress-settings-admin-tools-compatibility', // Page
    'seopress_setting_section_tools_compatibility' // Section
);

add_settings_field(
    'seopress_setting_section_tools_compatibility_fusion', // ID
    __('Fusion Builder compatibility', 'wp-seopress'), // Title
    'seopress_setting_section_tools_compatibility_fusion_callback', // Callback
    'seopress-settings-admin-tools-compatibility', // Page
    'seopress_setting_section_tools_compatibility' // Section
);

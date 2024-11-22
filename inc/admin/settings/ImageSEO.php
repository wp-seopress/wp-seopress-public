<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Image SECTION============================================================================
add_settings_section(
    'seopress_setting_section_advanced_image', // ID
    '',
    //__("Image SEO","wp-seopress"), // Title
    'seopress_print_section_info_advanced_image', // Callback
    'seopress-settings-admin-advanced-image' // Page
);

add_settings_field(
    'seopress_advanced_advanced_attachments', // ID
    __('Redirect attachment pages to post parent', 'wp-seopress'), // Title
    'seopress_advanced_advanced_attachments_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_attachments_file', // ID
    __('Redirect attachment pages to their file URL', 'wp-seopress'), // Title
    'seopress_advanced_advanced_attachments_file_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_clean_filename', // ID
    __('Cleaning media filename', 'wp-seopress'), // Title
    'seopress_advanced_advanced_clean_filename_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_title_editor', // ID
    __('Automatically set the image Title', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_title_editor_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_alt_editor', // ID
    __('Automatically set the image Alt text', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_alt_editor_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_alt_target_kw', // ID
    __('Automatically set the image Alt text from target keywords', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_alt_target_kw_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_alt_txt', // ID
    __('Automatically set alt text on already inserted image', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_alt_txt_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_caption_editor', // ID
    __('Automatically set the image Caption', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_caption_editor_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

add_settings_field(
    'seopress_advanced_advanced_image_auto_desc_editor', // ID
    __('Automatically set the image Description', 'wp-seopress'), // Title
    'seopress_advanced_advanced_image_auto_desc_editor_callback', // Callback
    'seopress-settings-admin-advanced-image', // Page
    'seopress_setting_section_advanced_image' // Section
);

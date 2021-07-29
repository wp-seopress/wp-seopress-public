<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Titles & metas SECTION===================================================================
add_settings_section(
    'seopress_setting_section_titles_home', // ID
    '',
    //__("Home","wp-seopress"), // Title
    'print_section_info_titles', // Callback
    'seopress-settings-admin-titles-home' // Page
);

add_settings_field(
    'seopress_titles_sep', // ID
    __('Separator', 'wp-seopress'), // Title
    'seopress_titles_sep_callback', // Callback
    'seopress-settings-admin-titles-home', // Page
    'seopress_setting_section_titles_home' // Section
);

add_settings_field(
    'seopress_titles_home_site_title', // ID
    __('Site title', 'wp-seopress'), // Title
    'seopress_titles_home_site_title_callback', // Callback
    'seopress-settings-admin-titles-home', // Page
    'seopress_setting_section_titles_home' // Section
);

add_settings_field(
    'seopress_titles_home_site_desc', // ID
    __('Meta description', 'wp-seopress'), // Title
    'seopress_titles_home_site_desc_callback', // Callback
    'seopress-settings-admin-titles-home', // Page
    'seopress_setting_section_titles_home' // Section
);

//Single Post Types SECTION================================================================
add_settings_section(
    'seopress_setting_section_titles_single', // ID
    '',
    //__("Post Types","wp-seopress"), // Title
    'print_section_info_single', // Callback
    'seopress-settings-admin-titles-single' // Page
);

add_settings_field(
    'seopress_titles_single_titles', // ID
    '',
    'seopress_titles_single_titles_callback', // Callback
    'seopress-settings-admin-titles-single', // Page
    'seopress_setting_section_titles_single' // Section
);

if (is_plugin_active('buddypress/bp-loader.php') || is_plugin_active('buddyboss-platform/bp-loader.php')) {
    add_settings_field(
        'seopress_titles_bp_groups_title', // ID
        '',
        'seopress_titles_bp_groups_title_callback', // Callback
        'seopress-settings-admin-titles-single', // Page
        'seopress_setting_section_titles_single' // Section
    );

    add_settings_field(
        'seopress_titles_bp_groups_desc', // ID
        '',
        'seopress_titles_bp_groups_desc_callback', // Callback
        'seopress-settings-admin-titles-single', // Page
        'seopress_setting_section_titles_single' // Section
    );

    add_settings_field(
        'seopress_titles_bp_groups_noindex', // ID
        '',
        'seopress_titles_bp_groups_noindex_callback', // Callback
        'seopress-settings-admin-titles-single', // Page
        'seopress_setting_section_titles_single' // Section
    );
}

//Archives SECTION=========================================================================
add_settings_section(
    'seopress_setting_section_titles_archives', // ID
    '',
    //__("Archives","wp-seopress"), // Title
    'print_section_info_archives', // Callback
    'seopress-settings-admin-titles-archives' // Page
);

add_settings_field(
    'seopress_titles_archives_titles', // ID
    '',
    'seopress_titles_archives_titles_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_author_title', // ID
    '',
    //__('Title template','wp-seopress'),
    'seopress_titles_archives_author_title_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_author_desc', // ID
    '',
    //__('Meta description template','wp-seopress'),
    'seopress_titles_archives_author_desc_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_author_noindex', // ID
    '',
    //__("noindex","wp-seopress"), // Title
    'seopress_titles_archives_author_noindex_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_author_disable', // ID
    '',
    //__("disable","wp-seopress"), // Title
    'seopress_titles_archives_author_disable_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_date_title', // ID
    '',
    //__('Title template','wp-seopress'),
    'seopress_titles_archives_date_title_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_date_desc', // ID
    '',
    //__('Meta description template','wp-seopress'),
    'seopress_titles_archives_date_desc_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_date_noindex', // ID
    '',
    //__("noindex","wp-seopress"), // Title
    'seopress_titles_archives_date_noindex_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_date_disable', // ID
    '',
    //__("disable","wp-seopress"), // Title
    'seopress_titles_archives_date_disable_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_search_title', // ID
    '',
    //__('Title template','wp-seopress'),
    'seopress_titles_archives_search_title_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_search_desc', // ID
    '',
    //__('Meta description template','wp-seopress'),
    'seopress_titles_archives_search_desc_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_search_title_noindex', // ID
    '',
    //__('noindex','wp-seopress'),
    'seopress_titles_archives_search_title_noindex_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_404_title', // ID
    '',
    //__('Title template','wp-seopress'),
    'seopress_titles_archives_404_title_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

add_settings_field(
    'seopress_titles_archives_404_desc', // ID
    '',
    //__('Meta description template','wp-seopress'),
    'seopress_titles_archives_404_desc_callback', // Callback
    'seopress-settings-admin-titles-archives', // Page
    'seopress_setting_section_titles_archives' // Section
);

//Taxonomies SECTION=======================================================================
add_settings_section(
    'seopress_setting_section_titles_tax', // ID
    '',
    //__("Taxonomies","wp-seopress"), // Title
    'print_section_info_tax', // Callback
    'seopress-settings-admin-titles-tax' // Page
);

add_settings_field(
    'seopress_titles_tax_titles', // ID
    '',
    'seopress_titles_tax_titles_callback', // Callback
    'seopress-settings-admin-titles-tax', // Page
    'seopress_setting_section_titles_tax' // Section
);

//Advanced SECTION=========================================================================
add_settings_section(
    'seopress_setting_section_titles_advanced', // ID
    '',
    //__("Advanced","wp-seopress"), // Title
    'print_section_info_advanced', // Callback
    'seopress-settings-admin-titles-advanced' // Page
);

add_settings_field(
    'seopress_titles_noindex', // ID
    __('noindex', 'wp-seopress'), // Title
    'seopress_titles_noindex_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_nofollow', // ID
    __('nofollow', 'wp-seopress'), // Title
    'seopress_titles_nofollow_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_noodp', // ID
    __('noodp', 'wp-seopress'), // Title
    'seopress_titles_noodp_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_noimageindex', // ID
    __('noimageindex', 'wp-seopress'), // Title
    'seopress_titles_noimageindex_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_noarchive', // ID
    __('noarchive', 'wp-seopress'), // Title
    'seopress_titles_noarchive_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_nosnippet', // ID
    __('nosnippet', 'wp-seopress'), // Title
    'seopress_titles_nosnippet_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_nositelinkssearchbox', // ID
    __('nositelinkssearchbox', 'wp-seopress'), // Title
    'seopress_titles_nositelinkssearchbox_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_paged_rel', // ID
    __('Indicate paginated content to Google', 'wp-seopress'), // Title
    'seopress_titles_paged_rel_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

add_settings_field(
    'seopress_titles_paged_noindex', // ID
    __('noindex on paged archives', 'wp-seopress'), // Title
    'seopress_titles_paged_noindex_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);
add_settings_field(
    'seopress_titles_attachments_noindex', // ID
    __('noindex on attachment pages', 'wp-seopress'), // Title
    'seopress_titles_attachments_noindex_callback', // Callback
    'seopress-settings-admin-titles-advanced', // Page
    'seopress_setting_section_titles_advanced' // Section
);

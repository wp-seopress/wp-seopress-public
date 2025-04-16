<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Knowledge graph SECTION======================================================================
add_settings_section(
    'seopress_setting_section_social_knowledge', // ID
    '',
    //__("Knowledge graph","wp-seopress"), // Title
    'seopress_print_section_info_social_knowledge', // Callback
    'seopress-settings-admin-social-knowledge' // Page
);

add_settings_field(
    'seopress_social_knowledge_type', // ID
    __('Person or organization', 'wp-seopress'), // Title
    'seopress_social_knowledge_type_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_name', // ID
    __('Your name/organization', 'wp-seopress'), // Title
    'seopress_social_knowledge_name_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_img', // ID
    __('Your photo/organization logo', 'wp-seopress'), // Title
    'seopress_social_knowledge_img_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_desc', // ID
    __('Description', 'wp-seopress'), // Title
    'seopress_social_knowledge_desc_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_email', // ID
    __('Email', 'wp-seopress'), // Title
    'seopress_social_knowledge_email_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_phone', // ID
    __("Organization's phone number", 'wp-seopress'), // Title
    'seopress_social_knowledge_phone_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_contact_type', // ID
    __('Contact type', 'wp-seopress'), // Title
    'seopress_social_knowledge_contact_type_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_contact_option', // ID
    __('Contact option', 'wp-seopress'), // Title
    'seopress_social_knowledge_contact_option_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

add_settings_field(
    'seopress_social_knowledge_tax_id', // ID
    __('VAT ID', 'wp-seopress'), // Title
    'seopress_social_knowledge_tax_id_callback', // Callback
    'seopress-settings-admin-social-knowledge', // Page
    'seopress_setting_section_social_knowledge' // Section
);

//Social SECTION=====================================================================================
add_settings_section(
    'seopress_setting_section_social_accounts', // ID
    '',
    //__("Social","wp-seopress"), // Title
    'seopress_print_section_info_social_accounts', // Callback
    'seopress-settings-admin-social-accounts' // Page
);

add_settings_field(
    'seopress_social_accounts_facebook', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/facebook.svg') . '" alt="Facebook" width="24" height="24"> ' . __('Facebook page URL', 'wp-seopress'), // Title
    'seopress_social_accounts_facebook_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_twitter', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/x.svg') . '" alt="X" width="24" height="24"> ' . __('X Username', 'wp-seopress'), // Title
    'seopress_social_accounts_twitter_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_pinterest', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/pinterest.svg') . '" alt="Pinterest" width="24" height="24"> ' . __('Pinterest URL', 'wp-seopress'), // Title
    'seopress_social_accounts_pinterest_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_instagram', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/instagram.svg') . '" alt="Instagram" width="24" height="24"> ' . __('Instagram URL', 'wp-seopress'), // Title
    'seopress_social_accounts_instagram_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_youtube', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/youtube.svg') . '" alt="YouTube" width="24" height="24"> ' . __('YouTube URL', 'wp-seopress'), // Title
    'seopress_social_accounts_youtube_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_linkedin', // ID
    '<img class="seopress-social-icon" src="' . esc_url(SEOPRESS_URL_ASSETS . '/img/social/linkedin.svg') . '" alt="LinkedIn" width="24" height="24"> ' . __('LinkedIn URL', 'wp-seopress'), // Title
    'seopress_social_accounts_linkedin_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

add_settings_field(
    'seopress_social_accounts_extra', // ID
    __('Additional accounts', 'wp-seopress'), // Title
    'seopress_social_accounts_extra_callback', // Callback
    'seopress-settings-admin-social-accounts', // Page
    'seopress_setting_section_social_accounts' // Section
);

//Facebook SECTION=========================================================================
add_settings_section(
    'seopress_setting_section_social_facebook', // ID
    '',
    //__("Facebook","wp-seopress"), // Title
    'seopress_print_section_info_social_facebook', // Callback
    'seopress-settings-admin-social-facebook' // Page
);

add_settings_field(
    'seopress_social_facebook_og', // ID
    __('Enable Open Graph Data', 'wp-seopress'), // Title
    'seopress_social_facebook_og_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_img', // ID
    __('Select a default image', 'wp-seopress'), // Title
    'seopress_social_facebook_img_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_img_default', // ID
    __('Apply this image to all your og:image tag', 'wp-seopress'), // Title
    'seopress_social_facebook_img_default_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_img_cpt', // ID
    __('Define custom og:image tag for post type archive pages', 'wp-seopress'), // Title
    'seopress_social_facebook_img_cpt_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_link_ownership_id', // ID
    __('Facebook Link Ownership ID', 'wp-seopress'), // Title
    'seopress_social_facebook_link_ownership_id_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_admin_id', // ID
    __('Facebook Admin ID', 'wp-seopress'), // Title
    'seopress_social_facebook_admin_id_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

add_settings_field(
    'seopress_social_facebook_app_id', // ID
    __('Facebook App ID', 'wp-seopress'), // Title
    'seopress_social_facebook_app_id_callback', // Callback
    'seopress-settings-admin-social-facebook', // Page
    'seopress_setting_section_social_facebook' // Section
);

//Twitter SECTION==========================================================================
add_settings_section(
    'seopress_setting_section_social_twitter', // ID
    '',
    //__("X","wp-seopress"), // Title
    'seopress_print_section_info_social_twitter', // Callback
    'seopress-settings-admin-social-twitter' // Page
);

add_settings_field(
    'seopress_social_twitter_card', // ID
    __('Enable X Cards', 'wp-seopress'), // Title
    'seopress_social_twitter_card_callback', // Callback
    'seopress-settings-admin-social-twitter', // Page
    'seopress_setting_section_social_twitter' // Section
);

add_settings_field(
    'seopress_social_twitter_card_og', // ID
    __('Use Open Graph if no X Cards is filled', 'wp-seopress'), // Title
    'seopress_social_twitter_card_og_callback', // Callback
    'seopress-settings-admin-social-twitter', // Page
    'seopress_setting_section_social_twitter' // Section
);

add_settings_field(
    'seopress_social_twitter_card_img', // ID
    __('Default X Image', 'wp-seopress'), // Title
    'seopress_social_twitter_card_img_callback', // Callback
    'seopress-settings-admin-social-twitter', // Page
    'seopress_setting_section_social_twitter' // Section
);

add_settings_field(
    'seopress_social_twitter_card_img_size', // ID
    __('Image size for X Summary card', 'wp-seopress'), // Title
    'seopress_social_twitter_card_img_size_callback', // Callback
    'seopress-settings-admin-social-twitter', // Page
    'seopress_setting_section_social_twitter' // Section
);

//LinkedIn SECTION=================================================================================
add_settings_section(
    'seopress_setting_section_social_linkedin', // ID
    '',
    //__("LinkedIn","wp-seopress"), // Title
    'seopress_print_section_info_social_linkedin', // Callback
    'seopress-settings-admin-social-linkedin' // Page
);

add_settings_field(
    'seopress_social_li_img_size', // ID
    __('Post thumbnail image size', 'wp-seopress'), // Title
    'seopress_social_li_img_size_callback', // Callback
    'seopress-settings-admin-social-linkedin', // Page
    'seopress_setting_section_social_linkedin' // Section
);

//Fediverse SECTION================================================================================
add_settings_section(
    'seopress_setting_section_social_fediverse', // ID
    '',
    //__("Fediverse","wp-seopress"), // Title
    'seopress_print_section_info_social_fediverse', // Callback
    'seopress-settings-admin-social-fediverse' // Page
);

add_settings_field(
    'seopress_social_fv_creator', // ID
    __('Display Fediverse Creator tag', 'wp-seopress'), // Title
    'seopress_social_fv_creator_callback', // Callback
    'seopress-settings-admin-social-fediverse', // Page
    'seopress_setting_section_social_fediverse' // Section
);

<?php
/**
 * Analytics
 *
 * @package Settings
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

// Google Analytics Enable SECTION.
add_settings_section(
	'seopress_setting_section_google_analytics_enable', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_enable', // Callback.
	'seopress-settings-admin-google-analytics-enable' // Page.
);

add_settings_field(
	'seopress_google_analytics_enable', // ID.
	__( 'Enable Google Analytics tracking', 'wp-seopress' ), // Title.
	'seopress_google_analytics_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-enable', // Page.
	'seopress_setting_section_google_analytics_enable' // Section.
);

add_settings_field(
	'seopress_google_analytics_ga4', // ID.
	__( 'Enter your measurement ID (GA4)', 'wp-seopress' ), // Title.
	'seopress_google_analytics_ga4_callback', // Callback.
	'seopress-settings-admin-google-analytics-enable', // Page.
	'seopress_setting_section_google_analytics_enable' // Section.
);

add_settings_field(
	'seopress_google_analytics_ads', // ID.
	__( 'Enable Google Ads', 'wp-seopress' ), // Title.
	'seopress_google_analytics_ads_callback', // Callback.
	'seopress-settings-admin-google-analytics-enable', // Page.
	'seopress_setting_section_google_analytics_enable' // Section.
);

// Cookie bar / GDPR SECTION.
add_settings_section(
	'seopress_setting_section_google_analytics_gdpr', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_gdpr', // Callback.
	'seopress-settings-admin-google-analytics-gdpr' // Page.
);

add_settings_field(
	'seopress_google_analytics_hook', // ID.
	__( 'Where to load the cookie bar?', 'wp-seopress' ), // Title.
	'seopress_google_analytics_hook_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_disable', // ID.
	__( 'Analytics tracking opt-in', 'wp-seopress' ), // Title.
	'seopress_google_analytics_disable_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_half_disable', // ID.
	'', // Title.
	'seopress_google_analytics_half_disable_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_opt_out_edit_choice', // ID.
	__( 'Allow users to change their preferences', 'wp-seopress' ), // Title.
	'seopress_google_analytics_opt_out_edit_choice_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_opt_out_msg', // ID.
	__( 'Cookie consent message', 'wp-seopress' ), // Title.
	'seopress_google_analytics_opt_out_msg_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_opt_out_msg_ok', // ID.
	__( 'Accept button text', 'wp-seopress' ), // Title.
	'seopress_google_analytics_opt_out_msg_ok_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_opt_out_msg_close', // ID.
	__( 'Decline button text', 'wp-seopress' ), // Title.
	'seopress_google_analytics_opt_out_msg_close_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_opt_out_msg_edit', // ID.
	__( 'Cookie preferences button text', 'wp-seopress' ), // Title.
	'seopress_google_analytics_opt_out_msg_edit_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_exp_date', // ID.
	__( 'Cookie expiration (days)', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cb_exp_date_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_pos', // ID.
	__( 'Cookie bar position', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cb_pos_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_align', // ID.
	__( 'Cookie bar alignment', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cb_align_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_txt_align', // ID.
	__( 'Text alignment', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cb_txt_align_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_width', // ID.
	__( 'Cookie bar width', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cb_width_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_backdrop', // ID.
	'', // Title.
	'seopress_google_analytics_cb_backdrop_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_backdrop_bg', // ID.
	'', // Title.
	'seopress_google_analytics_cb_backdrop_bg_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_bg', // ID.
	'', // Title.
	'seopress_google_analytics_cb_bg_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_txt_col', // ID.
	'', // Title.
	'seopress_google_analytics_cb_txt_col_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_lk_col', // ID.
	'', // Title.
	'seopress_google_analytics_cb_lk_col_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_bg', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_bg_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_bg_hov', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_bg_hov_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_col', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_col_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_col_hov', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_col_hov_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_sec_bg', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_sec_bg_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_sec_bg_hov', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_sec_bg_hov_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_sec_col', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_sec_col_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

add_settings_field(
	'seopress_google_analytics_cb_btn_sec_col_hov', // ID.
	'', // Title.
	'seopress_google_analytics_cb_btn_sec_col_hov_callback', // Callback.
	'seopress-settings-admin-google-analytics-gdpr', // Page.
	'seopress_setting_section_google_analytics_gdpr' // Section.
);

// Google Analytics Custom Tracking SECTION.

add_settings_section(
	'seopress_setting_section_google_analytics_custom_tracking', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_custom_tracking', // Callback.
	'seopress-settings-admin-google-analytics-custom-tracking' // Page.
);

add_settings_field(
	'seopress_google_analytics_other_tracking', // ID.
	__( '[HEAD] Add an additional tracking code', 'wp-seopress' ), // Title.
	'seopress_google_analytics_other_tracking_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-tracking', // Page.
	'seopress_setting_section_google_analytics_custom_tracking' // Section.
);

add_settings_field(
	'seopress_google_analytics_other_tracking_body', // ID.
	__( '[BODY] Add an additional tracking code', 'wp-seopress' ), // Title.
	'seopress_google_analytics_other_tracking_body_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-tracking', // Page.
	'seopress_setting_section_google_analytics_custom_tracking' // Section.
);

add_settings_field(
	'seopress_google_analytics_other_tracking_footer', // ID.
	__( '[BODY (FOOTER)] Add an additional tracking code', 'wp-seopress' ), // Title.
	'seopress_google_analytics_other_tracking_footer_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-tracking', // Page.
	'seopress_setting_section_google_analytics_custom_tracking' // Section.
);

// Google Analytics Events SECTION.

add_settings_section(
	'seopress_setting_section_google_analytics_events', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_events', // Callback.
	'seopress-settings-admin-google-analytics-events' // Page.
);

add_settings_field(
	'seopress_google_analytics_link_tracking_enable', // ID.
	__( 'Enable external links tracking', 'wp-seopress' ), // Title.
	'seopress_google_analytics_link_tracking_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

add_settings_field(
	'seopress_google_analytics_download_tracking_enable', // ID.
	__( 'Enable downloads tracking (e.g. PDF, XLSX, DOCX...)', 'wp-seopress' ), // Title.
	'seopress_google_analytics_download_tracking_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

add_settings_field(
	'seopress_google_analytics_download_tracking', // ID.
	__( "Track downloads' clicks", 'wp-seopress' ), // Title.
	'seopress_google_analytics_download_tracking_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

add_settings_field(
	'seopress_google_analytics_affiliate_tracking_enable', // ID.
	__( 'Enable affiliate/outbound links tracking (e.g. aff, go, out, recommends)', 'wp-seopress' ), // Title.
	'seopress_google_analytics_affiliate_tracking_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

add_settings_field(
	'seopress_google_analytics_affiliate_tracking', // ID.
	__( 'Track affiliate/outbound links', 'wp-seopress' ), // Title.
	'seopress_google_analytics_affiliate_tracking_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

add_settings_field(
	'seopress_google_analytics_phone_tracking', // ID.
	__( 'Track phone links', 'wp-seopress' ), // Title.
	'seopress_google_analytics_phone_tracking_callback', // Callback.
	'seopress-settings-admin-google-analytics-events', // Page.
	'seopress_setting_section_google_analytics_events' // Section.
);

// Google Analytics Custom Dimensions SECTION.

add_settings_section(
	'seopress_setting_section_google_analytics_custom_dimensions', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_custom_dimensions', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions' // Page.
);

add_settings_field(
	'seopress_google_analytics_cd_author', // ID.
	__( 'Track Authors', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cd_author_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions', // Page.
	'seopress_setting_section_google_analytics_custom_dimensions' // Section.
);

add_settings_field(
	'seopress_google_analytics_cd_category', // ID.
	__( 'Track Categories', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cd_category_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions', // Page.
	'seopress_setting_section_google_analytics_custom_dimensions' // Section.
);

add_settings_field(
	'seopress_google_analytics_cd_tag', // ID.
	__( 'Track Tags', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cd_tag_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions', // Page.
	'seopress_setting_section_google_analytics_custom_dimensions' // Section.
);

add_settings_field(
	'seopress_google_analytics_cd_post_type', // ID.
	__( 'Track Post Types', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cd_post_type_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions', // Page.
	'seopress_setting_section_google_analytics_custom_dimensions' // Section.
);

add_settings_field(
	'seopress_google_analytics_cd_logged_in_user', // ID.
	__( 'Track Logged In Users', 'wp-seopress' ), // Title.
	'seopress_google_analytics_cd_logged_in_user_callback', // Callback.
	'seopress-settings-admin-google-analytics-custom-dimensions', // Page.
	'seopress_setting_section_google_analytics_custom_dimensions' // Section.
);

// Google Analytics Advanced SECTION.

add_settings_section(
	'seopress_setting_section_google_analytics_advanced', // ID.
	'',
	// __( "Advanced", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_advanced', // Callback.
	'seopress-settings-admin-google-analytics-advanced' // Page.
);

add_settings_field(
	'seopress_google_analytics_roles', // ID.
	__( 'Exclude user roles from tracking (GA, Matomo, MS Clarity, custom scripts)', 'wp-seopress' ), // Title.
	'seopress_google_analytics_roles_callback', // Callback.
	'seopress-settings-admin-google-analytics-advanced', // Page.
	'seopress_setting_section_google_analytics_advanced' // Section.
);

// Matomo SECTION.
add_settings_section(
	'seopress_setting_section_google_analytics_matomo', // ID.
	'',
	// __( "Google Analytics", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_matomo', // Callback.
	'seopress-settings-admin-google-analytics-matomo' // Page.
);

add_settings_field(
	'seopress_google_analytics_matomo_enable', // ID.
	__( 'Enable Matomo tracking', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_self_hosted', // ID.
	__( 'Self hosted Matomo installation', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_self_hosted_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_id', // ID.
	__( 'Enter your tracking ID', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_id_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_site_id', // ID.
	__( 'Enter your site ID', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_site_id_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_subdomains', // ID.
	__( 'Track visitors across all subdomains', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_subdomains_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_site_domain', // ID.
	__( 'Prepend the site domain', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_site_domain_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_no_js', // ID.
	__( 'Track users with JavaScript disabled', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_no_js_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_cross_domain', // ID.
	__( 'Enables cross domain linking', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_cross_domain_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_cross_domain_sites', // ID.
	__( 'Cross domain', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_cross_domain_sites_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);
add_settings_field(
	'seopress_google_analytics_matomo_dnt', // ID.
	__( 'Enable DoNotTrack detection', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_dnt_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_no_cookies', // ID.
	__( 'Disable all tracking cookies', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_no_cookies_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_link_tracking', // ID.
	__( 'Download & Outlink tracking', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_link_tracking_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

add_settings_field(
	'seopress_google_analytics_matomo_no_heatmaps', // ID.
	__( 'Disable all heatmaps and session recordings', 'wp-seopress' ), // Title.
	'seopress_google_analytics_matomo_no_heatmaps_callback', // Callback.
	'seopress-settings-admin-google-analytics-matomo', // Page.
	'seopress_setting_section_google_analytics_matomo' // Section.
);

// Microsoft Clarity SECTION.
add_settings_section(
	'seopress_setting_section_google_analytics_clarity', // ID.
	'',
	// __( "Microsoft Clarity", "wp-seopress" ), // Title.
	'seopress_print_section_info_google_analytics_clarity', // Callback.
	'seopress-settings-admin-google-analytics-clarity' // Page.
);

add_settings_field(
	'seopress_google_analytics_clarity_enable', // ID.
	__( 'Enable Microsoft Clarity', 'wp-seopress' ), // Title.
	'seopress_google_analytics_clarity_enable_callback', // Callback.
	'seopress-settings-admin-google-analytics-clarity', // Page.
	'seopress_setting_section_google_analytics_clarity' // Section.
);

add_settings_field(
	'seopress_google_analytics_clarity_project_id', // ID.
	__( 'Enter your Clarity project ID', 'wp-seopress' ), // Title.
	'seopress_google_analytics_clarity_project_id_callback', // Callback.
	'seopress-settings-admin-google-analytics-clarity', // Page.
	'seopress_setting_section_google_analytics_clarity' // Section.
);

<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Dashboard toggle features
 */
function seopress_toggle_features()
{
    check_ajax_referer('seopress_toggle_features_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['feature']) && isset($_POST['feature_value'])) {
            $feature = esc_attr($_POST['feature']);
            $feature_value = esc_attr($_POST['feature_value']);

            if ($feature === 'toggle-universal-metabox') {
                $seopress_advanced_option_name = get_option('seopress_advanced_option_name');
                if ($_POST['feature_value'] === '1') {
                    $seopress_advanced_option_name['seopress_advanced_appearance_universal_metabox_disable'] = '0';
                } else {
                    $seopress_advanced_option_name['seopress_advanced_appearance_universal_metabox_disable'] = '1';
                }
                update_option('seopress_advanced_option_name', $seopress_advanced_option_name, false);
            } else {
                $seopress_toggle_options = get_option('seopress_toggle');
                $seopress_toggle_options[$feature] = $feature_value;

                //Flush permalinks for XML sitemaps
                if ($feature_value === 'toggle-xml-sitemap') {
                    flush_rewrite_rules(false);
                }

                update_option('seopress_toggle', $seopress_toggle_options, 'yes', false);
            }
        }
        exit();
    }
}
add_action('wp_ajax_seopress_toggle_features', 'seopress_toggle_features');

/**
 * Dashboard Display Panel
 */
function seopress_display()
{
    check_ajax_referer('seopress_display_nonce', '_ajax_nonce', true);
    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        //Notifications Center
        if (isset($_POST['notifications_center'])) {
            $seopress_advanced_option_name                    = get_option('seopress_advanced_option_name');

            if ('1' == $_POST['notifications_center']) {
                $seopress_advanced_option_name['seopress_advanced_appearance_notifications'] = esc_attr($_POST['notifications_center']);
            } else {
                unset($seopress_advanced_option_name['seopress_advanced_appearance_notifications']);
            }

            update_option('seopress_advanced_option_name', $seopress_advanced_option_name, false);
        }
        //News Panel
        if (isset($_POST['news_center'])) {
            $seopress_advanced_option_name                    = get_option('seopress_advanced_option_name');

            if ('1' == $_POST['news_center']) {
                $seopress_advanced_option_name['seopress_advanced_appearance_news'] = esc_attr($_POST['news_center']);
            } else {
                unset($seopress_advanced_option_name['seopress_advanced_appearance_news']);
            }

            update_option('seopress_advanced_option_name', $seopress_advanced_option_name, false);
        }
        //Tools Panel
        if (isset($_POST['tools_center'])) {
            $seopress_advanced_option_name                    = get_option('seopress_advanced_option_name');

            if ('1' == $_POST['tools_center']) {
                $seopress_advanced_option_name['seopress_advanced_appearance_seo_tools'] = esc_attr($_POST['tools_center']);
            } else {
                unset($seopress_advanced_option_name['seopress_advanced_appearance_seo_tools']);
            }

            update_option('seopress_advanced_option_name', $seopress_advanced_option_name, false);
        }
        exit();
    }
}
add_action('wp_ajax_seopress_display', 'seopress_display');

/**
 * Dashboard hide notices
 */
function seopress_hide_notices()
{
    check_ajax_referer('seopress_hide_notices_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['notice']) && isset($_POST['notice_value'])) {
            $seopress_notices_options = get_option('seopress_notices', []);

            $notice = esc_html($_POST['notice']);
            $notice_value = esc_html($_POST['notice_value']);

            if ($notice !== false && $notice_value !== false) {
                $seopress_notices_options[$notice] = $notice_value;
            }
            update_option('seopress_notices', $seopress_notices_options, 'yes', false);
        }
        exit();
    }
}
add_action('wp_ajax_seopress_hide_notices', 'seopress_hide_notices');

/**
 * Dashboard switch view
 */
function seopress_switch_view()
{
    check_ajax_referer('seopress_switch_view_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['view'])) {
            $seopress_dashboard_options = get_option('seopress_dashboard', []);

            $view = esc_html($_POST['view']);

            if ($view !== false) {
                $seopress_dashboard_options['view'] = $view;
            }
            update_option('seopress_dashboard', $seopress_dashboard_options, false);
        }
        exit();
    }
}
add_action('wp_ajax_seopress_switch_view', 'seopress_switch_view');
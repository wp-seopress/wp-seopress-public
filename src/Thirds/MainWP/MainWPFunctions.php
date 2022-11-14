<?php
if ( ! defined('ABSPATH')) {
    exit;
}

if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && defined('SEOPRESS_PRO_VERSION') && version_compare(SEOPRESS_PRO_VERSION, '5.4', '<')) { //Quick fix to prevent fatal error for SEOPress < 5.4
    //do nothing
} else {
    /**
     * Return settings
     *
     * @return array
     */
    function seopress_return_settings() {
        $settings = array();

        $settings['seopress_activated']                             = get_option('seopress_activated');
        $settings['seopress_titles_option_name']                    = get_option('seopress_titles_option_name');
        $settings['seopress_social_option_name']                    = get_option('seopress_social_option_name');
        $settings['seopress_google_analytics_option_name']          = get_option('seopress_google_analytics_option_name');
        $settings['seopress_instant_indexing_option_name']          = get_option('seopress_instant_indexing_option_name');
        $settings['seopress_advanced_option_name']                  = get_option('seopress_advanced_option_name');
        $settings['seopress_xml_sitemap_option_name']               = get_option('seopress_xml_sitemap_option_name');
        $settings['seopress_pro_option_name']                       = get_option('seopress_pro_option_name');
        $settings['seopress_pro_mu_option_name']                    = get_option('seopress_pro_mu_option_name');
        $settings['seopress_pro_license_key']                       = get_option('seopress_pro_license_key');
        $settings['seopress_pro_license_status']                    = get_option('seopress_pro_license_status');
        $settings['seopress_bot_option_name']                       = get_option('seopress_bot_option_name');
        $settings['seopress_toggle']                                = get_option('seopress_toggle');
        $settings['seopress_google_analytics_lock_option_name']     = get_option('seopress_google_analytics_lock_option_name');
        $settings['seopress_tools_option_name']                     = get_option('seopress_tools_option_name');
        $settings['seopress_dashboard_option_name']                 = get_option('seopress_dashboard_option_name');

        return $settings;
    }

    /**
     * Seopress do import settings
     *
     * @param  array $settings
     *
     * @return void
     */
    function seopress_do_import_settings( $settings ) {
        if (false !== $settings['seopress_activated']) {
            update_option('seopress_activated', $settings['seopress_activated'], false);
        }
        if (false !== $settings['seopress_titles_option_name']) {
            update_option('seopress_titles_option_name', $settings['seopress_titles_option_name'], false);
        }
        if (false !== $settings['seopress_social_option_name']) {
            update_option('seopress_social_option_name', $settings['seopress_social_option_name'], false);
        }
        if (false !== $settings['seopress_google_analytics_option_name']) {
            update_option('seopress_google_analytics_option_name', $settings['seopress_google_analytics_option_name'], false);
        }
        if (false !== $settings['seopress_advanced_option_name']) {
            update_option('seopress_advanced_option_name', $settings['seopress_advanced_option_name'], false);
        }
        if (false !== $settings['seopress_xml_sitemap_option_name']) {
            update_option('seopress_xml_sitemap_option_name', $settings['seopress_xml_sitemap_option_name'], false);
        }
        if (false !== $settings['seopress_pro_option_name']) {
            update_option('seopress_pro_option_name', $settings['seopress_pro_option_name'], false);
        }
        if (false !== $settings['seopress_pro_mu_option_name']) {
            update_option('seopress_pro_mu_option_name', $settings['seopress_pro_mu_option_name'], false);
        }
        if (false !== $settings['seopress_pro_license_key']) {
            update_option('seopress_pro_license_key', $settings['seopress_pro_license_key'], false);
        }
        if (false !== $settings['seopress_pro_license_status']) {
            update_option('seopress_pro_license_status', $settings['seopress_pro_license_status'], false);
        }
        if (false !== $settings['seopress_bot_option_name']) {
            update_option('seopress_bot_option_name', $settings['seopress_bot_option_name'], false);
        }
        if (false !== $settings['seopress_toggle']) {
            update_option('seopress_toggle', $settings['seopress_toggle'], false);
        }
        if (false !== $settings['seopress_google_analytics_lock_option_name']) {
            update_option('seopress_google_analytics_lock_option_name', $settings['seopress_google_analytics_lock_option_name'], false);
        }
        if (false !== $settings['seopress_tools_option_name']) {
            update_option('seopress_tools_option_name', $settings['seopress_tools_option_name'], false);
        }
        if (false !== $settings['seopress_instant_indexing_option_name']) {
            update_option('seopress_instant_indexing_option_name', $settings['seopress_instant_indexing_option_name'], false);
        }
    }

    /**
     * Save settings for given option
     *
     * @param  array  $settings The settings to be saved.
     * @param  string $option The option name.
     *
     * @return void
     */
    function seopress_mainwp_save_settings( $settings, $option ) {
        update_option( $option, $settings );
    }

    /**
     * Flush rewrite rules.
     *
     * @return void
     */
    function seopress_flush_rewrite_rules() {
        flush_rewrite_rules( false );
    }
}

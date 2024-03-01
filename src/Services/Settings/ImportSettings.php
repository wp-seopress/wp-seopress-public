<?php

namespace SEOPress\Services\Settings;


class ImportSettings {
    public function handle($data = []){
        if (isset($data['seopress_activated']) &&  false !== $data['seopress_activated']) {
            update_option('seopress_activated', $data['seopress_activated'], false);
        }

        if (isset($data['seopress_titles_option_name']) &&  false !== $data['seopress_titles_option_name']) {
            update_option('seopress_titles_option_name', $data['seopress_titles_option_name'], false);
        }

        if (isset($data['seopress_social_option_name']) &&  false !== $data['seopress_social_option_name']) {
            update_option('seopress_social_option_name', $data['seopress_social_option_name'], false);
        }

        if (isset($data['seopress_google_analytics_option_name']) &&  false !== $data['seopress_google_analytics_option_name']) {
            update_option('seopress_google_analytics_option_name', $data['seopress_google_analytics_option_name'], false);
        }

        if (isset($data['seopress_advanced_option_name']) &&  false !== $data['seopress_advanced_option_name']) {
            update_option('seopress_advanced_option_name', $data['seopress_advanced_option_name'], false);
        }

        if (isset($data['seopress_xml_sitemap_option_name']) &&  false !== $data['seopress_xml_sitemap_option_name']) {
            update_option('seopress_xml_sitemap_option_name', $data['seopress_xml_sitemap_option_name'], false);
        }

        if (isset($data['seopress_pro_option_name']) &&  false !== $data['seopress_pro_option_name']) {
            update_option('seopress_pro_option_name', $data['seopress_pro_option_name'], false);
        }

        if (isset($data['seopress_pro_mu_option_name']) &&  false !== $data['seopress_pro_mu_option_name']) {
            update_option('seopress_pro_mu_option_name', $data['seopress_pro_mu_option_name'], false);
        }

        if (isset($data['seopress_pro_license_key']) &&  false !== $data['seopress_pro_license_key']) {
            update_option('seopress_pro_license_key', $data['seopress_pro_license_key'], false);
        }

        if (isset($data['seopress_pro_license_status']) &&  false !== $data['seopress_pro_license_status']) {
            update_option('seopress_pro_license_status', $data['seopress_pro_license_status'], false);
        }

        if (isset($data['seopress_bot_option_name']) &&  false !== $data['seopress_bot_option_name']) {
            update_option('seopress_bot_option_name', $data['seopress_bot_option_name'], false);
        }

        if (isset($data['seopress_toggle']) &&  false !== $data['seopress_toggle']) {
            update_option('seopress_toggle', $data['seopress_toggle'], false);
        }

        if (isset($data['seopress_google_analytics_lock_option_name']) &&  false !== $data['seopress_google_analytics_lock_option_name']) {
            update_option('seopress_google_analytics_lock_option_name', $data['seopress_google_analytics_lock_option_name'], false);
        }

        if (isset($data['seopress_tools_option_name']) &&  false !== $data['seopress_tools_option_name']) {
            update_option('seopress_tools_option_name', $data['seopress_tools_option_name'], false);
        }

        if (isset($data['seopress_instant_indexing_option_name']) &&  false !== $data['seopress_instant_indexing_option_name']) {
            update_option('seopress_instant_indexing_option_name', $data['seopress_instant_indexing_option_name'], false);
        }
    }

}

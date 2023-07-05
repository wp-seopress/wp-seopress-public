<?php

namespace SEOPress\Services\Settings;


class ExportSettings {
    public function handle(){
        $data = [];
        $data['seopress_activated']                             = get_option('seopress_activated');
        $data['seopress_titles_option_name']                    = get_option('seopress_titles_option_name');
        $data['seopress_social_option_name']                    = get_option('seopress_social_option_name');
        $data['seopress_google_analytics_option_name']          = get_option('seopress_google_analytics_option_name');
        $data['seopress_advanced_option_name']                  = get_option('seopress_advanced_option_name');
        $data['seopress_xml_sitemap_option_name']               = get_option('seopress_xml_sitemap_option_name');
        $data['seopress_pro_option_name']                       = get_option('seopress_pro_option_name');
        $data['seopress_pro_mu_option_name']                    = get_option('seopress_pro_mu_option_name');
        $data['seopress_pro_license_key']                       = get_option('seopress_pro_license_key');
        $data['seopress_pro_license_status']                    = get_option('seopress_pro_license_status');
        $data['seopress_bot_option_name']                       = get_option('seopress_bot_option_name');
        $data['seopress_toggle']                                = get_option('seopress_toggle');
        $data['seopress_google_analytics_lock_option_name']     = get_option('seopress_google_analytics_lock_option_name');
        $data['seopress_tools_option_name']                     = get_option('seopress_tools_option_name');
        $data['seopress_dashboard_option_name']                 = get_option('seopress_dashboard_option_name');
        $data['seopress_instant_indexing_option_name']          = get_option('seopress_instant_indexing_option_name');

         return $data;
    }
}

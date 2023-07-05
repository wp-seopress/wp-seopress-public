<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_sanitize_options_fields($input){

    $seopress_sanitize_fields = [
        'seopress_social_facebook_img_attachment_id',
        'seopress_social_facebook_img_attachment_width',
        'seopress_social_facebook_img_attachment_height',
        'seopress_titles_home_site_title',
        'seopress_titles_home_site_title_alt',
        'seopress_titles_home_site_desc',
        'seopress_titles_archives_author_title',
        'seopress_titles_archives_author_desc',
        'seopress_titles_archives_date_title',
        'seopress_titles_archives_date_desc',
        'seopress_titles_archives_search_title',
        'seopress_titles_archives_search_desc',
        'seopress_titles_archives_404_title',
        'seopress_titles_archives_404_desc',
        'seopress_xml_sitemap_html_exclude',
        'seopress_social_knowledge_name',
        'seopress_social_knowledge_img',
        'seopress_social_knowledge_phone',
        'seopress_social_accounts_facebook',
        'seopress_social_accounts_twitter',
        'seopress_social_accounts_pinterest',
        'seopress_social_accounts_instagram',
        'seopress_social_accounts_youtube',
        'seopress_social_accounts_linkedin',
        'seopress_social_accounts_extra',
        'seopress_social_facebook_link_ownership_id',
        'seopress_social_facebook_admin_id',
        'seopress_social_facebook_app_id',
        'seopress_google_analytics_ga4',
        'seopress_google_analytics_download_tracking',
        'seopress_google_analytics_opt_out_msg',
        'seopress_google_analytics_opt_out_msg_ok',
        'seopress_google_analytics_opt_out_msg_close',
        'seopress_google_analytics_opt_out_msg_edit',
        'seopress_google_analytics_other_tracking',
        'seopress_google_analytics_other_tracking_body',
        'seopress_google_analytics_optimize',
        'seopress_google_analytics_ads',
        'seopress_google_analytics_cross_domain',
        'seopress_google_analytics_matomo_id',
        'seopress_google_analytics_matomo_site_id',
        'seopress_google_analytics_matomo_cross_domain_sites',
        'seopress_google_analytics_cb_backdrop_bg',
        'seopress_google_analytics_cb_exp_date',
        'seopress_google_analytics_cb_bg',
        'seopress_google_analytics_cb_txt_col',
        'seopress_google_analytics_cb_lk_col',
        'seopress_google_analytics_cb_btn_bg',
        'seopress_google_analytics_cb_btn_col',
        'seopress_google_analytics_cb_btn_bg_hov',
        'seopress_google_analytics_cb_btn_col_hov',
        'seopress_google_analytics_cb_btn_sec_bg',
        'seopress_google_analytics_cb_btn_sec_col',
        'seopress_google_analytics_cb_btn_sec_bg_hov',
        'seopress_google_analytics_cb_btn_sec_col_hov',
        'seopress_google_analytics_cb_width',
        'seopress_instant_indexing_bing_api_key',
        'seopress_instant_indexing_manual_batch',
        'seopress_google_analytics_clarity_project_id',
        'seopress_google_analytics_matomo_widget_auth_token',
        //'seopress_instant_indexing_google_api_key',
    ];

    $seopress_esc_attr = [
        'seopress_titles_sep',
    ];

    $seopress_sanitize_site_verification = [
        'seopress_advanced_advanced_google',
        'seopress_advanced_advanced_bing',
        'seopress_advanced_advanced_pinterest',
        'seopress_advanced_advanced_yandex',
    ];

    $newOptions = ['seopress_social_facebook_img_attachment_id', 'seopress_social_facebook_img_height', 'seopress_social_facebook_img_width'];

    foreach ($newOptions as $key => $value) {
        if(!isset($input[$value]) && isset($_POST[$value])){
            $input[$value] = $_POST[$value];
        }
    }

    foreach ($seopress_sanitize_fields as $value) {
        if ( ! empty($input['seopress_google_analytics_matomo_widget_auth_token']) && 'seopress_google_analytics_matomo_widget_auth_token' == $value) {
            $options = get_option('seopress_google_analytics_option_name');

            $token = isset($options['seopress_google_analytics_matomo_widget_auth_token']) ? $options['seopress_google_analytics_matomo_widget_auth_token'] : null;

            $input[$value] = $input[$value] ==='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' ? $token : sanitize_text_field($input[$value]);
        } elseif ( ! empty($input['seopress_google_analytics_opt_out_msg']) && 'seopress_google_analytics_opt_out_msg' == $value) {
            $args = [
                    'strong' => [],
                    'em'     => [],
                    'br'     => [],
                    'a'      => [
                        'href'   => [],
                        'target' => [],
                    ],
            ];
            $input[$value] = wp_kses($input[$value], $args);
        } elseif (( ! empty($input['seopress_google_analytics_other_tracking']) && 'seopress_google_analytics_other_tracking' == $value) || ( ! empty($input['seopress_google_analytics_other_tracking_body']) && 'seopress_google_analytics_other_tracking_body' == $value) || ( ! empty($input['seopress_google_analytics_other_tracking_footer']) && 'seopress_google_analytics_other_tracking_footer' == $value)) {
            $input[$value] = $input[$value]; //No sanitization for this field
        } elseif (( ! empty($input['seopress_instant_indexing_manual_batch']) && 'seopress_instant_indexing_manual_batch' == $value) || (!empty($input['seopress_social_accounts_extra']) && 'seopress_social_accounts_extra' == $value )) {
            $input[$value] = sanitize_textarea_field($input[$value]);
        } elseif ( ! empty($input[$value])) {
            $input[$value] = sanitize_text_field($input[$value]);
        }
    }

    foreach ($seopress_esc_attr as $value) {
        if ( ! empty($input[$value])) {
            $input[$value] = esc_attr($input[$value]);
        }
    }

    foreach ($seopress_sanitize_site_verification as $value) {
        if ( ! empty($input[$value])) {
            if (preg_match('#content=\'([^"]+)\'#', $input[$value], $m)) {
                $input[$value] = esc_attr($m[1]);
            } elseif (preg_match('#content="([^"]+)"#', $input[$value], $m)) {
                $input[$value] = esc_attr($m[1]);
            } else {
                $input[$value] = esc_attr($input[$value]);
            }
        }
    }

    return $input;

}


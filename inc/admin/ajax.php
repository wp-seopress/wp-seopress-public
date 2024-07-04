<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview()
{
    check_ajax_referer('seopress_real_preview_nonce', '_ajax_nonce', true);

    if (!current_user_can('edit_posts') || !is_admin()) {
        return;
    }

    if (!isset($_GET['post_id'])) {
        return;
    }

    $id = $_GET['post_id'];
    $taxname = isset($_GET['tax_name']) ? $_GET['tax_name'] : null;


    if ('yes' == get_post_meta($id, '_seopress_redirections_enabled', true)) {
        $data['title'] = __('A redirect is active for this URL. Turn it off to get the Google preview and content analysis.', 'wp-seopress');
        wp_send_json_error($data);
        return;
    }

    $linkPreview   = seopress_get_service('RequestPreview')->getLinkRequest($id, $taxname);

    $domResult  = seopress_get_service('RequestPreview')->getDomById($id, $taxname);

    if(!$domResult['success']){
        $defaultResponse = [
            'title' =>  '...',
            'meta_desc' =>  '...',
        ];

        switch($domResult['code']){
            case 404:
                $defaultResponse['title'] = __('To get your Google snippet preview, publish your post!', 'wp-seopress');
                break;
            case 401:
                $defaultResponse['title'] = __('Your site is protected by an authentication.', 'wp-seopress');
                break;
        }

        wp_send_json_success($defaultResponse);
        return;
    }

    $str = $domResult['body'];

    $data = seopress_get_service('DomFilterContent')->getData($str, $id);
    $data = seopress_get_service('DomAnalysis')->getDataAnalyze($data, [
        "id" => $id,
    ]);

    $post = get_post($id);
    $score = seopress_get_service('DomAnalysis')->getScore($post);
    $data['score'] = $score;
    $keywords = seopress_get_service('DomAnalysis')->getKeywords([
        'id' => $id,
    ]);
    seopress_get_service('ContentAnalysisDatabase')->saveData($id, $data, $keywords);


    /**
     * We delete old values because we have a new structure
     *
     * @deprecated
     * @since 7.3.0
     */
    delete_post_meta($id, '_seopress_content_analysis_api');
    delete_post_meta($id, '_seopress_analysis_data');

    //Re-enable QM
    remove_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

    wp_send_json_success($data);

}
add_action('wp_ajax_seopress_do_real_preview', 'seopress_do_real_preview');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard toggle features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_toggle_features()
{
    check_ajax_referer('seopress_toggle_features_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['feature']) && isset($_POST['feature_value'])) {
            $seopress_toggle_options = get_option('seopress_toggle');
            $feature = esc_attr($_POST['feature']);
            $feature_value = esc_attr($_POST['feature_value']);

            $seopress_toggle_options[$feature] = $feature_value;

            //Flush permalinks for XML sitemaps
            if ($feature_value === 'toggle-xml-sitemap') {
                flush_rewrite_rules(false);
            }
            update_option('seopress_toggle', $seopress_toggle_options, 'yes', false);
        }
        exit();
    }
}
add_action('wp_ajax_seopress_toggle_features', 'seopress_toggle_features');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard Display Panel
///////////////////////////////////////////////////////////////////////////////////////////////////
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

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard hide notices
///////////////////////////////////////////////////////////////////////////////////////////////////
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

require_once __DIR__ . '/ajax-migrate/smart-crawl.php';
require_once __DIR__ . '/ajax-migrate/seopressor.php';
require_once __DIR__ . '/ajax-migrate/slim-seo.php';
require_once __DIR__ . '/ajax-migrate/platinum.php';
require_once __DIR__ . '/ajax-migrate/wpseo.php';
require_once __DIR__ . '/ajax-migrate/premium-seo-pack.php';
require_once __DIR__ . '/ajax-migrate/wp-meta-seo.php';
require_once __DIR__ . '/ajax-migrate/seo-ultimate.php';
require_once __DIR__ . '/ajax-migrate/squirrly.php';
require_once __DIR__ . '/ajax-migrate/seo-framework.php';
require_once __DIR__ . '/ajax-migrate/yoast.php';

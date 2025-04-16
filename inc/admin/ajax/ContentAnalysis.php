<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Content analysis real preview
 */
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


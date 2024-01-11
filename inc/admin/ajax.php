<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview()
{
    $docs = seopress_get_docs_links();

    check_ajax_referer('seopress_real_preview_nonce', $_GET['_ajax_nonce'], true);

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

    $str  = seopress_get_service('RequestPreview')->getDomById($id, $taxname);
    $data = seopress_get_service('DomFilterContent')->getData($str, $id);
    $data = seopress_get_service('DomAnalysis')->getDataAnalyze($data, [
        "id" => $id,
    ]);


    $targetKws = '';
    $targetKwsCount = [];
    //Get Target Keywords
    if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
        $targetKws          = esc_html(strtolower(stripslashes_deep($_GET['seopress_analysis_target_kw'])));
        $target = array_filter(explode(',', strtolower(get_post_meta($id, '_seopress_analysis_target_kw', true))));
        $target = apply_filters( 'seopress_content_analysis_target_keywords', $target, $id );

        $targetKwsCount = seopress_get_service('CountTargetKeywordsUse')->getCountByKeywords($target, $id);
    }


    /**
     * Rather than maintaining the transition of information.
     * We need to change the `GetContent` file to fit the new structure.
     * This is temporary until the update is complete.
     */

    // Morphism: Legacy Meta Title
    $metaTitleDensity = [];
    foreach($data['title']['matches'] as $key => $value){
        $fakeTable = []; // For match compatibiltiy with GetContent:289 (count)
        for ($i=0; $i < $value['count'] ; $i++) {
            $fakeTable[] = 1;
        }
        $metaTitleDensity[$value['key']] = [$fakeTable];
    }

    // Morphism: Legacy Meta Description
    $metaDescription = [];
    foreach($data['description']['matches'] as $key => $value){
        $fakeTable = []; // For match compatibiltiy with GetContent:275 (count)
        for ($i=0; $i < $value['count'] ; $i++) {
            $fakeTable[] = 1;
        }
        $metaDescription[$value['key']] = [$fakeTable];
    }

    $internalLinks = [
        'count' => 0,
        'links' => []
    ];
    // Morphism: Legacy Internal Links
    if(!empty($data['internal_links']['value'])){
        $internalLinks['count'] = count($data['internal_links']['value']);
        foreach($data['internal_links']['value'] as $key => $value){
            $internalLinks['links'][$value['id']] = [
               $value['url'] => $value['value']
            ];
        }
    }

    // Morphism: Legacy H1 Matches
    $headingH1Matches = [];
    if(!empty($data['h1']['matches'])){
        foreach ($data['h1']['matches'] as $key => $value) {
            $fakeTable = []; // For match compatibiltiy with GetContent:289 (count)
            for ($i=0; $i < $value['count'] ; $i++) {
                $fakeTable[] = 1;
            }

            $headingH1Matches[$value['key']] = [$fakeTable];
        }
    }

    // Morphism: Legacy H2 Matches
    $headingH2Matches = [];
    if(!empty($data['h2']['matches'])){
        foreach ($data['h2']['matches'] as $key => $value) {
            $fakeTable = []; // For match compatibiltiy with GetContent:289 (count)
            for ($i=0; $i < $value['count'] ; $i++) {
                $fakeTable[] = 1;
            }
            $headingH2Matches[$value['key']] = [$fakeTable];
        }
    }

    // Morphism: Legacy H3 Matches
    $headingH3Matches = [];
    if(!empty($data['h3']['matches'])){
        foreach ($data['h3']['matches'] as $key => $value) {
            $fakeTable = []; // For match compatibiltiy with GetContent:289 (count)
            for ($i=0; $i < $value['count'] ; $i++) {
                $fakeTable[] = 1;
            }
            $headingH3Matches[$value['key']] = [$fakeTable];
        }
    }

    // Morphism: Legacy images
    $images = [
        'images' => [
            'without_alt' => [],
            'with_alt' => [],
        ]
    ];
    if(!empty($data['images']['value'])){
        foreach ($data['images']['value'] as $key => $value) {
            if(empty($value['alt'])) {
                $images['images']['without_alt'][] = $value['src'];
            }
            else{
                $images['images']['with_alt'][] = $value['src'];
            }
        }
    }


    // Morphism: keywords permalink
    $kwPermalinks = ['matches' => []];
    if(!empty($data['kws_permalink']) && !empty($data['kws_permalink']['matches'])){
        foreach ($data['kws_permalink']['matches'] as $key => $value) {
            $kwPermalinks['matches'][$value['key']] = $value['count'];
        }
    }

    // Morphism: links no follow
    $linksNoFollow = [];
    if(!empty($data['links_no_follow']['value'])){
        foreach ($data['links_no_follow']['value'] as $key => $value) {
            $linksNoFollow[] = [$value['url'] => $value['value']];
        }
    }


    $outboundLinks = [];
    if(!empty($data['outbound_links']['value'])){
        foreach ($data['outbound_links']['value'] as $key => $value) {
            $outboundLinks[] = [$value['url'] => $value['value']];
        }
    }


    // Need to transform this to keep compatibility with older version
    $dataResponse = [
        'title' =>  $data['title']['value'],
        'meta_desc' =>  isset($data['description']['value']) ? $data['description']['value'] : '',
        'link_preview' => $linkPreview,
        'analyzed_content' => isset($data['analyzed_content']) ? $data['analyzed_content'] : '',
        'target_kws' => $targetKws,
        'target_kws_count' => $targetKwsCount,
        'meta_title' => [
            'matches' => $metaTitleDensity,
        ],
        'img' => $images,
        'meta_description' => [
            'matches' => $metaDescription
        ],
        'og_title' => [
            'count' => !empty($data['og:title']['value']) ? count($data['og:title']['value']) : '',
            'values'=> $data['og:title']['value']
        ],
        'og_desc' => [
            'count' => !empty($data['og:description']['value']) ? count($data['og:description']['value']) : '',
            'values'=> $data['og:description']['value']
        ],
        'og_img' => [
            'count' => !empty($data['og:image']['value']) ? count($data['og:image']['value']) : '',
            'values'=> $data['og:image']['value']
        ],
        'og_url' => [
            'count' => !empty($data['og:url']['value']) ? count($data['og:url']['value']) : '',
            'values'=> $data['og:url']['value']
        ],
        'og_site_name' => [
            'count' => !empty($data['og:site_name']['value']) ? count($data['og:site_name']['value']) : '',
            'values'=> $data['og:site_name']['value']
        ],
        'tw_title' => [
            'count' => !empty($data['twitter:title']['value']) ? count($data['twitter:title']['value']) : '',
            'values'=> $data['twitter:title']['value']
        ],
        'tw_desc' => [
            'count' => !empty($data['twitter:description']['value']) ? count($data['twitter:description']['value']) : '',
            'values'=> $data['twitter:description']['value']
        ],
        'tw_img' => [
            'count' => !empty($data['twitter:image']['value']) ? count($data['twitter:image']['value']) : '',
            'values'=> $data['twitter:image']['value']
        ],
        'canonical' => isset($data['canonical']['value'][0]) ? $data['canonical']['value'][0] : '',
        'all_canonical' => $data['canonical']['value'],
            'h1' => [
                'nomatches' => ['count' => !empty($data['h1']['value']) ? count($data['h1']['value']) : ''
            ],
            'values'=> $data['h1']['value'],
            'matches' => $headingH1Matches
        ],
        'h2' => [
            'nomatches' => ['count' => !empty($data['h2']['value']) ? count($data['h2']['value']) : ''],
            'values'=> $data['h2']['value'],
            'matches' => $headingH2Matches
        ],
        'h3' => [
            'nomatches' => ['count' => !empty($data['h3']['value']) ? count($data['h3']['value']) : ''],
            'values'=> $data['h3']['value'],
            'matches' => $headingH3Matches
        ],
        'meta_robots' => $data['meta_robots']['value'],
        'nofollow_links' => $linksNoFollow,
        'outbound_links' => $outboundLinks,
        'internal_links' => $internalLinks,
        'kws_permalink' => $kwPermalinks,
        'json' => $data['schemas']['value']
    ];

    $dataResponse['link_preview'] = $linkPreview;

    update_post_meta($id, '_seopress_analysis_data', $dataResponse);
    delete_post_meta($id, '_seopress_content_analysis_api');

    //Re-enable QM
    remove_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

    wp_send_json_success($dataResponse);

}
add_action('wp_ajax_seopress_do_real_preview', 'seopress_do_real_preview');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard toggle features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_toggle_features()
{
    check_ajax_referer('seopress_toggle_features_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['feature']) && isset($_POST['feature_value'])) {
            $seopress_toggle_options                    = get_option('seopress_toggle');
            $seopress_toggle_options[$_POST['feature']] = esc_attr($_POST['feature_value']);
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
    check_ajax_referer('seopress_display_nonce', $_POST['_ajax_nonce'], true);
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
    check_ajax_referer('seopress_hide_notices_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['notice']) && isset($_POST['notice_value'])) {
            $seopress_notices_options                   = get_option('seopress_notices');
            $seopress_notices_options[$_POST['notice']] = $_POST['notice_value'];
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
require_once __DIR__ . '/export/csv.php';

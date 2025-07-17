<?php

namespace SEOPress\Actions\Admin\Importer;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\RankMath\Tags;

class RankMath implements ExecuteHooksBackend {

    protected $tagsRankMath;

    public function __construct() {
        $this->tagsRankMath = new Tags();
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_ajax_seopress_rk_migration', [$this, 'process']);
    }

    /**
     * @since 4.3.0
     *
     * @return string
     */
    protected function migrateTermQuery() {
        wp_reset_postdata();

        $args = [
            'hide_empty' => false,
            'fields'     => 'ids',
        ];
        $rk_query_terms = get_terms($args);

        $getTermMetas = [
            '_seopress_titles_title'             => 'rank_math_title',
            '_seopress_titles_desc'              => 'rank_math_description',
            '_seopress_social_fb_title'          => 'rank_math_facebook_title',
            '_seopress_social_fb_desc'           => 'rank_math_facebook_description',
            '_seopress_social_fb_img'            => 'rank_math_facebook_image',
            '_seopress_social_twitter_title'     => 'rank_math_twitter_title',
            '_seopress_social_twitter_desc'      => 'rank_math_twitter_description',
            '_seopress_social_twitter_img'       => 'rank_math_twitter_image',
            '_seopress_robots_canonical'         => 'rank_math_canonical_url',
            '_seopress_analysis_target_kw'       => 'rank_math_focus_keyword',
        ];
        if ( ! $rk_query_terms) {
            wp_reset_postdata();

            return 'done';
        }

        foreach ($rk_query_terms as $term_id) {
            foreach ($getTermMetas as $key => $value) {
                $metaRankMath = get_term_meta($term_id, $value, true);
                if ( ! empty($metaRankMath)) {
                    update_term_meta($term_id, $key, $this->tagsRankMath->replaceTags($metaRankMath));
                }
            }

            if ('' != get_term_meta($term_id, 'rank_math_robots', true)) { //Import Robots NoIndex, NoFollow, NoImageIndex, NoSnippet
                $rank_math_robots = get_term_meta($term_id, 'rank_math_robots', true);

                if (in_array('noindex', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_index', 'yes');
                }
                if (in_array('nofollow', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                }
                if (in_array('noimageindex', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_imageindex', 'yes');
                }
                if (in_array('nosnippet', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_snippet', 'yes');
                }
            }
        }

        wp_reset_postdata();

        return 'done';
    }

    /**
     * @since 4.3.0
     *
     * @param int $offset
     * @param int $increment
     */
    protected function migratePostQuery($offset, $increment) {
        $args = [
            'posts_per_page' => $increment,
            'post_type'      => 'any',
            'post_status'    => 'any',
            'offset'         => $offset,
        ];

        $rk_query = get_posts($args);

        if ( ! $rk_query) {
            $offset += $increment;

            return $offset;
        }

        $getPostMetas = [
            '_seopress_titles_title'         => 'rank_math_title',
            '_seopress_titles_desc'          => 'rank_math_description',
            '_seopress_social_fb_title'      => 'rank_math_facebook_title',
            '_seopress_social_fb_desc'       => 'rank_math_facebook_description',
            '_seopress_social_fb_img'        => 'rank_math_facebook_image',
            '_seopress_social_twitter_title' => 'rank_math_twitter_title',
            '_seopress_social_twitter_desc'  => 'rank_math_twitter_description',
            '_seopress_social_twitter_img'   => 'rank_math_twitter_image',
            '_seopress_robots_canonical'     => 'rank_math_canonical_url',
            '_seopress_analysis_target_kw'   => 'rank_math_focus_keyword',
        ];

        foreach ($rk_query as $post) {
            foreach ($getPostMetas as $key => $value) {
                $metaRankMath = get_post_meta($post->ID, $value, true);
                if ( ! empty($metaRankMath)) {
                    update_post_meta($post->ID, $key, esc_html($this->tagsRankMath->replaceTags($metaRankMath)));
                }
            }

            if ('' != get_post_meta($post->ID, 'rank_math_robots', true)) { //Import Robots NoIndex, NoFollow, NoImageIndex, NoSnippet
                $rank_math_robots = get_post_meta($post->ID, 'rank_math_robots', true);

                if (is_array($rank_math_robots)) {
                    if (in_array('noindex', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if (in_array('nofollow', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if (in_array('noimageindex', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                    }
                    if (in_array('nosnippet', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                    }
                }
            }
        }

        $offset += $increment;

        return $offset;
    }

    /**
     * @since 8.0.0
     *
     * @return void
     */
    protected function migrateSettings() {
        $seopress_titles  = get_option( 'seopress_titles_option_name' );
        $seopress_social = get_option('seopress_social_option_name');
        $seopress_sitemap = get_option('seopress_xml_sitemap_option_name');
        $seopress_advanced = get_option('seopress_advanced_option_name');
        $seopress_pro = get_option('seopress_pro_option_name');
        $seopress_instant_indexing = get_option('seopress_instant_indexing_option_name');

        $rank_math_general = get_option( 'rank-math-options-general' );
        $rank_math_titles = get_option( 'rank-math-options-titles' );
        $rank_math_sitemap = get_option( 'rank-math-options-sitemap' );
        $rank_math_instant_indexing = get_option( 'rank-math-options-instant-indexing' );

        if (!empty($rank_math_instant_indexing)) {
            foreach ($rank_math_instant_indexing as $key => $value) {
                if ($key === 'indexnow_api_key') {
                    $seopress_instant_indexing['seopress_instant_indexing_bing_api_key'] = esc_html($value);
                }
            }
        }
        
        if (!empty($rank_math_general)) {
            foreach ($rank_math_general as $key => $value) {
                // Redirects 404 to
                if ($key === 'redirections_post_redirect') {
                    $type = [
                        'default' => 'none',
                        'home' => 'home',
                        'custom' => 'custom',
                    ];
                    $seopress_pro['seopress_404_redirect_home'] = esc_html($type[$value]);
                }
                // 404 custom URL
                if ($key === 'redirections_custom_url') {
                    $seopress_pro['seopress_404_redirect_custom_url'] = esc_url($value);
                }
                // Disable automatic redirects
                if ($key === 'redirections_post_redirect') {
                    if ($value === 'on') {
                        unset($seopress_pro['seopress_404_disable_automatic_redirects']);
                    } else {
                        $seopress_pro['seopress_404_disable_automatic_redirects'] = '1';
                    }
                }
                // Category URL
                if ($key === 'strip_category_base' && $value === 'on') {
                    $seopress_advanced['seopress_advanced_advanced_category_url'] = '1';
                } elseif ($key === 'strip_category_base' && $value === 'off') {
                    unset($seopress_advanced['seopress_advanced_advanced_category_url']);
                }
                // Remove WC Product Category Base
                if ($key === 'wc_remove_category_base' && $value === 'on') {
                    $seopress_advanced['seopress_advanced_advanced_product_cat_url'] = '1';
                } elseif ($key === 'wc_remove_category_base' && $value === 'off') {
                    unset($seopress_advanced['seopress_advanced_advanced_product_cat_url']);
                }
                // Remove WC generator tag
                if ($key === 'wc_remove_generator' && $value === 'on') {
                    $seopress_pro['seopress_woocommerce_meta_generator'] = '1';
                } elseif ($key === 'wc_remove_generator' && $value === 'off') {
                    unset($seopress_pro['seopress_woocommerce_meta_generator']);
                }
                // Remove WC structured data
                if ($key === 'remove_shop_snippet_data' && $value === 'on') {
                    $seopress_pro['seopress_woocommerce_schema_output'] = '1';
                } elseif ($key === 'remove_shop_snippet_data' && $value === 'off') {
                    unset($seopress_pro['seopress_woocommerce_schema_output']);
                }
                // Attachment Redirect URLs
                if ($key === 'attachment_redirect_urls' && $value === 'on') {
                    $seopress_advanced['seopress_advanced_advanced_attachments'] = '1';
                } elseif ($key === 'attachment_redirect_urls' && $value === 'off') {
                    unset($seopress_advanced['seopress_advanced_advanced_attachments']);
                }
                // Breadcrumbs
                if ($key === 'breadcrumbs' && $value === 'on') {
                    $seopress_pro['seopress_breadcrumbs_enable'] = '1';
                    $seopress_pro['seopress_breadcrumbs_json_enable'] = '1';
                } elseif ($key === 'breadcrumbs' && $value === 'off') {
                    unset($seopress_pro['seopress_breadcrumbs_enable']);
                    unset($seopress_pro['seopress_breadcrumbs_json_enable']);
                }
                // Breadcrumbs Separator
                if ($key === 'breadcrumbs_separator') {
                    $seopress_pro['seopress_breadcrumbs_separator'] = esc_html($value);
                }
                // Breadcrumbs Home
                if ($key === 'breadcrumbs_home_label') {
                    $seopress_pro['seopress_breadcrumbs_i18n_home'] = esc_html($value);
                }
                // Breadcrumbs Prefix
                if ($key === 'breadcrumbs_prefix') {
                    $seopress_pro['seopress_breadcrumbs_i18n_here'] = esc_html($value);
                }
                // Breadcrumbs Search Prefix
                if ($key === 'breadcrumbs_search_format') {
                    $seopress_pro['seopress_breadcrumbs_i18n_search'] = esc_html($value);
                }
                // Breadcrumbs 404 Crumbs
                if ($key === 'breadcrumbs_404_label') {
                    $seopress_pro['seopress_breadcrumbs_i18n_404'] = esc_html($value);
                }
                // Breadcrumbs Display Blog Page
                if ($key === 'breadcrumbs_blog_page') {
                    if ( $value === 'on' ) {
                        unset($seopress_pro['seopress_breadcrumbs_remove_blog_page']);
                    } elseif ( $value === 'off' ) {
                        $seopress_pro['seopress_breadcrumbs_remove_blog_page'] = '1';
                    }
                }
                // Google ownership
                if ( $key === 'google_verify' ) {
                    $seopress_advanced['seopress_advanced_advanced_google'] = esc_html($value);
                }
                // Bing ownership
                if ( $key === 'bing_verify' ) {
                    $seopress_advanced['seopress_advanced_advanced_bing'] = esc_html($value);
                }
                // Yandex ownership
                if ( $key === 'yandex_verify' ) {
                    $seopress_advanced['seopress_advanced_advanced_yandex'] = esc_html($value);
                }
                // Baidu ownership
                if ( $key === 'baidu_verify' ) {
                    $seopress_advanced['seopress_advanced_advanced_baidu'] = esc_html($value);
                }
                // Pinterest ownership
                if ( $key === 'pinterest_verify' ) {
                    $seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html($value);
                }
                // Custom Webmaster Tags
                if ( $key === 'custom_webmaster_tags' ) {
                    $accounts = array_filter(explode("\n", $value));
                    $accounts = implode("\n", array_map('esc_url', $accounts));
                    $seopress_social['seopress_social_accounts_extra'] = esc_html($accounts);
                }
                // RSS
                if ( $key === 'rss_before_content' || $key === 'rss_after_content' ) {
                    $rss_vars = [
                        '%%AUTHORLINK%%' => '<a href="%%author_permalink%%">%%post_author%%</a>',
                        '%%POSTLINK%%' => '<a href="%%post_permalink%%">%%post_title%%</a>',
                        '%%BLOGLINK%%' => '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a>',
                        '%%BLOGDESCLINK%%' => '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . ' ' . get_bloginfo('description') . '</a>',
                    ];
                    $value = str_replace(array_keys($rss_vars), array_values($rss_vars), $value);
                }
                if ( $key === 'rss_before_content' ) {
                    $args = [
                        'strong' => [],
                        'em' => [],
                        'br' => [],
                        'a' => ['href' => [], 'rel' => []],
                    ];
                    $seopress_pro['seopress_rss_before_html'] = wp_kses($value, $args);
                }
                if ( $key === 'rss_after_content' ) {
                    $args = [
                        'strong' => [],
                        'em' => [],
                        'br' => [],
                        'a' => ['href' => [], 'rel' => []],
                    ];
                    $seopress_pro['seopress_rss_after_html'] = wp_kses($value, $args);
                }
                // robots.txt file content
                if ( $key === 'robots_txt_content' ) {
                    if ( ! empty($value) ) {
                        $seopress_pro['seopress_robots_enable'] = '1';
                    }
                    $seopress_pro['seopress_robots_file'] = esc_html($value);
                }
            }
        }

        if (!empty($rank_math_titles)) {
            foreach ($rank_math_titles as $key => $value) {
                // Global meta robots
                if ($key === 'robots_global') {
                    if (in_array('noindex', $rank_math_titles['robots_global'])) {
                        $seopress_titles['seopress_titles_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_noindex']);
                    }
                    if (in_array('nofollow', $rank_math_titles['robots_global'])) {
                        $seopress_titles['seopress_titles_nofollow'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_nofollow']);
                    }
                    if (in_array('noimageindex', $rank_math_titles['robots_global'])) {
                        $seopress_titles['seopress_titles_noimageindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_noimageindex']);
                    }
                    if (in_array('nosnippet', $rank_math_titles['robots_global'])) {
                        $seopress_titles['seopress_titles_nosnippet'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_nosnippet']);
                    }
                } else {
                    unset($seopress_titles['seopress_titles_noindex']);
                    unset($seopress_titles['seopress_titles_nofollow']);
                    unset($seopress_titles['seopress_titles_noimageindex']);
                    unset($seopress_titles['seopress_titles_nosnippet']);
                }
                // Title separator
                if ($key === 'title_separator') {
                    $seopress_titles['seopress_titles_sep'] = esc_html($value);
                }
                // Open Graph image
                if ($key === 'open_graph_image' ) {
                    $seopress_social['seopress_social_knowledge_img'] = esc_url($value);
                }
                // Twitter card image size
                if ($key === 'twitter_card_type' ) {
                    $seopress_social['seopress_social_twitter_card_img_size'] = esc_html($value);
                }
                // Knowledge graph type
                if ($key === 'knowledgegraph_type') {
                    $type = [
                        'company' => 'Organization',
                        'person' => 'Person',
                    ];
                    $seopress_social['seopress_social_knowledge_type'] = esc_html($type[$value]);
                }
                // Website name
                if ($key === 'website_name') {
                    $seopress_titles['seopress_titles_home_site_title'] = esc_html($value);
                }
                // Website alternate name
                if ($key === 'website_alternate_name') {
                    $seopress_titles['seopress_titles_home_site_title_alt'] = esc_html($value);
                }
                // Knowledge graph name
                if ($key === 'knowledgegraph_name') {
                    $seopress_social['seopress_social_knowledge_name'] = esc_html($value);
                }
                // Knowledge graph logo
                if ($key === 'knowledgegraph_logo') {
                    $seopress_social['seopress_social_knowledge_img'] = esc_url($value);
                }
                // Facebook URL
                if ($key === 'social_url_facebook') {
                    $seopress_social['seopress_social_accounts_facebook'] = esc_url($value);
                }
                // Twitter URL
                if ($key === 'twitter_author_names') {
                    $seopress_social['seopress_social_accounts_twitter'] = esc_html($value);
                }
                // Custom Webmaster Tags
                if ( $key === 'custom_webmaster_tags' ) {
                    $accounts = array_filter(explode("\n", $value));
                    $accounts = implode("\n", array_map('esc_url', $accounts));
                    $seopress_social['seopress_social_accounts_extra'] = esc_html($accounts);
                }
                // Facebook Admin ID
                if ($key === 'facebook_admin_id') {
                    $seopress_social['seopress_social_facebook_admin_id'] = esc_html($value);
                }
                // Facebook App ID
                if ($key === 'facebook_app_id') {
                    $seopress_social['seopress_social_facebook_app_id'] = esc_html($value);
                }
                // Disable author archives
                if ($key === 'disable_author_archives') {
                    if ( $value === 'on' ) {
                        $seopress_titles['seopress_titles_archives_author_disable'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_author_disable']);
                    }
                }
                // Disable category archives
                if ($key === 'disable_category_archives') {
                    if ( $value === 'on' ) {
                        $seopress_titles['seopress_titles_archives_category_disable'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_category_disable']);
                    }
                }
                // Author archive title
                if ($key === 'author_custom_robots') {
                    if ($value === 'on' && in_array('noindex', $rank_math_titles['author_robots'])) {
                        $seopress_titles['seopress_titles_archives_author_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_author_noindex']);
                    }
                }
                // Author archive title
                if ($key === 'author_archive_title') {
                    $seopress_titles['seopress_titles_archives_author_title'] = esc_html($value);
                }
                // Author archive description
                if ($key === 'author_archive_description') {
                    $seopress_titles['seopress_titles_archives_author_desc'] = esc_html($value);
                }
                // Disable date archives
                if ($key === 'disable_date_archives') {
                    if ($value === 'on') {
                        $seopress_titles['seopress_titles_archives_date_disable'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_date_disable']);
                    }
                }
                // Date archive title
                if ($key === 'date_archive_title') {
                    $seopress_titles['seopress_titles_archives_date_title'] = esc_html($value);
                }
                // Date archive description
                if ($key === 'date_archive_description') {
                    $seopress_titles['seopress_titles_archives_date_desc'] = esc_html($value);
                }
                // Date archive noindex
                if ($key === 'date_archive_robots' && $value === 'on') {
                    if (in_array('noindex', $rank_math_titles['date_archive_robots'])) {
                        $seopress_titles['seopress_titles_archives_date_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_date_noindex']);
                    }
                }
                // 404 title
                if ($key === '404_title') {
                    $seopress_titles['seopress_titles_archives_404_title'] = esc_html($value);
                }
                // Search title
                if ($key === 'search_title') {
                    $seopress_titles['seopress_titles_archives_search_title'] = esc_html($value);
                }
                // Search noindex
                if ($key === 'noindex_search') {
                    if ($value === 'on') {
                        $seopress_titles['seopress_titles_archives_search_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_search_noindex']);
                    }
                }
                // Import CPT settings
                $postTypes = seopress_get_service('WordPressData')->getPostTypes();
                foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                    // Single title
                    if ( $key === 'pt_' . $seopress_cpt_key . '_title' ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['title'] = esc_html($value);
                    }
                    // Single description
                    if ( $key === 'pt_' . $seopress_cpt_key . '_description' ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['description'] = esc_html($value);
                    }
                    // Single noindex
                    if ( $key === 'pt_' . $seopress_cpt_key . '_custom_robots' && $value === 'on') {
                        unset($seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
                        if (!empty($rank_math_titles['pt_' . $seopress_cpt_key . '_robots']) && is_array($rank_math_titles['pt_' . $seopress_cpt_key . '_robots']) && in_array('noindex', $rank_math_titles['pt_' . $seopress_cpt_key . '_robots'])) {
                            $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'] = '1';
                        }
                        unset($seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow']);
                        if (!empty($rank_math_titles['pt_' . $seopress_cpt_key . '_robots']) && is_array($rank_math_titles['pt_' . $seopress_cpt_key . '_robots']) && in_array('nofollow', $rank_math_titles['pt_' . $seopress_cpt_key . '_robots'])) {
                            $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['nofollow'] = '1';
                        }
                    }
                    // Add meta box
                    if ( $key === 'pt_' . $seopress_cpt_key . '_add_meta_box' ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['enable'] = '1';
                        if ( $value === 'on' ) {
                            unset($seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['enable']);
                        }
                    }
                }
                // Import taxonomies settings
                $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
                foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
                    // Tax title
                    if ( $key === 'tax_' . $seopress_tax_key . '_title' ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = esc_html($value);
                    }
                    // Tax description
                    if ( $key === 'tax_' . $seopress_tax_key . '_description' ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = esc_html($value);
                    }
                    // Tax noindex
                    if ( $key === 'tax_' . $seopress_tax_key . '_custom_robots' && $value === 'on') {
                        unset($seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
                        if (!empty($rank_math_titles['tax_' . $seopress_tax_key . '_robots']) && is_array($rank_math_titles['tax_' . $seopress_tax_key . '_robots']) && in_array('noindex', $rank_math_titles['tax_' . $seopress_tax_key . '_robots'])) {
                            $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'] = '1';
                        }
                        unset($seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow']);
                        if (!empty($rank_math_titles['tax_' . $seopress_tax_key . '_robots']) && is_array($rank_math_titles['tax_' . $seopress_tax_key . '_robots']) && in_array('nofollow', $rank_math_titles['tax_' . $seopress_tax_key . '_robots'])) {
                            $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['nofollow'] = '1';
                        }
                    }
                    // Add meta box
                    if ( $key === 'tax_' . $seopress_tax_key . '_add_meta_box' ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['enable'] = '1';
                        if ( $value === 'on' ) {
                            unset($seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['enable']);
                        }
                    }
                }
            }
        }
        
        if (!empty($rank_math_sitemap)) {
            foreach ($rank_math_sitemap as $key => $value) {
                // Author archive sitemap
                if ($key === 'authors_sitemap') {
                    if ($value === 'on') {
                        $seopress_sitemap['seopress_xml_sitemap_author_enable'] = '1';
                    } else {
                        unset($seopress_sitemap['seopress_xml_sitemap_author_enable']);
                    }
                }
                // Include images
                if ($key === 'include_images' && $value === 'on') {
                    $seopress_sitemap['seopress_xml_sitemap_img_enable'] = '1';
                } elseif ($key === 'include_images' && $value === 'off') {
                    unset($seopress_sitemap['seopress_xml_sitemap_img_enable']);
                }
                // HTML sitemap
                if ($key === 'html_sitemap') {
                    if ($value === 'on') {
                        $seopress_sitemap['seopress_xml_sitemap_html_enable'] = '1';
                    } else {
                        unset($seopress_sitemap['seopress_xml_sitemap_html_enable']);
                    }
                }
                // HTML sitemap page
                if ($key === 'html_sitemap_page') {
                    $seopress_sitemap['seopress_xml_sitemap_html_mapping'] = esc_html($value);
                }
                // HTML sitemap sort
                if ($key === 'html_sitemap_sort') {
                    $sort = [
                        'published' => 'date',
                        'modified' => 'modified',
                        'alphabetical' => 'title',
                        'post_id' => 'ID'
                    ];
                    $seopress_sitemap['seopress_xml_sitemap_html_orderby'] = esc_html($sort[$value]);
                }
                // Import CPT settings
                $postTypes = seopress_get_service('WordPressData')->getPostTypes();
                foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                    // Include CPT in sitemap
                    if ( $key === 'pt_' . $seopress_cpt_key . '_sitemap' && $value === 'on' ) {
                        $seopress_sitemap['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'] = '1';
                    } elseif ( $key === 'pt_' . $seopress_cpt_key . '_sitemap' && $value === 'off' ) {
                        unset($seopress_sitemap['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include']);
                    }
                }
                // Import taxonomies settings
                $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
                foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
                    // Include tax in sitemap
                    if ( $key === 'tax_' . $seopress_tax_key . '_sitemap' && $value === 'on' ) {
                        $seopress_sitemap['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'] = '1';
                    } elseif ( $key === 'tax_' . $seopress_tax_key . '_sitemap' && $value === 'off' ) {
                        unset($seopress_sitemap['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include']);
                    }
                }
            }
        }

        update_option( 'seopress_titles_option_name', $seopress_titles, false );
        update_option( 'seopress_social_option_name', $seopress_social, false );
        update_option( 'seopress_xml_sitemap_option_name', $seopress_sitemap, false );
        update_option( 'seopress_advanced_option_name', $seopress_advanced, false );
        update_option( 'seopress_pro_option_name', $seopress_pro, false );
        update_option( 'seopress_instant_indexing_option_name', $seopress_instant_indexing, false );
    }

    /**
     * @since 4.3.0
     */
    public function process() {
        check_ajax_referer('seopress_rk_migrate_nonce', '_ajax_nonce', true);
        if ( ! is_admin()) {
            wp_send_json_error();

            return;
        }

        if ( ! current_user_can(seopress_capability('manage_options', 'migration'))) {
            wp_send_json_error();

            return;
        }

        $this->migrateSettings();

        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = $this->migrateTermQuery();
        } else {
            $offset = $this->migratePostQuery($offset, $increment);
        }

        $data           = [];

        $data['total'] = $total_count_posts;
        if ($offset >= $total_count_posts) {
            $data['count'] = $total_count_posts;
        } else {
            $data['count'] = $offset;
        }

        $data['offset'] = $offset;

        do_action('seopress_third_importer_rank_math', $offset, $increment);

        wp_send_json_success($data);
        exit();
    }
}

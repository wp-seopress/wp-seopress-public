<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* 
* Yoast migration
*/
function seopress_yoast_migration() {
    check_ajax_referer('seopress_yoast_migrate_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");
        $total_count_terms = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->terms}");

        $increment = 200;
        global $post;

        //=== Import settings ===//
        // Import titles
        $wpseo = get_option('wpseo');
        $wpseo_titles = get_option('wpseo_titles');
        $wpseo_social = get_option('wpseo_social');
        $seopress_titles  = get_option( 'seopress_titles_option_name' );
        $seopress_social = get_option('seopress_social_option_name');
        $seopress_advanced = get_option('seopress_advanced_option_name');
        $seopress_pro = get_option('seopress_pro_option_name');

        if ( !empty( $wpseo ) ) {
            foreach ( $wpseo as $key => $value ) {
                if ( $key === 'googleverify' ) {
                    $seopress_advanced['seopress_advanced_advanced_google'] = esc_html($value);
                }
                if ( $key === 'msverify' ) {
                    $seopress_advanced['seopress_advanced_advanced_bing'] = esc_html($value);
                }
                if ( $key === 'yandexverify' ) {
                    $seopress_advanced['seopress_advanced_advanced_yandex'] = esc_html($value);
                }
                if ( $key === 'baiduverify' ) {
                    $seopress_advanced['seopress_advanced_advanced_baidu'] = esc_html($value);
                }
                if ( $key === 'remove_shortlinks' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_shortlink'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_shortlink']);
                    }
                }
                if ( $key === 'remove_rsd_wlw_links' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_rsd'] = "1";
                        $seopress_advanced['seopress_advanced_advanced_wp_wlw'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_rsd']);
                        unset($seopress_advanced['seopress_advanced_advanced_wp_wlw']);
                    }
                }
                if ( $key === 'remove_oembed_links' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_oembed'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_oembed']);
                    }
                }
                if ( $key === 'remove_generator' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_generator'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_generator']);
                    }
                }
                if ( $key === 'remove_pingback_header' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_x_pingback'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_x_pingback']);
                    }
                }
                if ( $key === 'remove_powered_by_header' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_wp_x_powered_by'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_wp_x_powered_by']);
                    }   
                }
                if ( $key === 'remove_emoji_scripts' ) {
                    if ( $value === true ) {
                        $seopress_advanced['seopress_advanced_advanced_emoji'] = "1";
                    } else {
                        unset($seopress_advanced['seopress_advanced_advanced_emoji']);
                    }
                }
                // RSS Feeds
                if ( $key === 'remove_feed_global' ) {
                    if ( $value === true ) {
                        $seopress_pro['seopress_rss_disable_posts_feed'] = "1";
                    } else {
                        unset($seopress_pro['seopress_rss_disable_posts_feed']);
                    }
                }
                if ( $key === 'remove_feed_global_comments' ) {
                    if ( $value === true ) {
                        $seopress_pro['seopress_rss_disable_comments_feed'] = "1";
                    } else {
                        unset($seopress_pro['seopress_rss_disable_comments_feed']);
                    }
                }
                if ( $key === 'remove_feed_post_comments' ) {
                    if ( $value === true ) {
                        $seopress_pro['seopress_rss_disable_extra_feed'] = "1";
                    } else {
                        unset($seopress_pro['seopress_rss_disable_extra_feed']);
                    }
                }
            }
        }

        if ( !empty( $wpseo_titles ) ) {
            foreach ( $wpseo_titles as $key => $value ) {
                if ( $key === 'separator' ) {

                    $separator = [
                        'sc-dash' => '-',
                        'sc-ndash' => '&ndash;',
                        'sc-mdash' => '&mdash;',
                        'sc-colon' => ':',
                        'sc-middot' => '&middot;',
                        'sc-bull' => '&bull;',
                        'sc-star' => '*',
                        'sc-smstar' => '&#8902;',
                        'sc-pipe' => '|',
                        'sc-tilde' => '~',
                        'sc-laquo' => '&laquo;',
                        'sc-raquo' => '&raquo;',
                        'sc-gt' => '&lt;', // for some reason, the separator is reversed
                        'sc-lt' => '&gt;', // for some reason, the separator is reversed
                    ];

                    $seopress_titles['seopress_titles_sep'] = esc_html($separator[$value]);
                }
                if ( $key === 'website_name') {
                    $seopress_titles['seopress_titles_home_site_title'] = esc_html($value);
                }
                if ( $key === 'alternate_website_name') {
                    $seopress_titles['seopress_titles_home_site_title_alt'] = esc_html($value);
                }
                if ( $key === 'metadesc-home-wpseo') {
                    $seopress_titles['seopress_titles_home_site_desc'] = esc_html($value);
                }
                if ( $key === 'company_or_person' ) {
                    $type = [
                        'company' => 'Organization',
                        'person' => 'Person',
                    ];
                    $seopress_social['seopress_social_knowledge_type'] = esc_html($type[$value]);
                }
                if ( $key === 'company_name' ) {
                    $seopress_social['seopress_social_knowledge_name'] = esc_html($value);
                }
                if ( $key === 'company_logo' ) {
                    $seopress_social['seopress_social_knowledge_img'] = esc_url($value);
                }
                // Breadcrumbs
                if ( $key === 'breadcrumbs-enable' ) {
                    if ( $value === true ) {
                        $seopress_pro['seopress_breadcrumbs_enable'] = '1';
                        $seopress_pro['seopress_breadcrumbs_json_enable'] = '1';
                    } else {
                        unset($seopress_pro['seopress_breadcrumbs_enable']);
                        unset($seopress_pro['seopress_breadcrumbs_json_enable']);
                    }
                }
                if ( $key === 'breadcrumbs-sep' ) {
                    $seopress_pro['seopress_breadcrumbs_separator'] = esc_html($value);
                }
                if ( $key === 'breadcrumbs-home' ) {
                    $seopress_pro['seopress_breadcrumbs_i18n_home'] = esc_html($value);
                }
                if ( $key === 'breadcrumbs-prefix' ) {
                    $seopress_pro['seopress_breadcrumbs_i18n_here'] = esc_html($value);
                }
                if ( $key === 'breadcrumbs-searchprefix' ) {
                    $seopress_pro['seopress_breadcrumbs_i18n_search'] = esc_html($value);
                }
                if ( $key === 'breadcrumbs-404crumb' ) {
                    $seopress_pro['seopress_breadcrumbs_i18n_404'] = esc_html($value);
                }
                if ( $key === 'breadcrumbs-display-blog-page' ) {
                    if ( $value === true ) {
                        unset($seopress_pro['seopress_breadcrumbs_remove_blog_page']);
                    } else {
                        $seopress_pro['seopress_breadcrumbs_remove_blog_page'] = '1';
                    }
                }
                // RSS Feeds
                if ( $key === 'rssbefore' || $key === 'rssafter' ) {
                    $rss_vars = [
                        '%%AUTHORLINK%%' => '<a href="%%author_permalink%%">%%post_author%%</a>',
                        '%%POSTLINK%%' => '<a href="%%post_permalink%%">%%post_title%%</a>',
                        '%%BLOGLINK%%' => '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a>',
                        '%%BLOGDESCLINK%%' => '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . ' ' . get_bloginfo('description') . '</a>',
                    ];
                    $value = str_replace(array_keys($rss_vars), array_values($rss_vars), $value);
                }
                if ( $key === 'rssbefore' ) {
                    $args = [
                        'strong' => [],
                        'em' => [],
                        'br' => [],
                        'a' => ['href' => [], 'rel' => []],
                    ];
                    $seopress_pro['seopress_rss_before_html'] = wp_kses($value, $args);
                }
                if ( $key === 'rssafter' ) {
                    $args = [
                        'strong' => [],
                        'em' => [],
                        'br' => [],
                        'a' => ['href' => [], 'rel' => []],
                    ];
                    $seopress_pro['seopress_rss_after_html'] = wp_kses($value, $args);
                }

                // Import CPT settings
                $postTypes = seopress_get_service('WordPressData')->getPostTypes();
                foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
                    // Single title
                    if ( $key === 'title-' . $seopress_cpt_key ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['title'] = esc_html($value);
                    }
                    // Single description
                    if ( $key === 'metadesc-' . $seopress_cpt_key ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['description'] = esc_html($value);
                    }
                    // Single noindex
                    if ( $key === 'noindex-' . $seopress_cpt_key ) {
                        unset($seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['noindex']);
                        if (true === $value) {
                            $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['noindex'] = '1';
                        }
                    }
                    // Single Enable      
                    if ( $key === 'display-metabox-pt-' . $seopress_cpt_key ) {
                        $seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['enable'] = '1';              
                        if ( $value === true ) {
                            unset($seopress_titles['seopress_titles_single_titles'][$seopress_cpt_key]['enable']);
                        }
                    }
                    // Breadcrumbs
                    if ( $key === 'post_types-' . $seopress_cpt_key . '-maintax') {
                        $seopress_pro['seopress_breadcrumbs_tax'][$seopress_cpt_key]['tax'] = esc_html($value);
                    }
                }
                // Import taxonomies settings
                $taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
                foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
                    // Tax title
                    if ( $key === 'title-tax-' . $seopress_tax_key ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['title'] = esc_html($value);
                    }
                    // Tax description
                    if ( $key === 'metadesc-tax-' . $seopress_tax_key ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['description'] = esc_html($value);
                    }
                    // Tax noindex
                    if ( $key === 'noindex-tax-' . $seopress_tax_key ) {
                        unset($seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['noindex']);
                        if (true === $value) {
                            $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['noindex'] = '1';
                        }
                    }
                    // Tax Enable
                    if ( $key === 'display-metabox-tax-' . $seopress_tax_key ) {
                        $seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['enable'] = '1';              
                        if ( $value === true ) {
                            unset($seopress_titles['seopress_titles_tax_titles'][$seopress_tax_key]['enable']);
                        }
                    }
                    // Breadcrumbs
                    if ( $key === 'taxonomy-' . $seopress_tax_key . '-ptparent') {
                        $seopress_pro['seopress_breadcrumbs_cpt'][$seopress_tax_key]['cpt'] = esc_html($value);
                    }
                }
                // 404
                if ( $key === 'title-404-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_404_title'] = esc_html($value);
                }
                // Internal search
                if ( $key === 'title-search-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_search_title'] = esc_html($value);
                }
                // Date archive
                if ( $key === 'disable-date') {
                    if ( $value === true ) {
                        $seopress_titles['seopress_titles_archives_date_disable'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_date_disable']);
                    }
                }
                if ( $key === 'noindex-archive-wpseo' ) {
                    if ( $value === true ) {
                        $seopress_titles['seopress_titles_archives_date_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_date_noindex']);
                    }
                }
                if ( $key === 'title-archive-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_date_title'] = esc_html($value);
                }
                if ( $key === 'metadesc-archive-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_date_desc'] = esc_html($value);
                }
                // Author
                if ( $key === 'disable-author' ) {
                    if ( $value === true ) {
                        $seopress_titles['seopress_titles_archives_author_disable'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_author_disable']);
                    }
                }
                if ( $key === 'noindex-author-wpseo' ) {
                    if ( $value === true ) {
                        $seopress_titles['seopress_titles_archives_author_noindex'] = '1';
                    } else {
                        unset($seopress_titles['seopress_titles_archives_author_noindex']);
                    }
                }
                if ( $key === 'title-author-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_author_title'] = esc_html($value);
                }
                if ( $key === 'metadesc-author-wpseo' ) {
                    $seopress_titles['seopress_titles_archives_author_desc'] = esc_html($value);
                }
            }
        }

        // Import social
        if ( !empty( $wpseo_social ) ) {
            foreach ( $wpseo_social as $key => $value ) {
                if ( $key === 'facebook_site' ) {
                    $seopress_social['seopress_social_accounts_facebook'] = esc_url($value);
                }
                if ( $key === 'twitter_site' ) {
                    $seopress_social['seopress_social_accounts_twitter'] = esc_html($value);
                }
                if ( $key === 'other_social_urls' ) {
                    $accounts = implode("\n", array_map('esc_url', $value));
                    $seopress_social['seopress_social_accounts_extra'] = esc_html($accounts);
                }
                if ( $key === 'pinterestverify' ) {
                    $seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html($value);
                }
            }
        }
        
        update_option( 'seopress_titles_option_name', $seopress_titles, false );
        update_option( 'seopress_social_option_name', $seopress_social, false );
        update_option( 'seopress_advanced_option_name', $seopress_advanced, false );
        update_option( 'seopress_pro_option_name', $seopress_pro, false );

        // Import terms
        if ($offset > $total_count_posts) {
            wp_reset_postdata();

            $yoast_query_terms = get_option('wpseo_taxonomy_meta');

            if ($yoast_query_terms) {
                foreach ($yoast_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ('' != $term_value['wpseo_title']) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', esc_html($term_value['wpseo_title']));
                        }
                        if ('' != $term_value['wpseo_desc']) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', esc_html($term_value['wpseo_desc']));
                        }
                        if ('' != $term_value['wpseo_opengraph-title']) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', esc_html($term_value['wpseo_opengraph-title']));
                        }
                        if ('' != $term_value['wpseo_opengraph-description']) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', esc_html($term_value['wpseo_opengraph-description']));
                        }
                        if ('' != $term_value['wpseo_opengraph-image']) { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', esc_url($term_value['wpseo_opengraph-image']));
                        }
                        if ('' != $term_value['wpseo_twitter-title']) { //Import Twitter Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', esc_html($term_value['wpseo_twitter-title']));
                        }
                        if ('' != $term_value['wpseo_twitter-description']) { //Import Twitter Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', esc_html($term_value['wpseo_twitter-description']));
                        }
                        if ('' != $term_value['wpseo_twitter-image']) { //Import Twitter Image
                            update_term_meta($term_id, '_seopress_social_twitter_img', esc_url($term_value['wpseo_twitter-image']));
                        }
                        if ('noindex' == $term_value['wpseo_noindex']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ('' != $term_value['wpseo_canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', esc_url($term_value['wpseo_canonical']));
                        }
                        if ('' != $term_value['wpseo_bctitle']) { //Import Breadcrumb Title
                            update_term_meta($term_id, '_seopress_robots_breadcrumbs', esc_html($term_value['wpseo_bctitle']));
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_postdata();
        } else {
            // Import posts
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $yoast_query = get_posts($args);

            if ($yoast_query) {
                foreach ($yoast_query as $post) {
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', esc_html(get_post_meta($post->ID, '_yoast_wpseo_title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', esc_html(get_post_meta($post->ID, '_yoast_wpseo_metadesc', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', esc_html(get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', esc_html(get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', esc_url(get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', esc_html(get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', esc_html(get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url(get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true)));
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true)) { //Import Robots NoImageIndex, NoSnippet
                        $yoast_wpseo_meta_robots_adv = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true);

                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noimageindex')) {
                            update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'nosnippet')) {
                            update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', esc_url(get_post_meta($post->ID, '_yoast_wpseo_canonical', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_bctitle', true)) { //Import Breadcrumb Title
                        update_post_meta($post->ID, '_seopress_robots_breadcrumbs', esc_html(get_post_meta($post->ID, '_yoast_wpseo_bctitle', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) || '' != get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true)) { //Import Focus Keywords
                        $y_fkws_clean = []; //reset array

                        $y_fkws = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', false);

                        if ( ! empty($y_fkws)) {
                            foreach ($y_fkws as $value) {
                                foreach (json_decode($value) as $key => $value) {
                                    $y_fkws_clean[] .= esc_html($value->keyword);
                                }
                            }
                        }

                        $y_fkws_clean[] .= get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

                        update_post_meta($post->ID, '_seopress_analysis_target_kw', implode(',', $y_fkws_clean));
                    }

                    //Primary category
                    if (class_exists('WPSEO_Primary_Term')) {
                        if ('product' == get_post_type($post->ID)) {
                            $tax = 'product_cat';
                        } else {
                            $tax = 'category';
                        }

                        $primary_term = new WPSEO_Primary_Term($tax, $post->ID);

                        $primary_term = absint($primary_term->get_primary_term());

                        if ('' != $primary_term && is_int($primary_term)) {
                            update_post_meta($post->ID, '_seopress_robots_primary_cat', $primary_term);
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];

        $data['total'] = $total_count_posts;

        if ($offset >= $total_count_posts) {
            $data['count'] = $total_count_posts;
        } else {
            $data['count'] = $offset;
        }

        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_yoast_migration', 'seopress_yoast_migration');

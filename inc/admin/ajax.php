<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview() {
    check_ajax_referer('seopress_real_preview_nonce', $_GET['_ajax_nonce'], true);

    if (current_user_can('edit_posts') && is_admin()) {
        //Get cookies
        if (isset($_COOKIE)) {
            $cookies = [];

            foreach ($_COOKIE as $name => $value) {
                if ('PHPSESSID' !== $name) {
                    $cookies[] = new WP_Http_Cookie(['name' => $name, 'value' => $value]);
                }
            }
        }

        //Get post id
        if (isset($_GET['post_id'])) {
            $seopress_get_the_id = $_GET['post_id'];
        }

        if ('yes' == get_post_meta($seopress_get_the_id, '_seopress_redirections_enabled', true)) {
            $data['title'] = __('A redirect is active for this URL. Turn it off to get the Google preview and content analysis.', 'wp-seopress');
        } else {
            //Get cookies
            if (isset($_COOKIE)) {
                $cookies = [];

                foreach ($_COOKIE as $name => $value) {
                    if ('PHPSESSID' !== $name) {
                        $cookies[] = new WP_Http_Cookie(['name' => $name, 'value' => $value]);
                    }
                }
            }

            //Get post type
            if (isset($_GET['post_type'])) {
                $seopress_get_post_type = $_GET['post_type'];
            } else {
                $seopress_get_post_type = null;
            }

            //Origin
            if (isset($_GET['origin'])) {
                $seopress_origin = $_GET['origin'];
            }

            //Tax name
            if (isset($_GET['tax_name'])) {
                $seopress_tax_name = $_GET['tax_name'];
            }

            //Init
            $title     = '';
            $meta_desc = '';
            $data      = [];

            //Save Target KWs
            if ( ! isset($_GET['is_elementor'])) {
                if (isset($_GET['seopress_analysis_target_kw'])) {
                    delete_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw');
                    update_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', sanitize_text_field($_GET['seopress_analysis_target_kw']));
                }
            }

            //Fix Elementor
            if (isset($_GET['is_elementor']) && true == $_GET['is_elementor']) {
                $_GET['seopress_analysis_target_kw'] = get_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', true);
            }

            //DOM
            $dom                     = new DOMDocument();
            $internalErrors          = libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;

            //Get source code
            $args = [
                'blocking'    => true,
                'timeout'     => 30,
                'sslverify'   => false,
            ];

            if (isset($cookies) && ! empty($cookies)) {
                $args['cookies'] = $cookies;
            }
            $args = apply_filters('seopress_real_preview_remote', $args);

            $data['title'] = $cookies;

            if ('post' == $seopress_origin) { //Default: post type
                //Oxygen compatibility
                if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output')) {
                    $post_url = get_permalink((int) $seopress_get_the_id);
                    $post_url = add_query_arg('no_admin_bar', 1, $post_url);

                    $response = wp_remote_get($post_url, $args);
                } else {
                    $response = wp_remote_get(get_preview_post_link((int) $seopress_get_the_id, ['no_admin_bar' => 1]), $args);
                }
            } else { //Term taxonomy
                $response = wp_remote_get(get_term_link((int) $seopress_get_the_id, $seopress_tax_name), $args);
            }

            //Check for error
            if (is_wp_error($response) || '404' == wp_remote_retrieve_response_code($response)) {
                $data['title'] = __('To get your Google snippet preview, publish your post!', 'wp-seopress');
            } else {
                $response = wp_remote_retrieve_body($response);

                if ($dom->loadHTML('<?xml encoding="utf-8" ?>' . $response)) {
                    if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output')) {
                        $data = get_post_meta($seopress_get_the_id, '_seopress_analysis_data', true) ? get_post_meta($seopress_get_the_id, '_seopress_analysis_data', true) : $data = [];

                        if ( ! empty($data)) {
                            $data = array_slice($data, 0, 3);
                        }
                    }

                    //Disable wptexturize
                    add_filter('run_wptexturize', '__return_false');

                    //Get post content (used for Words counter)
                    $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', $seopress_get_the_id));

                    //Themify / Cornerstone compatibility
                    if (defined('THEMIFY_DIR') || is_plugin_active('cornerstone/cornerstone.php')) {
                        $seopress_get_the_content = get_post_field('post_content', $seopress_get_the_id);
                    }

                    //BeTheme is activated
                    $theme = wp_get_theme();
                    if ('betheme' == $theme->template || 'Betheme' == $theme->parent_theme) {
                        $seopress_get_the_content = $seopress_get_the_content . get_post_meta($seopress_get_the_id, 'mfn-page-items-seo', true);
                    }

                    //Add WC product excerpt
                    if ('product' == $seopress_get_post_type) {
                        $seopress_get_the_content =  $seopress_get_the_content . get_the_excerpt($seopress_get_the_id);
                    }

                    $seopress_get_the_content = apply_filters('seopress_content_analysis_content', $seopress_get_the_content, $seopress_get_the_id);

                    //Get Target Keywords
                    if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                        $data['target_kws']          = esc_html(strtolower(stripslashes_deep($_GET['seopress_analysis_target_kw'])));
                        $seopress_analysis_target_kw = array_filter(explode(',', strtolower(get_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', true))));

                        //Manage keywords with special characters
                        foreach ($seopress_analysis_target_kw as $key => $kw) {
                            $kw                            = str_replace('-', ' ', $kw); //remove dashes
                            $seopress_analysis_target_kw[] = htmlspecialchars_decode($kw, ENT_QUOTES);
                        }

                        //Remove duplicates
                        $seopress_analysis_target_kw = array_unique($seopress_analysis_target_kw);
                    }

                    $xpath = new DOMXPath($dom);

                    //Title
                    $list = $dom->getElementsByTagName('title');
                    if ($list->length > 0) {
                        $title         = $list->item(0)->textContent;
                        $data['title'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($title)));
                        if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                            foreach ($seopress_analysis_target_kw as $kw) {
                                if (preg_match_all('#\b(' . $kw . ')\b#iu', $data['title'], $m)) {
                                    $data['meta_title']['matches'][$kw][] = $m[0];
                                }
                            }
                        }
                    }

                    //Meta desc
                    $meta_description = $xpath->query('//meta[@name="description"]/@content');

                    foreach ($meta_description as $key=>$mdesc) {
                        $data['meta_desc'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags($mdesc->nodeValue))));
                    }

                    if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                        if ( ! empty($meta_description)) {
                            foreach ($meta_description as $meta_desc) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $meta_desc->nodeValue, $m)) {
                                        $data['meta_description']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }
                    }

                    //OG:title
                    $og_title = $xpath->query('//meta[@property="og:title"]/@content');

                    if ( ! empty($og_title)) {
                        $data['og_title']['count'] = count($og_title);
                        foreach ($og_title as $key=>$mogtitle) {
                            $data['og_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogtitle->nodeValue)));
                        }
                    }

                    //OG:description
                    $og_desc = $xpath->query('//meta[@property="og:description"]/@content');

                    if ( ! empty($og_desc)) {
                        $data['og_desc']['count'] = count($og_desc);
                        foreach ($og_desc as $key=>$mog_desc) {
                            $data['og_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_desc->nodeValue)));
                        }
                    }

                    //OG:image
                    $og_img = $xpath->query('//meta[@property="og:image"]/@content');

                    if ( ! empty($og_img)) {
                        $data['og_img']['count'] = count($og_img);
                        foreach ($og_img as $key=>$mog_img) {
                            $data['og_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_img->nodeValue)));
                        }
                    }

                    //OG:url
                    $og_url = $xpath->query('//meta[@property="og:url"]/@content');

                    if ( ! empty($og_url)) {
                        $data['og_url']['count'] = count($og_url);
                        foreach ($og_url as $key=>$mog_url) {
                            $url                        = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_url->nodeValue)));
                            $data['og_url']['values'][] = $url;
                            $url                        = wp_parse_url($url);
                            $data['og_url']['host']     = $url['host'];
                        }
                    }

                    //OG:site_name
                    $og_site_name = $xpath->query('//meta[@property="og:site_name"]/@content');

                    if ( ! empty($og_site_name)) {
                        $data['og_site_name']['count'] = count($og_site_name);
                        foreach ($og_site_name as $key=>$mog_site_name) {
                            $data['og_site_name']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_site_name->nodeValue)));
                        }
                    }

                    //Twitter:title
                    $tw_title = $xpath->query('//meta[@name="twitter:title"]/@content');

                    if ( ! empty($tw_title)) {
                        $data['tw_title']['count'] = count($tw_title);
                        foreach ($tw_title as $key=>$mtw_title) {
                            $data['tw_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_title->nodeValue)));
                        }
                    }

                    //Twitter:description
                    $tw_desc = $xpath->query('//meta[@name="twitter:description"]/@content');

                    if ( ! empty($tw_desc)) {
                        $data['tw_desc']['count'] = count($tw_desc);
                        foreach ($tw_desc as $key=>$mtw_desc) {
                            $data['tw_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_desc->nodeValue)));
                        }
                    }

                    //Twitter:image
                    $tw_img = $xpath->query('//meta[@name="twitter:image"]/@content');

                    if ( ! empty($tw_img)) {
                        $data['tw_img']['count'] = count($tw_img);
                        foreach ($tw_img as $key=>$mtw_img) {
                            $data['tw_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_img->nodeValue)));
                        }
                    }

                    //Twitter:image:src
                    $tw_img = $xpath->query('//meta[@name="twitter:image:src"]/@content');

                    if ( ! empty($tw_img)) {
                        $count = null;
                        if ( ! empty($data['tw_img']['count'])) {
                            $count = $data['tw_img']['count'];
                        }

                        $data['tw_img']['count'] = count($tw_img) + $count;

                        foreach ($tw_img as $key=>$mtw_img) {
                            $data['tw_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_img->nodeValue)));
                        }
                    }

                    //Canonical
                    $canonical = $xpath->query('//link[@rel="canonical"]/@href');

                    foreach ($canonical as $key=>$mcanonical) {
                        $data['canonical'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mcanonical->nodeValue)));
                    }

                    foreach ($canonical as $key=>$mcanonical) {
                        $data['all_canonical'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mcanonical->nodeValue)));
                    }

                    //h1
                    $h1 = $xpath->query('//h1');
                    if ( ! empty($h1)) {
                        $data['h1']['nomatches']['count'] = count($h1);
                        if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                            foreach ($h1 as $heading1) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $heading1->nodeValue, $m)) {
                                        $data['h1']['matches'][$kw][] = $m[0];
                                    }
                                }
                                $data['h1']['values'][] = esc_attr($heading1->nodeValue);
                            }
                        }
                    }

                    if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                        //h2
                        $h2 = $xpath->query('//h2');
                        if ( ! empty($h2)) {
                            foreach ($h2 as $heading2) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $heading2->nodeValue, $m)) {
                                        $data['h2']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }

                        //h3
                        $h3 = $xpath->query('//h3');
                        if ( ! empty($h3)) {
                            foreach ($h3 as $heading3) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $heading3->nodeValue, $m)) {
                                        $data['h3']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }

                        //Keywords density
                        if ( ! is_plugin_active('oxygen/functions.php') && ! function_exists('ct_template_output')) { //disable for Oxygen
                            foreach ($seopress_analysis_target_kw as $kw) {
                                if (preg_match_all('#\b(' . $kw . ')\b#iu', stripslashes_deep(strip_tags(wp_filter_nohtml_kses($seopress_get_the_content))), $m)) {
                                    $data['kws_density']['matches'][$kw][] = $m[0];
                                }
                            }
                        }

                        //Keywords in permalink
                        $post    = get_post($seopress_get_the_id);
                        $kw_slug = urldecode($post->post_name);

                        if (is_plugin_active('permalink-manager-pro/permalink-manager.php')) {
                            global $permalink_manager_uris;
                            $kw_slug = urldecode($permalink_manager_uris[$seopress_get_the_id]);
                        }

                        $kw_slug = str_replace('-', ' ', $kw_slug);

                        if (isset($kw_slug)) {
                            foreach ($seopress_analysis_target_kw as $kw) {
                                if (preg_match_all('#\b(' . remove_accents($kw) . ')\b#iu', strip_tags(wp_filter_nohtml_kses($kw_slug)), $m)) {
                                    $data['kws_permalink']['matches'][$kw][] = $m[0];
                                }
                            }
                        }
                    }

                    //Images
                    /*Standard images*/
                    $imgs = $xpath->query('//img');

                    if ( ! empty($imgs) && null != $imgs) {
                        //init
                        $data_img = [];
                        foreach ($imgs as $img) {
                            if ($img->hasAttribute('src')) {
                                //Exclude avatars from analysis
                                if ( ! preg_match_all('#\b(avatar)\b#iu', $img->getAttribute('class'), $m)) {
                                    if ($img->hasAttribute('width') || $img->hasAttribute('height')) {
                                        if ($img->getAttribute('width') > 1 || $img->getAttribute('height') > 1) {
                                            if ('' === $img->getAttribute('alt') || ! $img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                                $data_img[] .= $img->getAttribute('src');
                                            }
                                        }
                                    } elseif ('' === $img->getAttribute('alt') || ! $img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                        $img_src = download_url($img->getAttribute('src'));
                                        if (false === is_wp_error($img_src)) {
                                            if (filesize($img_src) > 100) {//Ignore files under 100 bytes
                                                $data_img[] .= $img->getAttribute('src');
                                            }
                                            @unlink($img_src);
                                        }
                                    }
                                }
                            }
                            $data['img']['images'] = $data_img;
                        }
                    }

                    //Meta robots
                    $meta_robots = $xpath->query('//meta[@name="robots"]/@content');
                    if ( ! empty($meta_robots)) {
                        foreach ($meta_robots as $key=>$value) {
                            $data['meta_robots'][$key][] = esc_attr($value->nodeValue);
                        }
                    }

                    //Meta google noimageindex / nositelinkssearchbox
                    $meta_google = $xpath->query('//meta[@name="google"]/@content');
                    if ( ! empty($meta_google)) {
                        foreach ($meta_google as $key=>$mgnoimg) {
                            $data['meta_google'][$key][] = esc_attr($mgnoimg->nodeValue);
                        }
                    }

                    //nofollow links
                    $nofollow_links = $xpath->query("//a[contains(@rel, 'nofollow')]");
                    if ( ! empty($nofollow_links)) {
                        foreach ($nofollow_links as $key=>$link) {
                            if ( ! preg_match_all('#\b(cancel-comment-reply-link)\b#iu', $link->getAttribute('id'), $m) && ! preg_match_all('#\b(comment-reply-link)\b#iu', $link->getAttribute('class'), $m)) {
                                $data['nofollow_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                            }
                        }
                    }
                }

                //outbound links
                $site_url       = wp_parse_url(get_home_url(), PHP_URL_HOST);
                $outbound_links = $xpath->query("//a[not(contains(@href, '" . $site_url . "'))]");
                if ( ! empty($outbound_links)) {
                    foreach ($outbound_links as $key=>$link) {
                        if ( ! empty(wp_parse_url($link->getAttribute('href'), PHP_URL_HOST))) {
                            $data['outbound_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                        }
                    }
                }

                //inbound links
                $permalink = get_permalink((int) $seopress_get_the_id);
                $args      = [
                    's'         => $permalink,
                    'post_type' => 'any',
                ];
                $inbound_links = new WP_Query($args);

                if ($inbound_links->have_posts()) {
                    $data['inbound_links']['count'] = $inbound_links->found_posts;

                    while ($inbound_links->have_posts()) {
                        $inbound_links->the_post();
                        $data['inbound_links']['links'][get_the_ID()] = [get_the_permalink() => get_the_title()];
                    }
                }
                wp_reset_postdata();

                //Words Counter
                if ( ! is_plugin_active('oxygen/functions.php') && ! function_exists('ct_template_output')) { //disable for Oxygen
                    if ('' != $seopress_get_the_content) {
                        $data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $matches);

                        if ( ! empty($matches[0])) {
                            $words_counter_unique = count(array_unique($matches[0]));
                        } else {
                            $words_counter_unique = '0';
                        }
                        $data['words_counter_unique'] = $words_counter_unique;
                    }
                }

                //Get schemas
                $json_ld = $xpath->query('//script[@type="application/ld+json"]');
                if ( ! empty($json_ld)) {
                    foreach ($json_ld as $node) {
                        $json = json_decode($node->nodeValue, true);
                        if (isset($json['@type'])) {
                            $data['json'][] = $json['@type'];
                        }
                    }
                }
            }

            libxml_use_internal_errors($internalErrors);
        }

        //Send data
        if (isset($data)) {
            //Oxygen builder
            if (get_post_meta($seopress_get_the_id, '_seopress_analysis_data_oxygen', true)) {
                $data2 = get_post_meta($seopress_get_the_id, '_seopress_analysis_data_oxygen', true);
                $data  = $data + $data2;
            }
            update_post_meta($seopress_get_the_id, '_seopress_analysis_data', $data);
        }

        //Re-enable QM
        remove_filter('user_has_cap', 'seopress_disable_qm', 10, 3);

        //Return
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_do_real_preview', 'seopress_do_real_preview');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Flush permalinks
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_flush_permalinks() {
    check_ajax_referer('seopress_flush_permalinks_nonce', $_GET['_ajax_nonce'], true);
    if (current_user_can(seopress_capability('manage_options', 'flush')) && is_admin()) {
        flush_rewrite_rules(false);
        exit();
    }
}
add_action('wp_ajax_seopress_flush_permalinks', 'seopress_flush_permalinks');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard toggle features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_toggle_features() {
    check_ajax_referer('seopress_toggle_features_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['feature']) && isset($_POST['feature_value'])) {
            $seopress_toggle_options                    = get_option('seopress_toggle');
            $seopress_toggle_options[$_POST['feature']] = $_POST['feature_value'];
            update_option('seopress_toggle', $seopress_toggle_options, 'yes');
        }
        exit();
    }
}
add_action('wp_ajax_seopress_toggle_features', 'seopress_toggle_features');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard hide notices
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_hide_notices() {
    check_ajax_referer('seopress_hide_notices_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['notice']) && isset($_POST['notice_value'])) {
            $seopress_notices_options                   = get_option('seopress_notices');
            $seopress_notices_options[$_POST['notice']] = $_POST['notice_value'];
            update_option('seopress_notices', $seopress_notices_options, 'yes');
        }
        exit();
    }
}
add_action('wp_ajax_seopress_hide_notices', 'seopress_hide_notices');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Yoast migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_yoast_migration() {
    check_ajax_referer('seopress_yoast_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $yoast_query_terms = get_option('wpseo_taxonomy_meta');

            if ($yoast_query_terms) {
                foreach ($yoast_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ('' != $term_value['wpseo_title']) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['wpseo_title']);
                        }
                        if ('' != $term_value['wpseo_desc']) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['wpseo_desc']);
                        }
                        if ('' != $term_value['wpseo_opengraph-title']) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['wpseo_opengraph-title']);
                        }
                        if ('' != $term_value['wpseo_opengraph-description']) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['wpseo_opengraph-description']);
                        }
                        if ('' != $term_value['wpseo_opengraph-image']) { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', $term_value['wpseo_opengraph-image']);
                        }
                        if ('' != $term_value['wpseo_twitter-title']) { //Import Twitter Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', $term_value['wpseo_twitter-title']);
                        }
                        if ('' != $term_value['wpseo_twitter-description']) { //Import Twitter Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $term_value['wpseo_twitter-description']);
                        }
                        if ('' != $term_value['wpseo_twitter-image']) { //Import Twitter Image
                            update_term_meta($term_id, '_seopress_social_twitter_img', $term_value['wpseo_twitter-image']);
                        }
                        if ('noindex' == $term_value['wpseo_noindex']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ('' != $term_value['wpseo_canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['wpseo_canonical']);
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
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
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_yoast_wpseo_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_yoast_wpseo_metadesc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true));
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true)) { //Import Robots NoOdp, NoImageIndex, NoArchive, NoSnippet
                        $yoast_wpseo_meta_robots_adv = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true);

                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noodp')) {
                            update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noimageindex')) {
                            update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noarchive')) {
                            update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'nosnippet')) {
                            update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_yoast_wpseo_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) || '' != get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true)) { //Import Focus Keywords
                        $y_fkws_clean = []; //reset array

                        $y_fkws = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', false);

                        foreach ($y_fkws as $value) {
                            foreach (json_decode($value) as $key => $value) {
                                $y_fkws_clean[] .= $value->keyword;
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

                        $primary_term = $primary_term->get_primary_term();

                        if ('' != $primary_term && is_int($primary_term)) {
                            update_post_meta($post->ID, '_seopress_robots_primary_cat', $primary_term);
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_yoast_migration', 'seopress_yoast_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//AIO migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_aio_migration() {
    check_ajax_referer('seopress_aio_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $aio_query = get_posts($args);

            if ($aio_query) {
                foreach ($aio_query as $post) {
                    if ('' != get_post_meta($post->ID, '_aioseo_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseo_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_title', true)) { //Import old title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseop_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_aioseo_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseo_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_description', true)) { //Import old meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseop_description', true));
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_og_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_aioseo_og_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_twitter_title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_aioseo_twitter_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Title
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_og_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_aioseo_og_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_twitter_description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_aioseo_twitter_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    $canonical_url = "SELECT p.canonical_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $canonical_url = $wpdb->get_results($canonical_url, ARRAY_A);

                    if ( ! empty($canonical_url[0]['canonical_url'])) {//Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', $canonical_url[0]['canonical_url']);
                    }

                    $og_img_url = "SELECT p.og_image_custom_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.og_image_type = 'custom_image' AND p.post_id = $post->ID";

                    $og_img_url = $wpdb->get_results($og_img_url, ARRAY_A);

                    if ( ! empty($og_img_url[0]['og_image_custom_url'])) {//Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', $og_img_url[0]['og_image_custom_url']);
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }

                    $tw_img_url = "SELECT p.twitter_image_custom_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.twitter_image_type = 'custom_image' AND p.post_id = $post->ID";

                    $tw_img_url = $wpdb->get_results($tw_img_url, ARRAY_A);

                    if ( ! empty($tw_img_url[0]['twitter_image_custom_url'])) {//Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', $tw_img_url[0]['twitter_image_custom_url']);
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }

                    $robots_noindex = "SELECT p.robots_noindex, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noindex = $wpdb->get_results($robots_noindex, ARRAY_A);

                    if ( ! empty($robots_noindex[0]['robots_noindex']) && '1' === $robots_noindex[0]['robots_noindex']) {//Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    } elseif ('on' == get_post_meta($post->ID, '_aioseop_noindex', true)) { //Import old Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }

                    $robots_nofollow = "SELECT p.robots_nofollow, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_nofollow = $wpdb->get_results($robots_nofollow, ARRAY_A);

                    if ( ! empty($robots_nofollow[0]['robots_nofollow']) && '1' === $robots_nofollow[0]['robots_nofollow']) {//Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    } elseif ('on' == get_post_meta($post->ID, '_aioseop_nofollow', true)) { //Import old Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }

                    $robots_noimageindex = "SELECT p.robots_noimageindex, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noimageindex = $wpdb->get_results($robots_noimageindex, ARRAY_A);

                    if ( ! empty($robots_noimageindex[0]['robots_noimageindex']) && '1' === $robots_noimageindex[0]['robots_noimageindex']) {//Import Robots NoImageIndex
                        update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                    }

                    $robots_noodp = "SELECT p.robots_noodp, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noodp = $wpdb->get_results($robots_noodp, ARRAY_A);

                    if ( ! empty($robots_noodp[0]['robots_noodp']) && '1' === $robots_noodp[0]['robots_noodp']) {//Import Robots NoOdp
                        update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
                    }

                    $robots_nosnippet = "SELECT p.robots_nosnippet, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_nosnippet = $wpdb->get_results($robots_nosnippet, ARRAY_A);

                    if ( ! empty($robots_nosnippet[0]['robots_nosnippet']) && '1' === $robots_nosnippet[0]['robots_nosnippet']) {//Import Robots NoSnippet
                        update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                    }

                    $robots_noarchive = "SELECT p.robots_noarchive, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noarchive = $wpdb->get_results($robots_noarchive, ARRAY_A);

                    if ( ! empty($robots_noarchive[0]['robots_noarchive']) && '1' === $robots_noarchive[0]['robots_noarchive']) {//Import Robots NoArchive
                        update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                    }

                    $keyphrases = "SELECT p.keyphrases, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $keyphrases = $wpdb->get_results($keyphrases, ARRAY_A);

                    if ( ! empty($keyphrases)) {
                        $keyphrases = json_decode($keyphrases[0]['keyphrases']);

                        if (isset($keyphrases->focus->keyphrase)) {
                            $keyphrases = $keyphrases->focus->keyphrase;

                            if ('' != $keyphrases) { //Import focus kw
                                update_post_meta($post->ID, '_seopress_analysis_target_kw', $keyphrases);
                            }
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_aio_migration', 'seopress_aio_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEO Framework migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_seo_framework_migration() {
    check_ajax_referer('seopress_seo_framework_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $args = [
                //'number' => $increment,
                'hide_empty' => false,
                //'offset' => $offset,
                'fields' => 'ids',
            ];
            $seo_framework_query_terms = get_terms($args);

            if ($seo_framework_query_terms) {
                foreach ($seo_framework_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, 'autodescription-term-settings', true)) {
                        $term_settings = get_term_meta($term_id, 'autodescription-term-settings', true);

                        if ( ! empty($term_settings['doctitle'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_settings['doctitle']);
                        }
                        if ( ! empty($term_settings['description'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_settings['description']);
                        }
                        if ( ! empty($term_settings['noindex'])) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($term_settings['nofollow'])) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                        if ( ! empty($term_settings['noarchive'])) { //Import Robots NoArchive
                            update_term_meta($term_id, '_seopress_robots_archive', 'yes');
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $seo_framework_query = get_posts($args);

            if ($seo_framework_query) {
                foreach ($seo_framework_query as $post) {
                    if ('' != get_post_meta($post->ID, '_genesis_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_genesis_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_genesis_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_genesis_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_open_graph_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_open_graph_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_open_graph_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_open_graph_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_social_image_url', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_social_image_url', true));
                    }
                    if ('' != get_post_meta($post->ID, '_twitter_title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_twitter_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_twitter_description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_twitter_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_social_image_url', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_social_image_url', true));
                    }
                    if ('1' == get_post_meta($post->ID, '_genesis_noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_genesis_nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_genesis_noarchive', true)) { //Import Robots NoArchive
                        update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_genesis_canonical_uri', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_genesis_canonical_uri', true));
                    }
                    if ('' != get_post_meta($post->ID, 'redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes');
                        update_post_meta($post->ID, '_seopress_redirections_type', '301');
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, 'redirect', true));
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seo_framework_migration', 'seopress_seo_framework_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Squirrly migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_squirrly_migration() {
    check_ajax_referer('seopress_squirrly_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'qss';
        $blog_id    = get_current_blog_id();

        $count_query = $wpdb->get_results("SELECT * FROM $table_name WHERE blog_id = $blog_id", ARRAY_A);

        if ( ! empty($count_query)) {
            foreach ($count_query as $value) {
                $post_id = url_to_postid($value['URL']);

                if (0 != $post_id && ! empty($value['seo'])) {
                    $seo = maybe_unserialize($value['seo']);

                    if ('' != $seo['title']) { //Import title tag
                        update_post_meta($post_id, '_seopress_titles_title', $seo['title']);
                    }
                    if ('' != $seo['description']) { //Import description tag
                        update_post_meta($post_id, '_seopress_titles_desc', $seo['description']);
                    }
                    if ('' != $seo['og_title']) { //Import Facebook Title
                        update_post_meta($post_id, '_seopress_social_fb_title', $seo['og_title']);
                    }
                    if ('' != $seo['og_description']) { //Import Facebook Desc
                        update_post_meta($post_id, '_seopress_social_fb_desc', $seo['og_description']);
                    }
                    if ('' != $seo['og_media']) { //Import Facebook Image
                        update_post_meta($post_id, '_seopress_social_fb_img', $seo['og_media']);
                    }
                    if ('' != $seo['tw_title']) { //Import Twitter Title
                        update_post_meta($post_id, '_seopress_social_twitter_title', $seo['tw_title']);
                    }
                    if ('' != $seo['tw_description']) { //Import Twitter Desc
                        update_post_meta($post_id, '_seopress_social_twitter_desc', $seo['tw_description']);
                    }
                    if ('' != $seo['tw_media']) { //Import Twitter Image
                        update_post_meta($post_id, '_seopress_social_twitter_img', $seo['tw_media']);
                    }
                    if (1 === $seo['noindex']) { //Import noindex
                        update_post_meta($post_id, '_seopress_robots_index', 'yes');
                    }
                    if (1 === $seo['nofollow']) { //Import nofollow
                        update_post_meta($post_id, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != $seo['canonical']) { //Import canonical
                        update_post_meta($post_id, '_seopress_robots_canonical', $seo['canonical']);
                    }
                }
            }
            $offset = 'done';
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_squirrly_migration', 'seopress_squirrly_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* SEO Ultimate migration
* @since 3.8.2
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_seo_ultimate_migration() {
    check_ajax_referer('seopress_seo_ultimate_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $su_query = get_posts($args);

            if ($su_query) {
                foreach ($su_query as $post) {
                    if ('' != get_post_meta($post->ID, '_su_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_su_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_su_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_su_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_su_og_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_su_og_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_su_og_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_su_og_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_su_og_image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_su_og_image', true));
                    }
                    if ('1' == get_post_meta($post->ID, '_su_meta_robots_noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_su_meta_robots_nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seo_ultimate_migration', 'seopress_seo_ultimate_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* WP Meta SEO migration
* @since 3.8.2
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_wp_meta_seo_migration() {
    check_ajax_referer('seopress_meta_seo_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $args = [
                'hide_empty' => false,
                'fields'     => 'ids',
            ];
            $wp_meta_seo_query_terms = get_terms($args);

            if ($wp_meta_seo_query_terms) {
                foreach ($wp_meta_seo_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, 'wpms_category_metatitle', true)) { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_term_meta($term_id, 'wpms_category_metatitle', true));
                    }
                    if ('' != get_term_meta($term_id, 'wpms_category_metadesc', true)) { //Import title desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_term_meta($term_id, 'wpms_category_metadesc', true));
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $wp_meta_seo_query = get_posts($args);

            if ($wp_meta_seo_query) {
                foreach ($wp_meta_seo_query as $post) {
                    if ('' != get_post_meta($post->ID, '_metaseo_metatitle', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_metaseo_metatitle', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_metaseo_metadesc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_metaseo_metaopengraph-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_metaseo_metaopengraph-image', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_metaseo_metatwitter-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-desc', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_metaseo_metatwitter-desc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-image', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_metaseo_metatwitter-image', true));
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_wp_meta_seo_migration', 'seopress_wp_meta_seo_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* Premium SEO Pack migration
* @since 3.8.7
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_premium_seo_pack_migration() {
    check_ajax_referer('seopress_premium_seo_pack_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $premium_query_terms = get_option('psp_taxonomy_seo');

            if ($premium_query_terms) {
                foreach ($premium_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ('' != $term_value['psp_meta']['title']) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['psp_meta']['title']);
                        }
                        if ('' != $term_value['psp_meta']['description']) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['psp_meta']['description']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_titlu']) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['psp_meta']['facebook_titlu']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_desc']) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['psp_meta']['facebook_desc']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_image']) { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', $term_value['psp_meta']['facebook_image']);
                        }
                        if ('noindex' == $term_value['psp_meta']['robots_index']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ('nofollow' == $term_value['psp_meta']['robots_follow']) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                        if ('' != $term_value['psp_meta']['canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['psp_meta']['canonical']);
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $premium_query = get_posts($args);

            if ($premium_query) {
                foreach ($premium_query as $post) {
                    $psp_meta = get_post_meta($post->ID, 'psp_meta', true);

                    if ( ! empty($psp_meta)) {
                        if ( ! empty($psp_meta['title'])) { //Import title tag
                            update_post_meta($post->ID, '_seopress_titles_title', $psp_meta['title']);
                        }
                        if ( ! empty($psp_meta['description'])) { //Import meta desc
                            update_post_meta($post->ID, '_seopress_titles_desc', $psp_meta['description']);
                        }
                        if ( ! empty($psp_meta['facebook_titlu'])) { //Import Facebook Title
                            update_post_meta($post->ID, '_seopress_social_fb_title', $psp_meta['facebook_titlu']);
                        }
                        if ( ! empty($psp_meta['facebook_desc'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $psp_meta['facebook_desc']);
                        }
                        if ( ! empty($psp_meta['facebook_image'])) { //Import Facebook Image
                            update_post_meta($post->ID, '_seopress_social_fb_img', $psp_meta['facebook_image']);
                        }
                        if ('noindex' == $psp_meta['robots_index']) { //Import Robots NoIndex
                            update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                        }
                        if ('nofollow' == $psp_meta['robots_follow']) { //Import Robots NoIndex
                            update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                        }
                        if ( ! empty($psp_meta['canonical'])) { //Import Canonical URL
                            update_post_meta($post->ID, '_seopress_robots_canonical', $psp_meta['canonical']);
                        }
                        if ( ! empty($psp_meta['mfocus_keyword'])) { //Import Focus Keywords
                            $target_kw = preg_split('/\r\n|\r|\n/', $psp_meta['mfocus_keyword']);

                            update_post_meta($post->ID, '_seopress_analysis_target_kw', implode(',', $target_kw));
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_premium_seo_pack_migration', 'seopress_premium_seo_pack_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* wpSEO migration
* @since 4.0
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_wpseo_migration() {
    check_ajax_referer('seopress_wpseo_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $args = [
                'hide_empty' => false,
                'fields'     => 'ids',
            ];
            $wpseo_query_terms = get_terms($args);

            if ($wpseo_query_terms) {
                foreach ($wpseo_query_terms as $term_id) {
                    if ('' != get_option('wpseo_category_' . $term_id . '_title')) { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_option('wpseo_category_' . $term_id . '_title'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id)) { //Import meta desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_option('wpseo_category_' . $term_id));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_title')) { //Import Facebook Title
                        update_term_meta($term_id, '_seopress_social_fb_title', get_option('wpseo_category_' . $term_id . '_og_title'));
                        update_term_meta($term_id, '_seopress_social_twitter_title', get_option('wpseo_category_' . $term_id . '_og_title'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_desc')) { //Import Facebook Desc
                        update_term_meta($term_id, '_seopress_social_fb_desc', get_option('wpseo_category_' . $term_id . '_og_desc'));
                        update_term_meta($term_id, '_seopress_social_twitter_desc', get_option('wpseo_category_' . $term_id . '_og_desc'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_image')) { //Import Facebook Image
                        update_term_meta($term_id, '_seopress_social_fb_img', get_option('wpseo_category_' . $term_id . '_og_image'));
                        update_term_meta($term_id, '_seopress_social_twitter_img', get_option('wpseo_category_' . $term_id . '_og_image'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_canonical')) { //Import Canonical URL
                        update_term_meta($term_id, '_seopress_robots_canonical', get_option('wpseo_category_' . $term_id . '_canonical'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_redirect')) { //Import Redirect URL
                        update_term_meta($term_id, '_seopress_redirections_value', get_option('wpseo_category_' . $term_id . '_redirect'));
                        update_term_meta($term_id, '_seopress_redirections_enabled', 'yes');
                    }
                    if ('4' == get_option('wpseo_category_' . $term_id . '_robots') || '5' == get_option('wpseo_category_' . $term_id . '_robots') || '3' == get_option('wpseo_category_' . $term_id . '_robots')) { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_index', 'yes');
                    }
                    if ('2' == get_option('wpseo_category_' . $term_id . '_robots')) { //Import Robots NoFollow
                        update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $wpseo_query = get_posts($args);

            if ($wpseo_query) {
                foreach ($wpseo_query as $post) {
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_wpseo_edit_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_wpseo_edit_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_wpseo_edit_og_title', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_wpseo_edit_og_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_wpseo_edit_og_description', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_wpseo_edit_og_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_wpseo_edit_og_image', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_wpseo_edit_og_image', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_keyword_0', true)) { //Import Target Keyword
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_post_meta($post->ID, '_wpseo_edit_keyword_0', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_wpseo_edit_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, '_wpseo_edit_redirect', true));
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes'); //Enable the redirect
                    }
                    if ('4' == get_post_meta($post->ID, '_wpseo_edit_robots', true) || '5' == get_post_meta($post->ID, '_wpseo_edit_robots', true) || '3' == get_post_meta($post->ID, '_wpseo_edit_robots', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('2' == get_post_meta($post->ID, '_wpseo_edit_robots', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_wpseo_migration', 'seopress_wpseo_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* Platinum SEO migration
* @since 4.5
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_platinum_seo_migration() {
    check_ajax_referer('seopress_platinum_seo_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $args = [
                'hide_empty' => false,
            ];
            $platinum_seo_query_terms = get_terms($args);

            if ($platinum_seo_query_terms) {
                foreach ($platinum_seo_query_terms as $term) {
                    if ( ! is_wp_error($term)) {
                        $tax = 'taxonomy';
                        if ('category' === $term->taxonomy) {
                            $tax = 'category';
                        }
                        if ('' != get_term_meta($term->term_id, 'psp_' . $tax . '_seo_metas_' . $term->term_id, true) || '' != get_term_meta($term->$term_id, 'psp_' . $tax . '_social_metas_' . $term->term_id, true)) {
                            $term_settings        = get_term_meta($term->term_id, 'psp_' . $tax . '_seo_metas_' . $term->term_id, true);
                            $term_social_settings = get_term_meta($term->term_id, 'psp_' . $tax . '_social_metas_' . $term->term_id, true);

                            if ( ! empty($term_settings['title'])) { //Import title tag
                                update_term_meta($term->term_id, '_seopress_titles_title', $term_settings['title']);
                            }
                            if ( ! empty($term_settings['description'])) { //Import meta desc
                                update_term_meta($term->term_id, '_seopress_titles_desc', $term_settings['description']);
                            }
                            if ( ! empty($term_social_settings['fb_title'])) { //Import Facebook Title
                                update_term_meta($term->term_id, '_seopress_social_fb_title', $term_social_settings['fb_title']);
                                update_term_meta($term->term_id, '_seopress_social_twitter_title', $term_social_settings['fb_title']);
                            }
                            if ( ! empty($term_social_settings['fb_description'])) { //Import Facebook Desc
                                update_term_meta($term->term_id, '_seopress_social_fb_desc', $term_social_settings['fb_description']);
                                update_term_meta($term->term_id, '_seopress_social_twitter_desc', $term_social_settings['fb_description']);
                            }
                            if ( ! empty($term_social_settings['fb_image'])) { //Import Facebook Image
                                update_term_meta($term->term_id, '_seopress_social_fb_img', $term_social_settings['fb_image']);
                                update_term_meta($term->term_id, '_seopress_social_twitter_img', $term_social_settings['fb_image']);
                            }
                            if ( ! empty($term_settings['canonical_url'])) { //Import Canonical URL
                                update_term_meta($term->term_id, '_seopress_robots_canonical', $term_settings['canonical_url']);
                            }
                            if ( ! empty($term_settings['redirect_to_url'])) { //Import Redirect URL
                                update_term_meta($term->term_id, '_seopress_redirections_value', $term_settings['redirect_to_url']);
                                update_term_meta($term->term_id, '_seopress_redirections_enabled', 'yes');
                                if ( ! empty($term_settings['redirect_status_code'])) {
                                    $status = $term_settings['redirect_status_code'];
                                    if ('303' === $term_settings['redirect_status_code']) {
                                        $status = '301';
                                    }

                                    update_term_meta($term->term_id, '_seopress_redirections_type', $status);
                                }
                            }
                            if ( ! empty($term_settings['noindex'])) { //Import Robots NoIndex
                                update_term_meta($term->term_id, '_seopress_robots_index', 'yes');
                            }
                            if ( ! empty($term_settings['nofollow'])) { //Import Robots NoFollow
                                update_term_meta($term->term_id, '_seopress_robots_follow', 'yes');
                            }
                            if ( ! empty($term_settings['noarchive'])) { //Import Robots NoArchive
                                update_term_meta($term->term_id, '_seopress_robots_archive', 'yes');
                            }
                            if ( ! empty($term_settings['nosnippet'])) { //Import Robots NoSnippet
                                update_term_meta($term->term_id, '_seopress_robots_snippet', 'yes');
                            }
                            if ( ! empty($term_settings['noimageindex'])) { //Import Robots NoImageIndex
                                update_term_meta($term->term_id, '_seopress_robots_imageindex', 'yes');
                            }
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $platinum_seo_query = get_posts($args);

            if ($platinum_seo_query) {
                foreach ($platinum_seo_query as $post) {
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_title', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_description', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_title', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_title', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_description', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_description', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_image', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_fb_image', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_keywords', true)) { //Import Target Keyword
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_keywords', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_canonical_url', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_canonical_url', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_redirect_to_url', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_value', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_redirect_to_url', true));
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes'); //Enable the redirect

                        if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_redirect_status_code', true)) {
                            $status = get_metadata('platinumseo', $post->ID, '_techblissonline_psp_redirect_status_code', true);
                            if ('303' === get_metadata('platinumseo', $post->ID, '_techblissonline_psp_redirect_status_code', true)) {
                                $status = '301';
                            }

                            update_term_meta($post->ID, '_seopress_redirections_type', $status);
                        }
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_noarchive', true)) { //Import Robots NoArchive
                        update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_nosnippet', true)) { //Import Robots NoSnippet
                        update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_noimageidx', true)) { //Import Robots NoImageIndex
                        update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_keywords', true)) { //Import Target Keywords
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_keywords', true));
                    }
                    if ('' != get_metadata('platinumseo', $post->ID, '_techblissonline_psp_preferred_term', true)) { //Import Primary category
                        if ('category' == get_metadata('platinumseo', $post->ID, '_techblissonline_psp_preferred_taxonomy', true) || 'product_cat' == get_metadata('platinumseo', $post->ID, '_techblissonline_psp_preferred_taxonomy', true)) {
                            update_post_meta($post->ID, '_seopress_robots_primary_cat', get_metadata('platinumseo', $post->ID, '_techblissonline_psp_preferred_term', true));
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_platinum_seo_migration', 'seopress_platinum_seo_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* SmartCrawl migration
* @since 4.5
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_smart_crawl_migration() {
    check_ajax_referer('seopress_smart_crawl_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $smart_crawl_query_terms = get_option('wds_taxonomy_meta');

            if ($smart_crawl_query_terms) {
                foreach ($smart_crawl_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ( ! empty($term_value['wds_title'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['wds_title']);
                        }
                        if ( ! empty($term_value['wds_desc'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['wds_desc']);
                        }
                        if ( ! empty($term_value['opengraph']['title'])) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['opengraph']['title']);
                        }
                        if ( ! empty($term_value['opengraph']['description'])) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['opengraph']['description']);
                        }
                        if ( ! empty($term_value['opengraph']['images'])) { //Import Facebook Image
                            $image_id = $term_value['opengraph']['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_term_meta($term_id, '_seopress_social_fb_img', $img_url);
                            }
                        }
                        if ( ! empty($term_value['twitter']['title'])) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', $term_value['twitter']['title']);
                        }
                        if ( ! empty($term_value['twitter']['description'])) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $term_value['twitter']['description']);
                        }
                        if ( ! empty($term_value['twitter']['images'])) { //Import Facebook Image
                            $image_id = $term_value['twitter']['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_term_meta($term_id, '_seopress_social_twitter_img', $img_url);
                            }
                        }
                        if ( ! empty($term_value['wds_noindex']) && 'noindex' == $term_value['wds_noindex']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($term_value['wds_nofollow']) && 'nofollow' == $term_value['wds_nofollow']) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                        if ('' != $term_value['wds_canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['wds_canonical']);
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $smart_crawl_query = get_posts($args);

            if ($smart_crawl_query) {
                foreach ($smart_crawl_query as $post) {
                    if ('' != get_post_meta($post->ID, '_wds_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_wds_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_wds_metadesc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_opengraph', true)) {
                        $_wds_opengraph = get_post_meta($post->ID, '_wds_opengraph', true);
                        if ( ! empty($_wds_opengraph['title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_wds_opengraph['title']); //Import Facebook Title
                        }
                        if ( ! empty($_wds_opengraph['description'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_wds_opengraph['description']);
                        }
                        if ( ! empty($_wds_opengraph['images'])) { //Import Facebook Image
                            $image_id = $_wds_opengraph['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_post_meta($post->ID, '_seopress_social_fb_img', $img_url);
                            }
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_wds_twitter', true)) {
                        $_wds_twitter = get_post_meta($post->ID, '_wds_twitter', true);
                        if ( ! empty($_wds_twitter['title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_wds_twitter['title']); //Import Twitter Title
                        }
                        if ( ! empty($_wds_twitter['description'])) { //Import Twitter Desc
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_wds_twitter['description']);
                        }
                        if ( ! empty($_wds_twitter['images'])) { //Import Twitter Image
                            $image_id = $_wds_twitter['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_post_meta($post->ID, '_seopress_social_twitter_img', $img_url);
                            }
                        }
                    }
                    if ('1' === get_post_meta($post->ID, '_wds_meta-robots-noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' === get_post_meta($post->ID, '_wds_meta-robots-nofollow', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_wds_meta-robots-adv', true)) {
                        $robots = get_post_meta($post->ID, '_wds_meta-robots-adv', true);
                        if ('' != $robots) {
                            $robots = explode(',', $robots);

                            if (in_array('noarchive', $robots)) { //Import Robots NoArchive
                                update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                            }
                            if (in_array('nosnippet', $robots)) { //Import Robots NoSnippet
                                update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                            }
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_wds_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_wds_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes');
                        update_post_meta($post->ID, '_seopress_redirections_type', '301');
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, '_wds_redirect', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_focus-keywords', true)) { //Import Focus Keywords
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_post_meta($post->ID, '_wds_focus-keywords', true));
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_smart_crawl_migration', 'seopress_smart_crawl_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* SEOPressor migration
* @since 4.5
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_seopressor_migration() {
    check_ajax_referer('seopress_seopressor_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $su_query = get_posts($args);

            if ($su_query) {
                foreach ($su_query as $post) {
                    if ( ! empty(get_post_meta($post->ID, '_seop_settings', true))) {
                        $_seop_settings = get_post_meta($post->ID, '_seop_settings', true);

                        if ( ! empty($_seop_settings['meta_title'])) { //Import title tag
                            update_post_meta($post->ID, '_seopress_titles_title', $_seop_settings['meta_title']);
                        }
                        if ( ! empty($_seop_settings['meta_description'])) { //Import meta desc
                            update_post_meta($post->ID, '_seopress_titles_desc', $_seop_settings['meta_description']);
                        }
                        if ( ! empty($_seop_settings['fb_title'])) { //Import Facebook Title
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_seop_settings['fb_title']);
                        }
                        if ( ! empty($_seop_settings['fb_description'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_seop_settings['fb_description']);
                        }
                        if ( ! empty($_seop_settings['fb_img'])) { //Import Facebook Image
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_seop_settings['fb_img']);
                        }
                        if ( ! empty($_seop_settings['tw_title'])) { //Import Twitter Title
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_seop_settings['tw_title']);
                        }
                        if ( ! empty($_seop_settings['tw_description'])) { //Import Twitter Desc
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_seop_settings['tw_description']);
                        }
                        if ( ! empty($_seop_settings['tw_image'])) { //Import Twitter Image
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_seop_settings['tw_image']);
                        }
                        if ( ! empty($_seop_settings['meta_rules'])) {
                            $robots = explode('#|#|#', $_seop_settings['meta_rules']);

                            if ( ! empty($robots)) {
                                if (in_array('noindex', $robots)) { //Import Robots NoIndex
                                    update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                                }
                                if (in_array('nofollow', $robots)) { //Import Robots NoFollow
                                    update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                                }
                                if (in_array('noarchive', $robots)) { //Import Robots NoArchive
                                    update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                                }
                                if (in_array('nosnippet', $robots)) { //Import Robots NoSnippet
                                    update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                                }
                                if (in_array('noodp', $robots)) { //Import Robots NoOdp
                                    update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
                                }
                                if (in_array('noimageindex', $robots)) { //Import Robots NoImageIndex
                                    update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                                }
                            }
                        }
                        if ('' != get_post_meta($post->ID, '_seop_kw_1', true) || '' != get_post_meta($post->ID, '_seop_kw_2', true) || '' != get_post_meta($post->ID, '_seop_kw_3', true)) { //Import Target Keyword
                            $kw   = [];
                            $kw[] = get_post_meta($post->ID, '_seop_kw_1', true);
                            $kw[] = get_post_meta($post->ID, '_seop_kw_2', true);
                            $kw[] = get_post_meta($post->ID, '_seop_kw_3', true);

                            $kw = implode(',', $kw);

                            if ( ! empty($kw)) {
                                update_post_meta($post->ID, '_seopress_analysis_target_kw', $kw);
                            }
                        }
                        if ( ! empty($_seop_settings['meta_canonical'])) { //Import Canonical URL
                            update_post_meta($post->ID, '_seopress_robots_canonical', $_seop_settings['meta_canonical']);
                        }
                        if ( ! empty($_seop_settings['meta_redirect'])) { //Import Redirect URL
                            update_post_meta($post->ID, '_seopress_redirections_value', $_seop_settings['meta_redirect']);
                            update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes'); //Enable the redirect
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seopressor_migration', 'seopress_seopressor_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Export SEOPress metadata to CSV
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_metadata_export() {
    check_ajax_referer('seopress_export_csv_metadata_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        $post_export = '';
        if (isset($_POST['post_export'])) {
            $post_export = esc_attr($_POST['post_export']);
        }

        $term_export = '';
        if (isset($_POST['term_export'])) {
            $term_export = esc_attr($_POST['term_export']);
        }

        //Get post types
        $seopress_get_post_types = [];
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            $seopress_get_post_types[] = $seopress_cpt_key;
        }

        //Get taxonomies
        $seopress_get_taxonomies = [];
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            $seopress_get_taxonomies[] = $seopress_tax_key;
        }

        global $wpdb;
        global $post;

        //Count posts
        $i     = 1;
        $sql   = '(';
        $count = count($seopress_get_post_types);
        foreach ($seopress_get_post_types as $cpt) {
            $sql .= '(post_type = "' . $cpt . '")';

            if ($i < $count) {
                $sql .= ' OR ';
            }

            ++$i;
        }
        $sql .= ')';

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*)
		FROM {$wpdb->posts}
		WHERE $sql
		AND (post_status = 'publish' OR post_status = 'pending' OR post_status = 'draft' OR post_status = 'auto-draft' OR post_status = 'future' OR post_status = 'private' OR post_status = 'inherit' OR post_status = 'trash') ");

        //Count terms
        $total_count_terms = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->terms}");

        $increment = 200;

        $csv          = '';
        $csv          = get_option('seopress_metadata_csv');
        $download_url = '';

        $settings['id']              = 				[];
        $settings['post_title']      =		[];
        $settings['url']             =				[];
        $settings['meta_title']      =		[];
        $settings['meta_desc']       =		[];
        $settings['fb_title']        =			[];
        $settings['fb_desc']         =			[];
        $settings['fb_img']          =			[];
        $settings['tw_title']        =			[];
        $settings['tw_desc']         =			[];
        $settings['tw_img']          =			[];
        $settings['noindex']         =			[];
        $settings['nofollow']        =			[];
        $settings['noodp']           =			[];
        $settings['noimageindex']    =		[];
        $settings['noarchive']       =		[];
        $settings['nosnippet']       =		[];
        $settings['canonical_url']   =	[];
        $settings['primary_cat']     =	[];
        $settings['redirect_active'] =	[];
        $settings['redirect_type']   =	[];
        $settings['redirect_url']    =		[];
        $settings['target_kw']       =		[];

        //Posts
        if ('done' != $post_export) {
            if ($offset > $total_count_posts) {
                wp_reset_query();
                //Reset offset once Posts export is done
                $offset = 0;
                update_option('seopress_metadata_csv', $csv);
                $post_export = 'done';
            } else {
                $args = [
                    'post_type'      => $seopress_get_post_types,
                    'posts_per_page' => $increment,
                    'offset'         => $offset,
                    'post_status'    => 'any',
                    'order'          => 'DESC',
                    'orderby'        => 'date',
                ];
                $args       = apply_filters('seopress_metadata_query_args', $args, $seopress_get_post_types, $increment, $offset);
                $meta_query = get_posts($args);

                if ($meta_query) {
                    // The Loop
                    foreach ($meta_query as $post) {
                        array_push($settings['id'], $post->ID);

                        array_push($settings['post_title'], $post->post_title);

                        array_push($settings['url'], get_permalink($post));

                        array_push($settings['meta_title'], get_post_meta($post->ID, '_seopress_titles_title', true));

                        array_push($settings['meta_desc'], get_post_meta($post->ID, '_seopress_titles_desc', true));

                        array_push($settings['fb_title'], get_post_meta($post->ID, '_seopress_social_fb_title', true));

                        array_push($settings['fb_desc'], get_post_meta($post->ID, '_seopress_social_fb_desc', true));

                        array_push($settings['fb_img'], get_post_meta($post->ID, '_seopress_social_fb_img', true));

                        array_push($settings['tw_title'], get_post_meta($post->ID, '_seopress_social_twitter_title', true));

                        array_push($settings['tw_desc'], get_post_meta($post->ID, '_seopress_social_twitter_desc', true));

                        array_push($settings['tw_img'], get_post_meta($post->ID, '_seopress_social_twitter_img', true));

                        array_push($settings['noindex'], get_post_meta($post->ID, '_seopress_robots_index', true));

                        array_push($settings['nofollow'], get_post_meta($post->ID, '_seopress_robots_follow', true));

                        array_push($settings['noodp'], get_post_meta($post->ID, '_seopress_robots_odp', true));

                        array_push($settings['noimageindex'], get_post_meta($post->ID, '_seopress_robots_imageindex', true));

                        array_push($settings['noarchive'], get_post_meta($post->ID, '_seopress_robots_archive', true));

                        array_push($settings['nosnippet'], get_post_meta($post->ID, '_seopress_robots_snippet', true));

                        array_push($settings['canonical_url'], get_post_meta($post->ID, '_seopress_robots_canonical', true));

                        array_push($settings['primary_cat'], get_post_meta($post->ID, 'seopress_robots_primary_cat', true));

                        array_push($settings['redirect_active'], get_post_meta($post->ID, '_seopress_redirections_enabled', true));

                        array_push($settings['redirect_type'], get_post_meta($post->ID, '_seopress_redirections_type', true));

                        array_push($settings['redirect_url'], get_post_meta($post->ID, '_seopress_redirections_value', true));

                        array_push($settings['target_kw'], get_post_meta($post->ID, '_seopress_analysis_target_kw', true));

                        $csv[] = array_merge(
                            $settings['id'],
                            $settings['post_title'],
                            $settings['url'],
                            $settings['meta_title'],
                            $settings['meta_desc'],
                            $settings['fb_title'],
                            $settings['fb_desc'],
                            $settings['fb_img'],
                            $settings['tw_title'],
                            $settings['tw_desc'],
                            $settings['tw_img'],
                            $settings['noindex'],
                            $settings['nofollow'],
                            $settings['noodp'],
                            $settings['noimageindex'],
                            $settings['noarchive'],
                            $settings['nosnippet'],
                            $settings['canonical_url'],
                            $settings['primary_cat'],
                            $settings['redirect_active'],
                            $settings['redirect_type'],
                            $settings['redirect_url'],
                            $settings['target_kw']
                        );

                        //Clean arrays
                        $settings['id']              =				[];
                        $settings['post_title']      =		[];
                        $settings['url']             =				[];
                        $settings['meta_title']      =		[];
                        $settings['meta_desc']       =		[];
                        $settings['fb_title']        =			[];
                        $settings['fb_desc']         =			[];
                        $settings['fb_img']          =			[];
                        $settings['tw_title']        =			[];
                        $settings['tw_desc']         =			[];
                        $settings['tw_img']          =			[];
                        $settings['noindex']         =			[];
                        $settings['nofollow']        =			[];
                        $settings['noodp']           =			[];
                        $settings['noimageindex']    =		[];
                        $settings['noarchive']       =		[];
                        $settings['nosnippet']       =		[];
                        $settings['canonical_url']   =	[];
                        $settings['primary_cat']     =	[];
                        $settings['redirect_active'] =	[];
                        $settings['redirect_type']   =	[];
                        $settings['redirect_url']    =		[];
                        $settings['target_kw']       =		[];
                    }
                }
                $offset += $increment;
                update_option('seopress_metadata_csv', $csv);
            }
        } elseif ('done' != $term_export) {
            //Terms
            if ($offset > $total_count_terms) {
                update_option('seopress_metadata_csv', $csv);
                $post_export = 'done';
                $term_export = 'done';
            } else {
                $args = [
                    'taxonomy'   => $seopress_get_taxonomies,
                    'number'     => $increment,
                    'offset'     => $offset,
                    'order'      => 'DESC',
                    'orderby'    => 'date',
                    'hide_empty' => false,
                ];

                $args = apply_filters('seopress_metadata_query_terms_args', $args, $seopress_get_taxonomies, $increment, $offset);

                $meta_query = get_terms($args);

                if ($meta_query) {
                    // The Loop
                    foreach ($meta_query as $term) {
                        array_push($settings['id'], $term->term_id);

                        array_push($settings['post_title'], $term->name);

                        array_push($settings['url'], get_term_link($term));

                        array_push($settings['meta_title'], get_term_meta($term->term_id, '_seopress_titles_title', true));

                        array_push($settings['meta_desc'], get_term_meta($term->term_id, '_seopress_titles_desc', true));

                        array_push($settings['fb_title'], get_term_meta($term->term_id, '_seopress_social_fb_title', true));

                        array_push($settings['fb_desc'], get_term_meta($term->term_id, '_seopress_social_fb_desc', true));

                        array_push($settings['fb_img'], get_term_meta($term->term_id, '_seopress_social_fb_img', true));

                        array_push($settings['tw_title'], get_term_meta($term->term_id, '_seopress_social_twitter_title', true));

                        array_push($settings['tw_desc'], get_term_meta($term->term_id, '_seopress_social_twitter_desc', true));

                        array_push($settings['tw_img'], get_term_meta($term->term_id, '_seopress_social_twitter_img', true));

                        array_push($settings['noindex'], get_term_meta($term->term_id, '_seopress_robots_index', true));

                        array_push($settings['nofollow'], get_term_meta($term->term_id, '_seopress_robots_follow', true));

                        array_push($settings['noodp'], get_term_meta($term->term_id, '_seopress_robots_odp', true));

                        array_push($settings['noimageindex'], get_term_meta($term->term_id, '_seopress_robots_imageindex', true));

                        array_push($settings['noarchive'], get_term_meta($term->term_id, '_seopress_robots_archive', true));

                        array_push($settings['nosnippet'], get_term_meta($term->term_id, '_seopress_robots_snippet', true));

                        array_push($settings['canonical_url'], get_term_meta($term->term_id, '_seopress_robots_canonical', true));

                        array_push($settings['redirect_active'], get_term_meta($term->term_id, '_seopress_redirections_enabled', true));

                        array_push($settings['redirect_type'], get_term_meta($term->term_id, '_seopress_redirections_type', true));

                        array_push($settings['redirect_url'], get_term_meta($term->term_id, '_seopress_redirections_value', true));

                        array_push($settings['target_kw'], get_term_meta($term->term_id, '_seopress_analysis_target_kw', true));

                        $csv[] = array_merge(
                            $settings['id'],
                            $settings['post_title'],
                            $settings['url'],
                            $settings['meta_title'],
                            $settings['meta_desc'],
                            $settings['fb_title'],
                            $settings['fb_desc'],
                            $settings['fb_img'],
                            $settings['tw_title'],
                            $settings['tw_desc'],
                            $settings['tw_img'],
                            $settings['noindex'],
                            $settings['nofollow'],
                            $settings['noodp'],
                            $settings['noimageindex'],
                            $settings['noarchive'],
                            $settings['nosnippet'],
                            $settings['canonical_url'],
                            $settings['redirect_active'],
                            $settings['redirect_type'],
                            $settings['redirect_url'],
                            $settings['target_kw']
                        );

                        //Clean arrays
                        $settings['id']              =				[];
                        $settings['post_title']      =		[];
                        $settings['url']             =				[];
                        $settings['meta_title']      =		[];
                        $settings['meta_desc']       =		[];
                        $settings['fb_title']        =			[];
                        $settings['fb_desc']         =			[];
                        $settings['fb_img']          =			[];
                        $settings['tw_title']        =			[];
                        $settings['tw_desc']         =			[];
                        $settings['tw_img']          =			[];
                        $settings['noindex']         =			[];
                        $settings['nofollow']        =			[];
                        $settings['noodp']           =			[];
                        $settings['noimageindex']    =		[];
                        $settings['noarchive']       =		[];
                        $settings['nosnippet']       =		[];
                        $settings['canonical_url']   =	[];
                        $settings['redirect_active'] =	[];
                        $settings['redirect_type']   =	[];
                        $settings['redirect_url']    =		[];
                        $settings['target_kw']       =		[];
                    }
                }
                $offset += $increment;
                $post_export = 'done';
                update_option('seopress_metadata_csv', $csv);
            }
        } else {
            $post_export = 'done';
            $term_export = 'done';
        }

        //Create download URL
        if ('done' == $post_export && 'done' == $term_export) {
            $args = array_merge($_POST, [
                'nonce'           => wp_create_nonce('seopress_csv_batch_export_nonce'),
                'page'            => 'seopress-import-export',
                'seopress_action' => 'seopress_download_batch_export',
            ]);

            $download_url = add_query_arg($args, admin_url('admin.php'));

            $offset = 'done';
        }

        //Return data to JSON
        $data                = [];
        $data['offset']      = $offset;
        $data['url']         = $download_url;
        $data['post_export'] = $post_export;
        $data['term_export'] = $term_export;
        wp_send_json_success($data);

        exit();
    }
}

add_action('wp_ajax_seopress_metadata_export', 'seopress_metadata_export');

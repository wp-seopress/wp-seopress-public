<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview()
{
    $docs = seopress_get_docs_links();

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
            $title      = '';
            $meta_desc  = '';
            $link       = '';
            $data       = [];

            //Save Target KWs
            if (! isset($_GET['is_elementor'])) {
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
                    $link = get_permalink((int) $seopress_get_the_id);
                    $link = add_query_arg('no_admin_bar', 1, $link);

                    $response = wp_remote_get($link, $args);
                    if (200 !== wp_remote_retrieve_response_code($response)) {
                        $link = get_permalink((int) $seopress_get_the_id);
                        $response = wp_remote_get($link, $args);
                    }
                } else {
                    $custom_args = ['no_admin_bar' => 1];

                    //Useful for Page / Theme builders
                    $custom_args = apply_filters('seopress_real_preview_custom_args', $custom_args);

					$link = add_query_arg('no_admin_bar', 1, get_preview_post_link((int) $seopress_get_the_id, $custom_args));

					$link = apply_filters('seopress_get_dom_link', $link, $seopress_get_the_id);

                    $response = wp_remote_get($link, $args);
                }
            } else { //Term taxonomy
                $link = get_term_link((int) $seopress_get_the_id, $seopress_tax_name);
                $response = wp_remote_get($link, $args);
            }

            $data['link_preview'] = $link;

            //Check for error
            if (is_wp_error($response) || '404' == wp_remote_retrieve_response_code($response)) {
                $data['title'] = __('To get your Google snippet preview, publish your post!', 'wp-seopress');
            } elseif (is_wp_error($response) || '401' == wp_remote_retrieve_response_code($response)) {
                $data['title']                   = sprintf(__('Your site is protected by an authentification. <a href="%s" target="_blank">Fix this</a> <span class="dashicons dashicons-external"></span>', 'wp-seopress'), $docs['google_preview']['authentification']);
            } else {
                $response = wp_remote_retrieve_body($response);

                if ($dom->loadHTML('<?xml encoding="utf-8" ?>' . $response)) {
                    if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output')) {
                        $data = get_post_meta($seopress_get_the_id, '_seopress_analysis_data', true) ? get_post_meta($seopress_get_the_id, '_seopress_analysis_data', true) : $data = [];

                        if (! empty($data)) {
                            $data = array_slice($data, 0, 3);
                        }
                    }

                    //Disable wptexturize
                    add_filter('run_wptexturize', '__return_false');

                    //Get post content (used for Words counter)
                    $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', $seopress_get_the_id));
                    $seopress_get_the_content = apply_filters('seopress_dom_analysis_get_post_content', $seopress_get_the_content);

                    //Cornerstone compatibility
                    if (is_plugin_active('cornerstone/cornerstone.php')) {
                        $seopress_get_the_content = get_post_field('post_content', $seopress_get_the_id);
                    }

                    //BeTheme is activated
                    $theme = wp_get_theme();
                    if ('betheme' == $theme->template || 'Betheme' == $theme->parent_theme) {
                        $seopress_get_the_content = $seopress_get_the_content . get_post_meta($seopress_get_the_id, 'mfn-page-items-seo', true);
                    }

                    //Themify compatibility
                    if (defined('THEMIFY_DIR') && method_exists('ThemifyBuilder_Data_Manager', '_get_all_builder_text_content')) {
                        global $ThemifyBuilder;
                        $builder_data = $ThemifyBuilder->get_builder_data($seopress_get_the_id);
                        $plain_text   = \ThemifyBuilder_Data_Manager::_get_all_builder_text_content($builder_data);
                        $plain_text   = do_shortcode($plain_text);

                        if ('' != $plain_text) {
                            $seopress_get_the_content = $plain_text;
                        }
                    }

                    //Add WC product excerpt
                    if ('product' == $seopress_get_post_type) {
                        $seopress_get_the_content =  $seopress_get_the_content . get_the_excerpt($seopress_get_the_id);
                    }

                    $seopress_get_the_content = apply_filters('seopress_content_analysis_content', $seopress_get_the_content, $seopress_get_the_id);

                    //Bricks compatibility
                    if (defined('BRICKS_DB_EDITOR_MODE') && ('bricks' == $theme->template || 'Bricks' == $theme->parent_theme)) {
                        $page_sections = get_post_meta($seopress_get_the_id, '_bricks_page_content', true);
                        $editor_mode   = get_post_meta($seopress_get_the_id, BRICKS_DB_EDITOR_MODE, true);

                        if (is_array($page_sections) && 'wordpress' !== $editor_mode) {
                            $seopress_get_the_content = Bricks\Frontend::render_sections($page_sections, $seopress_get_the_id, 'content', true);
                        }
                    }

                    //Get Target Keywords
                    if (isset($_GET['seopress_analysis_target_kw']) && ! empty($_GET['seopress_analysis_target_kw'])) {
                        $data['target_kws']          = esc_html(strtolower(stripslashes_deep($_GET['seopress_analysis_target_kw'])));
                        $seopress_analysis_target_kw = array_filter(explode(',', strtolower(get_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', true))));

                        $seopress_analysis_target_kw = apply_filters( 'seopress_content_analysis_target_keywords', $seopress_analysis_target_kw, $seopress_get_the_id );

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
                        if (! empty($meta_description)) {
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

                    if (! empty($og_title)) {
                        $data['og_title']['count'] = count($og_title);
                        foreach ($og_title as $key=>$mogtitle) {
                            $data['og_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogtitle->nodeValue)));
                        }
                    }

                    //OG:description
                    $og_desc = $xpath->query('//meta[@property="og:description"]/@content');

                    if (! empty($og_desc)) {
                        $data['og_desc']['count'] = count($og_desc);
                        foreach ($og_desc as $key=>$mog_desc) {
                            $data['og_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_desc->nodeValue)));
                        }
                    }

                    //OG:image
                    $og_img = $xpath->query('//meta[@property="og:image"]/@content');

                    if (! empty($og_img)) {
                        $data['og_img']['count'] = count($og_img);
                        foreach ($og_img as $key=>$mog_img) {
                            $data['og_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_img->nodeValue)));
                        }
                    }

                    //OG:url
                    $og_url = $xpath->query('//meta[@property="og:url"]/@content');

                    if (! empty($og_url)) {
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

                    if (! empty($og_site_name)) {
                        $data['og_site_name']['count'] = count($og_site_name);
                        foreach ($og_site_name as $key=>$mog_site_name) {
                            $data['og_site_name']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_site_name->nodeValue)));
                        }
                    }

                    //Twitter:title
                    $tw_title = $xpath->query('//meta[@name="twitter:title"]/@content');

                    if (! empty($tw_title)) {
                        $data['tw_title']['count'] = count($tw_title);
                        foreach ($tw_title as $key=>$mtw_title) {
                            $data['tw_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_title->nodeValue)));
                        }
                    }

                    //Twitter:description
                    $tw_desc = $xpath->query('//meta[@name="twitter:description"]/@content');

                    if (! empty($tw_desc)) {
                        $data['tw_desc']['count'] = count($tw_desc);
                        foreach ($tw_desc as $key=>$mtw_desc) {
                            $data['tw_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_desc->nodeValue)));
                        }
                    }

                    //Twitter:image
                    $tw_img = $xpath->query('//meta[@name="twitter:image"]/@content');

                    if (! empty($tw_img)) {
                        $data['tw_img']['count'] = count($tw_img);
                        foreach ($tw_img as $key=>$mtw_img) {
                            $data['tw_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_img->nodeValue)));
                        }
                    }

                    //Twitter:image:src
                    $tw_img = $xpath->query('//meta[@name="twitter:image:src"]/@content');

                    if (! empty($tw_img)) {
                        $count = null;
                        if (! empty($data['tw_img']['count'])) {
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
                    if (! empty($h1)) {
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
                        if (! empty($h2)) {
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
                        if (! empty($h3)) {
                            foreach ($h3 as $heading3) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b(' . $kw . ')\b#iu', $heading3->nodeValue, $m)) {
                                        $data['h3']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }

                        //Keywords density
                        if (! is_plugin_active('oxygen/functions.php') && ! function_exists('ct_template_output')) { //disable for Oxygen
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

                    if (! empty($imgs) && null != $imgs) {
                        //init
                        $img_without_alt = [];
                        $img_with_alt = [];
                        foreach ($imgs as $img) {
                            if ($img->hasAttribute('src')) {
                                if (! preg_match_all('#\b(avatar)\b#iu', $img->getAttribute('class'), $m)) {//Exclude avatars from analysis
                                    if ($img->hasAttribute('width') || $img->hasAttribute('height')) {
                                        if ($img->getAttribute('width') > 1 || $img->getAttribute('height') > 1) {//Ignore files with width and heigh <= 1
                                            if ('' === $img->getAttribute('alt') || ! $img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                                $img_without_alt[] .= $img->getAttribute('src');
                                            } else {
                                                $img_with_alt[] .= $img->getAttribute('src');
                                            }
                                        }
                                    } elseif ('' === $img->getAttribute('alt') || ! $img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                        $img_src = download_url($img->getAttribute('src'));
                                        if (false === is_wp_error($img_src)) {
                                            if (filesize($img_src) > 100) {//Ignore files under 100 bytes
                                                $img_without_alt[] .= $img->getAttribute('src');
                                            } else {
                                                $img_with_alt[] .= $img->getAttribute('src');
                                            }
                                            @unlink($img_src);
                                        }
                                    }
                                }
                            }
                            $data['img']['images']['without_alt'] = $img_without_alt;
                            $data['img']['images']['with_alt'] = $img_with_alt;
                        }
                    }

                    //Meta robots
                    $meta_robots = $xpath->query('//meta[@name="robots"]/@content');
                    if (! empty($meta_robots)) {
                        foreach ($meta_robots as $key=>$value) {
                            $data['meta_robots'][$key][] = esc_attr($value->nodeValue);
                        }
                    }

                    //Meta google noimageindex / nositelinkssearchbox
                    $meta_google = $xpath->query('//meta[@name="google"]/@content');
                    if (! empty($meta_google)) {
                        foreach ($meta_google as $key=>$mgnoimg) {
                            $data['meta_google'][$key][] = esc_attr($mgnoimg->nodeValue);
                        }
                    }

                    //nofollow links
                    $nofollow_links = $xpath->query("//a[contains(@rel, 'nofollow') and not(contains(@rel, 'ugc'))]");
                    if (! empty($nofollow_links)) {
                        foreach ($nofollow_links as $key=>$link) {
                            if (! preg_match_all('#\b(cancel-comment-reply-link)\b#iu', $link->getAttribute('id'), $m) && ! preg_match_all('#\b(comment-reply-link)\b#iu', $link->getAttribute('class'), $m)) {
                                $data['nofollow_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                            }
                        }
                    }
                }

                //outbound links
                $site_url       = wp_parse_url(get_home_url(), PHP_URL_HOST);
                $outbound_links = $xpath->query("//a[not(contains(@href, '" . $site_url . "'))]");
                if (! empty($outbound_links)) {
                    foreach ($outbound_links as $key=>$link) {
                        if (! empty(wp_parse_url($link->getAttribute('href'), PHP_URL_HOST))) {
                            $data['outbound_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                        }
                    }
                }

                //Internal links
                $permalink = get_permalink((int) $seopress_get_the_id);
                $args      = [
                    's'         => $permalink,
                    'post_type' => 'any',
                ];
                $internal_links = new WP_Query($args);

                if ($internal_links->have_posts()) {
                    $data['internal_links']['count'] = $internal_links->found_posts;

                    while ($internal_links->have_posts()) {
                        $internal_links->the_post();
                        $data['internal_links']['links'][get_the_ID()] = [get_the_permalink() => get_the_title()];
                    }
                }
                wp_reset_postdata();

                //Words Counter
                if (! is_plugin_active('oxygen/functions.php') && ! function_exists('ct_template_output')) { //disable for Oxygen
                    if ('' != $seopress_get_the_content) {
                        $data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $matches);

                        if (! empty($matches[0])) {
                            $words_counter_unique = count(array_unique($matches[0]));
                        } else {
                            $words_counter_unique = '0';
                        }
                        $data['words_counter_unique'] = $words_counter_unique;
                    }
                }

                //Get schemas
                $json_ld = $xpath->query('//script[@type="application/ld+json"]');
                if (! empty($json_ld)) {
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
function seopress_flush_permalinks()
{
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
//Dashboard drag and drop features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_dnd_features()
{
    check_ajax_referer('seopress_dnd_features_nonce');
    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['order']) && $_POST['order']) {
            $cards_order = get_option('seopress_dashboard_option_name');

            $cards_order['cards_order'] = $_POST['order'];

            update_option('seopress_dashboard_option_name', $cards_order);
        }
    }

    wp_send_json_success();
}
add_action('wp_ajax_seopress_dnd_features', 'seopress_dnd_features');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard News Panel
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_news()
{
    check_ajax_referer('seopress_news_nonce', $_POST['_ajax_nonce'], true);
    if (current_user_can(seopress_capability('manage_options', 'dashboard')) && is_admin()) {
        if (isset($_POST['news_max_items'])) {
            $seopress_dashboard_option_name                    = get_option('seopress_dashboard_option_name');
            $seopress_dashboard_option_name['news_max_items']  = intval($_POST['news_max_items']);
            update_option('seopress_dashboard_option_name', $seopress_dashboard_option_name, false);
        }
        exit();
    }
}
add_action('wp_ajax_seopress_news', 'seopress_news');

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
require_once __DIR__ . '/ajax-migrate/platinum.php';
require_once __DIR__ . '/ajax-migrate/wpseo.php';
require_once __DIR__ . '/ajax-migrate/premium-seo-pack.php';
require_once __DIR__ . '/ajax-migrate/wp-meta-seo.php';
require_once __DIR__ . '/ajax-migrate/seo-ultimate.php';
require_once __DIR__ . '/ajax-migrate/squirrly.php';
require_once __DIR__ . '/ajax-migrate/seo-framework.php';
require_once __DIR__ . '/ajax-migrate/yoast.php';

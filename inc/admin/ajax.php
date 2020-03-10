<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview() {
    check_ajax_referer( 'seopress_real_preview_nonce', $_GET['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability('edit_posts', 'real_preview' ) && is_admin() ) ) {

        //Get cookies
        if (isset($_COOKIE)) {
            $cookies = array();

            foreach ( $_COOKIE as $name => $value ) {
                if ( 'PHPSESSID' !== $name ) {
                    $cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
                }
            }
        }

        //Get post id
        if ( isset( $_GET['post_id'] ) ) {
            $seopress_get_the_id = $_GET['post_id'];
        }

        if (get_post_meta($seopress_get_the_id,'_seopress_redirections_enabled',true) =='yes') {
            $data['title'] = __('A redirect is active for this URL. Turn it off to get the Google preview and content analysis.', 'wp-seopress');
        } else {
            //Get cookies
            if (isset($_COOKIE)) { 
                $cookies = array();
        
                foreach ( $_COOKIE as $name => $value ) {
                    if ( 'PHPSESSID' !== $name ) {
                        $cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
                    }
                }
            }

            //Get post type
            if ( isset( $_GET['post_type'] ) ) {
                $seopress_get_post_type = $_GET['post_type'];
            }

            //Origin
            if ( isset( $_GET['origin'] ) ) {
                $seopress_origin = $_GET['origin'];
            }

            //Get post content (used for Words counter)
            $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', $seopress_get_the_id));

            //Themify compatibility
            if ( defined( 'THEMIFY_DIR' ) ) {
                $seopress_get_the_content = get_post_field('post_content', $seopress_get_the_id);
            }

            //Oxygen Builder compatibility
            if (is_plugin_active('oxygen/functions.php')) {
                $seopress_get_the_content = esc_attr(wp_strip_all_tags(do_shortcode(get_post_meta($seopress_get_the_id, 'ct_builder_shortcodes', true), true)));
            }

            $seopress_get_the_content = apply_filters('seopress_content_analysis_content', $seopress_get_the_content, $seopress_get_the_id);
            //Tax name
            if ( isset( $_GET['tax_name'] ) ) {
                $seopress_tax_name = $_GET['tax_name'];
            }

            //Init
            $title = '';
            $meta_desc = '';
            $data = array();

            //Save Target KWs
            if(isset($_GET['seopress_analysis_target_kw'])) {
                delete_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw');
                update_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', esc_html($_GET['seopress_analysis_target_kw']));
            }

            //DOM
            $dom = new DOMDocument();
            $internalErrors = libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = false;
            
            //Get source code
            $args = array(
                'blocking' => true,
                'timeout'  => 30,
                'sslverify'   => false,
            );

            if (isset($cookies) && !empty($cookies)) {
                $args['cookies'] = $cookies;
            }

            $args = apply_filters('seopress_real_preview_remote', $args);

            $data['title'] = $cookies;

            if ($seopress_origin =='post') { //Default: post type
                $response = wp_remote_get(get_preview_post_link((int)$seopress_get_the_id,array('no_admin_bar' => 1)), $args);
            } else { //Term taxonomy
                $response = wp_remote_get(get_term_link((int)$seopress_get_the_id, $seopress_tax_name), $args);
            }
            //Check for error
            if ( is_wp_error( $response ) || wp_remote_retrieve_response_code($response) =='404' ) {
                $data['title'] = __('To get your Google snippet preview, publish your post!', 'wp-seopress');
            } else {
                $response = wp_remote_retrieve_body($response);

                if($dom->loadHTML('<?xml encoding="utf-8" ?>' .$response)) {
                    //Get Target Keywords
                    if(isset($_GET['seopress_analysis_target_kw']) && !empty($_GET['seopress_analysis_target_kw'])) {
                        $data['target_kws'] = strtolower($_GET['seopress_analysis_target_kw']);
                        $seopress_analysis_target_kw = array_filter(explode(',', strtolower(get_post_meta($seopress_get_the_id,'_seopress_analysis_target_kw',true))));
                    }
                    $xpath = new DOMXPath($dom);

                    //Title
                    $list = $dom->getElementsByTagName("title");
                    if ($list->length > 0) {
                        $title = $list->item(0)->textContent;
                        $data['title'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($title)));
                        if(isset($_GET['seopress_analysis_target_kw']) && !empty($_GET['seopress_analysis_target_kw'])) {
                            foreach ($seopress_analysis_target_kw as $kw) {
                                if (preg_match_all('#\b('.$kw.')\b#iu', $data['title'], $m)) {
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

                    if(isset($_GET['seopress_analysis_target_kw']) && !empty($_GET['seopress_analysis_target_kw'])) {
                        if (!empty($meta_description)) {
                            foreach ($meta_description as $meta_desc) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b('.$kw.')\b#iu', $meta_desc->nodeValue, $m)) {
                                        $data['meta_description']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }
                    }
                    
                    //OG:title
                    $og_title = $xpath->query('//meta[@property="og:title"]/@content');
                    
                    if (!empty($og_title)) {
                        $data['og_title']['count'] = count($og_title);
                        foreach ($og_title as $key=>$mogtitle) {
                            $data['og_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogtitle->nodeValue)));
                        }
                    }

                    //OG:description
                    $og_desc = $xpath->query('//meta[@property="og:description"]/@content');

                    if (!empty($og_desc)) {
                        $data['og_desc']['count'] = count($og_desc);
                        foreach ($og_desc as $key=>$mog_desc) {
                            $data['og_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_desc->nodeValue)));
                        }
                    }

                    //OG:image
                    $og_img = $xpath->query('//meta[@property="og:image"]/@content');

                    if (!empty($og_img)) {
                        $data['og_img']['count'] = count($og_img);
                        foreach ($og_img as $key=>$mog_img) {
                            $data['og_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_img->nodeValue)));
                        }
                    }

                    //OG:url
                    $og_url = $xpath->query('//meta[@property="og:url"]/@content');

                    if (!empty($og_url)) {
                        $data['og_url']['count'] = count($og_url);
                        foreach ($og_url as $key=>$mog_url) {
                            $url = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_url->nodeValue)));
                            $data['og_url']['values'][] = $url;
                            $url = wp_parse_url($url);
                            $data['og_url']['host'] = $url['host'];
                        }
                    }

                    //OG:site_name
                    $og_site_name = $xpath->query('//meta[@property="og:site_name"]/@content');

                    if (!empty($og_site_name)) {
                        $data['og_site_name']['count'] = count($og_site_name);
                        foreach ($og_site_name as $key=>$mog_site_name) {
                            $data['og_site_name']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mog_site_name->nodeValue)));
                        }
                    }

                    //Twitter:title
                    $tw_title = $xpath->query('//meta[@name="twitter:title"]/@content');

                    if (!empty($tw_title)) {
                        $data['tw_title']['count'] = count($tw_title);
                        foreach ($tw_title as $key=>$mtw_title) {
                            $data['tw_title']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_title->nodeValue)));
                        }
                    }

                    //Twitter:description
                    $tw_desc = $xpath->query('//meta[@name="twitter:description"]/@content');

                    if (!empty($tw_desc)) {
                        $data['tw_desc']['count'] = count($tw_desc);
                        foreach ($tw_desc as $key=>$mtw_desc) {
                            $data['tw_desc']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_desc->nodeValue)));
                        }
                    }

                    //Twitter:image
                    $tw_img = $xpath->query('//meta[@name="twitter:image"]/@content');

                    if (!empty($tw_img)) {
                        $data['tw_img']['count'] = count($tw_img);
                        foreach ($tw_img as $key=>$mtw_img) {
                            $data['tw_img']['values'][] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtw_img->nodeValue)));
                        }
                    }

                    //Twitter:image:src
                    $tw_img = $xpath->query('//meta[@name="twitter:image:src"]/@content');

                    if (!empty($tw_img)) {
                        $count = NULL;
                        if (!empty($data['tw_img']['count'])) {
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

                    if(isset($_GET['seopress_analysis_target_kw']) && !empty($_GET['seopress_analysis_target_kw'])) {
                        //h1
                        $h1 = $xpath->query("//h1");
                        if (!empty($h1)) {
                            $data['h1']['nomatches']['count'] = count($h1);
                            foreach ($h1 as $heading1) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b('.$kw.')\b#iu', $heading1->nodeValue, $m)) {
                                        $data['h1']['matches'][$kw][] = $m[0];
                                    }
                                }
                                $data['h1']['values'][] = esc_attr($heading1->nodeValue);
                            }
                        }

                        //h2
                        $h2 = $xpath->query("//h2");
                        if (!empty($h2)) {
                            foreach ($h2 as $heading2) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b('.$kw.')\b#iu', $heading2->nodeValue, $m)) {
                                        $data['h2']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }

                        //h3
                        $h3 = $xpath->query("//h3");
                        if (!empty($h3)) {
                            foreach ($h3 as $heading3) {
                                foreach ($seopress_analysis_target_kw as $kw) {
                                    if (preg_match_all('#\b('.$kw.')\b#iu', $heading3->nodeValue, $m)) {
                                        $data['h3']['matches'][$kw][] = $m[0];
                                    }
                                }
                            }
                        }

                        //Keywords density
                        foreach ($seopress_analysis_target_kw as $kw) {
                            if (preg_match_all('#\b('.$kw.')\b#iu', strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $m)) {
                                $data['kws_density']['matches'][$kw][] = $m[0];
                            }
                        }

                        //Keywords in permalink
                        $post = get_post($seopress_get_the_id);
                        $kw_slug = urldecode($post->post_name);
                        $kw_slug = str_replace("-", " ", $kw_slug);

                        if (isset($kw_slug)) {
                            foreach ($seopress_analysis_target_kw as $kw) {
                                if (preg_match_all('#\b('.remove_accents($kw).')\b#iu', strip_tags(wp_filter_nohtml_kses($kw_slug)), $m)) {
                                    $data['kws_permalink']['matches'][$kw][] = $m[0];
                                }
                            }
                        }
                    }

                    //Images
                    /*Standard images*/
                    $imgs = $xpath->query("//img");

                    if (!empty($imgs) && $imgs !=NULL) {
                        //init
                        $data_img = array();
                        foreach ($imgs as $img) {
                            if ($img->hasAttribute('src')) {
                                if ($img->hasAttribute('width') || $img->hasAttribute('height')) {
                                    if ($img->getAttribute('width') > 1 || $img->getAttribute('height') > 1) {
                                        if ($img->getAttribute('alt') ==='' || !$img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                            $data_img[] .= $img->getAttribute('src');
                                        }
                                    }
                                } elseif ($img->getAttribute('alt') ==='' || !$img->hasAttribute('alt')) {//if alt is empty or doesn't exist
                                    $img_src = download_url($img->getAttribute('src'));
                                    if (is_wp_error($img_src) === false) {
                                        if (filesize($img_src) > 100) {//Ignore files under 100 bytes
                                            $data_img[] .= $img->getAttribute('src');
                                        }
                                    }
                                    unlink($img_src);
                                }
                            }
                            $data['img']['images'] = $data_img;
                        }
                    }

                    //Meta robots
                    $meta_robots = $xpath->query('//meta[@name="robots"]/@content');
                    if (!empty($meta_robots)) {
                        foreach ($meta_robots as $key=>$value) {
                            $data['meta_robots'][$key][] = esc_attr($value->nodeValue);
                        }
                    }

                    //Meta google noimageindex / nositelinkssearchbox
                    $meta_google = $xpath->query('//meta[@name="google"]/@content');
                    if (!empty($meta_google)) {
                        foreach ($meta_google as $key=>$mgnoimg) {
                            $data['meta_google'][$key][] = esc_attr($mgnoimg->nodeValue);
                        }
                    }

                    //nofollow links
                    $nofollow_links = $xpath->query("//a[contains(@rel, 'nofollow')]");
                    if (!empty($nofollow_links)) {
                        foreach ($nofollow_links as $key=>$link) {
                            $data['nofollow_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                        }
                    }
                }

                //outbound links
                $site_url  = wp_parse_url(get_home_url(), PHP_URL_HOST);
                $outbound_links = $xpath->query("//a[not(contains(@href, '".$site_url."'))]");
                if (!empty($outbound_links)) {
                    foreach ($outbound_links as $key=>$link) {
                        if (!empty(wp_parse_url($link->getAttribute('href'), PHP_URL_HOST))) {
                            $data['outbound_links'][$key][$link->getAttribute('href')] = esc_attr($link->nodeValue);
                        }
                    }
                }

                //Words Counter
                if ($seopress_get_the_content !='') {
                    $data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $matches);

                    $words_counter_unique = count(array_unique($matches[0]));
                    $data['words_counter_unique'] = $words_counter_unique;
                }

                //Get schemas
                $json_ld = $xpath->query( '//script[@type="application/ld+json"]' );
                if (!empty($json_ld)) {
                    foreach($json_ld as $node) {
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
        if(isset($data)){
            update_post_meta($seopress_get_the_id, '_seopress_analysis_data', $data);
        }

        //Return
        wp_send_json_success($data);
    }
}
add_action('wp_ajax_seopress_do_real_preview', 'seopress_do_real_preview');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Flush permalinks
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_flush_permalinks() {
    check_ajax_referer( 'seopress_flush_permalinks_nonce', $_GET['_ajax_nonce'], true );
    flush_rewrite_rules();
	die();
}
add_action('wp_ajax_seopress_flush_permalinks', 'seopress_flush_permalinks');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard toggle features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_toggle_features() {
    check_ajax_referer( 'seopress_toggle_features_nonce', $_POST['_ajax_nonce'], true );

	if ( isset( $_POST['feature']) && isset( $_POST['feature_value'] )) {
		$seopress_toggle_options = get_option('seopress_toggle');
		$seopress_toggle_options[$_POST['feature']] = $_POST['feature_value'];
		update_option('seopress_toggle', $seopress_toggle_options, 'yes');
	}
	die();
}
add_action('wp_ajax_seopress_toggle_features', 'seopress_toggle_features');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard hide notices
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_hide_notices() {
    check_ajax_referer( 'seopress_hide_notices_nonce', $_POST['_ajax_nonce'], true );

    if ( isset( $_POST['notice']) && isset( $_POST['notice_value'] )) {
        $seopress_notices_options = get_option('seopress_notices');
        $seopress_notices_options[$_POST['notice']] = $_POST['notice_value'];
        update_option('seopress_notices', $seopress_notices_options, 'yes');
    }
    die();
}
add_action('wp_ajax_seopress_hide_notices', 'seopress_hide_notices');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Yoast migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_yoast_migration() {
    check_ajax_referer( 'seopress_yoast_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset']) && isset( $_POST['offset'] )) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $yoast_query_terms = get_option('wpseo_taxonomy_meta');

            if ($yoast_query_terms) {

                foreach ($yoast_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ($term_value['wpseo_title'] !='') { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['wpseo_title']);
                        }
                        if ($term_value['wpseo_desc'] !='') { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['wpseo_desc']);
                        }
                        if ($term_value['wpseo_opengraph-title'] !='') { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['wpseo_opengraph-title']);
                        }
                        if ($term_value['wpseo_opengraph-description'] !='') { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['wpseo_opengraph-description']);
                        }
                        if ($term_value['wpseo_opengraph-image'] !='') { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', $term_value['wpseo_opengraph-image']);
                        }
                        if ($term_value['wpseo_twitter-title'] !='') { //Import Twitter Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', $term_value['wpseo_twitter-title']);
                        }
                        if ($term_value['wpseo_twitter-description'] !='') { //Import Twitter Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $term_value['wpseo_twitter-description']);
                        }
                        if ($term_value['wpseo_twitter-image'] !='') { //Import Twitter Image
                            update_term_meta($term_id, '_seopress_social_twitter_img', $term_value['wpseo_twitter-image']);
                        }
                        if ($term_value['wpseo_noindex'] =='noindex') { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', "yes");
                        }
                        if ($term_value['wpseo_canonical'] !='') { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['wpseo_canonical']);
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = array(
                'posts_per_page' => $increment,
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset,
            );

            $yoast_query = get_posts( $args );

            if ($yoast_query) {
                foreach ($yoast_query as $post) {
                    if (get_post_meta($post->ID, '_yoast_wpseo_title', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_yoast_wpseo_title', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_metadesc', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_yoast_wpseo_metadesc', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true) !='') { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true) !='') { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true) !='') { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true) !='') { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true) !='') { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true) !='') { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true) =='1') { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', "yes");
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true) =='1') { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', "yes");
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true) !='') { //Import Robots NoOdp, NoImageIndex, NoArchive, NoSnippet
                        $yoast_wpseo_meta_robots_adv = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true);

                        if (strpos($yoast_wpseo_meta_robots_adv, 'noodp') !== false) {
                        	update_post_meta($post->ID, '_seopress_robots_odp', "yes");
                    	}
                    	if (strpos($yoast_wpseo_meta_robots_adv, 'noimageindex') !== false) {
                        	update_post_meta($post->ID, '_seopress_robots_imageindex', "yes");
                        }
                        if (strpos($yoast_wpseo_meta_robots_adv, 'noarchive') !== false) {
                        	update_post_meta($post->ID, '_seopress_robots_archive', "yes");
                        }
                        if (strpos($yoast_wpseo_meta_robots_adv, 'nosnippet') !== false) {
                        	update_post_meta($post->ID, '_seopress_robots_snippet', "yes");
                        }
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_canonical', true) !='') { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_yoast_wpseo_canonical', true));
                    }
                    if (get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) !='' || get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true) !='') { //Import Focus Keywords
                        $y_fkws_clean = array(); //reset array

                        $y_fkws = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', false);

                        foreach ($y_fkws as $value) {
                            foreach (json_decode($value) as $key => $value) {
                                $y_fkws_clean[] .= $value->keyword;
                            }
                        }

                        $y_fkws_clean[] .= get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

                        update_post_meta($post->ID, '_seopress_analysis_target_kw', implode(',',$y_fkws_clean));
                    }
                }
            }
            $offset += $increment;
        }
        $data = array();
        $data['offset'] = $offset;
        wp_send_json_success($data);
    	die();
    }
}
add_action('wp_ajax_seopress_yoast_migration', 'seopress_yoast_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//AIO migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_aio_migration() {
    check_ajax_referer( 'seopress_aio_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset2']) && isset( $_POST['offset2'] )) {
            $offset2 = absint($_POST['offset2']);
        }

        global $wpdb;
        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );

        $increment = 200;
        global $post;

        if ($offset2 > $total_count_posts) {
            wp_reset_query();

            $args = array(
                //'number' => $increment,
                'hide_empty' => false,
                //'offset' => $offset2,
                'fields' => 'ids',
            );
            $aio_query_terms = get_terms($args);

            if ($aio_query_terms) {
                foreach ($aio_query_terms as $term_id) {
                    if (get_term_meta($term_id, '_aioseop_title', true) !='') { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_term_meta($term_id, '_aioseop_title', true));
                    }
                    if (get_term_meta($term_id, '_aioseop_description', true) !='') { //Import meta desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_term_meta($term_id, '_aioseop_description', true));
                    }
                    if (get_term_meta($term_id, '_aioseop_opengraph_settings', true) !='') { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_term_meta($term_id, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                            update_term_meta($term_id, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }
                    if (get_term_meta($term_id, '_aioseop_opengraph_settings', true) !='') { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_desc'])) {
                            update_term_meta($term_id, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                        }
                    }
                    if (get_term_meta($term_id, '_aioseop_opengraph_settings', true) !='') { //Import Facebook Image
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_term_meta($term_id, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }
                    if (get_term_meta($term_id, '_aioseop_opengraph_settings', true) !='') { //Import Twitter Image
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_term_meta($term_id, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }
                    if (get_term_meta($term_id, '_aioseop_noindex', true) =='on') { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_index', "yes");
                    }
                    if (get_term_meta($term_id, '_aioseop_nofollow', true) =='on') { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_follow', "yes");
                    }
                }
            }
            $offset2 = 'done';
            wp_reset_query();
        } else {
            $args = array(
                'posts_per_page' => $increment,
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset2,
            );

            $aio_query = get_posts( $args );

            if ($aio_query) {
                foreach ($aio_query as $post) {
                    if (get_post_meta($post->ID, '_aioseop_title', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseop_title', true));
                    }
                    if (get_post_meta($post->ID, '_aioseop_description', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseop_description', true));
                    }
                    if (get_post_meta($post->ID, '_aioseop_opengraph_settings', true) !='') { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }
                    if (get_post_meta($post->ID, '_aioseop_opengraph_settings', true) !='') { //Import Facebook / Twitter Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_desc'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                        }
                    }
                    if (get_post_meta($post->ID, '_aioseop_opengraph_settings', true) !='') { //Import Facebook Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }
                    if (get_post_meta($post->ID, '_aioseop_opengraph_settings', true) !='') { //Import Twitter Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }
                    if (get_post_meta($post->ID, '_aioseop_noindex', true) =='on') { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', "yes");
                    }
                    if (get_post_meta($post->ID, '_aioseop_nofollow', true) =='on') { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', "yes");
                    }
                }
            }
            $offset2 += $increment;
        }
        $data = array();
        $data['offset2'] = $offset2;
        wp_send_json_success($data);
        die();
    }
}
add_action('wp_ajax_seopress_aio_migration', 'seopress_aio_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEO Framework migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_seo_framework_migration() {
    check_ajax_referer( 'seopress_seo_framework_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset3']) && isset( $_POST['offset3'] )) {
            $offset3 = absint($_POST['offset3']);
        }

        global $wpdb;
        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );

        $increment = 200;
        global $post;

        if ($offset3 > $total_count_posts) {
            wp_reset_query();

            $args = array(
                //'number' => $increment,
                'hide_empty' => false,
                //'offset' => $offset3,
                'fields' => 'ids',
            );
            $seo_framework_query_terms = get_terms($args);

            if ($seo_framework_query_terms) {
                foreach ($seo_framework_query_terms as $term_id) {
                    if (get_term_meta($term_id, 'autodescription-term-settings', true) !='') {
                        $term_settings = get_term_meta($term_id, 'autodescription-term-settings', true);

                        if (!empty($term_settings['doctitle'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_settings['doctitle']);
                        }
                        if (!empty($term_settings['description'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_settings['description']);
                        }
                        if (!empty($term_settings['noindex'])) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', "yes");
                        }
                        if (!empty($term_settings['nofollow'])) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', "yes");
                        }
                        if (!empty($term_settings['noarchive'])) { //Import Robots NoArchive
                            update_term_meta($term_id, '_seopress_robots_archive', "yes");
                        }
                    }
                }
            }
            $offset3 = 'done';
            wp_reset_query();
        } else {
            $args = array(
                'posts_per_page' => $increment,
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset3,
            );

            $seo_framework_query = get_posts( $args );

            if ($seo_framework_query) {
                foreach ($seo_framework_query as $post) {
                    if (get_post_meta($post->ID, '_genesis_title', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_genesis_title', true));
                    }
                    if (get_post_meta($post->ID, '_genesis_description', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_genesis_description', true));
                    }
                    if (get_post_meta($post->ID, '_open_graph_title', true) !='') { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_open_graph_title', true));
                    }
                    if (get_post_meta($post->ID, '_open_graph_description', true) !='') { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_open_graph_description', true));
                    }
                    if (get_post_meta($post->ID, '_social_image_url', true) !='') { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_social_image_url', true));
                    }
                    if (get_post_meta($post->ID, '_twitter_title', true) !='') { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_twitter_title', true));
                    }
                    if (get_post_meta($post->ID, '_twitter_description', true) !='') { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_twitter_description', true));
                    }
                    if (get_post_meta($post->ID, '_social_image_url', true) !='') { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_social_image_url', true));
                    }
                    if (get_post_meta($post->ID, '_genesis_noindex', true) =='1') { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', "yes");
                    }
                    if (get_post_meta($post->ID, '_genesis_nofollow', true) =='1') { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', "yes");
                    }
                    if (get_post_meta($post->ID, '_genesis_noarchive', true) =='1') { //Import Robots NoArchive
                        update_post_meta($post->ID, '_seopress_robots_archive', "yes");
                    }
                    if (get_post_meta($post->ID, '_genesis_canonical_uri', true) !='') { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_genesis_canonical_uri', true));
                    }
                    if (get_post_meta($post->ID, 'redirect', true) !='') { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes');
                        update_post_meta($post->ID, '_seopress_redirections_type', '301');
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, 'redirect', true));
                    }
                }
            }
            $offset3 += $increment;
        }
        $data = array();
        $data['offset3'] = $offset3;
        wp_send_json_success($data);
        die();
    }
}
add_action('wp_ajax_seopress_seo_framework_migration', 'seopress_seo_framework_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//RK migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_rk_migration() {
    check_ajax_referer( 'seopress_rk_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset4']) && isset( $_POST['offset4'] )) {
            $offset4 = absint($_POST['offset4']);
        }

        global $wpdb;
        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );

        $increment = 200;
        global $post;

        if ($offset4 > $total_count_posts) {
            wp_reset_query();

            $args = array(
                'hide_empty' => false,
                'fields' => 'ids',
            );
            $rk_query_terms = get_terms($args);

            if ($rk_query_terms) {
                foreach ($rk_query_terms as $term_id) {
                    if (get_term_meta($term_id, 'rank_math_title', true) !='') { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_term_meta($term_id, 'rank_math_title', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_description', true) !='') { //Import title desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_term_meta($term_id, 'rank_math_description', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_facebook_title', true) !='') { //Import Facebook Title
                        update_term_meta($term_id, '_seopress_social_fb_title', get_term_meta($term_id, 'rank_math_facebook_title', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_facebook_description', true) !='') { //Import Facebook Desc
                        update_term_meta($term_id, '_seopress_social_fb_desc', get_term_meta($term_id, 'rank_math_facebook_description', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_facebook_image', true) !='') { //Import Facebook Image
                        update_term_meta($term_id, '_seopress_social_fb_img', get_term_meta($term_id, 'rank_math_facebook_image', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_twitter_title', true) !='') { //Import Twitter Title
                        update_term_meta($term_id, '_seopress_social_twitter_title', get_term_meta($term_id, 'rank_math_twitter_title', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_twitter_description', true) !='') { //Import Twitter Desc
                        update_term_meta($term_id, '_seopress_social_twitter_desc', get_term_meta($term_id, 'rank_math_twitter_description', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_twitter_image', true) !='') { //Import Twitter Image
                        update_term_meta($term_id, '_seopress_social_twitter_img', get_term_meta($term_id, 'rank_math_twitter_image', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_robots', true) !='') { //Import Robots NoIndex, NoFollow, NoOdp, NoImageIndex, NoArchive, NoSnippet
                        $rank_math_robots = get_term_meta($term_id, 'rank_math_robots', true);

                        if (in_array('noindex', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_index', "yes");
                        }
                        if (in_array('nofollow', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_follow', "yes");
                        }
                        if (in_array('noodp', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_odp', "yes");
                        }
                        if (in_array('noimageindex', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_imageindex', "yes");
                        }
                        if (in_array('noarchive', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_archive', "yes");
                        }
                        if (in_array('nosnippet', $rank_math_robots)) {
                            update_term_meta($term_id, '_seopress_robots_snippet', "yes");
                        }
                    }
                    if (get_term_meta($term_id, 'rank_math_canonical_url', true) !='') { //Import Canonical URL
                        update_term_meta($term_id, '_seopress_robots_canonical', get_term_meta($term_id, 'rank_math_canonical_url', true));
                    }
                    if (get_term_meta($term_id, 'rank_math_focus_keyword', true) !='') { //Import Focus Keywords
                        update_term_meta($term_id, '_seopress_analysis_target_kw', get_term_meta($term_id, 'rank_math_focus_keyword', true));
                    }


                }
            }
            $offset4 = 'done';
            wp_reset_query();
        } else {
            $args = array(
                'posts_per_page' => $increment,
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset4,
            );

            $rk_query = get_posts( $args );

            if ($rk_query) {
                foreach ($rk_query as $post) {
                    if (get_post_meta($post->ID, 'rank_math_title', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, 'rank_math_title', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_description', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, 'rank_math_description', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_facebook_title', true) !='') { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, 'rank_math_facebook_title', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_facebook_description', true) !='') { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, 'rank_math_facebook_description', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_facebook_image', true) !='') { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, 'rank_math_facebook_image', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_twitter_title', true) !='') { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, 'rank_math_twitter_title', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_twitter_description', true) !='') { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, 'rank_math_twitter_description', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_twitter_image', true) !='') { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, 'rank_math_twitter_image', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_robots', true) !='') { //Import Robots NoIndex, NoFollow, NoOdp, NoImageIndex, NoArchive, NoSnippet
                        $rank_math_robots = get_post_meta($post->ID, 'rank_math_robots', true);

                        if (in_array('noindex', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_index', "yes");
                        }
                        if (in_array('nofollow', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_follow', "yes");
                        }
                        if (in_array('noodp', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_odp', "yes");
                        }
                        if (in_array('noimageindex', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_imageindex', "yes");
                        }
                        if (in_array('noarchive', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_archive', "yes");
                        }
                        if (in_array('nosnippet', $rank_math_robots)) {
                            update_post_meta($post->ID, '_seopress_robots_snippet', "yes");
                        }
                    }
                    if (get_post_meta($post->ID, 'rank_math_canonical_url', true) !='') { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, 'rank_math_canonical_url', true));
                    }
                    if (get_post_meta($post->ID, 'rank_math_focus_keyword', true) !='') { //Import Focus Keywords
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_post_meta($post->ID, 'rank_math_focus_keyword', true));
                    }
                }
            }
            $offset4 += $increment;
        }
        $data = array();
        $data['offset4'] = $offset4;
        wp_send_json_success($data);
        die();
    }
}
add_action('wp_ajax_seopress_rk_migration', 'seopress_rk_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Squirrly migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_squirrly_migration() {
    check_ajax_referer( 'seopress_squirrly_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset5']) && isset( $_POST['offset5'] )) {
            $offset5 = absint($_POST['offset5']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'qss';
        $blog_id = get_current_blog_id();

        $count_query = $wpdb->get_results( "SELECT * FROM $table_name WHERE blog_id = $blog_id", ARRAY_A );

        if (!empty($count_query)) {
            foreach($count_query as $value) {
                $post_id = url_to_postid($value['URL']);

                if ($post_id !=0 && !empty($value['seo'])) {
                    $seo = maybe_unserialize($value['seo']);

                    if ($seo['title'] !='') { //Import title tag
                        update_post_meta($post_id, '_seopress_titles_title', $seo['title']);
                    }
                    if ($seo['description'] !='') { //Import description tag
                        update_post_meta($post_id, '_seopress_titles_desc', $seo['description']);
                    }
                    if ($seo['og_title'] !='') { //Import Facebook Title
                        update_post_meta($post_id, '_seopress_social_fb_title', $seo['og_title']);
                    }
                    if ($seo['og_description'] !='') { //Import Facebook Desc
                        update_post_meta($post_id, '_seopress_social_fb_desc', $seo['og_description']);
                    }
                    if ($seo['og_media'] !='') { //Import Facebook Image
                        update_post_meta($post_id, '_seopress_social_fb_img', $seo['og_media']);
                    }
                    if ($seo['tw_title'] !='') { //Import Twitter Title
                        update_post_meta($post_id, '_seopress_social_twitter_title', $seo['tw_title']);
                    }
                    if ($seo['tw_description'] !='') { //Import Twitter Desc
                        update_post_meta($post_id, '_seopress_social_twitter_desc', $seo['tw_description']);
                    }
                    if ($seo['tw_media'] !='') { //Import Twitter Image
                        update_post_meta($post_id, '_seopress_social_twitter_img', $seo['tw_media']);
                    }
                    if ($seo['noindex'] === 1) { //Import noindex
                        update_post_meta($post_id, '_seopress_robots_index', 'yes');
                    }
                    if ($seo['nofollow'] === 1) { //Import nofollow
                        update_post_meta($post_id, '_seopress_robots_follow', 'yes');
                    }
                    if ($seo['canonical'] !='') { //Import canonical
                        update_post_meta($post_id, '_seopress_robots_canonical', $seo['canonical']);
                    }
                }
            }
            $offset5 = 'done';
        }
        $data = array();
        $data['offset5'] = $offset5;
        wp_send_json_success($data);
        die();
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
    check_ajax_referer( 'seopress_seo_ultimate_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset7']) && isset( $_POST['offset7'] )) {
            $offset7 = absint($_POST['offset7']);
        }
        
        global $wpdb;

        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
        
        $increment = 200;
        global $post;
    	
        if ($offset7 > $total_count_posts) {
            $offset7 = 'done';
            wp_reset_query();
        } else {
            $args = array(  
                'posts_per_page' => $increment,  
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset7, 
            );
            
            $su_query = get_posts( $args );
            
            if ($su_query) {  
                foreach ($su_query as $post) {
                    if (get_post_meta($post->ID, '_su_title', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_su_title', true));
                    }
                    if (get_post_meta($post->ID, '_su_description', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_su_description', true));
                    }
                    if (get_post_meta($post->ID, '_su_og_title', true) !='') { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_su_og_title', true));
                    }
                    if (get_post_meta($post->ID, '_su_og_description', true) !='') { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_su_og_description', true));
                    }
                    if (get_post_meta($post->ID, '_su_og_image', true) !='') { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_su_og_image', true));
                    }
                    if (get_post_meta($post->ID, '_su_meta_robots_noindex', true) =='1') { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', "yes");
                    }
                    if (get_post_meta($post->ID, '_su_meta_robots_nofollow', true) =='1') { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', "yes");
                    }
                }
            }
            $offset7 += $increment;
        }
        $data = array();
        $data['offset7'] = $offset7;
        wp_send_json_success($data);
    	die();
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
    check_ajax_referer( 'seopress_meta_seo_migrate_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset8']) && isset( $_POST['offset8'] )) {
            $offset8 = absint($_POST['offset8']);
        }
        
        global $wpdb;
        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
        
        $increment = 200;
        global $post;
        
        if ($offset8 > $total_count_posts) {
            wp_reset_query();

            $args = array(
                'hide_empty' => false,
                'fields' => 'ids',
            );
            $wp_meta_seo_query_terms = get_terms($args);

            if ($wp_meta_seo_query_terms) { 
                foreach ($wp_meta_seo_query_terms as $term_id) {
                    if (get_term_meta($term_id, 'wpms_category_metatitle', true) !='') { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_term_meta($term_id, 'wpms_category_metatitle', true));
                    }
                    if (get_term_meta($term_id, 'wpms_category_metadesc', true) !='') { //Import title desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_term_meta($term_id, 'wpms_category_metadesc', true));
                    }
                }
            }
            $offset8 = 'done';
            wp_reset_query();
        } else {
            $args = array(  
                'posts_per_page' => $increment,
                'post_type' => 'any',
                'post_status' => 'any',
                'offset' => $offset8,
            );
            
            $wp_meta_seo_query = get_posts( $args );
            
            if ($wp_meta_seo_query) {  
                foreach ($wp_meta_seo_query as $post) {
                    if (get_post_meta($post->ID, '_metaseo_metatitle', true) !='') { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_metaseo_metatitle', true));
                    }
                    if (get_post_meta($post->ID, '_metaseo_metadesc', true) !='') { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_metaseo_metadesc', true));
                    }
                    if (get_post_meta($post->ID, '_metaseo_metaopengraph-title', true) !='') { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_metaseo_metaopengraph-title', true));
                    }            
                    if (get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true) !='') { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true));
                    }            
                    if (get_post_meta($post->ID, '_metaseo_metaopengraph-image', true) !='') { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_metaseo_metaopengraph-image', true));
                    }            
                    if (get_post_meta($post->ID, '_metaseo_metatwitter-title', true) !='') { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_metaseo_metatwitter-title', true));
                    }            
                    if (get_post_meta($post->ID, '_metaseo_metatwitter-desc', true) !='') { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_metaseo_metatwitter-desc', true));
                    }            
                    if (get_post_meta($post->ID, '_metaseo_metatwitter-image', true) !='') { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_metaseo_metatwitter-image', true));
                    }
                }
            }
            $offset8 += $increment;
        }
        $data = array();
        $data['offset8'] = $offset8;
        wp_send_json_success($data);
        die();
    }
}
add_action('wp_ajax_seopress_wp_meta_seo_migration', 'seopress_wp_meta_seo_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Export SEOPress metadata to CSV
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_metadata_export() {
    check_ajax_referer( 'seopress_export_csv_metadata_nonce', $_POST['_ajax_nonce'], true );

    if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) && is_admin() ) ) {

        if ( isset( $_POST['offset6']) && isset( $_POST['offset6'] )) {
            $offset6 = absint($_POST['offset6']);
        }

        $seopress_get_post_types = array();
        foreach (seopress_get_post_types() as $seopress_cpt_key => $seopress_cpt_value) {
            $seopress_get_post_types[] = $seopress_cpt_key;
        }

        global $wpdb;
        global $post;

        $total_count_posts = (int)$wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );

        $increment = 200;

        $csv = '';
        $csv = get_option('seopress_metadata_csv');
        $download_url = '';

        $settings["id"] = array();
        $settings["post_title"] = array();
        $settings["url"] = array();
        $settings["meta_title"] = array();
        $settings["meta_desc"] = array();
        $settings["fb_title"] = array();
        $settings["fb_desc"] = array();
        $settings["fb_img"] = array();
        $settings["tw_title"] = array();
        $settings["tw_desc"] = array();
        $settings["tw_img"] = array();
        $settings["noindex"] = array();
        $settings["nofollow"] = array();
        $settings["noodp"] = array();
        $settings["noimageindex"] = array();
        $settings["noarchive"] = array();
        $settings["nosnippet"] = array();
        $settings["canonical_url"] = array();
        $settings["target_kw"] = array();

        if ($offset6 > $total_count_posts) {
            wp_reset_query();

            update_option('seopress_metadata_csv', $csv);

            $args = array_merge( $_POST, array(
                'nonce'      => wp_create_nonce( 'seopress_csv_batch_export_nonce' ),
                'page' => 'seopress-import-export',
                'seopress_action' => 'seopress_download_batch_export',
            ) );

            $download_url = add_query_arg( $args, admin_url('admin.php') );

            $offset6 = 'done';
        } else {
            $args = array(
                'post_type' => $seopress_get_post_types,
                'posts_per_page' => $increment,
                'offset' => $offset6,
                'post_status' => 'any',
                'order' => 'DESC',
                'orderby' => 'date',
            );
            $args = apply_filters( 'seopress_metadata_query_args', $args, $seopress_get_post_types, $increment, $offset6 );
            $meta_query = get_posts( $args );

            if ($meta_query) {
                // The Loop
                foreach ($meta_query as $post) {
                    array_push($settings["id"], $post->ID);

                    array_push($settings["post_title"], $post->post_title);

                    array_push($settings["url"], get_permalink($post));

                    array_push($settings["meta_title"], get_post_meta( $post->ID, '_seopress_titles_title', true ));

                    array_push($settings["meta_desc"], get_post_meta( $post->ID, '_seopress_titles_desc', true ));

                    array_push($settings["fb_title"], get_post_meta( $post->ID, '_seopress_social_fb_title', true ));

                    array_push($settings["fb_desc"], get_post_meta( $post->ID, '_seopress_social_fb_desc', true ));

                    array_push($settings["fb_img"], get_post_meta( $post->ID, '_seopress_social_fb_img', true ));

                    array_push($settings["tw_title"], get_post_meta( $post->ID, '_seopress_social_twitter_title', true ));

                    array_push($settings["tw_desc"], get_post_meta( $post->ID, '_seopress_social_twitter_desc', true ));

                    array_push($settings["tw_img"], get_post_meta( $post->ID, '_seopress_social_twitter_img', true ));

                    array_push($settings["noindex"], get_post_meta( $post->ID, '_seopress_robots_index', true ));

                    array_push($settings["nofollow"], get_post_meta( $post->ID, '_seopress_robots_follow', true ));

                    array_push($settings["noodp"], get_post_meta( $post->ID, '_seopress_robots_odp', true ));

                    array_push($settings["noimageindex"], get_post_meta( $post->ID, '_seopress_robots_imageindex', true ));

                    array_push($settings["noarchive"], get_post_meta( $post->ID, '_seopress_robots_archive', true ));

                    array_push($settings["nosnippet"], get_post_meta( $post->ID, '_seopress_robots_snippet', true ));

                    array_push($settings["canonical_url"], get_post_meta( $post->ID, '_seopress_robots_canonical', true ));

                    array_push($settings["target_kw"], get_post_meta( $post->ID, '_seopress_analysis_target_kw', true ));

                    $csv[] = array_merge($settings["id"],$settings["post_title"],$settings["url"],$settings["meta_title"],$settings["meta_desc"],$settings["fb_title"],$settings["fb_desc"],$settings["fb_img"],$settings["tw_title"],$settings["tw_desc"],$settings["tw_img"],$settings["noindex"],$settings["nofollow"],$settings["noodp"],$settings["noimageindex"],$settings["noarchive"],$settings["nosnippet"],$settings["canonical_url"],$settings["target_kw"]);

                    //Clean arrays
                    $settings["id"] = array();
                    $settings["post_title"] = array();
                    $settings["url"] = array();
                    $settings["meta_title"] = array();
                    $settings["meta_desc"] = array();
                    $settings["fb_title"] = array();
                    $settings["fb_desc"] = array();
                    $settings["fb_img"] = array();
                    $settings["tw_title"] = array();
                    $settings["tw_desc"] = array();
                    $settings["tw_img"] = array();
                    $settings["noindex"] = array();
                    $settings["nofollow"] = array();
                    $settings["noodp"] = array();
                    $settings["noimageindex"] = array();
                    $settings["noarchive"] = array();
                    $settings["nosnippet"] = array();
                    $settings["canonical_url"] = array();
                    $settings["target_kw"] = array();

                }
            }
            $offset6 += $increment;
            update_option('seopress_metadata_csv', $csv);
        }

        $data = array();
        $data['offset6'] = $offset6;
        $data['url'] = $download_url;
        wp_send_json_success($data);

        die();
    }
}
add_action('wp_ajax_seopress_metadata_export', 'seopress_metadata_export');

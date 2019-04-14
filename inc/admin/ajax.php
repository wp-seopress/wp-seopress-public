<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get real preview + content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_real_preview() {            
    check_ajax_referer( 'seopress_real_preview_nonce', $_GET['_ajax_nonce'], true );

    if (current_user_can('edit_posts') && is_admin()) {

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

        //Get post type
        if ( isset( $_GET['post_type'] ) ) {
            $seopress_get_post_type = $_GET['post_type'];
        }

        //Origin
        if ( isset( $_GET['origin'] ) ) {
            $seopress_origin = $_GET['origin'];
        }

        //Tax name
        if ( isset( $_GET['tax_name'] ) ) {
            $seopress_tax_name = $_GET['tax_name'];
        }

        //Get post content (used for Words counter)
        $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', $seopress_get_the_id));
        $seopress_get_the_content = apply_filters('seopress_content_analysis_content', $seopress_get_the_content, $seopress_get_the_id);

        //Init
        $title = '';
        $meta_desc = '';
        $data = array();

        //Save Target KWs
        if(isset($_GET['seopress_analysis_target_kw']) && !empty($_GET['seopress_analysis_target_kw'])) {
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
                $seopress_analysis_target_kw = explode(',', get_post_meta($seopress_get_the_id,'_seopress_analysis_target_kw',true));

                $xpath = new DOMXPath($dom);

                //Title
                $list = $dom->getElementsByTagName("title");
                if ($list->length > 0) {
                    $title = $list->item(0)->textContent;
                    $data['title'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($title)));
                    foreach ($seopress_analysis_target_kw as $kw) {
                        if (preg_match_all('#\b('.$kw.')\b#iu', $data['title'], $m)) {
                            $data['meta_title']['matches'][$kw][] = $m[0];
                        }
                    }
                }

                //Meta desc
                $meta_description = $xpath->query('//meta[@name="description"]/@content');

                foreach ($meta_description as $key=>$mdesc) {
                    $data['meta_desc'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags($mdesc->nodeValue))));
                }

                if (!empty($meta_description)) {
                    foreach ($meta_description as $meta_desc) {
                        foreach ($seopress_analysis_target_kw as $kw) {
                            if (preg_match_all('#\b('.$kw.')\b#iu', $meta_desc->nodeValue, $m)) {
                                $data['meta_description']['matches'][$kw][] = $m[0];
                            }
                        }
                    }
                }

                //OG:title
                $og_title = $xpath->query('//meta[@property="og:title"]/@content');

                foreach ($og_title as $key=>$mogtitle) {
                    $data['og_title'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogtitle->nodeValue)));
                }

                //OG:description
                $og_desc = $xpath->query('//meta[@property="og:description"]/@content');

                foreach ($og_desc as $key=>$mogdesc) {
                    $data['og_desc'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogdesc->nodeValue)));
                }

                //OG:image
                $og_img = $xpath->query('//meta[@property="og:image"]/@content');

                foreach ($og_img as $key=>$mogimg) {
                    $data['og_img'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mogimg->nodeValue)));
                }

                //Twitter:title
                $tw_title = $xpath->query('//meta[@name="twitter:title"]/@content');

                foreach ($tw_title as $key=>$mtwtitle) {
                    $data['tw_title'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtwtitle->nodeValue)));
                }

                //Twitter:description
                $tw_desc = $xpath->query('//meta[@name="twitter:description"]/@content');

                foreach ($tw_desc as $key=>$mtwdesc) {
                    $data['tw_desc'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtwdesc->nodeValue)));
                }

                //Twitter:image
                $tw_img = $xpath->query('//meta[@name="twitter:image"]/@content');

                foreach ($tw_img as $key=>$mtwimg) {
                    $data['tw_img'] = esc_attr(stripslashes_deep(wp_filter_nohtml_kses($mtwimg->nodeValue)));
                }

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

                //Images
                /*Standard images*/
                $imgs = $xpath->query("//img");
                
                if (!empty($imgs) && $imgs !=NULL) {
                    //init
                    $data_img = array();
                    foreach ($imgs as $img) {
                        if ($img->getAttribute('alt') =='') {
                            $data_img[] .= $img->getAttribute('src');
                        }
                    }
                    $data['img']['images'] = $data_img;
                }

                //Meta robots
                $meta_robots = $xpath->query('//meta[@name="robots"]/@content');
                if (!empty($meta_robots)) {
                    foreach ($meta_robots as $key=>$value) {
                        $data['meta_robots'][$key][] = $value->nodeValue;
                    }
                }

                //nofollow links
                $nofollow_links = $xpath->query("//a[contains(@rel, 'nofollow')]");
                if (!empty($nofollow_links)) {
                    foreach ($nofollow_links as $key=>$link) {
                        $data['nofollow_links'][$key][] = $link->nodeValue;
                    }
                }

                //Words Counter
                if ($seopress_get_the_content !='') {
                    $data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $matches);

                    $words_counter_unique = count(array_unique($matches[0]));
                    $data['words_counter_unique'] = $words_counter_unique;
                }
            }
        }
        
        libxml_use_internal_errors($internalErrors);

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
	wp_remote_get(admin_url( 'options-permalink.php' ), array(blocking => true));
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

    if (current_user_can('manage_options') && is_admin()) { 

        if ( isset( $_POST['offset']) && isset( $_POST['offset'] )) {
            $offset = absint($_POST['offset']);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';
        $count_query = $wpdb->get_results( "SELECT * FROM $table_name" );
        $total_count_posts = $wpdb->num_rows;
        
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

    if (current_user_can('manage_options') && is_admin()) { 

        if ( isset( $_POST['offset2']) && isset( $_POST['offset2'] )) {
            $offset2 = absint($_POST['offset2']);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';
        $count_query = $wpdb->get_results( "SELECT * FROM $table_name" );
        $total_count_posts = $wpdb->num_rows;
        
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
    
    if (current_user_can('manage_options') && is_admin()) { 

        if ( isset( $_POST['offset3']) && isset( $_POST['offset3'] )) {
            $offset3 = absint($_POST['offset3']);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';
        $count_query = $wpdb->get_results( "SELECT * FROM $table_name" );
        $total_count_posts = $wpdb->num_rows;
        
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

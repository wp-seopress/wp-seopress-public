<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Content analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_do_content_analysis() {
    check_ajax_referer( 'seopress_content_analysis_nonce', $_POST['_ajax_nonce'], true );

    //Init variables
    $seopress_analysis_data = array();

    //Get post id
    if ( isset( $_POST['post_id'] ) ) {
        $seopress_get_the_id = $_POST['post_id'];
    }

    //Get post type
    if ( isset( $_POST['post_type'] ) ) {
        $seopress_get_post_type = $_POST['post_type'];
    }

    //Save Target KWs
    if(isset($_POST['seopress_analysis_target_kw']) && !empty($_POST['seopress_analysis_target_kw'])) {
        delete_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw');
        update_post_meta($seopress_get_the_id, '_seopress_analysis_target_kw', esc_html($_POST['seopress_analysis_target_kw']));
    }

    //Get post content
    $seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', $seopress_get_the_id));

    //Get Target Keywords
    $seopress_analysis_target_kw = array_filter(array_map('trim', explode(',', get_post_meta($seopress_get_the_id,'_seopress_analysis_target_kw',true))));

    //Get Post Title
    $seopress_get_the_title = get_post_field('post_title', $seopress_get_the_id);
    if ($seopress_get_the_title !='') {
        $data_post_title_clean = explode(' ',implode(' ', (array)$seopress_get_the_title));
        $seopress_analysis_data['post_title'][] = array_intersect($data_post_title_clean, $seopress_analysis_target_kw);
    }

    //Get Meta Title
    $seopress_titles_title = get_post_meta($seopress_get_the_id, '_seopress_titles_title', true);
    if ($seopress_titles_title !='') {
        $data_title_clean = explode(' ',implode(' ', (array)$seopress_titles_title));
        $seopress_analysis_data['title'][] = array_intersect($data_title_clean, $seopress_analysis_target_kw);
    }

    //Get Meta Description
    $seopress_titles_desc = get_post_meta($seopress_get_the_id, '_seopress_titles_desc', true);
    if ($seopress_titles_desc !='') {
        $data_desc_clean = explode(' ',implode(' ', (array)$seopress_titles_desc));
        $seopress_analysis_data['desc'][] = array_intersect($data_desc_clean, $seopress_analysis_target_kw);
    }

    //DomDocument
    $dom = new domDocument;
    $internalErrors = libxml_use_internal_errors(true);
    $dom->loadHTML($seopress_get_the_content);
    $dom->preserveWhiteSpace = false;
    $domxpath = new DOMXPath($dom);

    //Words counter
    $seopress_analysis_data['words_counter'] = str_word_count(strip_tags($seopress_get_the_content));
    $words_counter_unique = count(array_unique(str_word_count($seopress_get_the_content, 1)));
    $seopress_analysis_data['words_counter_unique'] = $words_counter_unique - 1;
    
    //h1
    $h1 = $domxpath->query("//h1");

    if (!empty($h1)) {
        
        foreach ($h1 as $heading1) {
            $data_h1[] .= $heading1->nodeValue;
        }
        $seopress_analysis_data['h1'] = $data_h1;
    }  

    //h2
    $h2 = $domxpath->query("//h2");

    if (!empty($h2)) {
        
        foreach ($h2 as $heading2) {
            $data_h2[] .= $heading2->nodeValue;
        }
        $data_h2_clean = explode(' ',implode(' ', $data_h2));

        $seopress_analysis_data['h2'][] = array_intersect($data_h2_clean, $seopress_analysis_target_kw);
    }

    //h3
    $h3 = $domxpath->query("//h3");

    if (!empty($h3)) {
        
        foreach ($h3 as $heading3) {
            $data_h3[] .= $heading3->nodeValue;
        }
        $data_h3_clean = explode(' ',implode(' ', $data_h3));

        $seopress_analysis_data['h3'][] = array_intersect($data_h3_clean, $seopress_analysis_target_kw);
    }

    //Images
    /*Standard images*/
    $imgs = $domxpath->query("//img");
    
    if (!empty($imgs) && $imgs !=NULL) {
        foreach ($imgs as $img) {
            if ($img->getAttribute('alt') =='') {
                $data_img[] .= $img->getAttribute('src');
            }
        }
        $seopress_analysis_data['img']['images'] = $data_img;
    }

    /*WooCommerce*/
    if  ( 'product' == $seopress_get_post_type ) {
        $product_id = $seopress_get_the_id;
        $product = new WC_product($product_id);
        $product_img_ids = $product->get_gallery_image_ids();
        if (!empty($product_img_ids)) {
            foreach ($product_img_ids as $product_img_id) {
                $alt = get_post_meta($product_img_id, '_wp_attachment_image_alt', true);
                if ($alt =='') {
                    $seopress_analysis_data['img']['product_img'][] .= wp_get_attachment_thumb_url($product_img_id);
                }
            }
        }
    }

    /*Post Thumbnail*/
    if (has_post_thumbnail($seopress_get_the_id)) {
        $thumbnail_id = get_post_thumbnail_id($seopress_get_the_id);
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        if ($alt =='') {
            $seopress_analysis_data['img']['post_thumbnail'] = get_the_post_thumbnail($seopress_get_the_id);
        }
    }

    //nofollow links
    $nofollow_links = $domxpath->query("//a[@rel='nofollow']"); //AMELIORER CHECK SI PLUSIEURS ATTR
    foreach ($nofollow_links as $key=>$link) {
        $seopress_analysis_data['nofollow_links'][$key] .= $link->nodeValue;
    }

    libxml_use_internal_errors($internalErrors);

    //Send data
    if(isset($seopress_analysis_data)){
        update_post_meta($seopress_get_the_id, '_seopress_analysis_data', $seopress_analysis_data);
    }
    wp_send_json_success();
}
add_action('wp_ajax_seopress_do_content_analysis', 'seopress_do_content_analysis');

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

    global $post;
	
    $args = array(  
        'posts_per_page' => '-1',  
        'post_type' => 'any',  
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

    wp_reset_query();

	die();
}
add_action('wp_ajax_seopress_yoast_migration', 'seopress_yoast_migration');


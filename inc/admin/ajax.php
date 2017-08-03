<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Flush permalinks
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_flush_permalinks() {
	flush_rewrite_rules();
	die();
}
add_action('wp_ajax_seopress_flush_permalinks', 'seopress_flush_permalinks');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Dashboard toggle features
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_toggle_features() {
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


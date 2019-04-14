<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//WPML
add_filter( 'seopress_sitemaps_index_cpt_query', function( $args ) {
    global $sitepress, $sitepress_settings;

    $sitepress_settings['auto_adjust_ids'] = 0;
    remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
    remove_filter( 'category_link', array( $sitepress, 'category_link_adjust_id' ), 1 );

    return $args;
});

function seopress_xml_sitemap_index() {
	$seopress_sitemaps ='<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .='<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	//CPT
	if (seopress_xml_sitemap_post_types_list_option() !='') {
		foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
			foreach ($cpt_value as $_cpt_key => $_cpt_value) {
				if($_cpt_value =='1') {
					$args = array('post_type' => $cpt_key, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'posts_per_page' => 1, 'meta_key' => '_seopress_robots_index', 'meta_value' => 'yes', 'meta_compare' => 'NOT EXISTS', 'order' => 'DESC', 'orderby' => 'modified', 'lang' => '', 'has_password' => false, 'suppress_filters' => true);
					
					$args = apply_filters('seopress_sitemaps_index_cpt_query', $args, $cpt_key);

					$get_latest_post = new WP_Query($args);

				    if($get_latest_post->have_posts()){
						$seopress_sitemaps .= "\n";
						$seopress_sitemaps .= '<sitemap>';
						$seopress_sitemaps .= "\n";
						$seopress_sitemaps .= '<loc>';
						$seopress_sitemaps .= home_url().'/sitemaps/'.$cpt_key.'.xml';
						$seopress_sitemaps .= '</loc>';
				    	$seopress_sitemaps .= "\n";
						$seopress_sitemaps .= '<lastmod>';
				        $seopress_sitemaps .= date("c", strtotime($get_latest_post->posts[0]->post_modified));
				        $seopress_sitemaps .= '</lastmod>';
						$seopress_sitemaps .= "\n";
						$seopress_sitemaps .= '</sitemap>';
					}
				}
			}
		}
	}

	//Taxonomies
	if (seopress_xml_sitemap_taxonomies_list_option() !='') {
		//Init
		$seopress_xml_terms_list = array();
		foreach (seopress_xml_sitemap_taxonomies_list_option() as $tax_key => $tax_value) {
			foreach ($tax_value as $_tax_key => $_tax_value) {
				if($_tax_value =='1') {
					$seopress_xml_terms_list[] .= $tax_key;
				}
			}
		}
		foreach ($seopress_xml_terms_list as $term_value) {
			$args = array(
			    'taxonomy' => $term_value,
			    'hide_empty' => false,
			    'lang' => ''
			);
			$args = apply_filters('seopress_sitemaps_index_tax_query', $args, $term_value);
			
			$terms = get_terms($args);
			
			if (!empty($terms)) {
				$seopress_sitemaps .= "\n";
				$seopress_sitemaps .= '<sitemap>';
				$seopress_sitemaps .= "\n";
				$seopress_sitemaps .= '<loc>';
				$seopress_sitemaps .= home_url().'/sitemaps/'.$term_value.'.xml';
				$seopress_sitemaps .= '</loc>';
				$seopress_sitemaps .= "\n";
				$seopress_sitemaps .= '</sitemap>';
			}
		}
	}

	//Google News
	if (function_exists("seopress_xml_sitemap_news_enable_option") && seopress_xml_sitemap_news_enable_option() !='' 
		&& function_exists('seopress_get_toggle_news_option') && seopress_get_toggle_news_option() =='1') {
		//Include Custom Post Types
		function seopress_xml_sitemap_news_cpt_option() {
	    	$seopress_xml_sitemap_news_cpt_option = get_option("seopress_pro_option_name");
		    if ( ! empty ( $seopress_xml_sitemap_news_cpt_option ) ) {
		        foreach ($seopress_xml_sitemap_news_cpt_option as $key => $seopress_xml_sitemap_news_cpt_value)
		            $options[$key] = $seopress_xml_sitemap_news_cpt_value;
		         if (isset($seopress_xml_sitemap_news_cpt_option['seopress_news_name_post_types_list'])) { 
		            return $seopress_xml_sitemap_news_cpt_option['seopress_news_name_post_types_list'];
		         }
		    }
		}
		if (seopress_xml_sitemap_news_cpt_option() !='') {
			$seopress_xml_sitemap_news_cpt_array = array();
		    foreach (seopress_xml_sitemap_news_cpt_option() as $cpt_key => $cpt_value) {
		        foreach ($cpt_value as $_cpt_key => $_cpt_value) {
		            if($_cpt_value =='1') {
		                array_push($seopress_xml_sitemap_news_cpt_array, $cpt_key);
		            }
		        }
		    }
		}
		
		$args = array('post_type' => $seopress_xml_sitemap_news_cpt_array, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'posts_per_page' => 1, 'orderby' => 'modified', 'meta_key' => '_seopress_robots_index', 'meta_value' => 'yes', 'meta_compare' => 'NOT EXISTS', 'order' => 'DESC', 'lang' => '', 'has_password' => false);

		$args = apply_filters('seopress_sitemaps_index_gnews_query', $args);

		$get_latest_post = new WP_Query($args);
	    if($get_latest_post->have_posts()){
	    	$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '<sitemap>';
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '<loc>';
			$seopress_sitemaps .= home_url().'/sitemaps/news.xml';
			$seopress_sitemaps .= '</loc>';
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '<lastmod>';
			$seopress_sitemaps .= date("c", strtotime($get_latest_post->posts[0]->post_modified));
			$seopress_sitemaps .= '</lastmod>';
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '</sitemap>';
		}
	}

	//Video sitemap
	if (function_exists("seopress_xml_sitemap_video_enable_option") && seopress_xml_sitemap_video_enable_option() !='') {
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<sitemap>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<loc>';
		$seopress_sitemaps .= home_url().'/sitemaps/video.xml';
		$seopress_sitemaps .= '</loc>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '</sitemap>';
	}

	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .='</sitemapindex>';
	
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_index();
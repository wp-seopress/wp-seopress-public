<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML/HTML Sitemap
//=================================================================================================
//HTML Sitemap Enable
function seopress_xml_sitemap_html_enable_option() {
	$seopress_xml_sitemap_html_enable_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_enable_option ) ) {
		foreach ($seopress_xml_sitemap_html_enable_option as $key => $seopress_xml_sitemap_html_enable_value)
			$options[$key] = $seopress_xml_sitemap_html_enable_value;
		 if (isset($seopress_xml_sitemap_html_enable_option['seopress_xml_sitemap_html_enable'])) { 
		 	return $seopress_xml_sitemap_html_enable_option['seopress_xml_sitemap_html_enable'];
		 }
	}
}

//HTML Sitemap mapping
function seopress_xml_sitemap_html_mapping_option() {
	$seopress_xml_sitemap_html_mapping_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_mapping_option ) ) {
		foreach ($seopress_xml_sitemap_html_mapping_option as $key => $seopress_xml_sitemap_html_mapping_value)
			$options[$key] = $seopress_xml_sitemap_html_mapping_value;
		 if (isset($seopress_xml_sitemap_html_mapping_option['seopress_xml_sitemap_html_mapping'])) { 
		 	return $seopress_xml_sitemap_html_mapping_option['seopress_xml_sitemap_html_mapping'];
		 }
	}
}

//HTML Sitemap Exclude
function seopress_xml_sitemap_html_exclude_option() {
	$seopress_xml_sitemap_html_exclude_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_exclude_option ) ) {
		foreach ($seopress_xml_sitemap_html_exclude_option as $key => $seopress_xml_sitemap_html_exclude_value)
			$options[$key] = $seopress_xml_sitemap_html_exclude_value;
		 if (isset($seopress_xml_sitemap_html_exclude_option['seopress_xml_sitemap_html_exclude'])) { 
		 	return $seopress_xml_sitemap_html_exclude_option['seopress_xml_sitemap_html_exclude'];
		 }
	}
}

if (seopress_xml_sitemap_html_enable_option() =='1' && seopress_xml_sitemap_html_mapping_option() !='') {
	function seopress_xml_sitemap_html_display() {
		if(is_page(explode(',', seopress_xml_sitemap_html_mapping_option()))) {	
			function seopress_xml_sitemap_html_hook($content) {
				if (seopress_xml_sitemap_html_exclude_option() !='') {
					$seopress_xml_sitemap_html_exclude_option = seopress_xml_sitemap_html_exclude_option();
				} else {
					$seopress_xml_sitemap_html_exclude_option = '';
				}
				//CPT
				if (seopress_xml_sitemap_post_types_list_option() !='') {
					$content .= '<div class="wrap-html-sitemap">';
					foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
						$obj = get_post_type_object( $cpt_key );
						$content .= '<h2>'.$obj->labels->singular_name.'</h2>';
						foreach ($cpt_value as $_cpt_key => $_cpt_value) {
							$content .= '<ul>';
							if($_cpt_value =='1') {
						    	$args = array( 'posts_per_page' => 1000, 'order'=> 'DESC', 'orderby' => 'date', 'post_type' => $cpt_key, 'post_status' => 'publish', 'meta_key' => '_seopress_robots_index', 'meta_value' => 'yes', 'meta_compare' => 'NOT EXISTS', 'fields' => 'ids', 'exclude' => $seopress_xml_sitemap_html_exclude_option );
								$postslist = get_posts( $args );
								foreach ( $postslist as $post ) {
								  	setup_postdata( $post );
								  	$content .= '<li>';
									$content .= '<a href="'.get_permalink($post).'">'.get_the_title($post).'</a>';
									$content .= ' - '.get_the_date('j F Y', $post);
									$content .= '</li>';
								}
								wp_reset_postdata();
							}
							$content .= '</ul>';
						}
					}
					$content .= '</div>';
				}
			    
			    return $content;
			}
			add_filter('the_content', 'seopress_xml_sitemap_html_hook');
		}
	}
	add_action('wp_head', 'seopress_xml_sitemap_html_display');
}
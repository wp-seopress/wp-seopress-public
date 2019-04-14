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

//HTML Sitemap Order
function seopress_xml_sitemap_html_order_option() {
	$seopress_xml_sitemap_html_order_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_order_option ) ) {
		foreach ($seopress_xml_sitemap_html_order_option as $key => $seopress_xml_sitemap_html_order_value)
			$options[$key] = $seopress_xml_sitemap_html_order_value;
		 if (isset($seopress_xml_sitemap_html_order_option['seopress_xml_sitemap_html_order'])) { 
		 	return $seopress_xml_sitemap_html_order_option['seopress_xml_sitemap_html_order'];
		 }
	}
}

//HTML Sitemap Order by
function seopress_xml_sitemap_html_orderby_option() {
	$seopress_xml_sitemap_html_orderby_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_orderby_option ) ) {
		foreach ($seopress_xml_sitemap_html_orderby_option as $key => $seopress_xml_sitemap_html_orderby_value)
			$options[$key] = $seopress_xml_sitemap_html_orderby_value;
		 if (isset($seopress_xml_sitemap_html_orderby_option['seopress_xml_sitemap_html_orderby'])) { 
		 	return $seopress_xml_sitemap_html_orderby_option['seopress_xml_sitemap_html_orderby'];
		 }
	}
}

//HTML Sitemap Date
function seopress_xml_sitemap_html_date_option() {
	$seopress_xml_sitemap_html_date_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_html_date_option ) ) {
		foreach ($seopress_xml_sitemap_html_date_option as $key => $seopress_xml_sitemap_html_date_value)
			$options[$key] = $seopress_xml_sitemap_html_date_value;
		 if (isset($seopress_xml_sitemap_html_date_option['seopress_xml_sitemap_html_date'])) { 
		 	return $seopress_xml_sitemap_html_date_option['seopress_xml_sitemap_html_date'];
		 }
	}
}

if (seopress_xml_sitemap_html_enable_option() =='1') {
	function seopress_xml_sitemap_html_display() {
		if (seopress_xml_sitemap_html_mapping_option() !='') {
			if(is_page(explode(',', seopress_xml_sitemap_html_mapping_option()))) {	
				add_filter('the_content', 'seopress_xml_sitemap_html_hook');
			}
		}
		function seopress_xml_sitemap_html_hook($content) {
			//Exclude IDs
			if (seopress_xml_sitemap_html_exclude_option() !='') {
				$seopress_xml_sitemap_html_exclude_option = seopress_xml_sitemap_html_exclude_option();
			} else {
				$seopress_xml_sitemap_html_exclude_option = '';
			}

			//Order
			if (seopress_xml_sitemap_html_order_option() !='') {
				$seopress_xml_sitemap_html_order_option = seopress_xml_sitemap_html_order_option();
			} else {
				$seopress_xml_sitemap_html_order_option = '';
			}

			//Orderby
			if (seopress_xml_sitemap_html_orderby_option() !='') {
				$seopress_xml_sitemap_html_orderby_option = seopress_xml_sitemap_html_orderby_option();
			} else {
				$seopress_xml_sitemap_html_orderby_option = '';
			}

			//CPT
			if (seopress_xml_sitemap_post_types_list_option() !='') {
				$content .= '<div class="wrap-html-sitemap">';
				foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
					$obj = get_post_type_object( $cpt_key );
					if ($obj) {
						$content .= '<h2>'.$obj->labels->name.'</h2>';
					}
					foreach ($cpt_value as $_cpt_key => $_cpt_value) {
						if($_cpt_value =='1') {
							$args = array(
								'posts_per_page' => 1000,
                                'order'=> $seopress_xml_sitemap_html_order_option,
                                'orderby' => $seopress_xml_sitemap_html_orderby_option,
                                'post_type' => $cpt_key,
                                'post_status' => 'publish',
                                'meta_query' => array( array( 'key' => '_seopress_robots_index', 'value' => 'yes', 'compare' => 'NOT EXISTS' ) ),
                                'fields' => 'ids',
                                'exclude' => $seopress_xml_sitemap_html_exclude_option,
                                'suppress_filters' => false
                            );
							if ($cpt_key =='post') {
								$args_cat_query = array(
									'orderby'	=>	'name',
									'order'		=>	'ASC',
                                	'meta_query' => array( array( 'key' => '_seopress_robots_index', 'value' => 'yes', 'compare' => 'NOT EXISTS' ) ),
									'exclude' => $seopress_xml_sitemap_html_exclude_option,
									'suppress_filters' => false,
								);
								$args_cat_query = apply_filters('seopress_sitemaps_html_cat_query', $args_cat_query);

								$cats = get_categories($args_cat_query);
								if (!empty($cats)) {
									foreach($cats as $cat) {
										$content .= '<h3>'.$cat->name.'</h3>';
										unset($args['cat']);
										$args['cat'][] = $cat->term_id;
										require(dirname( __FILE__ ) . '/sitemap/template-html-sitemap.php');
									}
								}
							} else {
								require(dirname( __FILE__ ) . '/sitemap/template-html-sitemap.php');
							}
						}
						
					}
				}
				$content .= '</div>';
			}
			
			return $content;
		}
		add_shortcode( 'seopress_html_sitemap', 'seopress_xml_sitemap_html_hook' );
	}
	add_action('wp_head', 'seopress_xml_sitemap_html_display');
}

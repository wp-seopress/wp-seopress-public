<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$args = apply_filters( 'seopress_sitemaps_html_query', $args, $cpt_key );

if (is_post_type_hierarchical($cpt_key)) {
	$postslist = get_posts( $args );
	
	$args2 = [ 
		'post_type' => $cpt_key, 
		'include' => $postslist, 
		'sort_order' => $seopress_xml_sitemap_html_order_option, 
		'sort_column' => $seopress_xml_sitemap_html_orderby_option
	];

	$args2 = apply_filters('seopress_sitemaps_html_pages_query', $args2, $cpt_key);
	$postslist = get_pages( $args2 );
} else {
	$postslist = get_posts( $args );
}
if (!empty($postslist)) {
	$date = true;
	if (is_post_type_hierarchical($cpt_key)) {
		$walker_page = new Walker_Page();
		$content .= '<ul>';
		if (get_post_type_archive_link($cpt_key)) {
			$content .= '<li><a href="'.get_post_type_archive_link($cpt_key).'">'.$obj->labels->name.'</a></li>';
		}
		$depth = 0;
		$depth = apply_filters('seopress_sitemaps_html_pages_depth_query', $depth);

		$content .= $walker_page->walk($postslist, $depth);
		$content .= '</ul>'; // 0 means display all levels.
	} else {
		$content .= '<ul>';
			if ($cpt_key !='post' && isset($obj->labels->name)) {//check if not Post cpt
				$content .= '<li><a href="'.get_post_type_archive_link($cpt_key).'">'.$obj->labels->name.'</a></li>';
			}
			
			foreach ( $postslist as $post ) {
				setup_postdata( $post );
				$content .= '<li>';
				$content .= '<a href="'.get_permalink($post).'">'.get_the_title($post).'</a>';
				if (seopress_xml_sitemap_html_date_option() !='1') {
					$date = apply_filters( 'seopress_sitemaps_html_post_date', $date, $cpt_key );
					if ($date ===true) {
						$content .= ' - '.get_the_date('j F Y', $post);
					}
				}
				$content .= '</li>';
			}
			wp_reset_postdata();
		$content .= '</ul>';
	}
}
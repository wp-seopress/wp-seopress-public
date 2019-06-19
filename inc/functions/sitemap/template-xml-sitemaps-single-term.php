<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//WPML
function seopress_remove_wpml_home_url_filter( $home_url, $url, $path, $orig_scheme, $blog_id ) {
    return $url;
}
add_filter( 'wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5 );

add_filter( 'seopress_sitemaps_single_term_query', function( $args ) {
    global $sitepress, $sitepress_settings;

    $sitepress_settings['auto_adjust_ids'] = 0;
    remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
    remove_filter( 'category_link', array( $sitepress, 'category_link_adjust_id' ), 1 );

    return $args;
});

function seopress_xml_sitemap_single_term() {
	if( get_query_var( 'seopress_cpt') !== '' ) {
		$path = get_query_var( 'seopress_cpt');
	}

	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$args = array('taxonomy' => $path,'hide_empty' => false, 'number' => 1000, 'meta_query' => array( array( 'key' => '_seopress_robots_index', 'value' => 'yes', 'compare' => 'NOT EXISTS' ) ), 'fields' => 'ids', 'lang' => '');

   	$args = apply_filters('seopress_sitemaps_single_term_query', $args, $path);
	$termslist = get_terms( $args );
	foreach ( $termslist as $term ) {
		$seopress_sitemaps .= "\n";
	  	$seopress_sitemaps .= '<url>';
	  	$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<loc>';
		$seopress_sitemaps .= htmlspecialchars(urldecode(esc_url(get_term_link($term))));
		$seopress_sitemaps .= '</loc>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '</url>';
	}
	$seopress_sitemaps .= '</urlset>';
	$seopress_sitemaps .= "\n";
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_single_term();
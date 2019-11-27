<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//WPML
function seopress_remove_wpml_home_url_filter( $home_url, $url, $path, $orig_scheme, $blog_id ) {
    return $url;
}
add_filter( 'wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5 );

function seopress_xml_sitemap_author() {
	if( get_query_var( 'seopress_cpt') !== '' ) {
		$path = get_query_var( 'seopress_cpt');
	}

	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    $args = array('fields' => 'ID', 'orderby' => 'nicename', 'order' => 'ASC', 'has_published_posts' => array('post'), 'blog_id' => absint(get_current_blog_id()), 'lang' => '');
    $args = apply_filters('seopress_sitemaps_author_query', $args);
    
    $authorslist = get_users($args);
        
    foreach ( $authorslist as $author ) {
		$seopress_sitemaps .= "\n";
	  	$seopress_sitemaps .= '<url>';
	  	$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<loc>';
		$seopress_sitemaps .= htmlspecialchars(urldecode(esc_url(get_author_posts_url($author))));
		$seopress_sitemaps .= '</loc>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '</url>';
	}
	$seopress_sitemaps .= '</urlset>';
	$seopress_sitemaps .= "\n";
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_author();


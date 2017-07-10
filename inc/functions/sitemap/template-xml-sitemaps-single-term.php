<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//Robots
Header("X-Robots-Tag: noindex", true);

function seopress_xml_sitemap_single_term() {
	$path = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".xml");
	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$args = array('taxonomy' => $path,'hide_empty' => false, 'number' => 1000, 'lang' => '');
	$termslist = get_terms( $args );
	foreach ( $termslist as $term ) {
		$seopress_sitemaps .= "\n";
	  	$seopress_sitemaps .= '<url>';
	  	$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<loc>';
		$seopress_sitemaps .= esc_url(get_term_link($term));
		$seopress_sitemaps .= '</loc>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '</url>';
	}
	$seopress_sitemaps .= '</urlset>';
	$seopress_sitemaps .= "\n";
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_single_term();
?>
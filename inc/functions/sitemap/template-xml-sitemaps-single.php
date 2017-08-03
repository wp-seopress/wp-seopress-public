<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//Robots
Header("X-Robots-Tag: noindex", true);

function seopress_xml_sitemap_single() {
	$path = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".xml");
	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$seopress_sitemaps .= "\n";
	
				$args = array( 'posts_per_page' => 1000, 'order'=> 'DESC', 'orderby' => 'date', 'post_type' => $path, 'post_status' => 'publish', 'meta_key' => '_seopress_robots_index', 'meta_value' => 'yes', 'meta_compare' => 'NOT EXISTS', 'fields' => 'ids', 'lang' => '' );
				$postslist = get_posts( $args );
				foreach ( $postslist as $post ) {
				  	setup_postdata( $post );
				  	$seopress_sitemaps .= '<url>';
				  	$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<loc>';
					$seopress_sitemaps .= get_permalink($post);
					$seopress_sitemaps .= '</loc>';
					$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<lastmod>';
					$seopress_sitemaps .= get_the_date('c', $post);
					$seopress_sitemaps .= '';
					$seopress_sitemaps .= '</lastmod>';
					$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '</url>';
					$seopress_sitemaps .= "\n";
				}
				wp_reset_postdata();

	$seopress_sitemaps .= '</urlset>';
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_single();
?>
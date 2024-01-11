<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//XML

//Headers
seopress_get_service('SitemapHeaders')->printHeaders();

//WPML - Home URL
if ( 2 == apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
    add_filter('seopress_sitemaps_home_url', function($home_url) {
        $home_url = apply_filters( 'wpml_home_url', get_option( 'home' ));
        return trailingslashit($home_url);
    });
} else {
    add_filter('wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5);
}

function seopress_xml_sitemap_author() {
    if ('' !== get_query_var('seopress_cpt')) {
        $path = get_query_var('seopress_cpt');
    }

    $home_url = home_url() . '/';

	if (function_exists('pll_home_url')) {
		$home_url = pll_home_url();
	}

    $home_url = apply_filters('seopress_sitemaps_home_url', $home_url);

    $seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
    $seopress_sitemaps .= '<?xml-stylesheet type="text/xsl" href="' . $home_url . 'sitemaps_xsl.xsl"?>';
    $seopress_sitemaps .= "\n";
    $seopress_sitemaps .= apply_filters('seopress_sitemaps_urlset', '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    $args = [
        'fields'              => 'ID',
        'orderby'             => 'nicename',
        'order'               => 'ASC',
        'has_published_posts' => ['post'],
            'blog_id'         => absint(get_current_blog_id()),
            'lang'            => '',
    ];
    $args = apply_filters('seopress_sitemaps_author_query', $args);

    $authorslist = get_users($args);

    foreach ($authorslist as $author) {
        $seopress_sitemaps_url = '';
        // array with all the information needed for a sitemap url
        $seopress_url = [
            'loc'    => htmlspecialchars(urldecode(esc_url(get_author_posts_url($author)))),
            'mod'    => '',
            'images' => [],
        ];
        $seopress_sitemaps_url .= "\n";
        $seopress_sitemaps_url .= '<url>';
        $seopress_sitemaps_url .= "\n";
        $seopress_sitemaps_url .= '<loc>';
        $seopress_sitemaps_url .= $seopress_url['loc'];
        $seopress_sitemaps_url .= '</loc>';
        $seopress_sitemaps_url .= "\n";
        $seopress_sitemaps_url .= '</url>';

        $seopress_sitemaps .= apply_filters('seopress_sitemaps_url', $seopress_sitemaps_url, $seopress_url);
    }
    $seopress_sitemaps .= '</urlset>';
    $seopress_sitemaps .= "\n";

    $seopress_sitemaps = apply_filters('seopress_sitemaps_xml_author', $seopress_sitemaps);

    return $seopress_sitemaps;
}
echo seopress_xml_sitemap_author();

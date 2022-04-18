<?php

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Automatically ping Google / Bing daily for XML sitemaps
 *
 * @since 5.3.0
 *
 */
function seopress_xml_sitemaps_ping_cron_action() {

    //If site is set to noindex globally
    if ('1' === seopress_global_noindex_option() || '0' === get_option('blog_public')) {
        return;
    }
    //Check if XML sitemaps is enabled
    if ('1' !== seopress_xml_sitemap_general_enable_option() || '1' !== seopress_get_toggle_option('xml-sitemap')) {
        return;
    }
    $url = rawurlencode(get_option('home').'/sitemaps.xml/');

    $url = apply_filters( 'seopress_sitemaps_xml_ping_url', $url);

    $args = [
        'blocking' => false,
    ];

    $args = apply_filters( 'seopress_sitemaps_xml_ping_args', $args);

    wp_remote_get('https://www.google.com/ping?sitemap='.$url, $args);
    wp_remote_get('https://www.bing.com/ping?sitemap='.$url, $args);
}
add_action('seopress_xml_sitemaps_ping_cron', 'seopress_xml_sitemaps_ping_cron_action');

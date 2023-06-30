<?php

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Automatically ping Google daily for XML sitemaps
 *
 * @since 5.3.0
 * @updated 5.8.0
 *
 */
function seopress_xml_sitemaps_ping_cron_action() {
    //Disable if MainWP add-on enabled
    if (defined('SEOPRESS_WPMAIN_VERSION')) {
        return;
    }

    //If site is set to noindex globally
    if ('1' === seopress_get_service('TitleOption')->getTitleNoIndex() || '0' === get_option('blog_public')) {
        return;
    }
    //Check if XML sitemaps is enabled
    if ('1' !== seopress_get_service('SitemapOption')->isEnabled() || '1' !== seopress_get_toggle_option('xml-sitemap')) {
        return;
    }

    //Disable if IndexNow is enabled
    $options = get_option('seopress_instant_indexing_option_name');
    if ('1' == seopress_get_toggle_option('instant-indexing') && isset($options['engines']['bing']) && $options['engines']['bing'] === '1') {
        return;
    }

    $url = rawurlencode(get_option('home').'/sitemaps.xml/');

    $url = apply_filters( 'seopress_sitemaps_xml_ping_url', $url);

    $args = [
        'blocking' => false,
    ];

    $args = apply_filters( 'seopress_sitemaps_xml_ping_args', $args);

    wp_remote_get('https://www.google.com/ping?sitemap='.$url, $args);
}
add_action('seopress_xml_sitemaps_ping_cron', 'seopress_xml_sitemaps_ping_cron_action');

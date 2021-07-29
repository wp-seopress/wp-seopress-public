<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_xml_sitemaps_help_tab() {
    $docs   = seopress_get_docs_links();
    $screen = get_current_screen();

    $seopress_xml_sitemaps_help_tab_content = '
        <p>' . __('Watch our video to learn how to enable XML sitemaps to improve crawling and add them to Google Search Console.', 'wp-seopress') . '</p>
    ' . wp_oembed_get('https://www.youtube.com/watch?v=Bjfspe1nusY', ['width'=>530]);

    $screen->add_help_tab([
        'id'        => 'seopress_google_analytics_help_tab',
        'title'     => __('How-to', 'wp-seopress'),
        'content'   => $seopress_xml_sitemaps_help_tab_content,
    ]);

    $screen->set_help_sidebar(
        '<ul>
            <li><a href="' . $docs['sitemaps']['xml'] . '" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
        </ul>'
    );
}
add_action('load-' . $seopress_xml_sitemaps_help_tab, 'seopress_xml_sitemaps_help_tab');

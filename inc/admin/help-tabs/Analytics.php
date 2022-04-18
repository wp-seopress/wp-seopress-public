<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_google_analytics_help_tab() {
    $docs = seopress_get_docs_links();

    $screen = get_current_screen();

    $seopress_google_analytics_help_tab_content = '
        <p>' . __('Watch our video to learn how to connect your WordPress site with Google Analytics and get statistics right in your dashboard (PRO only).', 'wp-seopress') . '</p>
    ' . wp_oembed_get('https://www.youtube.com/watch?v=2EWdogYuFgs', ['width'=>530]);

    $screen->add_help_tab([
        'id'        => 'seopress_google_analytics_help_tab',
        'title'     => __('How-to', 'wp-seopress'),
        'content'   => $seopress_google_analytics_help_tab_content,
    ]);

    $screen->set_help_sidebar(
        '<ul>
            <li><a href="' . $docs['analytics']['connect'] . '" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
        </ul>'
    );
}
add_action('load-' . $seopress_google_analytics_help_tab, 'seopress_google_analytics_help_tab');

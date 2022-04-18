<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_social_networks_help_tab() {
    $docs   = seopress_get_docs_links();
    $screen = get_current_screen();

    $seopress_social_networks_help_tab_content = '
        <p>' . __('Watch our video to learn how to edit your Open Graph and Twitter Cards tags to improve social sharing.', 'wp-seopress') . '</p>
    ' . wp_oembed_get('https://www.youtube.com/watch?v=TX3AUsI6vKk', ['width'=>530]);

    $screen->add_help_tab([
        'id'        => 'seopress_social_networks_help_tab',
        'title'     => __('How-to', 'wp-seopress'),
        'content'   => $seopress_social_networks_help_tab_content,
    ]);

    $screen->set_help_sidebar(
        '<ul>
            <li><a href="' . $docs['social']['og'] . '" target="_blank">' . __('Read our guide', 'wp-seopress') . '</a></li>
        </ul>'
    );
}
add_action('load-' . $seopress_social_networks_help_tab, 'seopress_social_networks_help_tab');

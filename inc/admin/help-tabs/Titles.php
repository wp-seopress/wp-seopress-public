<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_titles_help_tab() {
    $docs  = seopress_get_docs_links();

    $screen = get_current_screen();

    $dyn_var = seopress_get_dyn_variables();

    $seopress_titles_help_tab_content = '<ul>';
    foreach ($dyn_var as $key => $value) {
        $seopress_titles_help_tab_content .= '<li>';
        $seopress_titles_help_tab_content .= '<strong>' . $key . '</strong> - <em>' . $value . '</em>';
        $seopress_titles_help_tab_content .= '</li>';
    }

    $seopress_titles_help_tab_content .= '<ul>';

    $seopress_titles_help_tab_content .= wp_oembed_get('https://www.youtube.com/watch?v=Jretu4Gpgo8', ['width'=>530]);

    $seopress_titles_help_robots_tab_content = wp_oembed_get('https://www.youtube.com/watch?v=Jretu4Gpgo8', ['width'=>530]);

    $screen->add_help_tab([
        'id'        => 'seopress_titles_help_tab',
        'title'     => __('Templates variables', 'wp-seopress'),
        'content'   => $seopress_titles_help_tab_content,
    ]);

    $screen->add_help_tab([
        'id'        => 'seopress_titles_help_robots_tab',
        'title'     => __('Edit your meta robots', 'wp-seopress'),
        'content'   => $seopress_titles_help_robots_tab_content,
    ]);

    $screen->set_help_sidebar(
        '<ul>
            <li><a href="' . $docs['guides'] . '" target="_blank">' . __('Browse our guides', 'wp-seopress') . '</a></li>
            <li><a href="' . $docs['faq'] . '" target="_blank">' . __('Read our FAQ', 'wp-seopress') . '</a></li>
            <li><a href="' . $docs['website'] . '" target="_blank">' . __('Check our website', 'wp-seopress') . '</a></li>
        </ul>'
    );
}
add_action('load-' . $seopress_titles_help_tab, 'seopress_titles_help_tab');

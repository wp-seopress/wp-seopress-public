<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$args = apply_filters( 'seopress_sitemaps_html_query', $args, $cpt_key );

$postslist = get_posts( $args );

if (!empty($postslist)) {
    $content .= '<ul>';
        foreach ( $postslist as $post ) {
            setup_postdata( $post );
            $content .= '<li>';
            $content .= '<a href="'.get_permalink($post).'">'.get_the_title($post).'</a>';
            if (seopress_xml_sitemap_html_date_option() !='1') {
                $content .= ' - '.get_the_date('j F Y', $post);
            }
            $content .= '</li>';
        }
        wp_reset_postdata();
    $content .= '</ul>';
}
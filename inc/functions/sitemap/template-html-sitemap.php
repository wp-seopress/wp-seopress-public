<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

$args = apply_filters( 'seopress_sitemaps_html_query', $args, $cpt_key );

if (is_post_type_hierarchical($cpt_key)) {
    $postslist = get_posts( $args );
    
    $args2 = array('include'=>$postslist, 'sort_order' => $seopress_xml_sitemap_html_order_option, 'sort_column' => $seopress_xml_sitemap_html_orderby_option);

    $args2 = apply_filters('seopress_sitemaps_html_pages_query', $args2);
    $postslist = get_pages( $args2 );
} else {
    $postslist = get_posts( $args );
}
if (!empty($postslist)) {
    if (is_post_type_hierarchical($cpt_key)) {
        $walker_page = new Walker_Page();
        $content .= '<ul>'.$walker_page->walk($postslist, 0).'</ul>'; // 0 means display all levels.
    } else {
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
}
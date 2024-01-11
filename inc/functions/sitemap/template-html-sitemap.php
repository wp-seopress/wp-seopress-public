<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$args = apply_filters('seopress_sitemaps_html_query', $args, $cpt_key);

if (is_post_type_hierarchical($cpt_key)) {
    $postslist = get_posts($args);

    $args2 = [
        'post_type'   => $cpt_key,
        'include'     => $postslist,
        'sort_order'  => $seopress_xml_sitemap_html_order_option,
        'sort_column' => $seopress_xml_sitemap_html_orderby_option,
    ];

    $args2     = apply_filters('seopress_sitemaps_html_pages_query', $args2, $cpt_key);
    $postslist = get_pages($args2);
} else {
    $postslist = get_posts($args);
}

if (! empty($postslist)) {
    $date = true;
    if (is_post_type_hierarchical($cpt_key)) {
        $walker_page = new Walker_Page();
        $html .= '<ul class="sp-list-posts sp-cpt-hierarchical">';

        $depth = 0;
        $depth = apply_filters('seopress_sitemaps_html_pages_depth_query', $depth);

        $html .= $walker_page->walk($postslist, $depth);
        $html .= '</ul>'; // 0 means display all levels.
    } else {

        $html .= '<ul class="sp-list-posts">';

        foreach ($postslist as $post) {
            setup_postdata($post);

            //Prevent duplicated items
            if ($cpt_key === 'post' || $cpt_key === 'product') {
                $tax = $cpt_key ==='product' ? $tax = $product_cat_slug  : $tax = 'category';
                if (!has_term($cat, $tax, $post)) {
                    continue;
                }
            }

            $post_title = apply_filters('seopress_sitemaps_html_post_title', get_the_title($post));

            $html .= '<li>';
            $html .= '<a href="' . get_permalink($post) . '">' . $post_title . '</a>';
            if ('1' !== seopress_get_service('SitemapOption')->getHtmlDate()) {
                $date = apply_filters('seopress_sitemaps_html_post_date', $date, $cpt_key);
                if (true === $date) {
                    $html .= ' - ' . get_the_date('j F Y', $post);
                }
            }
            $html .= '</li>';
        }
        wp_reset_postdata();
        $html .= '</ul>';
    }

}

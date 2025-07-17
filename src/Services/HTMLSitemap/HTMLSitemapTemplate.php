<?php

namespace SEOPress\Services\HTMLSitemap;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Services\Options\SitemapOption;

class HTMLSitemapTemplate {
    private $sitemapOption;

    public function __construct(SitemapOption $sitemapOption) {
        $this->sitemapOption = $sitemapOption;
    }

    public function render($cpt_key, $args) {
        $args = apply_filters('seopress_sitemaps_html_query', $args, $cpt_key);
        $html = '';

        if (is_post_type_hierarchical($cpt_key)) {
            $postslist = get_posts($args);

            $args2 = [
                'post_type'   => $cpt_key,
                'include'     => $postslist,
                'sort_order'  => $this->sitemapOption->getHtmlOrder(),
                'sort_column' => $this->sitemapOption->getHtmlOrderBy(),
            ];

            $args2 = apply_filters('seopress_sitemaps_html_pages_query', $args2, $cpt_key);
            $postslist = get_pages($args2);
        } else {
            $postslist = get_posts($args);
        }

        if (!empty($postslist)) {
            $date = true;
            if (is_post_type_hierarchical($cpt_key)) {
                $html .= $this->renderHierarchicalPosts($postslist);
            } else {
                $html .= $this->renderFlatPosts($postslist, $cpt_key);
            }
        }

        return $html;
    }

    private function renderHierarchicalPosts($postslist) {
        $walker_page = new \Walker_Page();
        $html = '<ul class="sp-list-posts sp-cpt-hierarchical">';

        $depth = apply_filters('seopress_sitemaps_html_pages_depth_query', 0);
        $html .= $walker_page->walk($postslist, $depth);
        $html .= '</ul>';

        return $html;
    }

    private function renderFlatPosts($postslist, $cpt_key) {
        $html = '<ul class="sp-list-posts">';

        foreach ($postslist as $post) {
            setup_postdata($post);

            if ('1' !== $this->sitemapOption->getHtmlNoHierarchy()) {
                if ($cpt_key === 'post' || $cpt_key === 'product') {
                    $tax = $cpt_key === 'product' ? 'product_cat' : 'category';
                    if (isset($cat) && !has_term($cat, $tax, $post)) {
                        continue;
                    }
                }
            }

            $post_title = apply_filters('seopress_sitemaps_html_post_title', get_the_title($post));

            $html .= '<li>';
            $html .= '<a href="' . get_permalink($post) . '">' . $post_title . '</a>';

            if ('1' !== $this->sitemapOption->getHtmlDate()) {
                $date = apply_filters('seopress_sitemaps_html_post_date', true, $cpt_key);
                if (true === $date) {
                    $date_format = apply_filters('seopress_sitemaps_html_post_date_format', 'j F Y');
                    $html .= ' - ' . get_the_date($date_format, $post);
                }
            }

            $html .= '</li>';
        }

        wp_reset_postdata();
        $html .= '</ul>';

        return $html;
    }
} 
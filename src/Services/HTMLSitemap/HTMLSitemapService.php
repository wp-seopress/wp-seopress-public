<?php

namespace SEOPress\Services\HTMLSitemap;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Services\Options\SitemapOption;

class HTMLSitemapService {
    private $sitemapOption;

    public function __construct(SitemapOption $sitemapOption) {
        $this->sitemapOption = $sitemapOption;
    }

    public function init() {
        if ('1' === $this->sitemapOption->getHtmlEnable()) {
            add_action('wp', [$this, 'display']);
            add_shortcode('seopress_html_sitemap', [$this, 'renderSitemap']);
        }
    }

    public function display() {
        if ('' !== $this->sitemapOption->getHtmlMapping()) {
            if (is_page(explode(',', $this->sitemapOption->getHtmlMapping()))) {
                add_filter('the_content', [$this, 'renderSitemap']);
            }
        }
    }

    public function renderSitemap($html = '') {
        $atts = shortcode_atts(
            [
                'cpt' => '',
                'terms_only' => false,
            ],
            $html,
            '[seopress_html_sitemap]'
        );

        $product_cat_slug = apply_filters('seopress_sitemaps_html_product_cat_slug', 'product_cat');
        
        $exclude_option = $this->sitemapOption->getHtmlExclude() ?: '';
        $order_option = $this->sitemapOption->getHtmlOrder() ?: '';
        $orderby_option = $this->sitemapOption->getHtmlOrderBy() ?: '';

        $html = '';

        if (!empty($this->sitemapOption->getPostTypesList())) {
            $html .= '<div class="wrap-html-sitemap sp-html-sitemap">';
            
            $post_types_list = $this->sitemapOption->getPostTypesList();

            if (isset($post_types_list['page'])) {
                $post_types_list = ['page' => $post_types_list['page']] + $post_types_list;
            }

            if (!empty($atts['cpt'])) {
                unset($post_types_list);
                $cpt = explode(',', $atts['cpt']);
                foreach ($cpt as $value) {
                    $post_types_list[$value] = ['include' => '1'];
                }
            }

            $post_types_list = apply_filters('seopress_sitemaps_html_cpt', $post_types_list);

            foreach ($post_types_list as $cpt_key => $cpt_value) {
                if (!empty($cpt_value)) {
                    $html .= '<div class="sp-wrap-cpt">';
                }

                $obj = get_post_type_object($cpt_key);
                if ($obj) {
                    $cpt_name = apply_filters('seopress_sitemaps_html_cpt_name', $obj->labels->name, $obj->name);
                    $html .= '<h2 class="sp-cpt-name">' . $cpt_name . '</h2>';

                    // Add archive link if post type has archives enabled
                    if ($this->hasPostTypeArchive($cpt_key)) {
                        $html .= $this->renderArchiveLink($cpt_key, $obj);
                    }
                }

                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        $args = $this->getQueryArgs($cpt_key, $exclude_option, $order_option, $orderby_option);
                        $args_cat_query = $this->getCategoryQueryArgs($exclude_option);

                        $cats = $this->getCategories($cpt_key, $args_cat_query, $product_cat_slug);

                        // Check if we should only display terms
                        $display_terms_only = apply_filters('seopress_sitemaps_html_display_terms_only', $atts['terms_only'], $cpt_key);

                        if (is_array($cats) && !empty($cats)) {
                            if ($display_terms_only) {
                                $html .= $this->renderTermsOnly($cats, $cpt_key);
                            } else if ('1' !== $this->sitemapOption->getHtmlNoHierarchy()) {
                                $html .= $this->renderHierarchicalSitemap($cats, $cpt_key, $args, $product_cat_slug);
                            } else {
                                $html .= $this->renderFlatSitemap($cpt_key, $args);
                            }
                        } else {
                            $html .= $this->renderFlatSitemap($cpt_key, $args);
                        }
                    }
                }

                if (!empty($cpt_value)) {
                    $html .= '</div>';
                }
            }

            $html .= '</div>';
        }

        return $html;
    }

    private function renderTermsOnly($terms, $cpt_key) {
        $html = '<div class="sp-wrap-terms">';
        $html .= '<ul class="sp-list-terms">';

        foreach ($terms as $term) {
            if (!is_wp_error($term) && is_object($term)) {
                $term_name = apply_filters('seopress_sitemaps_html_term_name', $term->name, $term);
                $term_url = get_term_link($term);
                
                if (!is_wp_error($term_url)) {
                    $html .= sprintf(
                        '<li class="sp-term-item"><a href="%s">%s</a></li>',
                        esc_url($term_url),
                        esc_html($term_name)
                    );
                }
            }
        }

        $html .= '</ul>';
        $html .= '</div>';

        return apply_filters('seopress_sitemaps_html_terms_output', $html, $terms, $cpt_key);
    }

    private function hasPostTypeArchive($post_type) {
        $post_type_obj = get_post_type_object($post_type);
        return $post_type_obj && $post_type_obj->has_archive;
    }

    private function renderArchiveLink($post_type, $post_type_obj) {
        if ('1' === $this->sitemapOption->getHtmlPostTypeArchive()) {
            return '';
        }
        
        $archive_url = get_post_type_archive_link($post_type);
        if (!$archive_url) {
            return '';
        }

        $archive_label = sprintf(
            /* translators: %s: post type archive label */
            __('View all %s', 'wp-seopress'),
            strtolower($post_type_obj->labels->name)
        );

        return sprintf(
            '<div class="sp-archive-link"><a href="%s">%s</a></div>',
            esc_url($archive_url),
            esc_html($archive_label)
        );
    }

    private function getQueryArgs($cpt_key, $exclude_option, $order_option, $orderby_option) {
        return [
            'posts_per_page'   => 1000,
            'order'            => $order_option,
            'orderby'          => $orderby_option,
            'post_type'        => $cpt_key,
            'post_status'      => 'publish',
            'meta_query'       => [
                'relation' => 'OR',
                [
                    'key'     => '_seopress_robots_index',
                    'value'   => '',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => '_seopress_robots_index',
                    'value'   => 'yes',
                    'compare' => '!=',
                ],
            ],
            'fields'           => 'ids',
            'exclude'          => $exclude_option,
            'suppress_filters' => false,
            'no_found_rows'    => true,
            'nopaging'         => true,
        ];
    }

    private function getCategoryQueryArgs($exclude_option) {
        return [
            'orderby'          => 'name',
            'order'            => 'ASC',
            'meta_query'       => [
                'relation' => 'OR',
                [
                    'key'     => '_seopress_robots_index',
                    'value'   => '',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => '_seopress_robots_index',
                    'value'   => 'yes',
                    'compare' => '!=',
                ],
            ],
            'exclude'          => $exclude_option,
            'suppress_filters' => false,
        ];
    }

    private function getCategories($cpt_key, $args_cat_query, $product_cat_slug) {
        if ('post' === $cpt_key) {
            $args_cat_query = apply_filters('seopress_sitemaps_html_cat_query', $args_cat_query);
            return get_categories($args_cat_query);
        } elseif ('product' === $cpt_key) {
            $args_cat_query = apply_filters('seopress_sitemaps_html_product_cat_query', $args_cat_query);
            return get_terms($product_cat_slug, $args_cat_query);
        }

        return apply_filters('seopress_sitemaps_html_hierarchical_terms_query', $cpt_key, $args_cat_query);
    }

    private function renderHierarchicalSitemap($cats, $cpt_key, $args, $product_cat_slug) {
        $html = '<div class="sp-wrap-cats">';

        foreach ($cats as $cat) {
            if (!is_wp_error($cat) && is_object($cat)) {
                $html .= '<div class="sp-wrap-cat">';
                $html .= '<h3 class="sp-cat-name"><a href="' . get_term_link($cat->term_id) . '">' . $cat->name . '</a></h3>';

                if ('post' === $cpt_key) {
                    unset($args['cat']);
                    $args['cat'][] = $cat->term_id;
                } elseif ('product' === $cpt_key) {
                    unset($args['tax_query']);
                    $args['tax_query'] = [[
                        'taxonomy' => $product_cat_slug,
                        'field'    => 'term_id',
                        'terms'    => $cat->term_id,
                    ]];
                }

                if ('post' !== $cpt_key && 'product' !== $cpt_key) {
                    $args['tax_query'] = apply_filters('seopress_sitemaps_html_hierarchical_tax_query', $cpt_key, $cat, $args);
                }

                $html .= $this->renderFlatSitemap($cpt_key, $args);
                $html .= '</div>';
            }
        }

        $html .= '</div>';
        return $html;
    }

    private function renderFlatSitemap($cpt_key, $args) {
        $template = new HTMLSitemapTemplate($this->sitemapOption);
        return $template->render($cpt_key, $args);
    }
} 
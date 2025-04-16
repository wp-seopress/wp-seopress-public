<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//XML/HTML Sitemap
//=================================================================================================
if ('1' === seopress_get_service('SitemapOption')->getHtmlEnable()) {
    function seopress_xml_sitemap_html_display()
    {
        if ('' !== seopress_get_service('SitemapOption')->getHtmlMapping()) {
            if (is_page(explode(',', seopress_get_service('SitemapOption')->getHtmlMapping()))) {
                add_filter('the_content', 'seopress_xml_sitemap_html_hook');
            }
        }

    }
    add_action('wp', 'seopress_xml_sitemap_html_display');

    function seopress_xml_sitemap_html_hook($html)
    {
        // Attributes
        $atts = shortcode_atts(
            [
                'cpt' => '',
            ],
            $html,
            '[seopress_html_sitemap]'
        );

        $product_cat_slug = 'product_cat';
        $product_cat_slug = apply_filters('seopress_sitemaps_html_product_cat_slug', $product_cat_slug);

        //Exclude IDs
        if ('' !== seopress_get_service('SitemapOption')->getHtmlExclude()) {
            $seopress_xml_sitemap_html_exclude_option = seopress_get_service('SitemapOption')->getHtmlExclude();
        } else {
            $seopress_xml_sitemap_html_exclude_option = '';
        }

        //Order
        if ('' !== seopress_get_service('SitemapOption')->getHtmlOrder()) {
            $seopress_xml_sitemap_html_order_option = seopress_get_service('SitemapOption')->getHtmlOrder();
        } else {
            $seopress_xml_sitemap_html_order_option = '';
        }

        //Orderby
        if ('' !== seopress_get_service('SitemapOption')->getHtmlOrderBy()) {
            $seopress_xml_sitemap_html_orderby_option = seopress_get_service('SitemapOption')->getHtmlOrderBy();
        } else {
            $seopress_xml_sitemap_html_orderby_option = '';
        }

        $html = '';

        //CPT
        if (!empty(seopress_get_service('SitemapOption')->getPostTypesList())) {
            $html .= '<div class="wrap-html-sitemap sp-html-sitemap">';

            $seopress_xml_sitemap_post_types_list_option = seopress_get_service('SitemapOption')->getPostTypesList();

            if (isset($seopress_xml_sitemap_post_types_list_option['page'])) {
                $seopress_xml_sitemap_post_types_list_option = ['page' => $seopress_xml_sitemap_post_types_list_option['page']] + $seopress_xml_sitemap_post_types_list_option; //Display page first
            }

            if (! empty($atts['cpt'])) {
                unset($seopress_xml_sitemap_post_types_list_option);

                $cpt = explode(',', $atts['cpt']);

                foreach ($cpt as $key => $value) {
                    $seopress_xml_sitemap_post_types_list_option[$value] = ['include' => '1'];
                }
            }

            $seopress_xml_sitemap_post_types_list_option = apply_filters('seopress_sitemaps_html_cpt', $seopress_xml_sitemap_post_types_list_option);

            foreach ($seopress_xml_sitemap_post_types_list_option as $cpt_key => $cpt_value) {
                if (! empty($cpt_value)) {
                    $html .= '<div class="sp-wrap-cpt">';
                }
                $obj = get_post_type_object($cpt_key);

                if ($obj) {
                    $cpt_name = $obj->labels->name;
                    $cpt_name = apply_filters('seopress_sitemaps_html_cpt_name', $cpt_name, $obj->name);

                    $html .= '<h2 class="sp-cpt-name">' . $cpt_name . '</h2>';
                }
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if ('1' == $_cpt_value) {
                        $args = [
                            'posts_per_page'   => 1000,
                            'order'            => $seopress_xml_sitemap_html_order_option,
                            'orderby'          => $seopress_xml_sitemap_html_orderby_option,
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
                            'exclude'          => $seopress_xml_sitemap_html_exclude_option,
                            'suppress_filters' => false,
                            'no_found_rows'    => true,
                            'nopaging'         => true,
                        ];

                        $args_cat_query = [
                            'orderby'	         => 'name',
                            'order'		          => 'ASC',
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
                            'exclude'          => $seopress_xml_sitemap_html_exclude_option,
                            'suppress_filters' => false,
                        ];
                        if ('post' === $cpt_key) {
                            $args_cat_query = apply_filters('seopress_sitemaps_html_cat_query', $args_cat_query);

                            $cats = get_categories($args_cat_query);
                        } elseif ('product' === $cpt_key) {
                            $args_cat_query = apply_filters('seopress_sitemaps_html_product_cat_query', $args_cat_query);

                            $cats = get_terms($product_cat_slug, $args_cat_query);
                        }

                        if ('post' !== $cpt_key && 'product' !== $cpt_key) {
                            $cats = apply_filters('seopress_sitemaps_html_hierarchical_terms_query', $cpt_key, $args_cat_query);
                        }

                        if (is_array($cats) && ! empty($cats) && '1' !== seopress_get_service('SitemapOption')->getHtmlNoHierarchy()) {
                            $html .= '<div class="sp-wrap-cats">';

                            foreach ($cats as $cat) {
                                if ( ! is_wp_error($cat) && is_object($cat)) {
                                    $html .= '<div class="sp-wrap-cat">';
                                    $html .= '<h3 class="sp-cat-name"><a href="'. get_term_link($cat->term_id) .'">' . $cat->name . '</a></h3>';

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

                                    require dirname(__FILE__) . '/sitemap/template-html-sitemap.php';

                                    $html .= '</div>';
                                }
                            }

                            $html .= '</div>';
                        } else {
                            require dirname(__FILE__) . '/sitemap/template-html-sitemap.php';
                        }
                    }
                }
                if (! empty($cpt_value)) {
                    $html .= '</div>';
                }
            }
            $html .= '</div>';
        }

        return $html;
    }
    add_shortcode('seopress_html_sitemap', 'seopress_xml_sitemap_html_hook');
}

<?php
/**
 * This file is no longer used since version 4.3.0.
 *
 * @deprecated 4.3.0
 */
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//XML

//Headers
if (function_exists('seopress_sitemaps_headers')) {
    seopress_sitemaps_headers();
}

//Remove primary category
remove_filter('post_link_category', 'seopress_titles_primary_cat_hook', 10, 3);

//WPML
add_filter('wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5);

add_filter('seopress_sitemaps_single_query', function ($args) {
    global $sitepress, $sitepress_settings;

    $sitepress_settings['auto_adjust_ids'] = 0;
    remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
    remove_filter('category_link', [$sitepress, 'category_link_adjust_id'], 1);

    return $args;
});

add_action('the_post', function ($post) {
    $language = apply_filters(
        'wpml_element_language_code',
        null,
        ['element_id' => $post->ID, 'element_type' => 'page']
    );
    do_action('wpml_switch_language', $language);
});

function seopress_xml_sitemap_single() {
    if ('' !== get_query_var('seopress_cpt')) {
        $path = get_query_var('seopress_cpt');
    }

    remove_all_filters('pre_get_posts');

    $offset = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '.xml');
    $offset = preg_match_all('/\d+/', $offset, $matches);
    $offset = end($matches[0]);

    //Max posts per paginated sitemap
    $max = 1000;
    $max = apply_filters('seopress_sitemaps_max_posts_per_sitemap', $max);

    if (isset($offset) && absint($offset) && '' != $offset && 0 != $offset) {
        $offset = (($offset - 1) * $max);
    } else {
        $offset = 0;
    }

    $home_url = home_url() . '/';

    if (function_exists('pll_home_url')) {
        $home_url = site_url() . '/';
    }

    $home_url = apply_filters('seopress_sitemaps_home_url', $home_url);

    $seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
    $seopress_sitemaps .= '<?xml-stylesheet type="text/xsl" href="' . $home_url . 'sitemaps_xsl.xsl"?>';
    $seopress_sitemaps .= "\n";
    $seopress_sitemaps .= apply_filters('seopress_sitemaps_urlset', '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">');
    $seopress_sitemaps .= "\n";

    if (true == get_post_type_archive_link($path) && 0 == $offset) {
        function seopress_titles_cpt_noindex_option($path) {
            $seopress_titles_cpt_noindex_option = get_option('seopress_titles_option_name');
            if ( ! empty($seopress_titles_cpt_noindex_option)) {
                foreach ($seopress_titles_cpt_noindex_option as $key => $seopress_titles_cpt_noindex_value) {
                    $options[$key] = $seopress_titles_cpt_noindex_value;
                }
                if (isset($seopress_titles_cpt_noindex_option['seopress_titles_archive_titles'][$path]['noindex'])) {
                    return $seopress_titles_cpt_noindex_option['seopress_titles_archive_titles'][$path]['noindex'];
                }
            }
        }
        if ('1' != seopress_titles_cpt_noindex_option($path)) {
            $seopress_sitemap_url = '';
            // array with all the information needed for a sitemap url
            $seopress_url = [
                'loc'    => htmlspecialchars(urldecode(get_post_type_archive_link($path))),
                'mod'    => '',
                'images' => [],
            ];
            $seopress_sitemap_url .= '<url>';
            $seopress_sitemap_url .= "\n";
            $seopress_sitemap_url .= '<loc>';
            $seopress_sitemap_url .= htmlspecialchars(urldecode(get_post_type_archive_link($path)));
            $seopress_sitemap_url .= '</loc>';
            $seopress_sitemap_url .= "\n";
            $seopress_sitemap_url .= '</url>';
            $seopress_sitemap_url .= "\n";

            $seopress_sitemaps .= apply_filters('seopress_sitemaps_url', $seopress_sitemap_url, $seopress_url);
        }
    }

    $args = [
        'posts_per_page' => 1000,
        'offset'         => $offset,
        'order'          => 'DESC',
        'orderby'        => 'modified',
        'post_type'      => $path,
        'post_status'    => 'publish',
        'meta_query'     => [
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
        'fields'       => 'ids',
        'lang'         => '',
        'has_password' => false,
    ];

    if ($path ==='attachment') {
        unset($args['post_status']);
    }

    $args = apply_filters('seopress_sitemaps_single_query', $args, $path);

    $postslist = get_posts($args);

    foreach ($postslist as $post) {
        setup_postdata($post);

        $dom    = '';
        $images = '';

        if (get_the_modified_date('c', $post)) {
            $seopress_mod = get_the_modified_date('c', $post);
        } else {
            $seopress_mod = get_post_modified_time('c', false, $post);
        }

        // initialize the sitemap url output
        $seopress_sitemap_url = '';
        // array with all the information needed for a sitemap url
        $seopress_url = [
            'loc'    => htmlspecialchars(urldecode(get_permalink($post))),
            'mod'    => $seopress_mod,
            'images' => [],
        ];

        $seopress_sitemap_url .= '<url>';
        $seopress_sitemap_url .= "\n";
        $seopress_sitemap_url .= '<loc>';
        $seopress_sitemap_url .= $seopress_url['loc'];
        $seopress_sitemap_url .= '</loc>';
        $seopress_sitemap_url .= "\n";
        $seopress_sitemap_url .= '<lastmod>';
        $seopress_sitemap_url .= $seopress_url['mod'];
        $seopress_sitemap_url .= '';
        $seopress_sitemap_url .= '</lastmod>';
        $seopress_sitemap_url .= "\n";

        //XML Image Sitemaps
        if ('1' == seopress_xml_sitemap_img_enable_option()) {
            //noimageindex?
            if ('yes' != get_post_meta($post, '_seopress_robots_imageindex', true)) {
                //Standard images
                $post_content   = '';
                $dom            = new domDocument();
                $internalErrors = libxml_use_internal_errors(true);

                $run_shortcodes = apply_filters('seopress_sitemaps_single_shortcodes', true);

                if (true === $run_shortcodes) {
                    //WP
                    if ('' != get_post_field('post_content', $post)) {
                        $post_content .= do_shortcode(get_post_field('post_content', $post));
                    }

                    //Oxygen Builder
                    if (is_plugin_active('oxygen/functions.php')) {
                        $post_content .= do_shortcode(get_post_meta($post, 'ct_builder_shortcodes', true));
                    }
                } else {
                    $post_content = get_post_field('post_content', $post);
                }

                if ('' != $post_content) {
                    if (function_exists('mb_convert_encoding')) {
                        $dom->loadHTML(mb_convert_encoding($post_content, 'HTML-ENTITIES', 'UTF-8'));
                    } else {
                        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $post_content);
                    }

                    $dom->preserveWhiteSpace = false;

                    if ('' != $dom->getElementsByTagName('img')) {
                        $images = $dom->getElementsByTagName('img');
                    }
                }
                libxml_use_internal_errors($internalErrors);

                //WooCommerce
                global $product;
                if ('' != $product && method_exists($product, 'get_gallery_image_ids')) {
                    $product_img = $product->get_gallery_image_ids();
                }

                //Post Thumbnail
                $post_thumbnail    = get_the_post_thumbnail_url($post);
                $post_thumbnail_id = get_post_thumbnail_id($post);

                if ((isset($images) && ! empty($images) && $images->length >= 1) || (isset($product) && ! empty($product_img)) || '' != $post_thumbnail) {
                    //Standard img
                    if (isset($images) && ! empty($images)) {
                        if ($images->length >= 1) {
                            foreach ($images as $img) {
                                $url = $img->getAttribute('src');
                                $url = apply_filters('seopress_sitemaps_single_img_url', $url);
                                if ('' != $url) {
                                    //Exclude Base64 img
                                    if (false === strpos($url, 'data:image/')) {
                                        /*
                                         *  Initiate $seopress_url['images] and needed data for the sitemap image template
                                         */

                                        if (true === seopress_is_absolute($url)) {
                                            //do nothing
                                        } else {
                                            $url = $home_url . $url;
                                        }

                                        //cleaning url
                                        $url = htmlspecialchars(urldecode(esc_attr(wp_filter_nohtml_kses($url))));

                                        //remove query strings
                                        $parse_url = wp_parse_url($url);

                                        if ( ! empty($parse_url['scheme']) && ! empty($parse_url['host']) && ! empty($parse_url['path'])) {
                                            $seopress_image_loc = '<![CDATA[' . $parse_url['scheme'] . '://' . $parse_url['host'] . $parse_url['path'] . ']]>';
                                        } else {
                                            $seopress_image_loc = '<![CDATA[' . $url . ']]>';
                                        }
                                        $seopress_image_caption = '';
                                        if ('' != $img->getAttribute('alt')) {
                                            $caption                = htmlspecialchars($img->getAttribute('alt'));
                                            $seopress_image_caption = '<![CDATA[' . $caption . ']]>';
                                        }
                                        $seopress_image_title = '';
                                        if ('' != $img->getAttribute('title')) {
                                            $title                = htmlspecialchars($img->getAttribute('title'));
                                            $seopress_image_title = '<![CDATA[' . $title . ']]>';
                                        }

                                        $seopress_url['images'][] = [
                                            'src'   => $seopress_image_loc,
                                            'title' => $seopress_image_title,
                                            'alt'   => $seopress_image_caption,
                                        ];

                                        /*
                                         * Build up the template.
                                         */
                                        $seopress_sitemap_url .= '<image:image>';
                                        $seopress_sitemap_url .= "\n";
                                        $seopress_sitemap_url .= '<image:loc>';
                                        $seopress_sitemap_url .= $seopress_image_loc;
                                        $seopress_sitemap_url .= '</image:loc>';
                                        $seopress_sitemap_url .= "\n";

                                        if ('' != $seopress_image_title) {
                                            $seopress_sitemap_url .= '<image:title>';
                                            $seopress_sitemap_url .= $seopress_image_title;
                                            $seopress_sitemap_url .= '</image:title>';
                                            $seopress_sitemap_url .= "\n";
                                        }

                                        if ('' != $seopress_image_caption) {
                                            $seopress_sitemap_url .= '<image:caption>';
                                            $seopress_sitemap_url .= $seopress_image_caption;
                                            $seopress_sitemap_url .= '</image:caption>';
                                            $seopress_sitemap_url .= "\n";
                                        }

                                        $seopress_sitemap_url .= '</image:image>';
                                        $seopress_sitemap_url .= "\n";
                                    }
                                }
                            }
                        }
                    }

                    //WooCommerce img
                    if ('' != $product && '' != $product_img) {
                        foreach ($product_img as $product_attachment_id) {
                            $seopress_image_loc = '<![CDATA[' . esc_attr(wp_filter_nohtml_kses(wp_get_attachment_url($product_attachment_id))) . ']]>';

                            $seopress_image_title = '';
                            if ('' != get_the_title($product_attachment_id)) {
                                $title = htmlspecialchars(get_the_title($product_attachment_id));
                                $seopress_image_title .= '<![CDATA[' . $title . ']]>';
                            }

                            $seopress_image_caption = '';
                            if ('' != get_post_meta($product_attachment_id, '_wp_attachment_image_alt', true)) {
                                $caption = htmlspecialchars(get_post_meta($product_attachment_id, '_wp_attachment_image_alt', true));
                                $seopress_image_caption .= '<![CDATA[' . $caption . ']]>';
                            }

                            $seopress_url['images'][] = [
                                'src'     => $seopress_image_loc,
                                'title'   => $seopress_image_title,
                                'caption' => $seopress_image_caption,
                            ];

                            /*
                             * Build up the template.
                             */
                            $seopress_sitemap_url .= '<image:image>';
                            $seopress_sitemap_url .= "\n";
                            $seopress_sitemap_url .= '<image:loc>';
                            $seopress_sitemap_url .= $seopress_image_loc;
                            $seopress_sitemap_url .= '</image:loc>';
                            $seopress_sitemap_url .= "\n";

                            if ('' != $seopress_image_title) {
                                $seopress_sitemap_url .= '<image:title>';
                                $seopress_sitemap_url .= $seopress_image_title;
                                $seopress_sitemap_url .= '</image:title>';
                                $seopress_sitemap_url .= "\n";
                            }

                            if ('' != $seopress_image_caption) {
                                $seopress_sitemap_url .= '<image:caption>';
                                $seopress_sitemap_url .= $seopress_image_caption;
                                $seopress_sitemap_url .= '</image:caption>';
                                $seopress_sitemap_url .= "\n";
                            }

                            $seopress_sitemap_url .= '</image:image>';
                            $seopress_sitemap_url .= "\n";
                        }
                    }
                    //Post thumbnail
                    if ('' != $post_thumbnail) {
                        $seopress_image_loc = '<![CDATA[' . $post_thumbnail . ']]>';

                        $seopress_image_title = '';
                        if ('' != get_the_title($post_thumbnail_id)) {
                            $title                = htmlspecialchars(get_the_title($post_thumbnail_id));
                            $seopress_image_title = '<![CDATA[' . $title . ']]>';
                        }

                        $seopress_image_caption = '';
                        if ('' != get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true)) {
                            $caption = htmlspecialchars(get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true));
                            $seopress_image_caption .= '<![CDATA[' . $caption . ']]>';
                        }

                        $seopress_url['images'][] = [
                            'src'     => $seopress_image_loc,
                            'title'   => $seopress_image_title,
                            'caption' => $seopress_image_caption,
                        ];

                        /*
                         * Build up the template.
                         */
                        $seopress_sitemap_url .= '<image:image>';
                        $seopress_sitemap_url .= "\n";
                        $seopress_sitemap_url .= '<image:loc>';
                        $seopress_sitemap_url .= $seopress_image_loc;
                        $seopress_sitemap_url .= '</image:loc>';
                        $seopress_sitemap_url .= "\n";

                        if ('' != $seopress_image_title) {
                            $seopress_sitemap_url .= '<image:title>';
                            $seopress_sitemap_url .= $seopress_image_title;
                            $seopress_sitemap_url .= '</image:title>';
                            $seopress_sitemap_url .= "\n";
                        }

                        if ('' != $seopress_image_caption) {
                            $seopress_sitemap_url .= '<image:caption>';
                            $seopress_sitemap_url .= $seopress_image_caption;
                            $seopress_sitemap_url .= '</image:caption>';
                            $seopress_sitemap_url .= "\n";
                        }

                        $seopress_sitemap_url .= '</image:image>';
                    }
                }
                $seopress_sitemap_url = apply_filters('seopress_sitemaps_single_img', $seopress_sitemap_url, $post);
            }
        }
        $seopress_sitemap_url .= '</url>';
        $seopress_sitemap_url .= "\n";
        $seopress_sitemaps .= apply_filters('seopress_sitemaps_url', $seopress_sitemap_url, $seopress_url);
    }
    wp_reset_postdata();

    $seopress_sitemaps .= '</urlset>';

    $seopress_sitemaps = apply_filters('seopress_sitemaps_xml_single', $seopress_sitemaps);

    return $seopress_sitemaps;
}
echo seopress_xml_sitemap_single();

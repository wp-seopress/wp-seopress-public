<?php
defined('ABSPATH') or exit('Cheatin&#8217; uh?');

if ('' !== get_query_var('seopress_cpt')) {
    $path = get_query_var('seopress_cpt');
}

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
echo '<?xml version="1.0" encoding="UTF-8"?>';
printf('<?xml-stylesheet type="text/xsl" href="%s"?>', $home_url . 'sitemaps_xsl.xsl');

$urlset = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

echo apply_filters('seopress_sitemaps_urlset', $urlset);

if (true == get_post_type_archive_link($path) && 0 == $offset) {
    if ( ! function_exists('seopress_get_service')) {
        return;
    }
    if ('1' != seopress_get_service('TitleOption')->getTitlesCptNoIndexByPath($path)) {
        $sitemap_url = '';
        // array with all the information needed for a sitemap url
        $seopress_url = [
            'loc'    => htmlspecialchars(urldecode(get_post_type_archive_link($path))),
            'mod'    => '',
            'images' => [],
        ];
        $sitemap_url = sprintf("<url>\n<loc>%s</loc>\n</url>", htmlspecialchars(urldecode(get_post_type_archive_link($path))));

        $sitemap_url = apply_filters('seopress_sitemaps_no_archive_link', $sitemap_url, $path);

        echo apply_filters('seopress_sitemaps_url', $sitemap_url, $seopress_url);
    }
}

remove_all_filters('pre_get_posts');

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
    'lang'         => '',
    'has_password' => false
];

if ('attachment' === $path) {
    unset($args['post_status']);
}

if ( $path == 'product' ) {
    $args['tax_query'][] = [
        'taxonomy' => 'product_visibility',
        'field'    => 'slug',
        'terms'    => ['exclude-from-catalog'],
        'operator' => 'NOT IN',
    ];
}

// Polylang: remove hidden languages
if (function_exists('get_languages_list') && is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php')) {
    $languages = PLL()->model->get_languages_list();
    if ( wp_list_filter( $languages, array( 'active' => false ) ) ) {
        $args['lang'] = wp_list_pluck( wp_list_filter( $languages, array( 'active' => false ), 'NOT' ), 'slug' );
    }
}

$args = apply_filters('seopress_sitemaps_single_query', $args, $path);

$postslist = get_posts($args);
foreach ($postslist as $post) {
    setup_postdata($post);

    $dom    = '';
    $images = '';

    $modified_date = '';
    if (get_the_modified_date('c', $post)) {
        $modified_date = get_the_modified_date('c', $post);
    } else {
        $modified_date = get_post_modified_time('c', false, $post);
    }

    $post_date = get_the_date('c', $post);
    $seopress_mod = $modified_date;

    if(!empty($modified_date)){
        if((new DateTime($post_date)) > (new DateTime($modified_date))){
            $seopress_mod = $post_date;
        }
    }

    // initialize the sitemap url output
    $sitemapData = '';
    // array with all the information needed for a sitemap url
    $seopress_url = [
        'loc'    => htmlspecialchars(urldecode(get_permalink($post))),
        'mod'    => $seopress_mod,
        'images' => [],
    ];

    $seopress_url = apply_filters( 'seopress_sitemaps_single_url', $seopress_url, $post );

    if (!empty($seopress_url['loc'])) {
        $sitemapData .= sprintf("\n<url>\n<loc>%s</loc>\n<lastmod>%s</lastmod>", $seopress_url['loc'], $seopress_url['mod']);

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
                $post_thumbnail    = get_the_post_thumbnail_url($post, 'full');
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
                                            $seopress_image_loc = sprintf('<![CDATA[%s://%s]]>', $parse_url['scheme'], $parse_url['host'] . $parse_url['path']);
                                        } else {
                                            $seopress_image_loc = '<![CDATA[' . $url . ']]>';
                                            $seopress_image_loc = sprintf('<![CDATA[%s]]>', $url);
                                        }
                                        $seopress_image_caption = '';
                                        if ('' != $img->getAttribute('alt')) {
                                            $caption                = htmlspecialchars($img->getAttribute('alt'));
                                            $seopress_image_caption = sprintf('<![CDATA[%s]]>', $caption);
                                        }
                                        $seopress_image_title = '';
                                        if ('' != $img->getAttribute('title')) {
                                            $title                = htmlspecialchars($img->getAttribute('title'));
                                            $seopress_image_title = sprintf('<![CDATA[%s]]>', $title);
                                        }

                                        $seopress_url['images'][] = [
                                            'src'   => $seopress_image_loc,
                                            'title' => $seopress_image_title,
                                            'alt'   => $seopress_image_caption,
                                        ];

                                        /*
                                        * Build up the template.
                                        */
                                        $sitemapData .= sprintf("\n<image:image>\n<image:loc>%s</image:loc>", $seopress_image_loc);

                                        if ('' != $seopress_image_title) {
                                            $sitemapData .= sprintf("\n<image:title>%s</image:title>", $seopress_image_title);
                                        }

                                        if ('' != $seopress_image_caption) {
                                            $sitemapData .= sprintf("\n<image:caption>%s</image:caption>", $seopress_image_caption);
                                        }

                                        $sitemapData .= "\n</image:image>";
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
                                $title                = htmlspecialchars(get_the_title($product_attachment_id));
                                $seopress_image_title = sprintf('<![CDATA[%s]]>', $title);
                            }

                            $seopress_image_caption = '';
                            if ('' != get_post_meta($product_attachment_id, '_wp_attachment_image_alt', true)) {
                                $caption                = htmlspecialchars(get_post_meta($product_attachment_id, '_wp_attachment_image_alt', true));
                                $seopress_image_caption = sprintf('<![CDATA[%s]]>', $caption);
                            }

                            $seopress_url['images'][] = [
                                'src'     => $seopress_image_loc,
                                'title'   => $seopress_image_title,
                                'caption' => $seopress_image_caption,
                            ];

                            /*
                            * Build up the template.
                            */

                            $sitemapData .= sprintf("\n<image:image>\n<image:loc>%s</image:loc>", $seopress_image_loc);

                            if ('' != $seopress_image_title) {
                                $sitemapData .= sprintf("\n<image:title>%s</image:title>", $seopress_image_title);
                            }

                            if ('' != $seopress_image_caption) {
                                $sitemapData .= sprintf("\n<image:caption>%s</image:caption>", $seopress_image_caption);
                            }

                            $sitemapData .= "\n</image:image>";
                        }
                    }
                    //Post thumbnail
                    if ('' != $post_thumbnail) {
                        $seopress_image_loc = '<![CDATA[' . $post_thumbnail . ']]>';

                        $seopress_image_title = '';
                        if ('' != get_the_title($post_thumbnail_id)) {
                            $title                = htmlspecialchars(get_the_title($post_thumbnail_id));
                            $seopress_image_title = sprintf('<![CDATA[%s]]>', $title);
                        }

                        $seopress_image_caption = '';
                        if ('' != get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true)) {
                            $caption                = htmlspecialchars(get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true));
                            $seopress_image_caption = sprintf('<![CDATA[%s]]>', $caption);
                        }

                        $seopress_url['images'][] = [
                            'src'     => $seopress_image_loc,
                            'title'   => $seopress_image_title,
                            'caption' => $seopress_image_caption,
                        ];

                        /*
                        * Build up the template.
                        */
                        $sitemapData .= sprintf("\n<image:image>\n<image:loc>%s</image:loc>", $seopress_image_loc);

                        if ('' != $seopress_image_title) {
                            $sitemapData .= sprintf("\n<image:title>%s</image:title>", $seopress_image_title);
                        }

                        if ('' != $seopress_image_caption) {
                            $sitemapData .= sprintf("\n<image:caption>%s</image:caption>", $seopress_image_caption);
                        }

                        $sitemapData .= "\n</image:image>";
                    }
                }

                $sitemapData = apply_filters('seopress_sitemaps_single_img', $sitemapData, $post);
            }
        }
        $sitemapData .= '</url>';
    }

    echo apply_filters('seopress_sitemaps_url', $sitemapData, $seopress_url);
}
wp_reset_postdata();
?>
</urlset>

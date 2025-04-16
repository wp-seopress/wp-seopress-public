<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

// No /category/ in URL
if (!empty(seopress_get_service('AdvancedOption')->getAdvancedRemoveCategoryURL())) {
    // Flush permalinks when creating/editing/deleting post categories
    add_action('created_category', 'flush_rewrite_rules');
    add_action('delete_category', 'flush_rewrite_rules');
    add_action('edited_category', 'flush_rewrite_rules');

    //@credits : WordPress VIP
    add_filter('category_rewrite_rules', 'seopress_filter_category_rewrite_rules');
    function seopress_filter_category_rewrite_rules($rules)
    {
        $categories = [];
        if (class_exists('Sitepress')) {
            global $sitepress;
            remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
            $categories = get_categories(['hide_empty' => false]);
            add_filter('terms_clauses', [$sitepress, 'terms_clauses'], 10, 4);
        } else {
            $categories = get_categories(['hide_empty' => false]);
        }

        if (!empty($categories)) {
            $slugs = array_map(function($category) {
                if (is_object($category) && !is_wp_error($category)) {
                    return (0 == $category->category_parent) ? $category->slug : trim(get_category_parents($category->term_id, false, '/', true), '/');
                }
            }, $categories);

            $slugs = array_filter($slugs); // Remove any null values

            if (!empty($slugs)) {
                $rules = array_reduce($slugs, function($carry, $slug) {
                    $carry['(' . $slug . ')/feed/(feed|rdf|rss|rss2|atom)?/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                    $carry['(' . $slug . ')/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                    $carry['(' . $slug . ')(/page/(\d+))?/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
                    return $carry;
                }, []);
            }
        }
        return apply_filters('seopress_category_rewrite_rules', $rules);
    }

    function seopress_remove_category_base($termlink, $term, $taxonomy)
    {
        if ('category' !== $taxonomy) {
            return $termlink;
        }

        $category_base = get_option('category_base') ?: 'category';
        if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
            $category_base = apply_filters('wpml_translate_single_string', $category_base, 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE);
        }

        $category_base = apply_filters('seopress_remove_category_base', $category_base);
        $category_base = ltrim($category_base, '/') . '/';

        return preg_replace('`' . preg_quote($category_base, '`') . '`u', '', $termlink, 1);
    }
    add_filter('term_link', 'seopress_remove_category_base', 10, 3);

    add_action('template_redirect', 'seopress_category_redirect', 1);
    function seopress_category_redirect()
    {
        if (!is_404()) {
            return;
        }

        global $wp;
        $current_url = user_trailingslashit(home_url(add_query_arg([], $wp->request)));

        $category_base = get_option('category_base') ?: 'category';
        if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
            $category_base = apply_filters('wpml_translate_single_string', $category_base, 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE);
        }

        $category_base = apply_filters('seopress_remove_category_base', $category_base);
        $regex = sprintf('/\/%s\//', str_replace('/', '\/', $category_base));

        if (preg_match($regex, $current_url)) {
            $new_url = str_replace('/' . $category_base, '', $current_url);
            wp_redirect($new_url, 301);
            exit();
        }
    }
}

// No /product-category/ in URL
if (!empty(seopress_get_service('AdvancedOption')->getAdvancedRemoveProductCategoryURL())) {
    // Flush permalinks when creating/editing/deleting product categories
    add_action('created_product_cat', 'flush_rewrite_rules');
    add_action('delete_product_cat', 'flush_rewrite_rules');
    add_action('edited_product_cat', 'flush_rewrite_rules');
    add_action('edited_term_taxonomy', 'flush_rewrite_rules');

    add_filter('product_cat_rewrite_rules', 'seopress_filter_product_category_rewrite_rules');
    function seopress_filter_product_category_rewrite_rules($rules)
    {
        $categories = [];
        if (class_exists('Sitepress')) {
            global $sitepress;
            remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
            $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
            add_filter('terms_clauses', [$sitepress, 'terms_clauses'], 10, 4);
        } else {
            $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        }

        if (!empty($categories)) {
            $slugs = array_map(function($category) {
                if (is_object($category) && !is_wp_error($category)) {
                    return (0 == $category->parent) ? $category->slug : trim(get_term_parents_list($category->term_id, 'product_cat', ['separator' => '/', 'link' => false]), '/');
                }
            }, $categories);

            $slugs = array_filter($slugs); // Remove any null values

            if (!empty($slugs)) {
                $rules = array_reduce($slugs, function($carry, $slug) {
                    $carry['(' . $slug . ')(/page/(\d+))?/?$'] = 'index.php?product_cat=$matches[1]&paged=$matches[3]';
                    $carry[$slug . '/(.+?)/page/?([0-9]{1,})/?$'] = 'index.php?product_cat=$matches[1]&paged=$matches[2]';
                    $carry[$slug . '/(.+?)/?$'] = 'index.php?product_cat=$matches[1]';
                    $carry[$slug . '/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                    $carry[$slug . '/(.+?)/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                    $carry[$slug . '/(.+?)/embed/?$'] = 'index.php?product_cat=$matches[1]&embed=true';
                    return $carry;
                }, []);
            }
        }
        return apply_filters('seopress_product_category_rewrite_rules', $rules);
    }

    function seopress_remove_product_category_base($termlink, $term, $taxonomy)
    {
        if ('product_cat' !== $taxonomy) {
            return $termlink;
        }

        $category_base = get_option('woocommerce_permalinks')['category_base'] ?: 'product-category';
        if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
            $category_base = apply_filters('wpml_translate_single_string', $category_base, 'WordPress', 'URL product_cat tax slug', ICL_LANGUAGE_CODE);
        }

        $category_base = apply_filters('seopress_remove_product_category_base', $category_base);
        $category_base = ltrim($category_base, '/') . '/';

        return preg_replace('`' . preg_quote($category_base, '`') . '`u', '', $termlink, 1);
    }
    add_filter('term_link', 'seopress_remove_product_category_base', 10, 3);

    add_action('template_redirect', 'seopress_product_category_redirect', 1);
    function seopress_product_category_redirect()
    {
        if (!is_404()) {
            return;
        }

        global $wp;
        $current_url = user_trailingslashit(home_url(add_query_arg([], $wp->request)));

        $category_base = get_option('woocommerce_permalinks')['category_base'] ?: 'product-category';
        if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
            $category_base = apply_filters('wpml_translate_single_string', $category_base, 'WordPress', 'URL product_cat tax slug', ICL_LANGUAGE_CODE);
        }

        $category_base = apply_filters('seopress_remove_product_category_base', $category_base);
        $regex = sprintf('/\/%s\//', str_replace('/', '\/', $category_base));

        if (preg_match($regex, $current_url)) {
            $new_url = str_replace('/' . $category_base, '', $current_url);
            wp_redirect($new_url, 301);
            exit();
        }
    }
}
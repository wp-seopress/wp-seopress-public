<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
function seopress_get_dynamic_variables($variables, $post, $is_oembed){
    //Init
    if (isset($is_oembed) && $is_oembed === false) {
        global $post;
    }
    global $term;
    global $wp_query;


    $seopress_titles_title_template       ='';
    $seopress_titles_description_template ='';
    $seopress_paged                       ='1';
    $seopress_context_paged               ='';
    $the_author_meta                      ='';
    $sep                                  ='';
    $seopress_get_post_title              ='';
    $seopress_excerpt                     ='';
    $seopress_content                     ='';
    $post_thumbnail_url                   ='';
    $post_url                             ='';
    $post_category                        ='';
    $post_tag                             ='';
    $get_search_query                     ='';
    $woo_single_cat_html                  ='';
    $woo_single_tag_html                  ='';
    $woo_single_price                     ='';
    $woo_single_price_exc_tax             ='';
    $woo_single_sku                       ='';
    $author_first_name                    ='';
    $author_last_name                     ='';
    $author_website                       ='';
    $author_nickname                      ='';
    $author_bio                           ='';
    $target_kw                            ='';
    $month_name_archive                   ='';

    //Excerpt length
    $seopress_excerpt_length = 50;
    $seopress_excerpt_length = apply_filters('seopress_excerpt_length', $seopress_excerpt_length);

    //Remove WordPress Filters
    $seopress_array_filters = ['category_description', 'tag_description', 'term_description'];
    foreach ($seopress_array_filters as $key => $value) {
        remove_filter($value, 'wpautop');
    }

    //Template variables
    if (seopress_get_service('TitleOption')->getSeparator()) {
        $sep = htmlspecialchars(seopress_get_service('TitleOption')->getSeparator());
    } else {
        $sep = '-';
    }

    if ( ! is_404() && '' != $post) {
        if (has_excerpt($post->ID)) {
            $seopress_excerpt = get_the_excerpt();
            $seopress_content = get_post_field('post_content', $post->ID);
        }
    }

    if (get_query_var('paged') > '1') {
        $seopress_paged = get_query_var('paged');
        $seopress_paged = apply_filters('seopress_paged', $seopress_paged);
    } else {
        $seopress_paged = '';
    }

    if (isset($wp_query->max_num_pages)) {
        if (get_query_var('paged') > 1) {
            $current_page = get_query_var('paged');
        } else {
            $current_page = 1;
        }

        if (get_query_var('paged') > 1) {
            /* translators: %1$d current page (e.g. 2) %2$d total number of pages (e.g. 30) */
            $seopress_context_paged = ' ' . $sep . ' ' . sprintf(esc_attr__('Page %1$d of %2$d', 'wp-seopress'), esc_attr($current_page), esc_attr($wp_query->max_num_pages));
        }
        $seopress_context_paged = apply_filters('seopress_context_paged', $seopress_context_paged);
    }

    if ((is_singular() || $is_oembed === true) && isset($post->post_author)) {
        $the_author_meta   = esc_attr(get_the_author_meta('display_name', $post->post_author));
        $author_first_name = esc_attr(get_the_author_meta('first_name', $post->post_author));
        $author_last_name  = esc_attr(get_the_author_meta('last_name', $post->post_author));
        $author_website    = esc_attr(get_the_author_meta('url', $post->post_author));
        $author_nickname   = esc_attr(get_the_author_meta('nickname', $post->post_author));
        $author_bio        = esc_attr(get_the_author_meta('description', $post->post_author));
    }

    if ((is_singular() || $is_oembed === true) && get_post_meta($post->ID, '_seopress_analysis_target_kw', true)) {
        $target_kw = get_post_meta($post->ID, '_seopress_analysis_target_kw', true);
    }

    if (is_author() && is_int(get_queried_object_id())) {
        $user_info = get_userdata(get_queried_object_id());

        if (isset($user_info) && is_object($user_info)) {
            $the_author_meta   = esc_attr($user_info->display_name);
            $author_first_name = esc_attr($user_info->first_name);
            $author_last_name  = esc_attr($user_info->last_name);
            $author_website    = esc_attr($user_info->url);
            $author_nickname   = esc_attr($user_info->nickname);
            $author_bio        = esc_attr($user_info->description);
        }
    }

    if ((is_singular() || $is_oembed === true) && isset($post)) {
        $post_thumbnail_url = get_the_post_thumbnail_url($post, 'full');
        $post_thumbnail_url = apply_filters('seopress_titles_post_thumbnail_url', $post_thumbnail_url);
    }

    if ((is_singular() || $is_oembed === true) && isset($post)) {
        $post_url = esc_url(get_permalink($post));
        $post_url = apply_filters('seopress_titles_post_url', $post_url);
    }

    if ((is_single() || $is_oembed === true) && has_category('', $post)) {
        $post_category_array = get_the_terms($post->ID, 'category');
        $post_category       = $post_category_array[0]->name;
        $post_category       = apply_filters('seopress_titles_cat', $post_category);
    }

    if ((is_single() || $is_oembed === true) && has_tag('', $post)) {
        $post_tag_array = get_the_terms($post->ID, 'post_tag');
        $post_tag       = $post_tag_array[0]->name;
        $post_tag       = apply_filters('seopress_titles_tag', $post_tag);
    }

    if ('' != get_search_query()) {
        $get_search_query = esc_attr('"' . get_search_query() . '"');
    } else {
        $get_search_query = esc_attr('" "');
    }
    $get_search_query = apply_filters('seopress_get_search_query', $get_search_query);

    //Post Title
    if ((is_singular() || $is_oembed === true) && isset($post)) {
        $seopress_get_post_title = get_post_field('post_title', $post->ID);
        $seopress_get_post_title = str_replace('<br>', ' ', $seopress_get_post_title);
        $seopress_get_post_title = esc_attr(strip_tags($seopress_get_post_title));
    }

    //Post Excerpt
    if ('' != $seopress_excerpt && true !== post_password_required($post->ID)) {
        $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($seopress_excerpt), true)))), $seopress_excerpt_length);
    } elseif ('' != $post && true !== post_password_required($post->ID)) {
        if ('' != get_post_field('post_content', $post->ID)) {
            $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post->ID), true))))), $seopress_excerpt_length);
        } else {
            $seopress_get_the_excerpt = null;
        }
    } else {
        $seopress_get_the_excerpt = null;
    }

    //Post Content
    if ('' != $post && true !== post_password_required($post->ID)) {
        if ('' != get_post_field('post_content', $post->ID)) {
            $seopress_content = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post->ID), true))))), $seopress_excerpt_length);
        } else {
            $seopress_content = null;
        }
    } else {
        $seopress_content = null;
    }

    //WooCommerce
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        if (is_singular(['product']) || $is_oembed === true) {
            //Woo Cat product
            $woo_single_cats = get_the_terms($post->ID, 'product_cat');

            if ($woo_single_cats && ! is_wp_error($woo_single_cats)) {
                $woo_single_cat = [];

                foreach ($woo_single_cats as $term) {
                    $woo_single_cat[$term->term_id] = $term->name;
                }

                $woo_single_cat = apply_filters('seopress_titles_product_cat', $woo_single_cat);

                $woo_single_cat_html = stripslashes_deep(wp_filter_nohtml_kses(join(', ', $woo_single_cat)));
            }

            //Woo Tag product
            $woo_single_tags = get_the_terms($post->ID, 'product_tag');

            if ($woo_single_tags && ! is_wp_error($woo_single_tags)) {
                $woo_single_tag = [];

                foreach ($woo_single_tags as $term) {
                    $woo_single_tag[$term->term_id] = $term->name;
                }

                $woo_single_tag = apply_filters('seopress_titles_product_tag', $woo_single_tag);

                $woo_single_tag_html = stripslashes_deep(wp_filter_nohtml_kses(join(', ', $woo_single_tag)));
            }

            if (isset($post->ID) && function_exists('wc_get_product')) {
                $product          = wc_get_product($post->ID);

                if (isset($product) && is_object($product)) {
                    //Woo Price
                    if (method_exists($product, 'get_price') && function_exists('wc_get_price_including_tax')) {
                        $woo_single_price = wc_get_price_including_tax($product);
                    }
                    //Woo Price tax excluded
                    if (method_exists($product, 'get_price') && function_exists('wc_get_price_excluding_tax')) {
                        $woo_single_price_exc_tax = wc_get_price_excluding_tax($product);
                    }
                    //Woo SKU Number
                    if (method_exists($product, 'get_sku')) {
                        $woo_single_sku = $product->get_sku();
                    }
                }
            }
        }
    }
    if (get_query_var('monthnum')) {
        global $wp_locale;
        $month_name_archive  = get_query_var('monthnum');
        if ( ! empty( $month_name_archive ) ) {
            $month_name_archive = esc_attr(wp_strip_all_tags($wp_locale->get_month( $month_name_archive ) ) );
        }
    }

    $seopress_titles_template_variables_array = [
        '%%sep%%',
        '%%sitetitle%%',
        '%%sitename%%',
        '%%tagline%%',
        '%%sitedesc%%',
        '%%title%%',
        '%%post_title%%',
        '%%post_excerpt%%',
        '%%excerpt%%',
        '%%post_content%%',
        '%%post_thumbnail_url%%',
        '%%post_url%%',
        '%%post_date%%',
        '%%date%%',
        '%%post_modified_date%%',
        '%%post_author%%',
        '%%post_category%%',
        '%%post_tag%%',
        '%%_category_title%%',
        '%%_category_description%%',
        '%%tag_title%%',
        '%%tag_description%%',
        '%%term_title%%',
        '%%term_description%%',
        '%%search_keywords%%',
        '%%current_pagination%%',
        '%%page%%',
        '%%cpt_plural%%',
        '%%archive_title%%',
        '%%archive_date%%',
        '%%archive_date_day%%',
        '%%archive_date_month%%',
        '%%archive_date_month_name%%',
        '%%archive_date_year%%',
        '%%wc_single_cat%%',
        '%%wc_single_tag%%',
        '%%wc_single_short_desc%%',
        '%%wc_single_price%%',
        '%%wc_single_price_exc_tax%%',
        '%%wc_sku%%',
        '%%currentday%%',
        '%%currentmonth%%',
        '%%currentmonth_short%%',
        '%%currentyear%%',
        '%%currentdate%%',
        '%%currenttime%%',
        '%%author_first_name%%',
        '%%author_last_name%%',
        '%%author_website%%',
        '%%author_nickname%%',
        '%%author_bio%%',
        '%%currentmonth_num%%',
        '%%target_keyword%%',
    ];

    $seopress_titles_template_variables_array = apply_filters('seopress_titles_template_variables_array', $seopress_titles_template_variables_array);

    $seopress_titles_template_replace_array = [
        $sep,
        get_bloginfo('name'),
        get_bloginfo('name'),
        get_bloginfo('description'),
        get_bloginfo('description'),
        $seopress_get_post_title,
        $seopress_get_post_title,
        $seopress_get_the_excerpt,
        $seopress_get_the_excerpt,
        $seopress_content,
        $post_thumbnail_url,
        $post_url,
        get_the_date('', $post),
        get_the_date('', $post),
        get_the_modified_date('', $post),
        $the_author_meta,
        $post_category,
        $post_tag,
        single_cat_title('', false),
        wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(category_description())), $seopress_excerpt_length),
        single_tag_title('', false),
        wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(tag_description())), $seopress_excerpt_length),
        single_term_title('', false),
        wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(term_description())), $seopress_excerpt_length),
        $get_search_query,
        $seopress_paged,
        $seopress_context_paged,
        post_type_archive_title('', false),
        get_the_archive_title(),
        get_query_var('monthnum') . ' - ' . get_query_var('year'),
        get_query_var('day'),
        get_query_var('monthnum'),
        $month_name_archive,
        get_query_var('year'),
        $woo_single_cat_html,
        $woo_single_tag_html,
        $seopress_get_the_excerpt,
        $woo_single_price,
        $woo_single_price_exc_tax,
        $woo_single_sku,
        date_i18n('j'),
        date_i18n('F'),
        date_i18n('M'),
        date('Y'),
        date_i18n(get_option('date_format')),
        current_time(get_option('time_format')),
        $author_first_name,
        $author_last_name,
        $author_website,
        $author_nickname,
        $author_bio,
        date_i18n('n'),
        $target_kw,
    ];

    $seopress_titles_template_replace_array = apply_filters('seopress_titles_template_replace_array', $seopress_titles_template_replace_array);

    $variables = [
        'post'                                     => $post,
        'term'                                     => $term,
        'seopress_titles_title_template'           => $seopress_titles_title_template,
        'seopress_titles_description_template'     => $seopress_titles_description_template,
        'seopress_paged'                           => $seopress_paged,
        'seopress_context_paged'                   => $seopress_context_paged,
        'the_author_meta'                          => $the_author_meta,
        'sep'                                      => $sep,
        'seopress_excerpt'                         => $seopress_excerpt,
        'post_category'                            => $post_category,
        'post_tag'                                 => $post_tag,
        'post_thumbnail_url'                       => $post_thumbnail_url,
        'post_url'                                 => $post_url,
        'get_search_query'                         => $get_search_query,
        'woo_single_cat_html'                      => $woo_single_cat_html,
        'woo_single_tag_html'                      => $woo_single_tag_html,
        'woo_single_price'                         => $woo_single_price,
        'woo_single_price_exc_tax'                 => $woo_single_price_exc_tax,
        'woo_single_sku'                           => $woo_single_sku,
        'author_first_name'                        => $author_first_name,
        'author_last_name'                         => $author_last_name,
        'author_website'                           => $author_website,
        'author_nickname'                          => $author_nickname,
        'author_bio'                               => $author_bio,
        'seopress_get_the_excerpt'                 => $seopress_get_the_excerpt,
        'seopress_titles_template_variables_array' => $seopress_titles_template_variables_array,
        'seopress_titles_template_replace_array'   => $seopress_titles_template_replace_array,
        'seopress_excerpt_length'                  => $seopress_excerpt_length,
    ];

    $variables = apply_filters('seopress_titles_template_variables', $variables);

    //Add WordPress Filters again
    $seopress_array_filters = ['category_description', 'tag_description', 'term_description'];
    foreach ($seopress_array_filters as $key => $value) {
        add_filter($value, 'wpautop');
    }

    return $variables;
}



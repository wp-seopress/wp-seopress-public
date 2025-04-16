<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* 
* SEO Framework migration
*/
function seopress_seo_framework_migration() {
    check_ajax_referer('seopress_seo_framework_migrate_nonce', '_ajax_nonce', true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");
        $total_count_terms = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->terms}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_postdata();
            $count_items = $total_count_posts;

            $args = [
                //'number' => $increment,
                'hide_empty' => false,
                //'offset' => $offset,
                'fields' => 'ids',
            ];
            $seo_framework_query_terms = get_terms($args);

            if ($seo_framework_query_terms) {
                foreach ($seo_framework_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, 'autodescription-term-settings', true)) {
                        $term_settings = get_term_meta($term_id, 'autodescription-term-settings', true);

                        if ( ! empty($term_settings['doctitle'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_settings['doctitle']);
                        }
                        if ( ! empty($term_settings['description'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_settings['description']);
                        }
                        if ( ! empty($term_settings['noindex'])) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($term_settings['nofollow'])) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                    }
                }
            }
            $offset = 'done';
            wp_reset_postdata();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $seo_framework_query = get_posts($args);

            if ($seo_framework_query) {
                foreach ($seo_framework_query as $post) {
                    if ('' != get_post_meta($post->ID, '_genesis_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', esc_html(get_post_meta($post->ID, '_genesis_title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_genesis_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', esc_html(get_post_meta($post->ID, '_genesis_description', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_open_graph_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', esc_html(get_post_meta($post->ID, '_open_graph_title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_open_graph_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', esc_html(get_post_meta($post->ID, '_open_graph_description', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_social_image_url', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', esc_url(get_post_meta($post->ID, '_social_image_url', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_twitter_title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', esc_html(get_post_meta($post->ID, '_twitter_title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_twitter_description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', esc_html(get_post_meta($post->ID, '_twitter_description', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_social_image_url', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url(get_post_meta($post->ID, '_social_image_url', true)));
                    }
                    if ('1' == get_post_meta($post->ID, '_genesis_noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_genesis_nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_genesis_canonical_uri', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', esc_url(get_post_meta($post->ID, '_genesis_canonical_uri', true)));
                    }
                    if ('' != get_post_meta($post->ID, 'redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes');
                        update_post_meta($post->ID, '_seopress_redirections_type', '301');
                        update_post_meta($post->ID, '_seopress_redirections_value', esc_url(get_post_meta($post->ID, 'redirect', true)));
                    }

                    //Primary category
                    if ('post' == get_post_type($post->ID)) {
                        $tax = 'category';
                    } elseif ('product' == get_post_type($post->ID)) {
                        $tax = 'product_cat';
                    }
                    if (isset($tax)) {
                        $primary_term = get_post_meta($post->ID, '_primary_term_'.$tax, true);

                        if ('' != $primary_term) {
                            update_post_meta($post->ID, '_seopress_robots_primary_cat', absint( $primary_term));
                        }
                    }
                }
            }
            $offset += $increment;

            if ($offset >= $total_count_posts) {
                $count_items = $total_count_posts;
            } else {
                $count_items = $offset;
            }
        }
        $data           = [];

        $data['count']          = $count_items;
        $data['total']          = $total_count_posts + $total_count_terms;

        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seo_framework_migration', 'seopress_seo_framework_migration');

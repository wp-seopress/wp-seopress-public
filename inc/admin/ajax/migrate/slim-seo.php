<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* 
* Slim SEO migration
*/
function seopress_slim_seo_migration() {
    check_ajax_referer('seopress_slim_seo_migrate_nonce', '_ajax_nonce', true);

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
            $slim_seo_query_terms = get_terms($args);

            if ($slim_seo_query_terms) {
                foreach ($slim_seo_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, 'slim_seo', true)) {
                        $term_settings = get_term_meta($term_id, 'slim_seo', true);

                        if ( ! empty($term_settings['title'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', esc_html($term_settings['title']));
                        }
                        if ( ! empty($term_settings['description'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', esc_html($term_settings['description']));
                        }
                        if ( ! empty($term_settings['noindex'])) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($term_settings['facebook_image'])) { //Import FB image
                            update_term_meta($term_id, '_seopress_social_fb_img', esc_url($term_settings['facebook_image']));
                        }
                        if ( ! empty($term_settings['twitter_image'])) { //Import Tw image
                            update_term_meta($term_id, '_seopress_social_twitter_img', esc_url($term_settings['twitter_image']));
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

            $slim_seo_query = get_posts($args);

            if ($slim_seo_query) {
                foreach ($slim_seo_query as $post) {
                    if ('' != get_post_meta($post->ID, 'slim_seo', true)) {
                        $post_settings = get_post_meta($post->ID, 'slim_seo', true);

                        if ( ! empty($post_settings['title'])) { //Import title tag
                            update_post_meta($post->ID, '_seopress_titles_title', esc_html($post_settings['title']));
                        }
                        if ( ! empty($post_settings['description'])) { //Import meta desc
                            update_post_meta($post->ID, '_seopress_titles_desc', esc_html($post_settings['description']));
                        }
                        if ( ! empty($post_settings['noindex'])) { //Import Robots NoIndex
                            update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($post_settings['facebook_image'])) { //Import FB image
                            update_post_meta($post->ID, '_seopress_social_fb_img', esc_url($post_settings['facebook_image']));
                        }
                        if ( ! empty($post_settings['twitter_image'])) { //Import Tw image
                            update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url($post_settings['twitter_image']));
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
add_action('wp_ajax_seopress_slim_seo_migration', 'seopress_slim_seo_migration');

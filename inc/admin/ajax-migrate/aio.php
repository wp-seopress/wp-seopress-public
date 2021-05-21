<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//AIO migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_aio_migration() {
    check_ajax_referer('seopress_aio_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            wp_reset_query();

            $args = [
                //'number' => $increment,
                'hide_empty' => false,
                //'offset' => $offset,
                'fields' => 'ids',
            ];
            $aio_query_terms = get_terms($args);

            if ($aio_query_terms) {
                foreach ($aio_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, '_aioseop_title', true)) { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_term_meta($term_id, '_aioseop_title', true));
                    }
                    if ('' != get_term_meta($term_id, '_aioseop_description', true)) { //Import meta desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_term_meta($term_id, '_aioseop_description', true));
                    }
                    if ('' != get_term_meta($term_id, '_aioseop_opengraph_settings', true)) { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_term_meta($term_id, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                            update_term_meta($term_id, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }
                    if ('' != get_term_meta($term_id, '_aioseop_opengraph_settings', true)) { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_desc'])) {
                            update_term_meta($term_id, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                        }
                    }
                    if ('' != get_term_meta($term_id, '_aioseop_opengraph_settings', true)) { //Import Facebook Image
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_term_meta($term_id, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }
                    if ('' != get_term_meta($term_id, '_aioseop_opengraph_settings', true)) { //Import Twitter Image
                        $_aioseop_opengraph_settings = get_term_meta($term_id, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_term_meta($term_id, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }
                    if ('on' == get_term_meta($term_id, '_aioseop_noindex', true)) { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_index', 'yes');
                    }
                    if ('on' == get_term_meta($term_id, '_aioseop_nofollow', true)) { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                    }
                }
            }
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $aio_query = get_posts($args);

            if ($aio_query) {
                foreach ($aio_query as $post) {
                    if ('' != get_post_meta($post->ID, '_aioseop_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseop_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_aioseop_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseop_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import Facebook / Twitter Title
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import Facebook / Twitter Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_desc'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_desc']);
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import Facebook Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import Twitter Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }
                    if ('on' == get_post_meta($post->ID, '_aioseop_noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('on' == get_post_meta($post->ID, '_aioseop_nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_aio_migration', 'seopress_aio_migration');

<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* wpSEO migration
* @since 4.0
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_wpseo_migration() {
    check_ajax_referer('seopress_wpseo_migrate_nonce', $_POST['_ajax_nonce'], true);

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
            wp_reset_query();
            $count_items = $total_count_posts;

            $args = [
                'hide_empty' => false,
                'fields'     => 'ids',
            ];
            $wpseo_query_terms = get_terms($args);

            if ($wpseo_query_terms) {
                foreach ($wpseo_query_terms as $term_id) {
                    if ('' != get_option('wpseo_category_' . $term_id . '_title')) { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', get_option('wpseo_category_' . $term_id . '_title'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id)) { //Import meta desc
                        update_term_meta($term_id, '_seopress_titles_desc', get_option('wpseo_category_' . $term_id));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_title')) { //Import Facebook Title
                        update_term_meta($term_id, '_seopress_social_fb_title', get_option('wpseo_category_' . $term_id . '_og_title'));
                        update_term_meta($term_id, '_seopress_social_twitter_title', get_option('wpseo_category_' . $term_id . '_og_title'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_desc')) { //Import Facebook Desc
                        update_term_meta($term_id, '_seopress_social_fb_desc', get_option('wpseo_category_' . $term_id . '_og_desc'));
                        update_term_meta($term_id, '_seopress_social_twitter_desc', get_option('wpseo_category_' . $term_id . '_og_desc'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_og_image')) { //Import Facebook Image
                        update_term_meta($term_id, '_seopress_social_fb_img', get_option('wpseo_category_' . $term_id . '_og_image'));
                        update_term_meta($term_id, '_seopress_social_twitter_img', get_option('wpseo_category_' . $term_id . '_og_image'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_canonical')) { //Import Canonical URL
                        update_term_meta($term_id, '_seopress_robots_canonical', get_option('wpseo_category_' . $term_id . '_canonical'));
                    }
                    if ('' != get_option('wpseo_category_' . $term_id . '_redirect')) { //Import Redirect URL
                        update_term_meta($term_id, '_seopress_redirections_value', get_option('wpseo_category_' . $term_id . '_redirect'));
                        update_term_meta($term_id, '_seopress_redirections_enabled', 'yes');
                    }
                    if ('4' == get_option('wpseo_category_' . $term_id . '_robots') || '5' == get_option('wpseo_category_' . $term_id . '_robots') || '3' == get_option('wpseo_category_' . $term_id . '_robots')) { //Import Robots NoIndex
                        update_term_meta($term_id, '_seopress_robots_index', 'yes');
                    }
                    if ('2' == get_option('wpseo_category_' . $term_id . '_robots')) { //Import Robots NoFollow
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

            $wpseo_query = get_posts($args);

            if ($wpseo_query) {
                foreach ($wpseo_query as $post) {
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_wpseo_edit_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_wpseo_edit_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_wpseo_edit_og_title', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_wpseo_edit_og_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_wpseo_edit_og_description', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_wpseo_edit_og_description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_og_image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_wpseo_edit_og_image', true));
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_wpseo_edit_og_image', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_keyword_0', true)) { //Import Target Keyword
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_post_meta($post->ID, '_wpseo_edit_keyword_0', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_wpseo_edit_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wpseo_edit_redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, '_wpseo_edit_redirect', true));
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes'); //Enable the redirect
                    }
                    if ('4' == get_post_meta($post->ID, '_wpseo_edit_robots', true) || '5' == get_post_meta($post->ID, '_wpseo_edit_robots', true) || '3' == get_post_meta($post->ID, '_wpseo_edit_robots', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('2' == get_post_meta($post->ID, '_wpseo_edit_robots', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
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
add_action('wp_ajax_seopress_wpseo_migration', 'seopress_wpseo_migration');

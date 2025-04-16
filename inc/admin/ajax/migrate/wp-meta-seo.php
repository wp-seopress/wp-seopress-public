<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* 
* WP Meta SEO migration
*/
function seopress_wp_meta_seo_migration() {
    check_ajax_referer('seopress_meta_seo_migrate_nonce', '_ajax_nonce', true);

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
                'hide_empty' => false,
                'fields'     => 'ids',
            ];
            $wp_meta_seo_query_terms = get_terms($args);

            if ($wp_meta_seo_query_terms) {

                foreach ($wp_meta_seo_query_terms as $term_id) {
                    if ('' != get_term_meta($term_id, 'wpms_category_metatitle', true)) { //Import title tag
                        update_term_meta($term_id, '_seopress_titles_title', esc_html(get_term_meta($term_id, 'wpms_category_metatitle', true)));
                    }
                    if ('' != get_term_meta($term_id, 'wpms_category_metadesc', true)) { //Import title desc
                        update_term_meta($term_id, '_seopress_titles_desc', esc_html(get_term_meta($term_id, 'wpms_category_metadesc', true)));
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

            $wp_meta_seo_query = get_posts($args);

            if ($wp_meta_seo_query) {
                foreach ($wp_meta_seo_query as $post) {
                    if ('' != get_post_meta($post->ID, '_metaseo_metatitle', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', esc_html(get_post_meta($post->ID, '_metaseo_metatitle', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', esc_html(get_post_meta($post->ID, '_metaseo_metadesc', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', esc_html(get_post_meta($post->ID, '_metaseo_metaopengraph-title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', esc_html(get_post_meta($post->ID, '_metaseo_metaopengraph-desc', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metaopengraph-image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', esc_url(get_post_meta($post->ID, '_metaseo_metaopengraph-image', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', esc_html(get_post_meta($post->ID, '_metaseo_metatwitter-title', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-desc', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', esc_html(get_post_meta($post->ID, '_metaseo_metatwitter-desc', true)));
                    }
                    if ('' != get_post_meta($post->ID, '_metaseo_metatwitter-image', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url(get_post_meta($post->ID, '_metaseo_metatwitter-image', true)));
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
add_action('wp_ajax_seopress_wp_meta_seo_migration', 'seopress_wp_meta_seo_migration');

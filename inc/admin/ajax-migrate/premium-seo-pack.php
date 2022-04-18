<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* Premium SEO Pack migration
* @since 3.8.7
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_premium_seo_pack_migration() {
    check_ajax_referer('seopress_premium_seo_pack_migrate_nonce', $_POST['_ajax_nonce'], true);

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
            $count_items = $total_count_posts;
            wp_reset_query();

            $premium_query_terms = get_option('psp_taxonomy_seo');

            if ($premium_query_terms) {
                foreach ($premium_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ('' != $term_value['psp_meta']['title']) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['psp_meta']['title']);
                        }
                        if ('' != $term_value['psp_meta']['description']) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['psp_meta']['description']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_titlu']) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['psp_meta']['facebook_titlu']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_desc']) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['psp_meta']['facebook_desc']);
                        }
                        if ('' != $term_value['psp_meta']['facebook_image']) { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', $term_value['psp_meta']['facebook_image']);
                        }
                        if ('noindex' == $term_value['psp_meta']['robots_index']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ('nofollow' == $term_value['psp_meta']['robots_follow']) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                        if ('' != $term_value['psp_meta']['canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['psp_meta']['canonical']);
                        }
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

            $premium_query = get_posts($args);

            if ($premium_query) {
                foreach ($premium_query as $post) {
                    $psp_meta = get_post_meta($post->ID, 'psp_meta', true);

                    if ( ! empty($psp_meta)) {
                        if ( ! empty($psp_meta['title'])) { //Import title tag
                            update_post_meta($post->ID, '_seopress_titles_title', $psp_meta['title']);
                        }
                        if ( ! empty($psp_meta['description'])) { //Import meta desc
                            update_post_meta($post->ID, '_seopress_titles_desc', $psp_meta['description']);
                        }
                        if ( ! empty($psp_meta['facebook_titlu'])) { //Import Facebook Title
                            update_post_meta($post->ID, '_seopress_social_fb_title', $psp_meta['facebook_titlu']);
                        }
                        if ( ! empty($psp_meta['facebook_desc'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $psp_meta['facebook_desc']);
                        }
                        if ( ! empty($psp_meta['facebook_image'])) { //Import Facebook Image
                            update_post_meta($post->ID, '_seopress_social_fb_img', $psp_meta['facebook_image']);
                        }
                        if ('noindex' == $psp_meta['robots_index']) { //Import Robots NoIndex
                            update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                        }
                        if ('nofollow' == $psp_meta['robots_follow']) { //Import Robots NoIndex
                            update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                        }
                        if ( ! empty($psp_meta['canonical'])) { //Import Canonical URL
                            update_post_meta($post->ID, '_seopress_robots_canonical', $psp_meta['canonical']);
                        }
                        if ( ! empty($psp_meta['mfocus_keyword'])) { //Import Focus Keywords
                            $target_kw = preg_split('/\r\n|\r|\n/', $psp_meta['mfocus_keyword']);

                            update_post_meta($post->ID, '_seopress_analysis_target_kw', implode(',', $target_kw));
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
add_action('wp_ajax_seopress_premium_seo_pack_migration', 'seopress_premium_seo_pack_migration');

<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* SmartCrawl migration
* @since 4.5
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_smart_crawl_migration() {
    check_ajax_referer('seopress_smart_crawl_migrate_nonce', $_POST['_ajax_nonce'], true);

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

            $smart_crawl_query_terms = get_option('wds_taxonomy_meta');

            if ($smart_crawl_query_terms) {
                foreach ($smart_crawl_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ( ! empty($term_value['wds_title'])) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['wds_title']);
                        }
                        if ( ! empty($term_value['wds_desc'])) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['wds_desc']);
                        }
                        if ( ! empty($term_value['opengraph']['title'])) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['opengraph']['title']);
                        }
                        if ( ! empty($term_value['opengraph']['description'])) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['opengraph']['description']);
                        }
                        if ( ! empty($term_value['opengraph']['images'])) { //Import Facebook Image
                            $image_id = $term_value['opengraph']['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_term_meta($term_id, '_seopress_social_fb_img', $img_url);
                            }
                        }
                        if ( ! empty($term_value['twitter']['title'])) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', $term_value['twitter']['title']);
                        }
                        if ( ! empty($term_value['twitter']['description'])) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $term_value['twitter']['description']);
                        }
                        if ( ! empty($term_value['twitter']['images'])) { //Import Facebook Image
                            $image_id = $term_value['twitter']['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_term_meta($term_id, '_seopress_social_twitter_img', $img_url);
                            }
                        }
                        if ( ! empty($term_value['wds_noindex']) && 'noindex' == $term_value['wds_noindex']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ( ! empty($term_value['wds_nofollow']) && 'nofollow' == $term_value['wds_nofollow']) { //Import Robots NoFollow
                            update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                        }
                        if ('' != $term_value['wds_canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['wds_canonical']);
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

            $smart_crawl_query = get_posts($args);

            if ($smart_crawl_query) {
                foreach ($smart_crawl_query as $post) {
                    if ('' != get_post_meta($post->ID, '_wds_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_wds_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_wds_metadesc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_opengraph', true)) {
                        $_wds_opengraph = get_post_meta($post->ID, '_wds_opengraph', true);
                        if ( ! empty($_wds_opengraph['title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_wds_opengraph['title']); //Import Facebook Title
                        }
                        if ( ! empty($_wds_opengraph['description'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_wds_opengraph['description']);
                        }
                        if ( ! empty($_wds_opengraph['images'])) { //Import Facebook Image
                            $image_id = $_wds_opengraph['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_post_meta($post->ID, '_seopress_social_fb_img', $img_url);
                            }
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_wds_twitter', true)) {
                        $_wds_twitter = get_post_meta($post->ID, '_wds_twitter', true);
                        if ( ! empty($_wds_twitter['title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_wds_twitter['title']); //Import Twitter Title
                        }
                        if ( ! empty($_wds_twitter['description'])) { //Import Twitter Desc
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_wds_twitter['description']);
                        }
                        if ( ! empty($_wds_twitter['images'])) { //Import Twitter Image
                            $image_id = $_wds_twitter['images'][0];
                            $img_url  = wp_get_attachment_url($image_id);

                            if (isset($img_url) && '' != $img_url) {
                                update_post_meta($post->ID, '_seopress_social_twitter_img', $img_url);
                            }
                        }
                    }
                    if ('1' === get_post_meta($post->ID, '_wds_meta-robots-noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' === get_post_meta($post->ID, '_wds_meta-robots-nofollow', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_wds_meta-robots-adv', true)) {
                        $robots = get_post_meta($post->ID, '_wds_meta-robots-adv', true);
                        if ('' != $robots) {
                            $robots = explode(',', $robots);

                            if (in_array('noarchive', $robots)) { //Import Robots NoArchive
                                update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                            }
                            if (in_array('nosnippet', $robots)) { //Import Robots NoSnippet
                                update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                            }
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_wds_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_wds_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_redirect', true)) { //Import Redirect URL
                        update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes');
                        update_post_meta($post->ID, '_seopress_redirections_type', '301');
                        update_post_meta($post->ID, '_seopress_redirections_value', get_post_meta($post->ID, '_wds_redirect', true));
                    }
                    if ('' != get_post_meta($post->ID, '_wds_focus-keywords', true)) { //Import Focus Keywords
                        update_post_meta($post->ID, '_seopress_analysis_target_kw', get_post_meta($post->ID, '_wds_focus-keywords', true));
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
add_action('wp_ajax_seopress_smart_crawl_migration', 'seopress_smart_crawl_migration');

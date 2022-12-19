<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
/* SEOPressor migration
* @since 4.5
* @author Benjamin Denis
*/
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_seopressor_migration() {
    check_ajax_referer('seopress_seopressor_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = 'done';
            wp_reset_query();
        } else {
            $args = [
                'posts_per_page' => $increment,
                'post_type'      => 'any',
                'post_status'    => 'any',
                'offset'         => $offset,
            ];

            $su_query = get_posts($args);

            if ($su_query) {
                foreach ($su_query as $post) {
                    if ( ! empty(get_post_meta($post->ID, '_seop_settings', true))) {
                        $_seop_settings = get_post_meta($post->ID, '_seop_settings', true);

                        if ( ! empty($_seop_settings['meta_title'])) { //Import title tag
                            update_post_meta($post->ID, '_seopress_titles_title', $_seop_settings['meta_title']);
                        }
                        if ( ! empty($_seop_settings['meta_description'])) { //Import meta desc
                            update_post_meta($post->ID, '_seopress_titles_desc', $_seop_settings['meta_description']);
                        }
                        if ( ! empty($_seop_settings['fb_title'])) { //Import Facebook Title
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_seop_settings['fb_title']);
                        }
                        if ( ! empty($_seop_settings['fb_description'])) { //Import Facebook Desc
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_seop_settings['fb_description']);
                        }
                        if ( ! empty($_seop_settings['fb_img'])) { //Import Facebook Image
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_seop_settings['fb_img']);
                        }
                        if ( ! empty($_seop_settings['tw_title'])) { //Import Twitter Title
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_seop_settings['tw_title']);
                        }
                        if ( ! empty($_seop_settings['tw_description'])) { //Import Twitter Desc
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_seop_settings['tw_description']);
                        }
                        if ( ! empty($_seop_settings['tw_image'])) { //Import Twitter Image
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_seop_settings['tw_image']);
                        }
                        if ( ! empty($_seop_settings['meta_rules'])) {
                            $robots = explode('#|#|#', $_seop_settings['meta_rules']);

                            if ( ! empty($robots)) {
                                if (in_array('noindex', $robots)) { //Import Robots NoIndex
                                    update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                                }
                                if (in_array('nofollow', $robots)) { //Import Robots NoFollow
                                    update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                                }
                                if (in_array('noarchive', $robots)) { //Import Robots NoArchive
                                    update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                                }
                                if (in_array('nosnippet', $robots)) { //Import Robots NoSnippet
                                    update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                                }
                                if (in_array('noimageindex', $robots)) { //Import Robots NoImageIndex
                                    update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                                }
                            }
                        }
                        if ('' != get_post_meta($post->ID, '_seop_kw_1', true) || '' != get_post_meta($post->ID, '_seop_kw_2', true) || '' != get_post_meta($post->ID, '_seop_kw_3', true)) { //Import Target Keyword
                            $kw   = [];
                            $kw[] = get_post_meta($post->ID, '_seop_kw_1', true);
                            $kw[] = get_post_meta($post->ID, '_seop_kw_2', true);
                            $kw[] = get_post_meta($post->ID, '_seop_kw_3', true);

                            $kw = implode(',', $kw);

                            if ( ! empty($kw)) {
                                update_post_meta($post->ID, '_seopress_analysis_target_kw', $kw);
                            }
                        }
                        if ( ! empty($_seop_settings['meta_canonical'])) { //Import Canonical URL
                            update_post_meta($post->ID, '_seopress_robots_canonical', $_seop_settings['meta_canonical']);
                        }
                        if ( ! empty($_seop_settings['meta_redirect'])) { //Import Redirect URL
                            update_post_meta($post->ID, '_seopress_redirections_value', $_seop_settings['meta_redirect']);
                            update_post_meta($post->ID, '_seopress_redirections_enabled', 'yes'); //Enable the redirect
                        }
                    }
                }
            }
            $offset += $increment;
        }
        $data           = [];
        $data['offset'] = $offset;
        $data['total'] = $total_count_posts;

        if ($offset >= $total_count_posts) {
            $data['count'] = $total_count_posts;
        } else {
            $data['count'] = $offset;
        }

        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seopressor_migration', 'seopress_seopressor_migration');

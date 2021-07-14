<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//AIO migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_aio_migration()
{
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
                    if ('' != get_post_meta($post->ID, '_aioseo_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseo_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_title', true)) { //Import old title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_aioseop_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_aioseo_description', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseo_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_description', true)) { //Import old meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_aioseop_description', true));
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_og_title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_aioseo_og_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_twitter_title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_aioseo_twitter_title', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Title
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_title', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_og_description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_aioseo_og_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    if ('' != get_post_meta($post->ID, '_aioseo_twitter_description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_aioseo_twitter_description', true));
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Desc
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_title'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_desc', $_aioseop_opengraph_settings['aioseop_opengraph_settings_title']);
                        }
                    }

                    $canonical_url = "SELECT p.canonical_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $canonical_url = $wpdb->get_results($canonical_url, ARRAY_A);

                    if (! empty($canonical_url[0]['canonical_url'])) {//Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', $canonical_url[0]['canonical_url']);
                    }

                    $og_img_url = "SELECT p.og_image_custom_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.og_image_type = 'custom_image' AND p.post_id = $post->ID";

                    $og_img_url = $wpdb->get_results($og_img_url, ARRAY_A);

                    if (! empty($og_img_url[0]['og_image_custom_url'])) {//Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', $og_img_url[0]['og_image_custom_url']);
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
                            update_post_meta($post->ID, '_seopress_social_fb_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']);
                        }
                    }

                    $tw_img_url = "SELECT p.twitter_image_custom_url, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.twitter_image_type = 'custom_image' AND p.post_id = $post->ID";

                    $tw_img_url = $wpdb->get_results($tw_img_url, ARRAY_A);

                    if (! empty($tw_img_url[0]['twitter_image_custom_url'])) {//Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', $tw_img_url[0]['twitter_image_custom_url']);
                    } elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Image
                        $_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
                        if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'])) {
                            update_post_meta($post->ID, '_seopress_social_twitter_img', $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']);
                        }
                    }

                    $robots_noindex = "SELECT p.robots_noindex, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noindex = $wpdb->get_results($robots_noindex, ARRAY_A);

                    if (! empty($robots_noindex[0]['robots_noindex']) && '1' === $robots_noindex[0]['robots_noindex']) {//Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    } elseif ('on' == get_post_meta($post->ID, '_aioseop_noindex', true)) { //Import old Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }

                    $robots_nofollow = "SELECT p.robots_nofollow, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_nofollow = $wpdb->get_results($robots_nofollow, ARRAY_A);

                    if (! empty($robots_nofollow[0]['robots_nofollow']) && '1' === $robots_nofollow[0]['robots_nofollow']) {//Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    } elseif ('on' == get_post_meta($post->ID, '_aioseop_nofollow', true)) { //Import old Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }

                    $robots_noimageindex = "SELECT p.robots_noimageindex, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noimageindex = $wpdb->get_results($robots_noimageindex, ARRAY_A);

                    if (! empty($robots_noimageindex[0]['robots_noimageindex']) && '1' === $robots_noimageindex[0]['robots_noimageindex']) {//Import Robots NoImageIndex
                        update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                    }

                    $robots_noodp = "SELECT p.robots_noodp, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noodp = $wpdb->get_results($robots_noodp, ARRAY_A);

                    if (! empty($robots_noodp[0]['robots_noodp']) && '1' === $robots_noodp[0]['robots_noodp']) {//Import Robots NoOdp
                        update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
                    }

                    $robots_nosnippet = "SELECT p.robots_nosnippet, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_nosnippet = $wpdb->get_results($robots_nosnippet, ARRAY_A);

                    if (! empty($robots_nosnippet[0]['robots_nosnippet']) && '1' === $robots_nosnippet[0]['robots_nosnippet']) {//Import Robots NoSnippet
                        update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                    }

                    $robots_noarchive = "SELECT p.robots_noarchive, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $robots_noarchive = $wpdb->get_results($robots_noarchive, ARRAY_A);

                    if (! empty($robots_noarchive[0]['robots_noarchive']) && '1' === $robots_noarchive[0]['robots_noarchive']) {//Import Robots NoArchive
                        update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                    }

                    $keyphrases = "SELECT p.keyphrases, p.post_id
                    FROM {$wpdb->prefix}aioseo_posts p
                    WHERE p.post_id = $post->ID";

                    $keyphrases = $wpdb->get_results($keyphrases, ARRAY_A);

                    if (! empty($keyphrases)) {
                        $keyphrases = json_decode($keyphrases[0]['keyphrases']);

                        if (isset($keyphrases->focus->keyphrase)) {
                            $keyphrases = $keyphrases->focus->keyphrase;

                            if ('' != $keyphrases) { //Import focus kw
                                update_post_meta($post->ID, '_seopress_analysis_target_kw', $keyphrases);
                            }
                        }
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

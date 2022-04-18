<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Squirrly migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_squirrly_migration() {
    check_ajax_referer('seopress_squirrly_migrate_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset']) && isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'qss';
        $blog_id    = get_current_blog_id();

        $count_query = $wpdb->get_results("SELECT * FROM $table_name WHERE blog_id = $blog_id", ARRAY_A);

        if ( ! empty($count_query)) {
            foreach ($count_query as $value) {
                $post_id = url_to_postid($value['URL']);

                if (0 != $post_id && ! empty($value['seo'])) {
                    $seo = maybe_unserialize($value['seo']);

                    if ('' != $seo['title']) { //Import title tag
                        update_post_meta($post_id, '_seopress_titles_title', $seo['title']);
                    }
                    if ('' != $seo['description']) { //Import description tag
                        update_post_meta($post_id, '_seopress_titles_desc', $seo['description']);
                    }
                    if ('' != $seo['og_title']) { //Import Facebook Title
                        update_post_meta($post_id, '_seopress_social_fb_title', $seo['og_title']);
                    }
                    if ('' != $seo['og_description']) { //Import Facebook Desc
                        update_post_meta($post_id, '_seopress_social_fb_desc', $seo['og_description']);
                    }
                    if ('' != $seo['og_media']) { //Import Facebook Image
                        update_post_meta($post_id, '_seopress_social_fb_img', $seo['og_media']);
                    }
                    if ('' != $seo['tw_title']) { //Import Twitter Title
                        update_post_meta($post_id, '_seopress_social_twitter_title', $seo['tw_title']);
                    }
                    if ('' != $seo['tw_description']) { //Import Twitter Desc
                        update_post_meta($post_id, '_seopress_social_twitter_desc', $seo['tw_description']);
                    }
                    if ('' != $seo['tw_media']) { //Import Twitter Image
                        update_post_meta($post_id, '_seopress_social_twitter_img', $seo['tw_media']);
                    }
                    if (1 === $seo['noindex']) { //Import noindex
                        update_post_meta($post_id, '_seopress_robots_index', 'yes');
                    }
                    if (1 === $seo['nofollow']) { //Import nofollow
                        update_post_meta($post_id, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != $seo['canonical']) { //Import canonical
                        update_post_meta($post_id, '_seopress_robots_canonical', $seo['canonical']);
                    }
                }
            }
            $offset = 'done';
        }
        $data           = [];
        $data['offset'] = $offset;
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_squirrly_migration', 'seopress_squirrly_migration');

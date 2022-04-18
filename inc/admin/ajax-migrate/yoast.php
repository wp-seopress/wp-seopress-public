<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Yoast migration
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_yoast_migration() {
    check_ajax_referer('seopress_yoast_migrate_nonce', $_POST['_ajax_nonce'], true);

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

            $yoast_query_terms = get_option('wpseo_taxonomy_meta');

            if ($yoast_query_terms) {
                foreach ($yoast_query_terms as $taxonomies => $taxonomie) {
                    foreach ($taxonomie as $term_id => $term_value) {
                        if ('' != $term_value['wpseo_title']) { //Import title tag
                            update_term_meta($term_id, '_seopress_titles_title', $term_value['wpseo_title']);
                        }
                        if ('' != $term_value['wpseo_desc']) { //Import meta desc
                            update_term_meta($term_id, '_seopress_titles_desc', $term_value['wpseo_desc']);
                        }
                        if ('' != $term_value['wpseo_opengraph-title']) { //Import Facebook Title
                            update_term_meta($term_id, '_seopress_social_fb_title', $term_value['wpseo_opengraph-title']);
                        }
                        if ('' != $term_value['wpseo_opengraph-description']) { //Import Facebook Desc
                            update_term_meta($term_id, '_seopress_social_fb_desc', $term_value['wpseo_opengraph-description']);
                        }
                        if ('' != $term_value['wpseo_opengraph-image']) { //Import Facebook Image
                            update_term_meta($term_id, '_seopress_social_fb_img', $term_value['wpseo_opengraph-image']);
                        }
                        if ('' != $term_value['wpseo_twitter-title']) { //Import Twitter Title
                            update_term_meta($term_id, '_seopress_social_twitter_title', $term_value['wpseo_twitter-title']);
                        }
                        if ('' != $term_value['wpseo_twitter-description']) { //Import Twitter Desc
                            update_term_meta($term_id, '_seopress_social_twitter_desc', $term_value['wpseo_twitter-description']);
                        }
                        if ('' != $term_value['wpseo_twitter-image']) { //Import Twitter Image
                            update_term_meta($term_id, '_seopress_social_twitter_img', $term_value['wpseo_twitter-image']);
                        }
                        if ('noindex' == $term_value['wpseo_noindex']) { //Import Robots NoIndex
                            update_term_meta($term_id, '_seopress_robots_index', 'yes');
                        }
                        if ('' != $term_value['wpseo_canonical']) { //Import Canonical URL
                            update_term_meta($term_id, '_seopress_robots_canonical', $term_value['wpseo_canonical']);
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

            $yoast_query = get_posts($args);

            if ($yoast_query) {
                foreach ($yoast_query as $post) {
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_title', true)) { //Import title tag
                        update_post_meta($post->ID, '_seopress_titles_title', get_post_meta($post->ID, '_yoast_wpseo_title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_metadesc', true)) { //Import meta desc
                        update_post_meta($post->ID, '_seopress_titles_desc', get_post_meta($post->ID, '_yoast_wpseo_metadesc', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true)) { //Import Facebook Title
                        update_post_meta($post->ID, '_seopress_social_fb_title', get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true)) { //Import Facebook Desc
                        update_post_meta($post->ID, '_seopress_social_fb_desc', get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true)) { //Import Facebook Image
                        update_post_meta($post->ID, '_seopress_social_fb_img', get_post_meta($post->ID, '_yoast_wpseo_opengraph-image', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true)) { //Import Twitter Title
                        update_post_meta($post->ID, '_seopress_social_twitter_title', get_post_meta($post->ID, '_yoast_wpseo_twitter-title', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true)) { //Import Twitter Desc
                        update_post_meta($post->ID, '_seopress_social_twitter_desc', get_post_meta($post->ID, '_yoast_wpseo_twitter-description', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true)) { //Import Twitter Image
                        update_post_meta($post->ID, '_seopress_social_twitter_img', get_post_meta($post->ID, '_yoast_wpseo_twitter-image', true));
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true)) { //Import Robots NoIndex
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if ('1' == get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true)) { //Import Robots NoFollow
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true)) { //Import Robots NoOdp, NoImageIndex, NoArchive, NoSnippet
                        $yoast_wpseo_meta_robots_adv = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-adv', true);

                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noodp')) {
                            update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noimageindex')) {
                            update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'noarchive')) {
                            update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                        }
                        if (false !== strpos($yoast_wpseo_meta_robots_adv, 'nosnippet')) {
                            update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
                        }
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_canonical', true)) { //Import Canonical URL
                        update_post_meta($post->ID, '_seopress_robots_canonical', get_post_meta($post->ID, '_yoast_wpseo_canonical', true));
                    }
                    if ('' != get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) || '' != get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', true)) { //Import Focus Keywords
                        $y_fkws_clean = []; //reset array

                        $y_fkws = get_post_meta($post->ID, '_yoast_wpseo_focuskeywords', false);

                        foreach ($y_fkws as $value) {
                            foreach (json_decode($value) as $key => $value) {
                                $y_fkws_clean[] .= $value->keyword;
                            }
                        }

                        $y_fkws_clean[] .= get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);

                        update_post_meta($post->ID, '_seopress_analysis_target_kw', implode(',', $y_fkws_clean));
                    }

                    //Primary category
                    if (class_exists('WPSEO_Primary_Term')) {
                        if ('product' == get_post_type($post->ID)) {
                            $tax = 'product_cat';
                        } else {
                            $tax = 'category';
                        }

                        $primary_term = new WPSEO_Primary_Term($tax, $post->ID);

                        $primary_term = $primary_term->get_primary_term();

                        if ('' != $primary_term && is_int($primary_term)) {
                            update_post_meta($post->ID, '_seopress_robots_primary_cat', $primary_term);
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
add_action('wp_ajax_seopress_yoast_migration', 'seopress_yoast_migration');

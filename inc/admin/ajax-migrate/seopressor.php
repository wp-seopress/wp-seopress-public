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
                                if (in_array('noodp', $robots)) { //Import Robots NoOdp
                                    update_post_meta($post->ID, '_seopress_robots_odp', 'yes');
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
        wp_send_json_success($data);
        exit();
    }
}
add_action('wp_ajax_seopress_seopressor_migration', 'seopress_seopressor_migration');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Export SEOPress metadata to CSV
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_metadata_export() {
    check_ajax_referer('seopress_export_csv_metadata_nonce', $_POST['_ajax_nonce'], true);

    if (current_user_can(seopress_capability('manage_options', 'migration')) && is_admin()) {
        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        $post_export = '';
        if (isset($_POST['post_export'])) {
            $post_export = esc_attr($_POST['post_export']);
        }

        $term_export = '';
        if (isset($_POST['term_export'])) {
            $term_export = esc_attr($_POST['term_export']);
        }

        //Get post types
        $seopress_get_post_types = [];
        $postTypes = seopress_get_service('WordPressData')->getPostTypes();
        foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
            $seopress_get_post_types[] = $seopress_cpt_key;
        }

        //Get taxonomies
        $seopress_get_taxonomies = [];
        foreach (seopress_get_taxonomies() as $seopress_tax_key => $seopress_tax_value) {
            $seopress_get_taxonomies[] = $seopress_tax_key;
        }

        global $wpdb;
        global $post;

        //Count posts
        $i     = 1;
        $sql   = '(';
        $count = count($seopress_get_post_types);
        foreach ($seopress_get_post_types as $cpt) {
            $sql .= '(post_type = "' . $cpt . '")';

            if ($i < $count) {
                $sql .= ' OR ';
            }

            ++$i;
        }
        $sql .= ')';

        $total_count_posts = (int) $wpdb->get_var("SELECT count(*)
		FROM {$wpdb->posts}
		WHERE $sql
		AND (post_status = 'publish' OR post_status = 'pending' OR post_status = 'draft' OR post_status = 'auto-draft' OR post_status = 'future' OR post_status = 'private' OR post_status = 'inherit' OR post_status = 'trash') ");

        //Count terms
        $total_count_terms = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->terms}");

        $increment = 200;

        $csv          = '';
        $csv          = get_option('seopress_metadata_csv');
        $download_url = '';

        $settings['id']              = 				[];
        $settings['post_title']      =		[];
        $settings['url']             =				[];
        $settings['meta_title']      =		[];
        $settings['meta_desc']       =		[];
        $settings['fb_title']        =			[];
        $settings['fb_desc']         =			[];
        $settings['fb_img']          =			[];
        $settings['tw_title']        =			[];
        $settings['tw_desc']         =			[];
        $settings['tw_img']          =			[];
        $settings['noindex']         =			[];
        $settings['nofollow']        =			[];
        $settings['noodp']           =			[];
        $settings['noimageindex']    =		[];
        $settings['noarchive']       =		[];
        $settings['nosnippet']       =		[];
        $settings['canonical_url']   =	[];
        $settings['primary_cat']     =	[];
        $settings['redirect_active'] =	[];
        $settings['redirect_type']   =	[];
        $settings['redirect_url']    =		[];
        $settings['target_kw']       =		[];

        //Posts
        if ('done' != $post_export) {
            if ($offset > $total_count_posts) {
                wp_reset_query();
                //Reset offset once Posts export is done
                $offset = 0;
                update_option('seopress_metadata_csv', $csv, false);
                $post_export = 'done';
            } else {
                $args = [
                    'post_type'      => $seopress_get_post_types,
                    'posts_per_page' => $increment,
                    'offset'         => $offset,
                    'post_status'    => 'any',
                    'order'          => 'DESC',
                    'orderby'        => 'date',
                ];
                $args       = apply_filters('seopress_metadata_query_args', $args, $seopress_get_post_types, $increment, $offset);
                $meta_query = get_posts($args);

                if ($meta_query) {
                    // The Loop
                    foreach ($meta_query as $post) {
                        array_push($settings['id'], $post->ID);

                        array_push($settings['post_title'], $post->post_title);

                        array_push($settings['url'], get_permalink($post));

                        array_push($settings['meta_title'], get_post_meta($post->ID, '_seopress_titles_title', true));

                        array_push($settings['meta_desc'], get_post_meta($post->ID, '_seopress_titles_desc', true));

                        array_push($settings['fb_title'], get_post_meta($post->ID, '_seopress_social_fb_title', true));

                        array_push($settings['fb_desc'], get_post_meta($post->ID, '_seopress_social_fb_desc', true));

                        array_push($settings['fb_img'], get_post_meta($post->ID, '_seopress_social_fb_img', true));

                        array_push($settings['tw_title'], get_post_meta($post->ID, '_seopress_social_twitter_title', true));

                        array_push($settings['tw_desc'], get_post_meta($post->ID, '_seopress_social_twitter_desc', true));

                        array_push($settings['tw_img'], get_post_meta($post->ID, '_seopress_social_twitter_img', true));

                        array_push($settings['noindex'], get_post_meta($post->ID, '_seopress_robots_index', true));

                        array_push($settings['nofollow'], get_post_meta($post->ID, '_seopress_robots_follow', true));

                        array_push($settings['noodp'], get_post_meta($post->ID, '_seopress_robots_odp', true));

                        array_push($settings['noimageindex'], get_post_meta($post->ID, '_seopress_robots_imageindex', true));

                        array_push($settings['noarchive'], get_post_meta($post->ID, '_seopress_robots_archive', true));

                        array_push($settings['nosnippet'], get_post_meta($post->ID, '_seopress_robots_snippet', true));

                        array_push($settings['canonical_url'], get_post_meta($post->ID, '_seopress_robots_canonical', true));

                        array_push($settings['primary_cat'], get_post_meta($post->ID, 'seopress_robots_primary_cat', true));

                        array_push($settings['redirect_active'], get_post_meta($post->ID, '_seopress_redirections_enabled', true));

                        array_push($settings['redirect_type'], get_post_meta($post->ID, '_seopress_redirections_type', true));

                        array_push($settings['redirect_url'], get_post_meta($post->ID, '_seopress_redirections_value', true));

                        array_push($settings['target_kw'], get_post_meta($post->ID, '_seopress_analysis_target_kw', true));

                        $csv[] = array_merge(
                            $settings['id'],
                            $settings['post_title'],
                            $settings['url'],
                            $settings['meta_title'],
                            $settings['meta_desc'],
                            $settings['fb_title'],
                            $settings['fb_desc'],
                            $settings['fb_img'],
                            $settings['tw_title'],
                            $settings['tw_desc'],
                            $settings['tw_img'],
                            $settings['noindex'],
                            $settings['nofollow'],
                            $settings['noodp'],
                            $settings['noimageindex'],
                            $settings['noarchive'],
                            $settings['nosnippet'],
                            $settings['canonical_url'],
                            $settings['primary_cat'],
                            $settings['redirect_active'],
                            $settings['redirect_type'],
                            $settings['redirect_url'],
                            $settings['target_kw']
                        );

                        //Clean arrays
                        $settings['id']              =				[];
                        $settings['post_title']      =		[];
                        $settings['url']             =				[];
                        $settings['meta_title']      =		[];
                        $settings['meta_desc']       =		[];
                        $settings['fb_title']        =			[];
                        $settings['fb_desc']         =			[];
                        $settings['fb_img']          =			[];
                        $settings['tw_title']        =			[];
                        $settings['tw_desc']         =			[];
                        $settings['tw_img']          =			[];
                        $settings['noindex']         =			[];
                        $settings['nofollow']        =			[];
                        $settings['noodp']           =			[];
                        $settings['noimageindex']    =		[];
                        $settings['noarchive']       =		[];
                        $settings['nosnippet']       =		[];
                        $settings['canonical_url']   =	[];
                        $settings['primary_cat']     =	[];
                        $settings['redirect_active'] =	[];
                        $settings['redirect_type']   =	[];
                        $settings['redirect_url']    =		[];
                        $settings['target_kw']       =		[];
                    }
                }
                $offset += $increment;
                update_option('seopress_metadata_csv', $csv, false);
            }
        } elseif ('done' != $term_export) {
            //Terms
            if ($offset > $total_count_terms) {
                update_option('seopress_metadata_csv', $csv, false);
                $post_export = 'done';
                $term_export = 'done';
            } else {
                $args = [
                    'taxonomy'   => $seopress_get_taxonomies,
                    'number'     => $increment,
                    'offset'     => $offset,
                    'order'      => 'DESC',
                    'orderby'    => 'date',
                    'hide_empty' => false,
                ];

                $args = apply_filters('seopress_metadata_query_terms_args', $args, $seopress_get_taxonomies, $increment, $offset);

                $meta_query = get_terms($args);

                if ($meta_query) {
                    // The Loop
                    foreach ($meta_query as $term) {
                        array_push($settings['id'], $term->term_id);

                        array_push($settings['post_title'], $term->name);

                        array_push($settings['url'], get_term_link($term));

                        array_push($settings['meta_title'], get_term_meta($term->term_id, '_seopress_titles_title', true));

                        array_push($settings['meta_desc'], get_term_meta($term->term_id, '_seopress_titles_desc', true));

                        array_push($settings['fb_title'], get_term_meta($term->term_id, '_seopress_social_fb_title', true));

                        array_push($settings['fb_desc'], get_term_meta($term->term_id, '_seopress_social_fb_desc', true));

                        array_push($settings['fb_img'], get_term_meta($term->term_id, '_seopress_social_fb_img', true));

                        array_push($settings['tw_title'], get_term_meta($term->term_id, '_seopress_social_twitter_title', true));

                        array_push($settings['tw_desc'], get_term_meta($term->term_id, '_seopress_social_twitter_desc', true));

                        array_push($settings['tw_img'], get_term_meta($term->term_id, '_seopress_social_twitter_img', true));

                        array_push($settings['noindex'], get_term_meta($term->term_id, '_seopress_robots_index', true));

                        array_push($settings['nofollow'], get_term_meta($term->term_id, '_seopress_robots_follow', true));

                        array_push($settings['noodp'], get_term_meta($term->term_id, '_seopress_robots_odp', true));

                        array_push($settings['noimageindex'], get_term_meta($term->term_id, '_seopress_robots_imageindex', true));

                        array_push($settings['noarchive'], get_term_meta($term->term_id, '_seopress_robots_archive', true));

                        array_push($settings['nosnippet'], get_term_meta($term->term_id, '_seopress_robots_snippet', true));

                        array_push($settings['canonical_url'], get_term_meta($term->term_id, '_seopress_robots_canonical', true));

                        array_push($settings['redirect_active'], get_term_meta($term->term_id, '_seopress_redirections_enabled', true));

                        array_push($settings['redirect_type'], get_term_meta($term->term_id, '_seopress_redirections_type', true));

                        array_push($settings['redirect_url'], get_term_meta($term->term_id, '_seopress_redirections_value', true));

                        array_push($settings['target_kw'], get_term_meta($term->term_id, '_seopress_analysis_target_kw', true));

                        $csv[] = array_merge(
                            $settings['id'],
                            $settings['post_title'],
                            $settings['url'],
                            $settings['meta_title'],
                            $settings['meta_desc'],
                            $settings['fb_title'],
                            $settings['fb_desc'],
                            $settings['fb_img'],
                            $settings['tw_title'],
                            $settings['tw_desc'],
                            $settings['tw_img'],
                            $settings['noindex'],
                            $settings['nofollow'],
                            $settings['noodp'],
                            $settings['noimageindex'],
                            $settings['noarchive'],
                            $settings['nosnippet'],
                            $settings['canonical_url'],
                            $settings['redirect_active'],
                            $settings['redirect_type'],
                            $settings['redirect_url'],
                            $settings['target_kw']
                        );

                        //Clean arrays
                        $settings['id']              =				[];
                        $settings['post_title']      =		[];
                        $settings['url']             =				[];
                        $settings['meta_title']      =		[];
                        $settings['meta_desc']       =		[];
                        $settings['fb_title']        =			[];
                        $settings['fb_desc']         =			[];
                        $settings['fb_img']          =			[];
                        $settings['tw_title']        =			[];
                        $settings['tw_desc']         =			[];
                        $settings['tw_img']          =			[];
                        $settings['noindex']         =			[];
                        $settings['nofollow']        =			[];
                        $settings['noodp']           =			[];
                        $settings['noimageindex']    =		[];
                        $settings['noarchive']       =		[];
                        $settings['nosnippet']       =		[];
                        $settings['canonical_url']   =	[];
                        $settings['redirect_active'] =	[];
                        $settings['redirect_type']   =	[];
                        $settings['redirect_url']    =		[];
                        $settings['target_kw']       =		[];
                    }
                }
                $offset += $increment;
                $post_export = 'done';
                update_option('seopress_metadata_csv', $csv, false);
            }
        } else {
            $post_export = 'done';
            $term_export = 'done';
        }

        //Create download URL
        if ('done' == $post_export && 'done' == $term_export) {
            $args = array_merge($_POST, [
                'nonce'           => wp_create_nonce('seopress_csv_batch_export_nonce'),
                'page'            => 'seopress-import-export',
                'seopress_action' => 'seopress_download_batch_export',
            ]);

            $download_url = add_query_arg($args, admin_url('admin.php'));

            $offset = 'done';
        }

        //Return data to JSON
        $data                = [];
        $data['offset']      = $offset;
        $data['url']         = $download_url;
        $data['post_export'] = $post_export;
        $data['term_export'] = $term_export;
        wp_send_json_success($data);

        exit();
    }
}

add_action('wp_ajax_seopress_metadata_export', 'seopress_metadata_export');

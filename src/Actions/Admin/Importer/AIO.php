<?php

namespace SEOPress\Actions\Admin\Importer;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\AIO\Tags;

class AIO implements ExecuteHooksBackend {
    public function __construct() {
        $this->tagsAIO = new Tags();
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_ajax_seopress_aio_migration', [$this, 'process']);
    }

    /**
     * @since 4.3.0
     *
     * @param int $offset
     * @param int $increment
     */
    protected function migratePostQuery($offset, $increment) {
        global $wpdb;
        $args = [
            'posts_per_page' => $increment,
            'post_type'      => 'any',
            'post_status'    => 'any',
            'offset'         => $offset,
        ];

        $aio_query = get_posts($args);

        if ( ! $aio_query) {
            $offset += $increment;

            return $offset;
        }

        $getPostMetas = [
            '_seopress_titles_title'         => '_aioseo_title',
            '_seopress_titles_desc'          => '_aioseo_description',
            '_seopress_social_fb_title'      => '_aioseo_og_title',
            '_seopress_social_fb_desc'       => '_aioseo_og_description',
            '_seopress_social_twitter_title' => '_aioseo_twitter_title',
            '_seopress_social_twitter_desc'  => '_aioseo_twitter_description',
        ];

        foreach ($aio_query as $post) {
            foreach ($getPostMetas as $key => $value) {
                $metaAIO = get_post_meta($post->ID, $value, true);
                if ( ! empty($metaAIO)) {
                    update_post_meta($post->ID, $key, $this->tagsAIO->replaceTags($metaAIO));
                }
            }

            //Canonical URL
            $canonical_url = "SELECT p.canonical_url, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $canonical_url = $wpdb->get_results($canonical_url, ARRAY_A);

            if (! empty($canonical_url[0]['canonical_url'])) {//Import Canonical URL
                update_post_meta($post->ID, '_seopress_robots_canonical', $canonical_url[0]['canonical_url']);
            }

            //OG Image
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

            //Twitter Image
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

            //Meta robots "noindex"
            $robots_noindex = "SELECT p.robots_noindex, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $robots_noindex = $wpdb->get_results($robots_noindex, ARRAY_A);

            if (! empty($robots_noindex[0]['robots_noindex']) && '1' === $robots_noindex[0]['robots_noindex']) {//Import Robots NoIndex
                update_post_meta($post->ID, '_seopress_robots_index', 'yes');
            } elseif ('on' == get_post_meta($post->ID, '_aioseop_noindex', true)) { //Import old Robots NoIndex
                update_post_meta($post->ID, '_seopress_robots_index', 'yes');
            }

            //Meta robots "nofollow"
            $robots_nofollow = "SELECT p.robots_nofollow, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $robots_nofollow = $wpdb->get_results($robots_nofollow, ARRAY_A);

            if (! empty($robots_nofollow[0]['robots_nofollow']) && '1' === $robots_nofollow[0]['robots_nofollow']) {//Import Robots NoFollow
                update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
            } elseif ('on' == get_post_meta($post->ID, '_aioseop_nofollow', true)) { //Import old Robots NoFollow
                update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
            }

            //Meta robots "noimageindex"
            $robots_noimageindex = "SELECT p.robots_noimageindex, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $robots_noimageindex = $wpdb->get_results($robots_noimageindex, ARRAY_A);

            if (! empty($robots_noimageindex[0]['robots_noimageindex']) && '1' === $robots_noimageindex[0]['robots_noimageindex']) {//Import Robots NoImageIndex
                update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
            }

            //Meta robots "nosnippet"
            $robots_nosnippet = "SELECT p.robots_nosnippet, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $robots_nosnippet = $wpdb->get_results($robots_nosnippet, ARRAY_A);

            if (! empty($robots_nosnippet[0]['robots_nosnippet']) && '1' === $robots_nosnippet[0]['robots_nosnippet']) {//Import Robots NoSnippet
                update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
            }

            //Meta robots "noarchive"
            $robots_noarchive = "SELECT p.robots_noarchive, p.post_id
            FROM {$wpdb->prefix}aioseo_posts p
            WHERE p.post_id = $post->ID";

            $robots_noarchive = $wpdb->get_results($robots_noarchive, ARRAY_A);

            if (! empty($robots_noarchive[0]['robots_noarchive']) && '1' === $robots_noarchive[0]['robots_noarchive']) {//Import Robots NoArchive
                update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
            }

            //Target keywords
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

        $offset += $increment;

        return $offset;
    }

    /**
     * @since 4.3.0
     */
    public function process() {
        check_ajax_referer('seopress_aio_migrate_nonce', $_POST['_ajax_nonce'], true);
        if ( ! is_admin()) {
            wp_send_json_error();

            return;
        }

        if ( ! current_user_can(seopress_capability('manage_options', 'migration'))) {
            wp_send_json_error();

            return;
        }

        if (isset($_POST['offset'])) {
            $offset = absint($_POST['offset']);
        }

        global $wpdb;
        $total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

        $increment = 200;
        global $post;

        if ($offset > $total_count_posts) {
            $offset = 'done';
        } else {
            $offset = $this->migratePostQuery($offset, $increment);
        }

        $data           = [];
        $data['offset'] = $offset;

        do_action('seopress_third_importer_aio', $offset, $increment);

        wp_send_json_success($data);
        exit();
    }
}

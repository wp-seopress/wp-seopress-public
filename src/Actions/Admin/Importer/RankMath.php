<?php

namespace SEOPress\Actions\Admin\Importer;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\RankMath\Tags;

class RankMath implements ExecuteHooksBackend {
    public function __construct() {
        $this->tagsRankMath = new Tags();
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_ajax_seopress_rk_migration', [$this, 'process']);
    }

    /**
     * @since 4.3.0
     *
     * @return string
     */
    protected function migrateTermQuery() {
        wp_reset_query();

        $args = [
            'hide_empty' => false,
            'fields'     => 'ids',
        ];
        $rk_query_terms = get_terms($args);

        $getTermMetas = [
            '_seopress_titles_title'             => 'rank_math_title',
            '_seopress_titles_desc'              => 'rank_math_description',
            '_seopress_social_fb_title'          => 'rank_math_facebook_title',
            '_seopress_social_fb_desc'           => 'rank_math_facebook_description',
            '_seopress_social_fb_img'            => 'rank_math_facebook_image',
            '_seopress_social_twitter_title'     => 'rank_math_twitter_title',
            '_seopress_social_twitter_desc'      => 'rank_math_twitter_description',
            '_seopress_social_twitter_img'       => 'rank_math_twitter_image',
            '_seopress_robots_canonical'         => 'rank_math_canonical_url',
            '_seopress_analysis_target_kw'       => 'rank_math_focus_keyword',
        ];
        if ( ! $rk_query_terms) {
            wp_reset_query();

            return 'done';
        }

        foreach ($rk_query_terms as $term_id) {
            foreach ($getTermMetas as $key => $value) {
                $metaRankMath = get_term_meta($term_id, $value, true);
                if ( ! empty($metaRankMath)) {
                    update_term_meta($term_id, $key, $this->tagsRankMath->replaceTags($metaRankMath));
                }
            }

            if ('' != get_term_meta($term_id, 'rank_math_robots', true)) { //Import Robots NoIndex, NoFollow, NoImageIndex, NoArchive, NoSnippet
                $rank_math_robots = get_term_meta($term_id, 'rank_math_robots', true);

                if (in_array('noindex', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_index', 'yes');
                }
                if (in_array('nofollow', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_follow', 'yes');
                }
                if (in_array('noimageindex', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_imageindex', 'yes');
                }
                if (in_array('noarchive', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_archive', 'yes');
                }
                if (in_array('nosnippet', $rank_math_robots)) {
                    update_term_meta($term_id, '_seopress_robots_snippet', 'yes');
                }
            }
        }

        wp_reset_query();

        return 'done';
    }

    /**
     * @since 4.3.0
     *
     * @param int $offset
     * @param int $increment
     */
    protected function migratePostQuery($offset, $increment) {
        $args = [
            'posts_per_page' => $increment,
            'post_type'      => 'any',
            'post_status'    => 'any',
            'offset'         => $offset,
        ];

        $rk_query = get_posts($args);

        if ( ! $rk_query) {
            $offset += $increment;

            return $offset;
        }

        $getPostMetas = [
            '_seopress_titles_title'         => 'rank_math_title',
            '_seopress_titles_desc'          => 'rank_math_description',
            '_seopress_social_fb_title'      => 'rank_math_facebook_title',
            '_seopress_social_fb_desc'       => 'rank_math_facebook_description',
            '_seopress_social_fb_img'        => 'rank_math_facebook_image',
            '_seopress_social_twitter_title' => 'rank_math_twitter_title',
            '_seopress_social_twitter_desc'  => 'rank_math_twitter_description',
            '_seopress_social_twitter_img'   => 'rank_math_twitter_image',
            '_seopress_robots_canonical'     => 'rank_math_canonical_url',
            '_seopress_analysis_target_kw'   => 'rank_math_focus_keyword',
        ];

        foreach ($rk_query as $post) {
            foreach ($getPostMetas as $key => $value) {
                $metaRankMath = get_post_meta($post->ID, $value, true);
                if ( ! empty($metaRankMath)) {
                    update_post_meta($post->ID, $key, $this->tagsRankMath->replaceTags($metaRankMath));
                }
            }

            if ('' != get_post_meta($post->ID, 'rank_math_robots', true)) { //Import Robots NoIndex, NoFollow, NoImageIndex, NoArchive, NoSnippet
                $rank_math_robots = get_post_meta($post->ID, 'rank_math_robots', true);

                if (is_array($rank_math_robots)) {
                    if (in_array('noindex', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_index', 'yes');
                    }
                    if (in_array('nofollow', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
                    }
                    if (in_array('noimageindex', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
                    }
                    if (in_array('noarchive', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_archive', 'yes');
                    }
                    if (in_array('nosnippet', $rank_math_robots)) {
                        update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
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
        check_ajax_referer('seopress_rk_migrate_nonce', $_POST['_ajax_nonce'], true);
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
            $offset = $this->migrateTermQuery();
        } else {
            $offset = $this->migratePostQuery($offset, $increment);
        }

        $data           = [];
        $data['offset'] = $offset;

        do_action('seopress_third_importer_rank_math', $offset, $increment);

        wp_send_json_success($data);
        exit();
    }
}

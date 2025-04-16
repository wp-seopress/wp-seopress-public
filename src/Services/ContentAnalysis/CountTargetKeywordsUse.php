<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class CountTargetKeywordsUse
{
    public function getCountByKeywords($targetKeywords, $postId = null)
    {
        if(empty($targetKeywords)){
            return [];
        }

        $hashed = md5(serialize($targetKeywords) . $postId);
        $cached = get_transient('seopress_content_analysis_count_target_keywords_use_' . $hashed);
        if(false !== $cached){
            return $cached;
        }

        $targetKeywords = array_map('trim', $targetKeywords);

        global $wpdb;

        $query = "SELECT pm.post_id, pm.meta_value
            FROM {$wpdb->postmeta} AS pm
            JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_seopress_analysis_target_kw'
            AND p.post_type != 'elementor_library'
            AND pm.meta_value LIKE %s
            AND p.post_status IN ('publish', 'draft', 'pending', 'future') ";

        $data = [];

        foreach ($targetKeywords as $key => $keyword) {
            $rows = $wpdb->get_results($wpdb->prepare($query, "%$keyword%"), ARRAY_A);

            $data[] = [
                "key" => $keyword,
                "rows" => array_values(array_filter(array_map(function($row) use ($keyword, $postId) {
                    $post = get_post($postId);
                    $post_type_object = get_post_type_object($post->post_type);

                    $values = array_map('trim', explode(',', $row['meta_value']));

                    if(!in_array($keyword, $values, true) || $postId === $row['post_id']){
                        return null;
                    }

                    return [
                        'post_id' => absint($row['post_id']),
                        'edit_link' => admin_url(sprintf($post_type_object->_edit_link . '&action=edit', absint($row['post_id']))),
                        'title' => esc_html(get_the_title($row['post_id'])),
                    ];
                }, $rows))),
            ];
        }

        set_transient('seopress_content_analysis_count_target_keywords_use_' . $hashed, $data, 5 * MINUTE_IN_SECONDS);

        return $data;

    }
}

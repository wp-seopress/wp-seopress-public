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
        error_log("caheeeddd ====" . $hashed);
        $cached = get_transient('seopress_content_analysis_count_target_keywords_use_' . $hashed);
        if(false !== $cached){
            return $cached;
        }

        $targetKeywords = array_map('trim', $targetKeywords);

        global $wpdb;


        $query = "SELECT post_id, meta_value
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_seopress_analysis_target_kw'
        AND meta_value LIKE %s";

        $data = [];

        foreach ($targetKeywords as $key => $keyword) {
            $rows = $wpdb->get_results($wpdb->prepare($query, "%$keyword%"), ARRAY_A);
            $data[] = [
                "key" => $keyword,
                "rows" => array_values(array_filter(array_map(function($row) use ($keyword, $postId) {
                    $values = array_map('trim', explode(',', $row['meta_value']));

                    if(!in_array($keyword, $values, true) || $postId === $row['post_id']){
                        return null;
                    }

                    return $row['post_id'];
                }, $rows)))
            ];
        }

        set_transient('seopress_content_analysis_count_target_keywords_use_' . $hashed, $data, 5 * MINUTE_IN_SECONDS);

        return $data;

    }
}


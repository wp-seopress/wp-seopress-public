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

        return $data;

    }
}


<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class CountTargetKeywordsUse
{
    public function getCountByKeywords($targetKeywords)
    {
        if(empty($targetKeywords)){
            return [];
        }

        global $wpdb;


        $query = "SELECT post_id
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_seopress_analysis_target_kw'
        AND meta_value LIKE %s";

        $data = [];

        foreach ($targetKeywords as $key => $value) {
            $rows = $wpdb->get_results($wpdb->prepare($query, "%$value%"), ARRAY_A);
            $data[] = [
                "key" => $value,
                "rows" => array_map(function($row){
                    return $row['post_id'];
                }, $rows)
            ];
        }

        return $data;

    }
}


<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

class SearchUrl
{
    public function searchByPostName($value) {
        global $wpdb;

        $limit   = apply_filters('seopress_search_url_result_limit', 50);
        if($limit > 200){
            $limit = 200;
        }

        $postTypes = seopress_get_service('WordPressData')->getPostTypes();

        $postTypes = array_map(function($v) {
            return "'" . esc_sql($v) . "'";
        }, array_keys($postTypes));

        $data = $wpdb->get_results($wpdb->prepare("
			SELECT p.id, p.post_title
			FROM $wpdb->posts p
            WHERE (
                p.post_name LIKE %s
                OR p.post_title LIKE %s
            )
            AND p.post_status = 'publish'
            AND p.post_type IN (" . implode(',',$postTypes) . ")
			LIMIT %d", '%' . $value . '%', '%' . $value . '%', $limit), ARRAY_A);

        foreach ($data as $key => $value) {
            $data[$key]['guid'] = get_permalink($value['id']);
        }
        return $data;
    }
}

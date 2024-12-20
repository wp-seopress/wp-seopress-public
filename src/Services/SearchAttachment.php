<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

class SearchAttachment
{
    public function searchByUrl($url) {
        global $wpdb;
        $parsed_url = wp_parse_url($url);
        $path = $parsed_url['path'];
        
        // Extract the file name without the size attribute
        $filename = basename($path);
        $filename_parts = explode('-', $filename);
        array_pop($filename_parts); // Remove the size attribute part
        $clean_filename = implode('-', $filename_parts);
        
        $limit   = apply_filters('seopress_search_attachment_result_limit', 50);
        if($limit > 200){
            $limit = 200;
        }
        $data = $wpdb->get_results($wpdb->prepare("
			SELECT *
			FROM $wpdb->posts p
            WHERE p.guid LIKE %s
            AND p.post_type = 'attachment'
			LIMIT %d", '%' . $clean_filename . '%', $limit), ARRAY_A);

            
        foreach ($data as $key => $value) {
            $data[$key] = $value['ID'];
        }

        return $data;
    }
}

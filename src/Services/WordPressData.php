<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

class WordPressData
{
    public function getPostTypes() {
        global $wp_post_types;

        $args = [
            'show_ui' => true,
            'public'  => true,
        ];

        $post_types = get_post_types($args, 'objects', 'and');
        unset(
            $post_types['attachment'],
            $post_types['seopress_rankings'],
            $post_types['seopress_backlinks'],
            $post_types['seopress_404'],
            $post_types['elementor_library'],
            $post_types['customer_discount'],
            $post_types['cuar_private_file'],
            $post_types['cuar_private_page'],
            $post_types['ct_template']
        );
        $post_types = apply_filters('seopress_post_types', $post_types);

        return $post_types;
    }
}

<?php

namespace SEOPress\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class ContentAnalysis {
    public static function getData() {
        $data = [
            'all_canonical'=> [
                'title'  => __('Canonical URL', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'schemas'=> [
                'title'  => __('Structured data types', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'old_post'=> [
                'title'  => __('Last modified date', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'words_counter'=> [
                'title'  => __('Words counter', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'keywords_density'=> [
                'title'  => __('Keywords density', 'wp-seopress'),
                'impact' => null,
                'desc'   => null,
            ],
            'keywords_permalink'=> [
                'title'  => __('Keywords in permalink', 'wp-seopress'),
                'impact' => null,
                'desc'   => null,
            ],
            'headings'=> [
                'title'  => __('Headings', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'meta_title'=> [
                'title'  => __('Meta title', 'wp-seopress'),
                'impact' => null,
                'desc'   => null,
            ],
            'meta_desc'=> [
                'title'  => __('Meta description', 'wp-seopress'),
                'impact' => null,
                'desc'   => null,
            ],
            'social'=> [
                'title'  => __('Social meta tags', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'robots'=> [
                'title'  => __('Meta robots', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'img_alt'=> [
                'title'  => __('Alternative texts of images', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'nofollow_links'=> [
                'title'  => __('NoFollow Links', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'outbound_links'=> [
                'title'  => __('Outbound Links', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
            'internal_links'=> [
                'title'  => __('Internal Links', 'wp-seopress'),
                'impact' => 'good',
                'desc'   => null,
            ],
        ];

        return apply_filters('seopress_get_content_analysis_data', $data);
    }
}

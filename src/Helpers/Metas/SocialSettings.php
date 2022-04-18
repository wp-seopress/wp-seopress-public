<?php

namespace SEOPress\Helpers\Metas;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class SocialSettings {
    /**
     * @since 5.0.0
     *
     * @param int|null $id
     *
     * @return array[]
     *
     *    key: string post meta
     *    use_default: default value need to use
     *    default: default value
     *    label: string label
     *    placeholder
     */
    public static function getMetaKeys($id = null) {
        $data = apply_filters('seopress_api_meta_social_settings', [
            [
                'key'         => '_seopress_social_fb_title',
                'type'        => 'input',
                'placeholder' => __('Enter your Facebook title', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'label'       => __('Facebook Title', 'wp-seopress'),
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_social_fb_desc',
                'type'        => 'textarea',
                'placeholder' => __('Enter your Facebook description', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'label'       => __('Facebook description', 'wp-seopress'),
                'visible'     => true,
            ],
            [
                'key'                => '_seopress_social_fb_img',
                'type'               => 'upload',
                'placeholder'        => __('Select your default thumbnail', 'wp-seopress'),
                'use_default'        => '',
                'default'            => '',
                'label'              => __('Facebook thumbnail', 'wp-seopress'),
                'visible'            => true,
                'description'        => __('Minimum size: 200x200px, ideal ratio 1.91:1, 8Mb max. (eg: 1640x856px or 3280x1712px for retina screens)', 'wp-seopress'),
            ],
            [
                'key'                => '_seopress_social_fb_img_attachment_id',
                'type'               => 'hidden',
            ],
            [
                'key'                => '_seopress_social_fb_img_width',
                'type'               => 'hidden',
            ],
            [
                'key'                => '_seopress_social_fb_img_height',
                'type'               => 'hidden',
            ],
            [
                'key'         => '_seopress_social_twitter_title',
                'type'        => 'input',
                'placeholder' => __('Enter your Twitter title', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'label'       => __('Twitter Title', 'wp-seopress'),
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_social_twitter_desc',
                'type'        => 'textarea',
                'placeholder' => __('Enter your Twitter description', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'label'       => __('Twitter description', 'wp-seopress'),
                'visible'     => true,
            ],
            [
                'key'                => '_seopress_social_twitter_img',
                'type'               => 'upload',
                'placeholder'        => __('Select your default thumbnail', 'wp-seopress'),
                'use_default'        => '',
                'default'            => '',
                'label'              => __('Twitter Thumbnail', 'wp-seopress'),
                'visible'            => true,
                'description'        => __('Minimum size: 144x144px (300x157px with large card enabled), ideal ratio 1:1 (2:1 with large card), 5Mb max.', 'wp-seopress'),
            ],
            [
                'key'                => '_seopress_social_twitter_img_attachment_id',
                'type'               => 'hidden',
            ],
            [
                'key'                => '_seopress_social_twitter_img_width',
                'type'               => 'hidden',
            ],
            [
                'key'                => '_seopress_social_twitter_img_height',
                'type'               => 'hidden',
            ],
        ], $id);

        return $data;
    }
}

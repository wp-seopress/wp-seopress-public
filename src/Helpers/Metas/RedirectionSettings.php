<?php

namespace SEOPress\Helpers\Metas;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class RedirectionSettings {
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
        $defaultStatus = seopress_get_service('RedirectionMeta')->getPostMetaStatus($id);
        if($defaultStatus === null || empty($defaultStatus)){
            $defaultStatus = 'both';
        }

        $defaultType = seopress_get_service('RedirectionMeta')->getPostMetaType($id);
        if($defaultType === null || empty($defaultType)){
            $defaultType = 301;
        }

        $data = apply_filters('seopress_api_meta_redirection_settings', [
            [
                'key'         => '_seopress_redirections_enabled',
                'type'        => 'checkbox',
                'placeholder' => '',
                'use_default' => '',
                'default'     => '',
                'label'       => __('Enabled redirection?', 'wp-seopress'),
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_redirections_logged_status',
                'type'        => 'select',
                'placeholder' => '',
                'use_default' => true,
                'default'     => $defaultStatus,
                'label'       => __('Select a login status:', 'wp-seopress'),
                'options'     => [
                    ['value' => 'both', 'label' =>  __('All', 'wp-seopress')],
                    ['value' => 'only_logged_in', 'label' =>  __('Only Logged In', 'wp-seopress')],
                    ['value' => 'only_not_logged_in', 'label' =>  __('Only Not Logged In', 'wp-seopress')],
                ],
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_redirections_type',
                'type'        => 'select',
                'placeholder' => '',
                'use_default' => true,
                'default'     => $defaultType,
                'label'       => __('Select a redirection type:', 'wp-seopress'),
                'options'     => [
                    ['value' => 301, 'label' =>  __('301 Moved Permanently', 'wp-seopress')],
                    ['value' => 302, 'label' =>  __('302 Found / Moved Temporarily', 'wp-seopress')],
                    ['value' => 307, 'label' =>  __('307 Moved Temporarily', 'wp-seopress')]
                ],
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_redirections_value',
                'type'        => 'input',
                'placeholder' => __('Enter your new URL in absolute (e.g. https://www.example.com/)', 'wp-seopress'),
                'label'       => __('URL redirection', 'wp-seopress'),
                'description' => __('Enter some keywords to auto-complete this field against your content', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'visible'     => true,
            ],
        ], $id);

        return $data;
    }
}

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
                'key'         => '_seopress_redirections_type',
                'type'        => 'select',
                'placeholder' => '',
                'use_default' => '',
                'default'     => '',
                'label'       => __('Select a redirection type:', 'wp-seopress'),
                'options'     => [
                    ['value' => 301, 'label' =>  __('301 Moved Permanently', 'wp-seopress')],
                    ['value' => 302, 'label' =>  __('302 Found / Moved Temporarily', 'wp-seopress')],
                    ['value' => 307, 'label' =>  __('307 Moved Temporarily', 'wp-seopress')],
                    ['value' => 410, 'label' =>  __('410 Gone', 'wp-seopress')],
                    ['value' => 451, 'label' =>  __('451 Unavailable For Legal Reasons', 'wp-seopress')],
                ],
                'visible'     => true,
            ],
            [
                'key'         => '_seopress_redirections_value',
                'type'        => 'input',
                'placeholder' => __('Enter your new URL in absolute (eg: https://www.example.com/)', 'wp-seopress'),
                'label'       => __('URL redirection', 'wp-seopress'),
                'use_default' => '',
                'default'     => '',
                'visible'     => true,
            ],
        ], $id);

        return $data;
    }
}

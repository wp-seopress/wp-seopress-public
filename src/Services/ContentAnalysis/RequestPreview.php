<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class RequestPreview
{
    /**
     * @param int $id
     *
     * @return string
     */
    public function getDomById($id)
    {
        $args = [
            'redirection' => 2,
            'timeout'         => 30,
            'sslverify'       => false,
        ];

        //Get cookies
        $cookies = [];
        if (isset($_COOKIE)) {
            foreach ($_COOKIE as $name => $value) {
                if ('PHPSESSID' !== $name) {
                    $cookies[] = new \WP_Http_Cookie(['name' => $name, 'value' => $value]);
                }
            }
        }

        if (! empty($cookies)) {
            $args['cookies'] = $cookies;
        }

        $args = apply_filters('seopress_real_preview_remote', $args);

        $customArgs = ['no_admin_bar' => 1];

        //Useful for Page / Theme builders
        $customArgs = apply_filters('seopress_real_preview_custom_args', $customArgs);


        $link = add_query_arg('no_admin_bar', 1, get_preview_post_link((int) $id, $customArgs));
        $link = apply_filters('seopress_get_dom_link', $link, $id);

        try {
            $response = wp_remote_get($link, $args);
            $body     = wp_remote_retrieve_body($response);

            return $body;
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class RequestPreview
{
    public function getLinkRequest($id, $taxname = null){
        $args = ['no_admin_bar' => 1];

        //Useful for Page / Theme builders
        $args = apply_filters('seopress_real_preview_custom_args', $args);

        //Oxygen / beTheme compatibility
        $theme = wp_get_theme();
        if (
            (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output') && $oxygen_metabox_enabled === true)
            ||
            ('betheme' == $theme->template || 'Betheme' == $theme->parent_theme)
        ) {
            $link = get_permalink((int) $id);
            $link = add_query_arg('no_admin_bar', 1, $link);
        } else {
            $link = add_query_arg('no_admin_bar', 1, get_preview_post_link((int) $id, $args));
        }

        if(!empty($taxname)){
            $link = get_term_link((int) $id, $taxname);
            $link = add_query_arg('no_admin_bar', 1, $link);
        }


        $link = apply_filters('seopress_get_dom_link', $link, $id);

        return $link;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getDomById($id, $taxname = null)
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

        $link = $this->getLinkRequest($id, $taxname);

        try {
            $response = wp_remote_get($link, $args);
            $body     = wp_remote_retrieve_body($response);

            return $body;
        } catch (\Exception $e) {
            return null;
        }
    }
}

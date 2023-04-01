<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class InternalLinks
{
    public function getDataByXPath($xpath, $options)
    {
        $data = [];

        $permalink = get_permalink((int) $options['id']);

        $args      = [
            's'         => $permalink,
            'post_type' => 'any',
        ];
        $items = new \WP_Query($args);

        if ($items->have_posts()) {
            while ($items->have_posts()) {
                $items->the_post();
                $post_type_object = get_post_type_object(get_post_type());
                $data[] = [
                    "id" => get_the_ID(),
                    "edit_post_link" => admin_url(sprintf($post_type_object->_edit_link . '&action=edit', get_the_ID())),
                    "url" => get_the_permalink(),
                    "value" => get_the_title()
                ];
            }
        }

        wp_reset_postdata();

        //Internal links for Oxygen Builder
        $oxygen_metabox_enabled = get_option('oxygen_vsb_ignore_post_type_'.get_post_type($options['id'])) ? false : true;
        if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output') && $oxygen_metabox_enabled === true) {
            $args      = [
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'key' => 'ct_builder_shortcodes',
                        'value' => $permalink,
                        'compare' => 'LIKE'
                    ]
                ],
                'post_type' => 'any',
            ];

            $items = new \WP_Query($args);

            if ($items->have_posts()) {
                while ($items->have_posts()) {
                    $items->the_post();
                    $post_type_object = get_post_type_object(get_post_type());
                    $data[] = [
                        "id" => get_the_ID(),
                        "edit_post_link" => admin_url(sprintf($post_type_object->_edit_link . '&action=edit', get_the_ID())),
                        "url" => get_the_permalink(),
                        "value" => get_the_title()
                    ];
                }
            }
            wp_reset_postdata();
        }

        return $data;
    }
}

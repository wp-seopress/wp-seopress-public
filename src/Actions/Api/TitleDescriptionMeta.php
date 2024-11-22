<?php

namespace SEOPress\Actions\Api;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class TitleDescriptionMeta implements ExecuteHooks {
    /**
     * @var int|null
     */
    private $current_user;

    public function hooks() {
        $this->current_user = wp_get_current_user()->ID;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 4.7.0
     *
     * @return void
     */
    public function register() {
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/title-description-metas', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGet'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => function($request) {
                $post_id = $request['id'];
                $current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

                if ( ! user_can( $current_user, 'edit_post', $post_id )) {
                    return false;
                }

                return true;
            },
        ]);

        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/title-description-metas', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'processPut'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => function($request) {
                $post_id = $request['id'];
                return current_user_can('edit_post', $post_id);
            },
        ]);
    }

    /**
     * @since 4.7.0
     */
    public function processGet(\WP_REST_Request $request) {
        $id    = $request->get_param('id');

        $title       = get_post_meta($id, '_seopress_titles_title', true);
        $description = get_post_meta($id, '_seopress_titles_desc', true);

        return new \WP_REST_Response([
            'title'           => html_entity_decode($title, ENT_QUOTES | ENT_XML1, 'UTF-8'),
            'description'     => html_entity_decode($description, ENT_QUOTES | ENT_XML1, 'UTF-8'),
        ]);
    }

    /**
     * @since 4.7.0
     */
    public function processPut(\WP_REST_Request $request) {
        $id     = $request->get_param('id');
        $params = $request->get_params();

        $dataKeysSave = [
            'title'       => '_seopress_titles_title',
            'description' => '_seopress_titles_desc',
        ];

        foreach ($dataKeysSave as $key => $value) {
            if ( ! isset($params[$key])) {
                continue;
            }

            if (empty($params[$key])) {
                delete_post_meta($id, $value);
                continue;
            }

            $sanitized_value = '';
            if ($key === 'title') {
                $sanitized_value = sanitize_text_field($params[$key]);
            } elseif ($key === 'description') {
                $sanitized_value = sanitize_textarea_field($params[$key]);
            }

            update_post_meta($id, $value, $sanitized_value);
        }

        return new \WP_REST_Response([
            'code' => 'success',
        ]);
    }
}

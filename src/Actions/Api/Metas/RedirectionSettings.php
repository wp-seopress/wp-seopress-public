<?php

namespace SEOPress\Actions\Api\Metas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\RedirectionSettings as RedirectionSettingsHelper;

class RedirectionSettings implements ExecuteHooks {
    /**
     * @var int|null
     */
    private $current_user;

    public function hooks() {
        $this->current_user = wp_get_current_user()->ID;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function register() {
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/redirection-settings', [
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

        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/redirection-settings', [
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
     * @since 5.0.0
     */
    public function processPut(\WP_REST_Request $request) {

        $id     = $request->get_param('id');
        $metas = RedirectionSettingsHelper::getMetaKeys($id);
        $params = $request->get_params();

        try {

            //Elementor sync
            $elementor = get_post_meta($id, '_elementor_page_settings', true);

            $dataKeysSave = ['_seopress_redirections_value', '_seopress_redirections_enabled', '_seopress_redirections_enabled_regex', '_seopress_redirections_logged_status', '_seopress_redirections_param', '_seopress_redirections_type'];

            foreach ($metas as $key => $value) {
                if ( ! isset($params[$value['key']])) {
                    continue;
                }

                $item = $params[$value['key']];

                if (!in_array($value['key'], $dataKeysSave)) {
                    continue;
                }

                if ($value['key'] ==='_seopress_redirections_value') {
                    $item = sanitize_url($item);
                }

                if ($value['key'] ==='_seopress_redirections_enabled' || $value['key'] ==='_seopress_redirections_enabled_regex') {
                    $item = sanitize_text_field($item);
                }

                if ($value['key'] ==='_seopress_redirections_logged_status') {
                    $logged_status = sanitize_text_field($item);

                    $allowed_options = ['both', 'only_logged_in', 'only_not_logged_in'];

                    if (in_array($logged_status, $allowed_options, true)) {
                        $item = $logged_status;
                    }
                }

                if ($value['key'] ==='_seopress_redirections_param') {
                    $redirections_param = sanitize_text_field($item);

                    $allowed_options = ['exact_match', 'without_param', 'with_ignored_param'];

                    if (in_array($redirections_param, $allowed_options, true)) {
                        $item = $redirections_param;
                    }
                }

                if ($value['key'] ==='_seopress_redirections_type') {
                    $redirection_type = intval($item);

                    $allowed_options = [301, 302, 307];

                    if (in_array($redirection_type, $allowed_options, true)) {
                        $item = $redirection_type;
                    }
                }

                if(!empty($item)){
                    update_post_meta($id, $value['key'], $item);
                }
                else{
                    delete_post_meta($id, $value['key']);
                }

                if (! empty($elementor)) {
                    $elementor[$value['key']] = $item;
                }
            }

            if(!empty($elementor)){
                update_post_meta($id, '_elementor_page_settings', $elementor);
            }

            return new \WP_REST_Response([
                'code' => 'success',
            ]);
        } catch (\Exception $e) {
            return new \WP_REST_Response([
                'code'         => 'error',
                'code_message' => 'execution_failed',
            ], 403);
        }
    }

    /**
     * @since 5.0.0
     */
    public function processGet(\WP_REST_Request $request) {
        $id    = $request->get_param('id');

        $metas = RedirectionSettingsHelper::getMetaKeys($id);

        $data = [];
        foreach ($metas as $key => $value) {
            if (isset($value['use_default']) && $value['use_default']) {
                $data[] = array_merge($value, [
                    'can_modify' => false,
                    'value'      => $value['default'],
                ]);
            } else {
                $result = get_post_meta($id, $value['key'], true);
                $data[] = array_merge($value, [
                    'can_modify' => true,
                    'value'      => 'checkbox' === $value['type'] ? ($result ? true : false) : $result,
                ]);
            }
        }

        return new \WP_REST_Response($data);
    }
}

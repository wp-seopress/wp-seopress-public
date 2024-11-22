<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class TargetKeywords implements ExecuteHooks
{
    /**
     * @var int|null
     */
    private $current_user;

    public function hooks()
    {
        $this->current_user = wp_get_current_user()->ID;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function register()
    {
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/target-keywords', [
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

        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/target-keywords', [
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
    public function processGet(\WP_REST_Request $request)
    {
        $id     = $request->get_param('id');
        $targetKeywords   =  array_filter(explode(',', strtolower(get_post_meta($id, '_seopress_analysis_target_kw', true))));

        $data = seopress_get_service('CountTargetKeywordsUse')->getCountByKeywords($targetKeywords, $id);

        return new \WP_REST_Response([
            'value' => $targetKeywords,
            'usage' => $data
        ]);
    }

    /**
     * @since 5.0.0
     */
    public function processPut(\WP_REST_Request $request)
    {
        $id     = $request->get_param('id');
        $params = $request->get_params();
        if (!isset($params['_seopress_analysis_target_kw'])) {
            return new \WP_REST_Response([
                'code'         => 'error',
                'code_message' => 'missed_parameters',
            ], 403);
        }

        try {
            $targetKeywords = implode(',',array_map('trim', explode(',',$params['_seopress_analysis_target_kw'])));
            update_post_meta($id, '_seopress_analysis_target_kw', sanitize_text_field($targetKeywords));

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
}

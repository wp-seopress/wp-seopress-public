<?php

namespace SEOPress\Actions\Api\Metas;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\RobotSettings as MetaRobotSettingsHelper;

class RobotSettings implements ExecuteHooks
{
    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 5.0.0
     *
     * @return void
     */
    public function register()
    {
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/meta-robot-settings', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGet'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/meta-robot-settings', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'processPut'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => function() {
                if (current_user_can('edit_posts')) {
                    return true;
                }
                return false;
            },
        ]);
    }

    /**
     * @since 5.0.0
     */
    public function processPut(\WP_REST_Request $request)
    {
        $id     = $request->get_param('id');

        $metas = MetaRobotSettingsHelper::getMetaKeys($id);

        $params = $request->get_params();

        try {


            //Elementor sync
            $elementor = get_post_meta($id, '_elementor_page_settings', true);

            $dataKeysSave = ['_seopress_robots_index', '_seopress_robots_follow', '_seopress_robots_imageindex', '_seopress_robots_archive', '_seopress_robots_snippet', '_seopress_robots_canonical', '_seopress_robots_primary_cat', '_seopress_robots_breadcrumbs'];

            foreach ($metas as $key => $value) {
                if (! isset($params[$value['key']])) {
                    continue;
                }
                $item = $params[$value['key']];

                if (!in_array($value['key'], $dataKeysSave)) {
                    continue;
                }

                if ($value['key'] ==='_seopress_robots_canonical') {
                    $item = sanitize_url($item);
                }

                if ($value['key'] ==='_seopress_robots_primary_cat' || $value['key'] ==='_seopress_robots_index' || $value['key'] ==='_seopress_robots_follow' || $value['key'] ==='_seopress_robots_imageindex' || $value['key'] ==='_seopress_robots_archive' || $value['key'] ==='_seopress_robots_snippet' || $value['key'] ==='_seopress_robots_breadcrumbs') {
                    $item = sanitize_text_field($item);
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
    public function processGet(\WP_REST_Request $request)
    {
        $id    = $request->get_param('id');

        $metas = MetaRobotSettingsHelper::getMetaKeys($id);

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
                    'value'      => 'checkbox' === $value['type'] ? ($result === true || $result === 'yes' ? 'yes' : '') : $result,
                ]);
            }
        }

        return new \WP_REST_Response($data);
    }
}

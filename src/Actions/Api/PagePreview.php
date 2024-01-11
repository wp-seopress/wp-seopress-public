<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

class PagePreview implements ExecuteHooks
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
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/page-preview', [
            'methods'             => 'GET',
            'callback'            => [$this, 'preview'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * @since 5.0.0
     */
    public function preview(\WP_REST_Request $request)
    {
        $apiHeader = new ApiHeader();
        $apiHeader->hooks();

        $id   = (int) $request->get_param('id');
        $str  = seopress_get_service('RequestPreview')->getDomById($id);
        $data = seopress_get_service('DomFilterContent')->getData($str, $id);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            $data['analyzed_content'] = seopress_get_service('DomAnalysis')->getPostContentAnalyze($id);
            $data['analyzed_content_id'] = $id;
        }

        $data['analysis_target_kw'] = [
            'value' => array_filter(explode(',', strtolower(get_post_meta($id, '_seopress_analysis_target_kw', true))))
        ];

        return new \WP_REST_Response($data);
    }
}

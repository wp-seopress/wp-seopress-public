<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

class ContentAnalysis implements ExecuteHooks
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
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/content-analysis', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/content-analysis', [
            'methods'             => 'POST',
            'callback'            => [$this, 'save'],
            'args'                => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    },
                ],
            ],
            'permission_callback' => function ($request) {
                $nonce = $request->get_header('x-wp-nonce');
                if ( ! wp_verify_nonce($nonce, 'wp_rest')) {
                    return false;
                }

                if(!current_user_can('edit_posts')){
                    return false;
                }

                return true;
            },
        ]);
    }

    /**
     * @since 5.0.0
     */
    public function get(\WP_REST_Request $request)
    {
        $apiHeader = new ApiHeader();
        $apiHeader->hooks();

        $id   = (int) $request->get_param('id');

        $linkPreview   = seopress_get_service('RequestPreview')->getLinkRequest($id);
        $str  = seopress_get_service('RequestPreview')->getDomById($id);
        $data = seopress_get_service('DomFilterContent')->getData($str, $id);
        $data = seopress_get_service('DomAnalysis')->getDataAnalyze($data, [
            "id" => $id,
        ]);

        $saveData = [
            'words_counter' => null,
            'score' => null,
        ];

        if (isset($data['words_counter'])) {
            $saveData['words_counter'] = $data['words_counter'];
        }

        update_post_meta($id, '_seopress_content_analysis_api', $saveData);
        $data['link_preview'] = $linkPreview;

        return new \WP_REST_Response($data);
    }



    /**
     * @since 5.0.0
     */
    public function save(\WP_REST_Request $request)
    {
        $id   = (int) $request->get_param('id');
        $score   =  $request->get_param('score');
        $wordsCounter   =  $request->get_param('words_counter');

        $data = [
            'words_counter' => $wordsCounter,
            'score' => $score
        ];


        update_post_meta($id, '_seopress_content_analysis_api', $data);

        return new \WP_REST_Response(["success" => true]);
    }
}

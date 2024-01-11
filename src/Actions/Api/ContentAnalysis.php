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

                if ($nonce && wp_verify_nonce($nonce, 'wp_rest')) {
                    if (current_user_can('edit_posts')) {
                        return true;
                    }
                }

                $authorization_header = $request->get_header('Authorization');

                if (!$authorization_header) {
                    return false;
                }

                $authorization_parts = explode(' ', $authorization_header);

                if (count($authorization_parts) !== 2 || $authorization_parts[0] !== 'Basic') {
                    return false;
                }

                $credentials = base64_decode($authorization_parts[1]);
                list($username, $password) = explode(':', $credentials);

                $wp_user = get_user_by('login', $username);

                $user = wp_authenticate_application_password($wp_user, $username, $password);

                if (is_wp_error($user)) {
                    return false;
                }

                if (!user_can($user, 'edit_posts')) {
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

        $exist = get_option('seopress_content_analysis_api_in_progress');
        if($exist){
            $data = get_post_meta($id, '_seopress_content_analysis_api');
            $linkPreview   = seopress_get_service('RequestPreview')->getLinkRequest($id);
            $data['link_preview'] = $linkPreview;
            return new \WP_REST_Response($data);
        }

        update_option('seopress_content_analysis_api_in_progress', true, false);

        $linkPreview   = seopress_get_service('RequestPreview')->getLinkRequest($id);
        $str  = seopress_get_service('RequestPreview')->getDomById($id);
        $data = seopress_get_service('DomFilterContent')->getData($str, $id);
        $data = seopress_get_service('DomAnalysis')->getDataAnalyze($data, [
            "id" => $id,
        ]);

        $saveData = [
            'internal_links' => null,
            'outbound_links' => null,
            'score' => null,
        ];

        if (isset($data['internal_links'])) {
            $saveData['internal_links'] = count($data['internal_links']['value']);
        }

        if (isset($data['outbound_links'])) {
            $saveData['outbound_links'] = count($data['outbound_links']['value']);
        }

        update_post_meta($id, '_seopress_content_analysis_api', $saveData);
        delete_post_meta($id, '_seopress_analysis_data');

        $data['link_preview'] = $linkPreview;

        delete_option('seopress_content_analysis_api_in_progress');

        return new \WP_REST_Response($data);
    }



    /**
     * @since 5.0.0
     */
    public function save(\WP_REST_Request $request)
    {
        $id   = (int) $request->get_param('id');
        $score   =  $request->get_param('score');
        $internal_links   =  $request->get_param('internal_links');
        $outbound_links   =  $request->get_param('outbound_links');

        $data = [
            'internal_links' => $internal_links,
            'outbound_links' => $outbound_links,
            'score' => $score
        ];


        update_post_meta($id, '_seopress_content_analysis_api', $data);
        delete_post_meta($id, '_seopress_analysis_data');

        return new \WP_REST_Response(["success" => true]);
    }
}

<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

class PagePreview implements ExecuteHooks
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
            'permission_callback' => function($request) {
                $post_id = $request['id'];
                $current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

                if ( ! user_can( $current_user, 'edit_post', $post_id )) {
                    return false;
                }

                return true;
            },
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
        $domResult  = seopress_get_service('RequestPreview')->getDomById($id);

        if (!$domResult['success']) {
            $defaultResponse = [
                'title' =>  '...',
                'meta_desc' =>  '...',
            ];

            switch($domResult['code']){
                case 404:
                    $defaultResponse['title'] = __('To get your Google snippet preview, publish your post!', 'wp-seopress');
                    break;
                case 401:
                    $defaultResponse['title'] = __('Your site is protected by an authentication.', 'wp-seopress');
                    break;
            }
            return new \WP_REST_Response($defaultResponse);
        }

        $str = $domResult['body'];

        $data = seopress_get_service('DomFilterContent')->getData($str, $id);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            $data['analyzed_content_id'] = $id;
        }

        $data['analysis_target_kw'] = [
            'value' => array_filter(explode(',', strtolower(get_post_meta($id, '_seopress_analysis_target_kw', true))))
        ];

        return new \WP_REST_Response($data);
    }
}

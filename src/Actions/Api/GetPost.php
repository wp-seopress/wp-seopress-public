<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\MetasHelper;

class GetPost implements ExecuteHooks
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
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)', [
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

        register_rest_route('seopress/v1', '/posts/by-url', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGetByUrl'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * @since 5.0.0
     * @param int $id
     * @return array
     */
    protected function getData($id){
	    return MetasHelper::getEntityMetasData($id);
    }

    /**
     * @since 5.0.0
     *
     * @param \WP_REST_Request $request
     */
    public function processGet(\WP_REST_Request $request)
    {
        $id     = $request->get_param('id');
        $data = $this->getData($id);

        return new \WP_REST_Response($data);
    }
    /**
     * @since 5.0.0
     *
     * @param \WP_REST_Request $request
     */
    public function processGetByUrl(\WP_REST_Request $request)
    {
        $url     = $request->get_param('url');

        if(empty($url) || !$url){
            return new \WP_Error("missing_parameters", "Need an URL");
        }

        try {
            $id = apply_filters('seopress_headless_url_to_postid', url_to_postid($url), $request);
            if(!$id){
                return new \WP_Error("not_found", "ID for URL not found");
            }

            $data = $this->getData($id);

            return new \WP_REST_Response($data);
        } catch (\Exception $e) {
            return new \WP_Error("unknow");
        }
    }


}

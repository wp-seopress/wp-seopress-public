<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

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
        $post_status = get_post_status($id) ?? null;

        if ($post_status !=='publish') {
            return null;
        }

        if (true === post_password_required($id)) {
            return null;
        }

        $context = seopress_get_service('ContextPage')->buildContextWithCurrentId($id)->getContext();

        $title = seopress_get_service('TitleMeta')->getValue($context);
        $description = seopress_get_service('DescriptionMeta')->getValue($context);
        $socialFacebook = seopress_get_service('SocialFacebookMeta')->getValue($context);
        $socialTwitter = seopress_get_service('SocialTwitterMeta')->getValue($context);
        $social = seopress_get_service('SocialMeta')->getValue($context);
        $robots = seopress_get_service('RobotMeta')->getValue($context, false);
        $redirections = seopress_get_service('RedirectionMeta')->getValue($context, false);

        $canonical =  '';
        if(isset($robots['canonical'])){
            $canonical = $robots['canonical'];
            unset($robots['canonical']);
        }

        $primarycat =  '';
        if(isset($robots['primarycat'])){
            $primarycat = $robots['primarycat'];
            unset($robots['primarycat']);
        }

        $breadcrumbs =  '';
        if(isset($robots['breadcrumbs'])){
            $breadcrumbs = $robots['breadcrumbs'];
            unset($robots['breadcrumbs']);
        }

        $data = [
            "title" => $title,
            "description" => $description,
            "canonical" => $canonical,
            "og" => $socialFacebook,
            "twitter" => $socialTwitter,
            "robots" => $robots,
            "primarycat" => $primarycat,
            "breadcrumbs" => $breadcrumbs,
            "redirections" => $redirections
        ];

        return apply_filters('seopress_headless_get_post', $data, $id, $context);

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

        wp_send_json_success($data);
        return;
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

            wp_send_json_success($data);
            return;
        } catch (\Exception $e) {
            return new \WP_Error("unknow");
        }
    }


}

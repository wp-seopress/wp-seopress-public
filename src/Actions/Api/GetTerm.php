<?php

namespace SEOPress\Actions\Api;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class GetTerm implements ExecuteHooks
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
        register_rest_route('seopress/v1', '/terms/(?P<id>\d+)', [
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

    }

    /**
     * @since 5.0.0
     * @param int $id
     * @return array
     */
    protected function getData($id, $taxonomy = 'category'){
        $context = seopress_get_service('ContextPage')->buildContextWithCurrentId($id, ['type' => 'term','taxonomy' => $taxonomy])->getContext();

        $title = seopress_get_service('TitleMeta')->getValue($context);
        $description = seopress_get_service('DescriptionMeta')->getValue($context);

        $social = seopress_get_service('SocialMeta')->getValue($context);
        $robots = seopress_get_service('RobotMeta')->getValue($context);
        $redirections = seopress_get_service('RedirectionMeta')->getValue($context);

        $canonical =  '';
        if(isset($robots['canonical'])){
            $canonical = $robots['canonical'];
            unset($robots['canonical']);
        }

        if(isset($robots['primarycat'])){
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
            "og" => $social['og'],
            "twitter" => $social['twitter'],
            "robots" => $robots,
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
        $taxonomy = $request->get_param('taxonomy');
        if($taxonomy === null){
            $taxonomy = 'category';
        }

        $data = $this->getData($id, $taxonomy);

        return new \WP_REST_Response($data);
    }

}

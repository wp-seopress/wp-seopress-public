<?php

namespace SEOPress\Actions\Api;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class PreviewTitleDescriptionMeta implements ExecuteHooks {
    public function hooks() {
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 4.7.0
     *
     * @return void
     */
    public function register() {
        register_rest_route('seopress/v1', '/posts/(?P<id>\d+)/preview-title-description-metas', [
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
     * @since 4.7.0
     */
    public function preview(\WP_REST_Request $request) {
        $id          = (int) $request->get_param('id');

        $title       = $request->get_param('title');
        $description = $request->get_param('description');

        if (null === $title && null === $description) {
            return new \WP_REST_Response([
                'code'         => 'error',
                'code_message' => 'missing_parameters',
            ], 401);
        }

        $contextPage = seopress_get_service('ContextPage')->buildContextWithCurrentId($id);

        $contextPage->setPostById($id);
        $contextPage->setIsSingle(true);

        $terms = get_the_terms($id, 'post_tag');

        if ( ! empty($terms)) {
            $contextPage->setHasTag(true);
        }

        $categories = get_the_terms($id, 'category');
        if ( ! empty($categories)) {
            $contextPage->setHasCategory(true);
        }

        $title         = seopress_get_service('TagsToString')->replace($title, $contextPage->getContext());
        $description   = seopress_get_service('TagsToString')->replace($description, $contextPage->getContext());

        return new \WP_REST_Response([
            'title'           => $title,
            'description'     => $description,
        ]);
    }
}

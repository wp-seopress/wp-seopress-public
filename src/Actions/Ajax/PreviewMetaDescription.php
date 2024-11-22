<?php

namespace SEOPress\Actions\Ajax;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;

class PreviewMetaDescription implements ExecuteHooksBackend {
    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_ajax_get_preview_meta_description', [$this, 'get']);
    }

    /**
     * @since 4.4.0
     *
     * @return array
     */
    public function get() {
        if ( ! isset($_GET['template'])) { //phpcs:ignore
            wp_send_json_error();
            return;
        }

        $template    = stripcslashes($_GET['template']);
        $postId      = isset($_GET['post_id']) ? (int) $_GET['post_id'] : null;
        $homeId      = isset($_GET['home_id']) ? (int) $_GET['home_id'] : null;
        $termId      = isset($_GET['term_id']) ? (int) $_GET['term_id'] : null;

        if ( !current_user_can('edit_post', $postId) ) {
            return;
        }

        $contextPage = seopress_get_service('ContextPage')->buildContextWithCurrentId((int) $_GET['post_id']);
        if ($postId) {
            $contextPage->setPostById((int) $_GET['post_id']);
            $contextPage->setIsSingle(true);

            $terms = get_the_terms($postId, 'post_tag');

            if ( ! empty($terms)) {
                $contextPage->setHasTag(true);
            }

            $categories = get_the_terms($postId, 'category');
            if ( ! empty($categories)) {
                $contextPage->setHasCategory(true);
            }
        }

        if ($postId === $homeId && null !== $homeId) {
            $contextPage->setIsHome(true);
        }

        if ($postId === $termId && null !== $termId) {
            $contextPage->setIsCategory(true);
            $contextPage->setTermId($termId);
        }

        $value   = seopress_get_service('TagsToString')->replace($template, $contextPage->getContext());

        wp_send_json_success($value);
    }
}

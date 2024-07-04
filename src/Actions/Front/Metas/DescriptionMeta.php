<?php

namespace SEOPress\Actions\Front\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class DescriptionMeta implements ExecuteHooksFrontend {

    protected $tagsToStringService;

    public function __construct() {
        $this->tagsToStringService = seopress_get_service('TagsToString');
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        if (apply_filters('seopress_old_wp_head_description', true)) {
            return;
        }
        add_action('wp_head', [$this, 'preLoad'], 0);
    }

    public function preLoad(){
        if ((function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && \Ecwid_Store_Page::is_store_page())) {//disable on wpForo pages to avoid conflicts
            return;
        }

        add_action('wp_head', [$this, 'render'], 1);
    }

    public function render() {
        $content = $this->getContent();

        if (empty($content)) {
            return;
        }

        $html = '<meta name="description" content="' . $content . '">';
        $html .= "\n";
        echo $html;
    }

    protected function getContent() {
        $context = seopress_get_service('ContextPage')->getContext();

        $description = seopress_get_service('DescriptionMeta')->getValue($context);

        return $description;
    }
}

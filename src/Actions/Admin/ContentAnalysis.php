<?php

namespace SEOPress\Actions\Admin;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;

class ContentAnalysis implements ExecuteHooksBackend {
    /**
     * @since 4.6.0
     *
     * @return void
     */
    public function hooks() {
        add_filter('seopress_content_analysis_content', [$this, 'addContent'], 10, 2);
    }

    public function addContent($content, $id) {
        if ( ! apply_filters('seopress_content_analysis_acf_fields', true)) {
            return $content;
        }

        if ( ! function_exists('get_field_objects')) {
            return $content;
        }

        return $content . seopress_get_service('ContentAnalysisAcfFields')->addAcfContent($id);
    }
}

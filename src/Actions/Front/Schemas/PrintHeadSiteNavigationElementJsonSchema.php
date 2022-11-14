<?php

namespace SEOPress\Actions\Front\Schemas;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class PrintHeadSiteNavigationElementJsonSchema implements ExecuteHooksFrontend {
    public function hooks() {
        add_action('wp_head', [$this, 'render'], 2);
    }

    public function render() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            return;
        }
        /**
         * Check if Rich Snippets toggle is ON
         *
         * @since 5.6
         * @author Benjamin
         */
        if (seopress_get_toggle_option('rich-snippets') !=='1') {
            return;
        }

        /**
         * Check if is homepage
         *
         * @since 5.6
         * @author Benjamin
         */
        if (!is_front_page()) {
            return;
        }

        if ('none' === seopress_pro_get_service('OptionPro')->getRichSnippetsSiteNavigation()) {
            return;
        }

        $jsons = seopress_get_service('JsonSchemaGenerator')->getJsonsEncoded([
            'site-navigation-element'
        ]);

        if ($jsons[0] === '[]') {
            return;
        }
        ?>
        <script type="application/ld+json"><?php echo apply_filters('seopress_schemas_site_navigation_element_html', $jsons[0]); ?></script>
        <?php
    }
}

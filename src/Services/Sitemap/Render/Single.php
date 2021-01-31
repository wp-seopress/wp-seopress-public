<?php

namespace SEOPress\Services\Sitemap\Render;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Single {
    const NAME_SERVICE = 'SitemapRenderSingle';

    /**
     * @since 4.3.0
     *
     * @return void
     */
    protected function hooksWPMLCompatibility() {
        add_filter('seopress_sitemaps_single_query', function ($args) {
            global $sitepress, $sitepress_settings;

            $sitepress_settings['auto_adjust_ids'] = 0;
            remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
            remove_filter('category_link', [$sitepress, 'category_link_adjust_id'], 1);

            return $args;
        });

        add_filter('wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5);
        add_action('the_post', function ($post) {
            $language = apply_filters('wpml_element_language_code', null, [
                  'element_id'   => $post->ID,
                  'element_type' => 'page',
              ]);
            do_action('wpml_switch_language', $language);
        });
    }

    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function render() {
        if ( ! function_exists('seopress_get_service')) {
            return;
        }

        seopress_get_service('SitemapHeaders')->printHeaders();

        //Remove primary category
        remove_filter('post_link_category', 'seopress_titles_primary_cat_hook', 10, 3);

        $this->hooksWPMLCompatibility();

        ob_start();
        include_once SEOPRESS_TEMPLATE_SITEMAP_DIR . '/single.php';
        $xml = ob_get_contents();
        ob_end_clean();

        echo apply_filters('seopress_sitemaps_xml_single', $xml);
    }
}

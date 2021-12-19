<?php

namespace SEOPress\Actions\Sitemap;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class Render implements ExecuteHooksFrontend {
    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function hooks() {
        add_action('pre_get_posts', [$this, 'render'], 1);
        add_action('template_redirect', [$this, 'sitemapShortcut'], 1);
    }

    /**
     * @since 4.3.0
     * @see @pre_get_posts
     *
     * @param Query $query
     *
     * @return void
     */
    public function render($query) {
        if ( ! $query->is_main_query()) {
            return;
        }

        if ('1' !== seopress_xml_sitemap_general_enable_option() || '1' !== seopress_get_toggle_option('xml-sitemap')) {
            return;
        }

        $filename = null;
        if ('1' === get_query_var('seopress_sitemap')) {
            $filename = 'template-xml-sitemaps.php';
        } elseif ('1' === get_query_var('seopress_sitemap_xsl')) {
            $filename = 'template-xml-sitemaps-xsl.php';
        } elseif ('1' === get_query_var('seopress_author')) {
            $filename = 'template-xml-sitemaps-author.php';
        } elseif ('' !== get_query_var('seopress_cpt')) {
            if (function_exists('seopress_xml_sitemap_post_types_list_option')
                && '' != seopress_xml_sitemap_post_types_list_option()
                && array_key_exists(get_query_var('seopress_cpt'), seopress_xml_sitemap_post_types_list_option())) {
                if ( ! function_exists('seopress_get_service')) {
                    return;
                }
                /*
                 * @since 4.3.0
                 */
                seopress_get_service('SitemapRenderSingle')->render();
                exit();
            } elseif (function_exists('seopress_xml_sitemap_taxonomies_list_option')
                && '' != seopress_xml_sitemap_taxonomies_list_option()
                && array_key_exists(get_query_var('seopress_cpt'), seopress_xml_sitemap_taxonomies_list_option())) {
                $filename = 'template-xml-sitemaps-single-term.php';
            }
        }

        if (null !== $filename && file_exists(SEOPRESS_PLUGIN_DIR_PATH . 'inc/functions/sitemap/' . $filename)) {
            include SEOPRESS_PLUGIN_DIR_PATH . 'inc/functions/sitemap/' . $filename;

            exit();
        }
    }

    /**
     * @since 4.3.0
     * @see @template_redirect
     *
     * @return void
     */
    public function sitemapShortcut() {
        //Redirect sitemap.xml to sitemaps.xml
        $get_current_url = get_home_url() . $_SERVER['REQUEST_URI'];
        if (in_array($get_current_url, [
                get_home_url() . '/sitemap.xml/',
                get_home_url() . '/sitemap.xml',
                get_home_url() . '/wp-sitemap.xml/',
                get_home_url() . '/wp-sitemap.xml',
                get_home_url() . '/sitemap_index.xml/',
                get_home_url() . '/sitemap_index.xml',
            ])) {
            wp_safe_redirect(get_home_url() . '/sitemaps.xml', 301);
            exit();
        }
    }
}

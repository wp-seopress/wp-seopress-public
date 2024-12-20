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
        add_filter( 'wp_sitemaps_enabled', [$this, 'sitemaps_enabled'] );
        add_action('template_redirect', [$this, 'sitemapShortcut'], 1);
    }

    /**
     * @since 7.7.0
     * @see @wp_sitemaps_enabled
     *
     * @return boolean
     */
    public function sitemaps_enabled() {
        if ('1' === seopress_get_toggle_option('xml-sitemap')) {
            return false;
        }

        return true;
    }

    /**
     * @since 7.0
     *
     * @return void
     */
    protected function hooksWPMLCompatibility() {
        if (!defined('ICL_SITEPRESS_VERSION')) {
            return;
        }

        //Check if WPML is not setup as multidomain
        if ( 2 != apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
            add_filter('request', function ($q) {
                $current_language = apply_filters('wpml_current_language', false);
                $default_language = apply_filters('wpml_default_language', false);
                if ($current_language !== $default_language) {
                    unset($q['seopress_sitemap']);
                    unset($q['seopress_cpt']);
                    unset($q['seopress_paged']);
                    unset($q['seopress_author']);
                    unset($q['seopress_sitemap_xsl']);
                    unset($q['seopress_sitemap_video_xsl']);
                }

                return $q;
            });
        }
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

        if ('1' !== seopress_get_service('SitemapOption')->isEnabled() || '1' !== seopress_get_toggle_option('xml-sitemap')) {
            return;
        }

        $filename = null;
        if ('1' === get_query_var('seopress_sitemap')) {
            $filename = 'template-xml-sitemaps.php';
        } elseif ('1' === get_query_var('seopress_sitemap_xsl')) {
            $filename = 'template-xml-sitemaps-xsl.php';
        } elseif ('1' === get_query_var('seopress_sitemap_video_xsl')) {
            $filename = 'template-xml-sitemaps-video-xsl.php';
        } elseif ('1' === get_query_var('seopress_author')) {
            $filename = 'template-xml-sitemaps-author.php';
        } elseif ('' !== get_query_var('seopress_cpt')) {
            if (!empty(seopress_get_service('SitemapOption')->getPostTypesList())
                && array_key_exists(get_query_var('seopress_cpt'), seopress_get_service('SitemapOption')->getPostTypesList())) {
                /*
                 * @since 4.3.0
                 */
                seopress_get_service('SitemapRenderSingle')->render();
                exit();
            } elseif (!empty(seopress_get_service('SitemapOption')->getTaxonomiesList())
                && array_key_exists(get_query_var('seopress_cpt'), seopress_get_service('SitemapOption')->getTaxonomiesList())) {
                $filename = 'template-xml-sitemaps-single-term.php';
            }
            else{
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                return;
            }
        }


        if ($filename === 'template-xml-sitemaps-video-xsl.php') {
            include SEOPRESS_PRO_PLUGIN_DIR_PATH . 'inc/functions/video-sitemap/' . $filename;
            exit();
        } elseif (null !== $filename && file_exists(SEOPRESS_PLUGIN_DIR_PATH . 'inc/functions/sitemap/' . $filename)) {
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
        if ('1' !== seopress_get_toggle_option('xml-sitemap')) {
            return;
        }

        if ('1' !== seopress_get_service('SitemapOption')->isEnabled()) {
            return;
        }

        //Redirect sitemap.xml to sitemaps.xml
		$request_uri = '';
		if (isset($_SERVER['REQUEST_URI'])) {
			$request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));
		}
        $path = trim(basename($request_uri), '/');

        $path_parts = explode('/', $path);
        $last_part = end($path_parts);

        $redirect_paths = [
            'sitemap.xml',
            'wp-sitemap.xml',
            'sitemap_index.xml',
        ];

        if (in_array($last_part, $redirect_paths)) {
            wp_safe_redirect(get_home_url() . '/sitemaps.xml', 301);
            exit();
        }
    }
}

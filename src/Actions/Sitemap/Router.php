<?php // phpcs:ignore

namespace SEOPress\Actions\Sitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Router
 */
class Router implements ExecuteHooks {
	/**
	 * The Router hooks.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'query_vars', array( $this, 'queryVars' ) );
	}

	/**
	 * The init function.
	 *
	 * @since 4.3.0
	 * @see init
	 *
	 * @return void
	 */
	public function init() {
		if ( '1' !== seopress_get_service( 'SitemapOption' )->isEnabled() || '1' !== seopress_get_toggle_option( 'xml-sitemap' ) ) {
			return;
		}

		// Use the static method to register rules.
		// This keeps the registration logic in one place.
		$sitemap_options = get_option( 'seopress_xml_sitemap_option_name' );
		$toggle_options  = get_option( 'seopress_toggle' );

		self::registerRewriteRules( $sitemap_options, $toggle_options );
	}

	/**
	 * Register sitemap rewrite rules
	 *
	 * This static method contains the actual rewrite rule registration logic.
	 * It's called by both init() (during normal requests) and by the activation hook
	 * (in seopress.php) to avoid code duplication.
	 *
	 * @since 9.4.0
	 *
	 * @param array $sitemap_options The sitemap options array.
	 * @param array $toggle_options The toggle options array.
	 * @return void
	 */
	public static function registerRewriteRules( $sitemap_options, $toggle_options ) {
		$is_sitemap_enabled = isset( $sitemap_options['seopress_xml_sitemap_general_enable'] ) && '1' === $sitemap_options['seopress_xml_sitemap_general_enable'];
		$is_toggle_enabled  = isset( $toggle_options['toggle-xml-sitemap'] ) && '1' === $toggle_options['toggle-xml-sitemap'];

		if ( ! $is_sitemap_enabled || ! $is_toggle_enabled ) {
			return;
		}

		// XML Index.
		add_rewrite_rule( '^sitemaps.xml$', 'index.php?seopress_sitemap=1', 'top' );

		// XSL Sitemap.
		add_rewrite_rule( '^sitemaps_xsl.xsl$', 'index.php?seopress_sitemap_xsl=1', 'top' );

		// XSL Video Sitemap.
		add_rewrite_rule( '^sitemaps_video_xsl.xsl$', 'index.php?seopress_sitemap_video_xsl=1', 'top' );

		add_rewrite_rule( '([^/]+?)-sitemap([0-9]+)?\.xml$', 'index.php?seopress_cpt=$matches[1]&seopress_paged=$matches[2]', 'top' );

		// XML Author.
		$is_author_enabled = isset( $sitemap_options['seopress_xml_sitemap_author_enable'] ) && '1' === $sitemap_options['seopress_xml_sitemap_author_enable'];
		if ( $is_author_enabled ) {
			add_rewrite_rule( 'author.xml?$', 'index.php?seopress_author=1', 'top' );
		}
	}

	/**
	 * The queryVars function.
	 *
	 * @since 4.3.0
	 * @see query_vars
	 *
	 * @param array $vars The query variables.
	 *
	 * @return array
	 */
	public function queryVars( $vars ) {
		$vars[] = 'seopress_sitemap';
		$vars[] = 'seopress_sitemap_xsl';
		$vars[] = 'seopress_sitemap_video_xsl';
		$vars[] = 'seopress_cpt';
		$vars[] = 'seopress_paged';
		$vars[] = 'seopress_author';

		return $vars;
	}
}

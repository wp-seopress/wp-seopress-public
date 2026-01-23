<?php // phpcs:ignore
/**
 * Render
 *
 * This file is used to render the XML sitemaps.
 *
 * @package Actions
 */
namespace SEOPress\Actions\Sitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * Render
 */
class Render implements ExecuteHooksFrontend {
	/**
	 * Default sitemap templates directory path
	 *
	 * @since 9.4.0
	 * @var string
	 */
	const SITEMAP_TEMPLATE_DIR = 'inc/functions/sitemap/';

	/**
	 * PRO video sitemap templates directory path
	 *
	 * @since 9.4.0
	 * @var string
	 */
	const PRO_VIDEO_TEMPLATE_DIR = 'inc/functions/video-sitemap/';

	/**
	 * The Render hooks.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'pre_get_posts', array( $this, 'render' ), 1 );
		add_filter( 'wp_sitemaps_enabled', array( $this, 'disable_wordpress_core_sitemap' ) );
		add_action( 'template_redirect', array( $this, 'sitemapShortcut' ), 1 );
	}

	/**
	 * The disable_wordpress_core_sitemap function.
	 *
	 * @since 7.7.0
	 * @see @wp_sitemaps_enabled
	 *
	 * @return boolean
	 */
	public function disable_wordpress_core_sitemap() {
		if ( '1' === seopress_get_toggle_option( 'xml-sitemap' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * The hooksWPMLCompatibility function.
	 *
	 * @since 7.0
	 *
	 * @return void
	 */
	protected function hooksWPMLCompatibility() {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		// Check if WPML is not setup as multidomain.
		if ( 2 !== apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
			add_filter(
				'request',
				function ( $q ) {
					$current_language = apply_filters( 'wpml_current_language', false );
					$default_language = apply_filters( 'wpml_default_language', false );
					if ( $current_language !== $default_language ) {
						unset( $q['seopress_sitemap'] );
						unset( $q['seopress_cpt'] );
						unset( $q['seopress_paged'] );
						unset( $q['seopress_author'] );
						unset( $q['seopress_sitemap_xsl'] );
						unset( $q['seopress_sitemap_video_xsl'] );
					}

					return $q;
				}
			);
		}
	}

	/**
	 * The render function.
	 *
	 * @since 4.3.0
	 * @since 9.4.0 Refactored to use route mapping array instead of if-elseif chain
	 * @see @pre_get_posts
	 *
	 * @param WP_Query $query The query object.
	 *
	 * @return void
	 */
	public function render( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if (
			'1' !== seopress_get_service( 'SitemapOption' )->isEnabled()
			|| '1' !== seopress_get_toggle_option( 'xml-sitemap' )
		) {
			return;
		}

		// Route mapping: query_var => template_file.
		// Templates are stored in the inc/functions/sitemap/ directory.
		$routes = array(
			'seopress_sitemap'           => 'template-xml-sitemaps.php',
			'seopress_sitemap_xsl'       => 'template-xml-sitemaps-xsl.php',
			'seopress_sitemap_video_xsl' => 'template-xml-sitemaps-video-xsl.php',
			'seopress_author'            => 'template-xml-sitemaps-author.php',
		);

		// Check simple routes first.
		foreach ( $routes as $query_var => $template ) {
			if ( '1' === get_query_var( $query_var ) ) {
				$this->render_template( $template );
				return;
			}
		}

		// Handle complex seopress_cpt route (post types and taxonomies).
		$cpt = get_query_var( 'seopress_cpt' );
		if ( '' !== $cpt ) {
			$this->render_cpt_sitemap( $cpt );
		}
	}

	/**
	 * Render a sitemap template file
	 *
	 * @since 9.4.0
	 * @param string $filename Template filename.
	 * @return void
	 */
	private function render_template( $filename ) {
		// Map PRO templates to their specific directories.
		$pro_templates = array(
			'template-xml-sitemaps-video-xsl.php' => self::PRO_VIDEO_TEMPLATE_DIR,
		);

		// Determine the correct path.
		if ( isset( $pro_templates[ $filename ] ) && defined( 'SEOPRESS_PRO_PLUGIN_DIR_PATH' ) ) {
			// PRO template.
			$filepath = SEOPRESS_PRO_PLUGIN_DIR_PATH . $pro_templates[ $filename ] . $filename;
		} else {
			// Standard template.
			$filepath = SEOPRESS_PLUGIN_DIR_PATH . self::SITEMAP_TEMPLATE_DIR . $filename;
		}

		// Include template if it exists.
		if ( file_exists( $filepath ) ) {
			include $filepath;
			exit();
		}
	}

	/**
	 * Render CPT or taxonomy sitemap
	 *
	 * @since 9.4.0
	 * @param string $cpt The post type or taxonomy slug.
	 * @return void
	 */
	private function render_cpt_sitemap( $cpt ) {
		// Check if it's a post type.
		$post_types = seopress_get_service( 'SitemapOption' )->getPostTypesList();
		if ( ! empty( $post_types ) && array_key_exists( $cpt, $post_types ) ) {
			seopress_get_service( 'SitemapRenderSingle' )->render();
			exit();
		}

		// Check if it's a taxonomy.
		$taxonomies = seopress_get_service( 'SitemapOption' )->getTaxonomiesList();
		if ( ! empty( $taxonomies ) && array_key_exists( $cpt, $taxonomies ) ) {
			$this->render_template( 'template-xml-sitemaps-single-term.php' );
			return;
		}

		// Not found - return 404.
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}

	/**
	 * The sitemapShortcut function.
	 *
	 * @since 4.3.0
	 * @see @template_redirect
	 *
	 * @return void
	 */
	public function sitemapShortcut() {
		if ( '1' !== seopress_get_toggle_option( 'xml-sitemap' ) ) {
			return;
		}

		if ( '1' !== seopress_get_service( 'SitemapOption' )->isEnabled() ) {
			return;
		}

		// Redirect sitemap.xml to sitemaps.xml.
		$request_uri = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$path = trim( basename( $request_uri ), '/' );

		$path_parts = explode( '/', $path );
		$last_part  = end( $path_parts );

		$redirect_paths = array(
			'sitemap.xml',
			'wp-sitemap.xml',
			'sitemap_index.xml',
		);

		if ( in_array( $last_part, $redirect_paths, true ) ) {
			wp_safe_redirect( get_home_url() . '/sitemaps.xml', 301 );
			exit();
		}
	}
}

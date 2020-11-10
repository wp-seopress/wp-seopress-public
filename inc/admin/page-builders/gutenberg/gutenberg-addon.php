<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Autoloader
 *
 * @param   string $class
 *
 * @return  boolean
 */
function wp_seopress_gutenberg_addon_autoloader( $class ) {
	$dir = '/inc';
	
	switch ( $class ) {
		case false !== strpos( $class, 'WPSeoPressGutenbergAddon\\FAQBlock\\' ):
			$class = strtolower( str_replace( 'WPSeoPressGutenbergAddon\\FAQBlock', '', $class ) );
			$dir .= '/blocks/faq-block/src';
		break;
		case false !== strpos( $class, 'WPSeoPressGutenbergAddon\\' ):
			$class = strtolower( str_replace( 'WPSeoPressGutenbergAddon', '', $class ) );
		break;
		default:
		return;
	}
	
	$filename = dirname( __FILE__ ) . $dir . str_replace( '_', '-', str_replace( '\\', '/class-', $class ) ) . '.php';
	
	if ( file_exists( $filename ) ) {
		require_once $filename;

		if ( class_exists( $class ) ) {
			return true;
		}
	}

	return false;
}
spl_autoload_register( 'wp_seopress_gutenberg_addon_autoloader' );

final class WP_SeoPress_Gutenberg_Addon {
	/**
	 * Class instance
	 *
	 * @var \WP_SeoPress_Gutenberg_Addon
	 */
	private static $instance = null;

	/**
	 * Load instance of the class
	 *
	 * @return  \WP_SeoPress_Gutenberg_Addon
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
			self::$instance->_constants();
			self::$instance->_load_objects();

			add_filter( 'block_categories', [ self::$instance, 'register_block_categories' ] );
		}

		return self::$instance;
	}

	/**
	 * Constructor private
	 *
	 * @return  void
	 */
	private function __construct() {

	}

	/**
	 * Define plugin constants
	 *
	 * @return  void
	 */
	private function _constants() {
		if ( ! defined( 'SEOPRESS_GUTENBERG_ADDON_DIR' ) ) {
			define( 'SEOPRESS_GUTENBERG_ADDON_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}

		if ( ! defined( 'SEOPRESS_GUTENBERG_ADDON_URL' ) ) {
			define( 'SEOPRESS_GUTENBERG_ADDON_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}
	}

	/**
	 * Register custom block categories
	 *
	 * @param   array  $categories  
	 *
	 * @return  array               
	 */
	public function register_block_categories( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'wpseopress',
					'title' => __( 'SEO', 'wp-seopress' ),
				),
			)
		);
	}

	/**
	 * Initiate classes
	 *
	 * @return  void
	 */
	private function _load_objects() {
		\WPSeoPressGutenbergAddon\FAQBlock\Register::get_instance();
	}
}

WP_SeoPress_Gutenberg_Addon::get_instance();

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
function wp_seopress_elementor_addon_autoloader( $class ) {
	$dir = '/inc';

	switch ( $class ) {
		case false !== strpos( $class, 'WPSeoPressElementorAddon\\Admin\\' ):
										$class = strtolower( str_replace( 'WPSeoPressElementorAddon\\Admin', '', $class ) );
										$dir  .= '/admin';
			break;
		case false !== strpos( $class, 'WPSeoPressElementorAddon\\Controls\\' ):
										$class = strtolower( str_replace( 'WPSeoPressElementorAddon\\Controls', '', $class ) );
										$dir  .= '/controls';
			break;
		case false !== strpos( $class, 'WPSeoPressElementorAddon\\' ):
										$class = strtolower( str_replace( 'WPSeoPressElementorAddon', '', $class ) );
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
spl_autoload_register( 'wp_seopress_elementor_addon_autoloader' );

final class WP_SeoPress_Elementor_Addon {
	/**
	 * Class instance
	 *
	 * @var \WP_SeoPress_Elementor_Addon
	 */
	private static $instance = null;

	/**
	 * Load instance of the class
	 *
	 * @return  \WP_SeoPress_Elementor_Addon
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
			self::$instance->_constants();
			self::$instance->_load_objects();
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
		if ( ! defined( 'SEOPRESS_ELEMENTOR_ADDON_DIR' ) ) {
			define( 'SEOPRESS_ELEMENTOR_ADDON_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}

		if ( ! defined( 'SEOPRESS_ELEMENTOR_ADDON_URL' ) ) {
			define( 'SEOPRESS_ELEMENTOR_ADDON_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}
	}

	/**
	 * Initiate classes
	 *
	 * @return  void
	 */
	private function _load_objects() {
		if ( is_admin() ) {
			\WPSeoPressElementorAddon\Register_Controls::get_instance();
			\WPSeoPressElementorAddon\Admin\Seopress_Meta_Helper::get_meta_fields();
			\WPSeoPressElementorAddon\Admin\Document_Settings_Section::get_instance();
		}
	}
}

WP_SeoPress_Elementor_Addon::get_instance();

function seopress_elementor_tabs_seo_start() {
	ob_start();
}

function seopress_elementor_tabs_seo_end() {
	$output  = \ob_get_clean();
	$search  = '/(<div class="elementor-component-tab elementor-panel-navigation-tab" data-tab="global">.*<\/div>)/m';
	$replace = '${1}<div id="seopress-seo-tab" class="elementor-panel-navigation-tab" data-tab="seo">SEO</div>';
	echo \preg_replace(
		$search,
		$replace,
		$output
	);
}
add_action( 'elementor/editor/footer', 'seopress_elementor_tabs_seo_start', 0 );
add_action( 'elementor/editor/footer', 'seopress_elementor_tabs_seo_end', 999 );
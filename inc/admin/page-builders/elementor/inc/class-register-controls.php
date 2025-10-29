<?php
/**
 * Register Controls
 *
 * @package Elementor
 */

namespace WPSeoPressElementorAddon;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Register Controls
 */
class Register_Controls {
	use \WPSeoPressElementorAddon\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function _initialize() {
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );
	}

	/**
	 * Register controls
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Controls manager.
	 *
	 * @return  void
	 */
	public function register_controls( $controls_manager ) {
		$controls_manager->register( new \WPSeoPressElementorAddon\Controls\Social_Preview_Control() );
		$controls_manager->register( new \WPSeoPressElementorAddon\Controls\Text_Letter_Counter_Control() );
		$controls_manager->register( new \WPSeoPressElementorAddon\Controls\Content_Analysis_Control() );
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$controls_manager->register( new \WPSeoPressElementorAddon\Controls\Google_Suggestions_Control() );
		}
	}
}

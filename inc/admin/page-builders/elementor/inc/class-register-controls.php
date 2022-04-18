<?php
namespace WPSeoPressElementorAddon;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Register_Controls {
	use \WPSeoPressElementorAddon\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function _initialize() {
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
	}

	/**
	 * Register controls
	 *
	 * @return  void
	 */
	public function register_controls( $controls_manager ) {
		$controls_manager->register_control( 'seopress-social-preview', new \WPSeoPressElementorAddon\Controls\Social_Preview_Control() );
		$controls_manager->register_control( 'seopresstextlettercounter', new \WPSeoPressElementorAddon\Controls\Text_Letter_Counter_Control() );
		$controls_manager->register_control( 'seopress-content-analysis', new \WPSeoPressElementorAddon\Controls\Content_Analysis_Control() );
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$controls_manager->register_control( 'seopress-google-suggestions', new \WPSeoPressElementorAddon\Controls\Google_Suggestions_Control() );
		}
	}
}

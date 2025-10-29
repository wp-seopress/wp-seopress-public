<?php
/**
 * Singleton
 *
 * @package Elementor
 */

namespace WPSeoPressElementorAddon;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Singleton
 */
trait Singleton {
	/**
	 * Instance of the object
	 *
	 * @var \Object Object.
	 */
	private static $instance = null;

	/**
	 * Setup singleton instanc
	 *
	 * @return  \Object Object.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Private consturct
	 *
	 * @return  void Void.
	 */
	private function __construct() {
		if ( method_exists( $this, '_initialize' ) ) {
			$this->_initialize();
		}
	}
}

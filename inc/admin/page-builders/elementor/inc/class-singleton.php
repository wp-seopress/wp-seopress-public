<?php
namespace WPSeoPressElementorAddon;

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
trait Singleton {
	/**
	 * Instance of the object
	 *
	 * @var \Object
	 */
	private static $instance = null;

	/**
	 * Setup singleton instanc
	 *
	 * @return  \Object
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
	 * @return  void
	 */
	private function __construct() {
		if ( method_exists( $this, '_initialize' ) ) {
			$this->_initialize();
		}
	}
}

<?php // phpcs:ignore

namespace SEOPress\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Container\ContainerSeopress;
use SEOPress\Core\Hooks\ActivationHook;
use SEOPress\Core\Hooks\DeactivationHook;
use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * Kernel
 */
abstract class Kernel {
	/**
	 * The container.
	 *
	 * @var ContainerSeopress
	 */
	protected static $container = null;

	/**
	 * The data.
	 *
	 * @var array
	 */
	protected static $data = array(
		'slug'      => null,
		'main_file' => null,
		'file'      => null,
		'root'      => null,
	);

	/**
	 * The set container function.
	 *
	 * @param ManageContainer $container The container.
	 *
	 * @return void
	 */
	public static function setContainer( ManageContainer $container ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		self::$container = self::getDefaultContainer();
	}

	/**
	 * The get default container function.
	 *
	 * @return ContainerSeopress
	 */
	protected static function getDefaultContainer() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return new ContainerSeopress();
	}

	public static function getContainer() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( null === self::$container ) {
			self::$container = self::getDefaultContainer();
		}

		return self::$container;
	}

	/**
	 * The handle hooks plugin function.
	 *
	 * @return void
	 */
	public static function handleHooksPlugin() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( current_filter() ) {
			case 'plugins_loaded':
				foreach ( self::getContainer()->getActions() as $key => $class ) {
					try {
						if ( ! class_exists( $class ) ) {
							continue;
						}

						$class = new $class();
						switch ( true ) {
							case $class instanceof ExecuteHooksBackend:
								if ( is_admin() ) {
									$class->hooks();
								}
								break;

							case $class instanceof ExecuteHooksFrontend:
								if ( ! is_admin() ) {
									$class->hooks();
								}
								break;

							case $class instanceof ExecuteHooks:
								$class->hooks();
								break;
						}
					} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Do nothing.
					}
				}
				break;
			case 'activate_' . self::$data['slug'] . '/' . self::$data['main_file'] . '.php':
				foreach ( self::getContainer()->getActions() as $key => $class ) {
					try {
						if ( ! class_exists( $class ) ) {
							continue;
						}
						$class = new $class();

						if ( $class instanceof ActivationHook ) {
							$class->activate();
						}
					} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Do nothing.
					}
				}
				break;
			case 'deactivate_' . self::$data['slug'] . '/' . self::$data['main_file'] . '.php':
				foreach ( self::getContainer()->getActions() as $key => $class ) {
					try {
						if ( ! class_exists( $class ) ) {
							continue;
						}
						$class = new $class();
						if ( $class instanceof DeactivationHook ) {
							$class->deactivate();
						}
					} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Do nothing.
					}
				}
				break;
		}
	}

	/**
	 * The build container function.
	 *
	 * @return void
	 */
	public static function buildContainer() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		self::buildClasses( self::$data['root'] . '/src/Services', 'services', 'Services\\' );
		self::buildClasses( self::$data['root'] . '/src/Thirds', 'services', 'Thirds\\' );
		self::buildClasses( self::$data['root'] . '/src/Actions', 'actions', 'Actions\\' );
	}

	/**
	 * The build classes function.
	 *
	 * @static
	 *
	 * @param string $path The path.
	 * @param string $type The type.
	 * @param string $namespace The namespace.
	 *
	 * @return void
	 */
	public static function buildClasses( $path, $type, $namespace = '' ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		try {
			$files = array_diff( scandir( $path ), array( '..', '.' ) );
			foreach ( $files as $filename ) {
				$path_check = $path . '/' . $filename;

				if ( is_dir( $path_check ) ) {
					self::buildClasses( $path_check, $type, $namespace . $filename . '\\' );
					continue;
				}

				$pathinfo = pathinfo( $filename );
				if ( isset( $pathinfo['extension'] ) && 'php' !== $pathinfo['extension'] ) {
					continue;
				}

				$data = '\\SEOPress\\' . $namespace . str_replace( '.php', '', $filename );

				switch ( $type ) {
					case 'services':
						self::getContainer()->setService( $data );
						break;
					case 'actions':
						self::getContainer()->setAction( $data );
						break;
				}
			}
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do nothing.
		}
	}

	/**
	 * The execute function.
	 *
	 * @param array $data The data.
	 *
	 * @return void
	 */
	public static function execute( $data ) {
		self::$data = array_merge( self::$data, $data );

		self::buildContainer();

		add_action( 'plugins_loaded', array( __CLASS__, 'handleHooksPlugin' ) );
		register_activation_hook( $data['file'], array( __CLASS__, 'handleHooksPlugin' ) );
		register_deactivation_hook( $data['file'], array( __CLASS__, 'handleHooksPlugin' ) );
	}
}

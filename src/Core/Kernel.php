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

						// Check the interface before construction: constructors may
						// resolve services that this request will never use.
						switch ( true ) {
							case is_a( $class, ExecuteHooksBackend::class, true ):
								if ( is_admin() ) {
									( new $class() )->hooks();
								}
								break;

							case is_a( $class, ExecuteHooksFrontend::class, true ):
								if ( ! is_admin() ) {
									( new $class() )->hooks();
								}
								break;

							case is_a( $class, ExecuteHooks::class, true ):
								( new $class() )->hooks();
								break;
						}
					} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Skip any class that cannot be loaded or instantiated.
						// class_exists() autoloads the file, so a class whose
						// parent interface/class is momentarily unavailable (an
						// in-progress plugin update swapping files, a stale
						// opcache, a partial deploy) throws \Error, not
						// \Exception. Catching \Throwable keeps one broken class
						// from white-screening the whole site during that window.
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
					} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Skip any class that cannot be loaded or instantiated.
						// class_exists() autoloads the file, so a class whose
						// parent interface/class is momentarily unavailable (an
						// in-progress plugin update swapping files, a stale
						// opcache, a partial deploy) throws \Error, not
						// \Exception. Catching \Throwable keeps one broken class
						// from white-screening the whole site during that window.
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
					} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Skip any class that cannot be loaded or instantiated.
						// class_exists() autoloads the file, so a class whose
						// parent interface/class is momentarily unavailable (an
						// in-progress plugin update swapping files, a stale
						// opcache, a partial deploy) throws \Error, not
						// \Exception. Catching \Throwable keeps one broken class
						// from white-screening the whole site during that window.
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
		$map = self::get_release_container_map();
		if ( null !== $map ) {
			self::getContainer()->set_service_definitions( $map['services'] );
		} else {
			self::buildClasses( self::$data['root'] . '/src/Services', 'services', 'Services\\' );
		}

		// These integrations include procedural hook registration at file load.
		self::buildClasses( self::$data['root'] . '/src/Thirds', 'services', 'Thirds\\' );

		if ( null !== $map ) {
			foreach ( $map['actions'] as $action ) {
				self::getContainer()->setAction( $action );
			}
		} else {
			self::buildClasses( self::$data['root'] . '/src/Actions', 'actions', 'Actions\\' );
		}
	}

	/**
	 * Use a matching release map, or discover classes normally in a checkout.
	 *
	 * @return array|null
	 */
	private static function get_release_container_map() {
		$file = self::$data['root'] . '/src/Core/container-map.php';
		if ( ! is_file( $file ) ) {
			return null;
		}
		try {
			$map = require $file;
			if ( ! is_array( $map ) || 1 !== ( $map['format'] ?? null ) ||
				! defined( 'SEOPRESS_VERSION' ) || SEOPRESS_VERSION !== ( $map['version'] ?? null ) ||
				empty( $map['services'] ) || empty( $map['actions'] ) || ! is_array( $map['services'] ) || ! is_array( $map['actions'] ) ) {
				return null;
			}
			foreach ( array_merge( array_values( $map['services'] ), array_values( $map['actions'] ) ) as $class ) {
				if ( ! is_string( $class ) || '' === $class ) {
					return null;
				}
			}
			return $map;
		} catch ( \Throwable $error ) {
			// A stale or partially written release must retain directory discovery.
			return null;
		}
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
		} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Keep building the container even if one file cannot be scanned
			// or read (mid-update file swap, permissions): a single bad entry
			// must not abort registration of every other class.
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

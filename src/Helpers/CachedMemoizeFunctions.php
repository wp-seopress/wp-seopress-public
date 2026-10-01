<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CachedMemoizeFunctions
 */
abstract class CachedMemoizeFunctions {
	/**
	 * The cache.
	 *
	 * @var array
	 */
	protected static $cache = array();

	/**
	 * The memoize function.
	 *
	 * @param callable $func The function.
	 *
	 * @return callable
	 */
	public static function memoize( $func ) {
		// Each wrapper owns its cache; equal arguments do not identify a callable.
		$cache = array();
		return function () use ( $func, &$cache ) {
			$args = func_get_args();
			$key  = md5( serialize( $args ) ); // phpcs:ignore -- This is safe to use serialize.

			if ( ! array_key_exists( $key, $cache ) ) {
				$cache[ $key ] = call_user_func_array( $func, $args );
			}

			return $cache[ $key ];
		};
	}
}

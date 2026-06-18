<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persistent, lazily-stamped install date.
 *
 * SEOPress has no reliable native install date: `seopress_activated` is a
 * one-shot activation flag that is deleted on the first admin load, not a date.
 * This helper stamps its own timestamp the first time it is read and never
 * deletes it, so install-age checks work for both brand-new installs (stamped
 * at install) and long-running ones (stamped on their first read after the
 * release that ships this helper).
 *
 * @since 10.0.0
 */
class InstallDate {

	/**
	 * Option storing the persistent install timestamp.
	 *
	 * @var string
	 */
	const OPTION = 'seopress_install_date';

	/**
	 * Get the persistent install timestamp, stamping it on first read.
	 *
	 * @return int Unix timestamp.
	 */
	public static function get() {
		$timestamp = (int) get_option( self::OPTION );

		if ( ! $timestamp ) {
			$timestamp = time();
			update_option( self::OPTION, $timestamp, false );
		}

		return $timestamp;
	}

	/**
	 * Number of days elapsed since the install timestamp.
	 *
	 * @return float
	 */
	public static function daysSince() {
		return ( time() - self::get() ) / DAY_IN_SECONDS;
	}
}

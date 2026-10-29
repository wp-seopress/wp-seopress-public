<?php // phpcs:ignore

namespace SEOPress\Tags\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Archive Date
 */
class ArchiveDate implements GetTagValue {
	const NAME = 'archive_date';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Archive Date', 'wp-seopress' );
	}

	/**
	 * The date of the current date archive, shared with the legacy
	 * %%archive_date%% replacement so both readers give the same value.
	 *
	 * A year archive has no month and a day archive has a day, so the value
	 * follows the archive type. A month archive, and any other page, keep the
	 * "month - year" value this variable always gave.
	 *
	 * @since 10.4.0
	 *
	 * @return string
	 */
	public static function resolve() {
		if ( is_year() ) {
			return (string) get_query_var( 'year' );
		}

		if ( is_day() ) {
			return sprintf( '%s - %s - %s', get_query_var( 'day' ), get_query_var( 'monthnum' ), get_query_var( 'year' ) );
		}

		return sprintf( '%s - %s', get_query_var( 'monthnum' ), get_query_var( 'year' ) );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = self::resolve();

		return apply_filters( 'seopress_get_tag_archive_date_value', $value, $context );
	}
}

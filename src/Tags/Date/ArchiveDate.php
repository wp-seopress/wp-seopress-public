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
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = sprintf( '%s - %s', get_query_var( 'monthnum' ), get_query_var( 'year' ) );

		return apply_filters( 'seopress_get_tag_archive_date_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Archive Date Day
 */
class ArchiveDateDay implements GetTagValue {
	const NAME = 'archive_date_day';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Day Archive Date', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = get_query_var( 'day' );

		return apply_filters( 'seopress_get_tag_archive_date_day_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Archive Date Month Name
 */
class ArchiveDateMonthName implements GetTagValue {
	const NAME = 'archive_date_month_name';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Month Name Archive Date', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = get_query_var( 'monthnum' );

		if ( empty( $value ) ) {
			return '';
		}
		try {
			$date = DateTime::createFromFormat( '!m', $value );

			$value = esc_attr( wp_strip_all_tags( ( $date->format( 'F' ) ) ) );

			return apply_filters( 'seopress_get_tag_archive_date_month_name_value', $value, $context );
		} catch ( \Exception $e ) {
			return apply_filters( 'seopress_get_tag_archive_date_month_name_value', '', $context );
		}
	}
}

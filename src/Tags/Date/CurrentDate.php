<?php // phpcs:ignore

namespace SEOPress\Tags\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Current Date
 */
class CurrentDate implements GetTagValue {
	const NAME = 'currentdate';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Current Date', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		return date_i18n( get_option( 'date_format' ) );
	}
}

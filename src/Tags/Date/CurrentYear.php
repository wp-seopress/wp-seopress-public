<?php // phpcs:ignore

namespace SEOPress\Tags\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Current Year
 */
class CurrentYear implements GetTagValue {
	const NAME = 'currentyear';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Current Year', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		return gmdate( 'Y' );
	}
}

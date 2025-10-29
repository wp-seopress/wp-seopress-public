<?php // phpcs:ignore

namespace SEOPress\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GetTagValue
 */
interface GetTagValue {
	/**
	 * The getValue function.
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	public function getValue( $context = null ); // phpcs:ignore -- TODO: check if method is outside this class before renaming.
}

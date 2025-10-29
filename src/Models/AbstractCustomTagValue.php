<?php // phpcs:ignore

namespace SEOPress\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AbstractCustomTagValue
 */
abstract class AbstractCustomTagValue {
	/**
	 * The buildRegex function.
	 *
	 * @param string $format The format.
	 *
	 * @return string
	 */
	protected function buildRegex( $format ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return '/^' . $format . '(?<field>(?:.*))/';
	}
}

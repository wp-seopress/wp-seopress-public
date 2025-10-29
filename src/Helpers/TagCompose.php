<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TagCompose
 */
abstract class TagCompose {
	/**
	 * The start_tag constant.
	 *
	 * @var string
	 */
	const START_TAG = '%%';
	const END_TAG   = '%%';

	/**
	 * The getValueWithTag function.
	 *
	 * @param string $tag The tag.
	 *
	 * @return string
	 */
	public static function getValueWithTag( $tag ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf( '%s%s%s', self::START_TAG, $tag, self::END_TAG );
	}
}

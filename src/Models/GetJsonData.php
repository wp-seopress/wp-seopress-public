<?php // phpcs:ignore

namespace SEOPress\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GetJsonData
 */
interface GetJsonData {
	/**
	 * The getJsonData function.
	 *
	 * @param array $context The context.
	 *
	 * @return array
	 */
	public function getJsonData( $context = null ); // phpcs:ignore -- TODO: check if method is outside this class before renaming.
}

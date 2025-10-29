<?php // phpcs:ignore

namespace SEOPress\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GetJsonFromFile
 */
interface GetJsonFromFile {
	/**
	 * The getJson function.
	 *
	 * @return string
	 */
	public function getJson(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

	/**
	 * The getArrayJson function.
	 *
	 * @return array
	 */
	public function getArrayJson(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.
}

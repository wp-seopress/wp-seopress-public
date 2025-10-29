<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

/**
 * TableInterface
 */
interface TableInterface {


	/**
	 * The getName function.
	 *
	 * @return string
	 */
	public function getName(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.

	/**
	 * The getColumns function.
	 *
	 * @return array
	 */
	public function getColumns(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.
}

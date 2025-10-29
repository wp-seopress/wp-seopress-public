<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

/**
 * TableStructureInterface
 */
interface TableStructureInterface {


	/**
	 * The getColumns function.
	 *
	 * @return array
	 */
	public function getColumns(); // phpcs:ignore -- TODO: check if method is outside this class before renaming.
}

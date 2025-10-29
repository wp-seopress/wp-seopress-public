<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableStructureInterface;

/**
 * TableStructure
 */
class TableStructure implements TableStructureInterface {

	/**
	 * The columns property.
	 *
	 * @var array
	 */
	protected $columns;

	/**
	 * The __construct function.
	 *
	 * @param array $columns The columns.
	 */
	public function __construct( $columns ) {
		$this->columns = $columns;
	}


	/**
	 * The getColumns function.
	 *
	 * @return array
	 */
	public function getColumns() {
		return $this->columns;
	}
}

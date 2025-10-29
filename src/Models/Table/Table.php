<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableStructureInterface;

/**
 * Table
 */
class Table implements TableInterface {

	/**
	 * The name property.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The alias property.
	 *
	 * @var string
	 */
	protected $alias;

	/**
	 * The version property.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * The structure property.
	 *
	 * @var TableStructureInterface
	 */
	protected $structure;

	/**
	 * The __construct function.
	 *
	 * @param string                  $name The name.
	 * @param TableStructureInterface $structure The structure.
	 * @param array                   $options The options.
	 */
	public function __construct( $name, TableStructureInterface $structure, $options = array() ) {
		$this->name      = $name;
		$this->structure = $structure;
		$this->alias     = isset( $options['alias'] ) ? $options['alias'] : substr( $name, 9, 3 );
		$this->version   = isset( $options['version'] ) ? (int) $options['version'] : 1;
	}

	/**
	 * The getName function.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * The getAlias function.
	 *
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * The getColumns function.
	 *
	 * @return array
	 */
	public function getColumns() {
		return $this->structure->getColumns();
	}

	/**
	 * The getVersion function.
	 *
	 * @return int
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * The getColumnByName function.
	 *
	 * @param string $name The name.
	 *
	 * @return TableColumnInterface
	 */
	public function getColumnByName( $name ) {
		foreach ( $this->getColumns() as $key => $value ) {
			if ( $value->getName() === $name ) {
				return $value;
			}
		}
	}
}

<?php // phpcs:ignore

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableColumnInterface;

/**
 * TableColumn
 */
class TableColumn implements TableColumnInterface {

	/**
	 * The name property.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The type property.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The primary_key property.
	 *
	 * @var bool
	 */
	protected $primary_key;

	/**
	 * The index property.
	 *
	 * @var bool
	 */
	protected $index;

	/**
	 * The default_value property.
	 *
	 * @var string
	 */
	protected $default_value;

	/**
	 * The __construct function.
	 *
	 * @param string $name The name.
	 * @param array  $data The data.
	 */
	public function __construct( $name, $data = array() ) {

		$this->name        = $name;
		$this->type        = isset( $data['type'] ) ? $data['type'] : 'varchar';
		$this->primary_key = isset( $data['primaryKey'] ) ? (bool) $data['primaryKey'] : false;
		$this->index       = isset( $data['index'] ) ? $data['index'] : false;
	}

	/**
	 * The getType function.
	 *
	 * @return string
	 */
	public function getType() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( 'datetime' !== $this->type ) {
			return $this->type;
		}

		if ( 'CURRENT_TIMESTAMP' !== $this->default_value ) {
			return $this->type;
		}

		global $wpdb;
		$server = $wpdb->get_var( 'SELECT VERSION()' ); // phpcs:ignore

		// Compatibility DB version < 5.6.5 don't support CURRENT_TIMESTAMP.
		if ( version_compare( $server, '5.6.5', '<' ) ) {
			return 'timestamp';
		}

		return $this->type;
	}

	/**
	 * The getName function.
	 *
	 * @return string
	 */
	public function getName() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->name;
	}

	/**
	 * The getPrimaryKey function.
	 *
	 * @return bool
	 */
	public function getPrimaryKey() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->primary_key;
	}

	/**
	 * The getIndex function.
	 *
	 * @return bool
	 */
	public function getIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->index;
	}
}

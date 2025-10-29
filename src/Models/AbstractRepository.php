<?php // phpcs:ignore

namespace SEOPress\Models;

use SEOPress\Models\Table\Table;

/**
 * AbstractRepository
 */
abstract class AbstractRepository {

	/**
	 * The table property.
	 *
	 * @var Table
	 */
	protected $table;

	/**
	 * The getTableName function.
	 *
	 * @return string
	 */
	protected function getTableName() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		return "{$wpdb->prefix}{$this->table->getName()}";
	}

	/**
	 * The getInsertInstruction function.
	 *
	 * @param array $args The arguments.
	 *
	 * @return string
	 */
	protected function getInsertInstruction( array $args ): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		$authorized_values = $this->getAuthorizedInsertValues();
		$columns           = $this->table->getColumns();

		$data = array();
		foreach ( $columns as $column ) {
			$name = $column->getName();

			if ( ! in_array( $name, $authorized_values, true ) ) {
				continue;
			}

			if ( ! isset( $args[ $name ] ) ) {
				continue;
			}

			$data[] = $name;
		}

		return "
			INSERT INTO {$this->getTableName()}
			(
				" . implode( ', ', $data ) . '
			) VALUES
		';
	}

	/**
	 * The getUpdateInstruction function.
	 *
	 * @return string
	 */
	protected function getUpdateInstruction(): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		return "
			UPDATE {$this->getTableName()}
		";
	}

	/**
	 * The getFormatValue function.
	 *
	 * @param mixed $value The value.
	 *
	 * @return string
	 */
	protected function getFormatValue( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( is_string( $value ) ) {
			return "'" . wp_slash( maybe_serialize( $value ) ) . "'";
		} elseif ( is_int( $value ) ) {
			return maybe_serialize( $value );
		} elseif ( $value instanceof \DateTime ) {
			return "'" . wp_slash( maybe_serialize( $value->format( 'Y-m-d H:i:s' ) ) ) . "'";
		} elseif ( is_array( $value ) ) {
			if ( empty( $value ) ) {
				return 'NULL';
			} else {
				return "'" . wp_slash( maybe_serialize( $value ) ) . "'";
			}
		}

		return 'NULL';
	}

	/**
	 * The getUpdateValues function.
	 *
	 * @param array $args The arguments.
	 *
	 * @return string
	 */
	public function getUpdateValues( array $args ): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		$authorized_values = $this->getAuthorizedUpdateValues();

		foreach ( $args as $key => $value ) {
			if ( ! in_array( $key, $authorized_values, true ) ) {
				unset( $args[ $key ] );
			}
		}

		return '
			SET ' . $this->constructSetClause( $args ) . '
		';
	}

	/**
	 * The constructValuesClause function.
	 *
	 * @param array $args The arguments.
	 *
	 * @return string
	 */
	public function constructValuesClause( array $args ): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$values = '(';

		$authorized_values = $this->getAuthorizedInsertValues();

		foreach ( $args as $key => $value ) {
			if ( ! in_array( $key, $authorized_values, true ) ) {
				unset( $args[ $key ] );
			}

			$values .= $this->getFormatValue( $value );
			$values .= ',';
		}

		$values = rtrim( $values, ',' ) . ')';

		return $values;
	}

	/**
	 * The constructSetClause function.
	 *
	 * @param array $data The data.
	 *
	 * @return string
	 */
	protected function constructSetClause( array $data ): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$set = '';

		foreach ( $data as $key => $value ) {
			$value = $this->getFormatValue( $value );

			$set .= "{$key}=$value";
			$set .= ',';
		}

		$set = rtrim( $set, ',' );

		return $set;
	}


	/**
	 * Get VALUES for INSERT INTO
	 *
	 * @param array $args The arguments.
	 *
	 * @return string
	 */
	protected function getInsertValuesInstruction( $args ): string { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$authorized_values = $this->getAuthorizedInsertValues();

		$columns = $this->table->getColumns();

		$data = array();
		foreach ( $columns as $column ) {
			$name = $column->getName();

			if ( ! in_array( $name, $authorized_values, true ) ) {
				continue;
			}
			if ( ! isset( $args[ $name ] ) ) {
				continue;
			}

			switch ( $name ) {
				case 'post_id':
					$data[] = (int) $args[ $name ];
					break;
				default:
					$data[] = $args[ $name ];
					break;
			}
		}

		return $this->constructValuesClause( $data );
	}
}

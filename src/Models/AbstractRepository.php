<?php
namespace SEOPress\Models;

use SEOPress\Models\Table\Table;

abstract class AbstractRepository
{
	/**
	 * @var Table
	 */

	protected $table;

	protected function getTableName(){
		global $wpdb;

		return "{$wpdb->prefix}{$this->table->getName()}";
	}

	protected function getInsertInstruction(array $args): string
	{
		global $wpdb;

		$authorizedValues = $this->getAuthorizedInsertValues();
		$columns = $this->table->getColumns();

		$data = [];
		foreach($columns as $column){
			$name = $column->getName();

			if(!in_array($name, $authorizedValues)){
				continue;
			}

			if(!isset($args[$name])){
				continue;
			}

			$data[] = $name;
		}

		return "
			INSERT INTO {$this->getTableName()}
			(
				" . implode(', ', $data) . "
			) VALUES
		";
	}

	protected function getUpdateInstruction(): string
	{
		global $wpdb;

		return "
			UPDATE {$this->getTableName()}
		";
	}

	protected function getFormatValue($value) {
		if (is_string($value)) {
			return "'" . wp_slash(maybe_serialize($value)) . "'";
		} elseif (is_int($value)) {
			return maybe_serialize($value);
		} elseif ($value instanceof \DateTime) {
			return "'" . wp_slash(maybe_serialize($value->format('Y-m-d H:i:s'))) . "'";
		} elseif (is_array($value)) {
			if (empty($value)) {
				return "NULL";
			} else {
				return "'" . wp_slash(maybe_serialize($value)) . "'";
			}
		}

		return "NULL";
	}

	public function getUpdateValues(array $args): string
	{
		global $wpdb;

		$authorizedValues = $this->getAuthorizedUpdateValues();

		foreach($args as $key => $value){
			if(!in_array($key, $authorizedValues)){
				unset($args[$key]);
			}
		}

		return "
			SET " . $this->constructSetClause($args) . "
		";
	}

	public function constructValuesClause(array $args): string {
		$values = "(";

		$authorizedValues = $this->getAuthorizedInsertValues();

		foreach ($args as $key => $value) {
			if(!in_array($key, $authorizedValues)){
				unset($args[$key]);
			}

			$values .= $this->getFormatValue($value);
			$values .= ",";
		}

		$values = rtrim($values, ",") . ")";

		return $values;
	}

	protected function constructSetClause(array $data): string {
		$set = "";

		foreach ($data as $key => $value) {
			$value = $this->getFormatValue($value);

			$set .= "{$key}=$value";
			$set .= ",";
		}

		$set = rtrim($set, ",");

		return $set;
	}


	/**
	 * Get VALUES for INSERT INTO
	 *
	 * @param array $args
	 * @return string
	 */
	protected function getInsertValuesInstruction($args): string
	{

		$authorizedValues = $this->getAuthorizedInsertValues();

		$columns = $this->table->getColumns();

		$data = [];
		foreach($columns as $column){
			$name = $column->getName();

			if(!in_array($name, $authorizedValues, true)){
				continue;
			}
			if(!isset($args[$name])){
				continue;
			}

			switch($name){
				case 'post_id':
					$data[] = (int) $args[$name];
					break;
				default:
					$data[] = $args[$name];
					break;

			}
		}

		return $this->constructValuesClause($data);
	}
}

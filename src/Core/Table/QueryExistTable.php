<?php // phpcs:ignore

namespace SEOPress\Core\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableInterface;

/**
 * QueryExistTable
 */
class QueryExistTable {

	/**
	 * The exist function.
	 *
	 * @param TableInterface $table The table.
	 *
	 * @return bool
	 */
	public function exist( TableInterface $table ) {

		global $wpdb;

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->prefix . $table->getName() ) );
		try {
			$result = $wpdb->query( $query );

			return false !== $result && 0 < $result;
		} catch ( \Exception $e ) {
			return false;
		}
	}
}

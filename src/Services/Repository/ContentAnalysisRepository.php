<?php // phpcs:ignore

namespace SEOPress\Services\Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use SEOPress\Models\AbstractRepository;

/**
 * ContentAnalysisRepository
 */
class ContentAnalysisRepository extends AbstractRepository {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$tables = seopress_get_service( 'TableList' )->getTables();
		seopress_get_service( 'TableManager' )->createTablesIfNeeded( $tables );
		$this->table = seopress_get_service( 'TableList' )->getTableContentAnalysis();
	}

	/**
	 * The getAuthorizedInsertValues function.
	 *
	 * @return array
	 */
	protected function getAuthorizedInsertValues(): array {
		return array(
			'post_id',
			'title',
			'description',
			'og_title',
			'og_description',
			'og_image',
			'og_url',
			'og_site_name',
			'twitter_title',
			'twitter_description',
			'twitter_image',
			'twitter_image_src',
			'canonical',
			'h1',
			'h2',
			'h3',
			'images',
			'meta_robots',
			'meta_google',
			'outbound_links',
			'internal_links',
			'json_schemas',
			'links_no_follow',
			'keywords',
			'data',
			'score',
			'permalink',
			'analysis_date',
		);
	}

	/**
	 * The getAuthorizedUpdateValues function.
	 *
	 * @return array
	 */
	protected function getAuthorizedUpdateValues(): array {
		return array(
			'title',
			'description',
			'og_title',
			'og_description',
			'og_image',
			'og_url',
			'og_site_name',
			'twitter_title',
			'twitter_description',
			'twitter_image',
			'twitter_image_src',
			'canonical',
			'h1',
			'h2',
			'h3',
			'images',
			'meta_robots',
			'meta_google',
			'outbound_links',
			'internal_links',
			'json_schemas',
			'links_no_follow',
			'keywords',
			'data',
			'score',
			'permalink',
			'analysis_date',
		);
	}

	/**
	 * The analysisAlreadyExistForPostId function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return bool
	 */
	public function analysisAlreadyExistForPostId( $post_id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		$post_id = absint( $post_id );

		$table_name = esc_sql( $this->getTableName() );

		$sql = $wpdb->prepare( "SELECT id FROM {$table_name} WHERE post_id = %d", $post_id ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name escaped with esc_sql() above, it cannot be bound.

		$result = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $sql is the prepared statement built on the line above.

		return ! empty( $result );
	}

	/**
	 * The insertContentAnalysis function.
	 *
	 * @param array $data The data.
	 */
	public function insertContentAnalysis( $data ) {

		global $wpdb;

		$columns = $this->prepareColumns( (array) $data, $this->getAuthorizedInsertValues(), array( 'post_id' ) );

		if ( empty( $columns ) ) {
			return null;
		}

		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- custom table, values are bound by $wpdb->insert().
			return $wpdb->insert( $this->getTableName(), $columns );
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * The updateContentAnalysis function.
	 *
	 * @param int   $post_id The post id.
	 * @param array $data The data.
	 */
	public function updateContentAnalysis( $post_id, $data ) {
		global $wpdb;

		$post_id = absint( $post_id );

		$columns = $this->prepareColumns( (array) $data, $this->getAuthorizedUpdateValues() );

		if ( empty( $columns ) ) {
			return null;
		}

		try {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- custom table, values and WHERE are bound by $wpdb->update().
			return $wpdb->update( $this->getTableName(), $columns, array( 'post_id' => $post_id ) );
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Read the latest analysis score of several posts in one query.
	 *
	 * getContentAnalysis() answers for a single post, which would mean one
	 * query per row for a listing. Site-wide abilities page through up to a
	 * hundred posts at a time, so they read the whole page at once here.
	 *
	 * @since 10.3.0
	 *
	 * @param array $post_ids The post IDs to read.
	 *
	 * @return array Post ID => the stored score value (usually an array of impacts).
	 */
	public function getScoresForPostIds( $post_ids ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		$post_ids = array_values( array_unique( array_filter( array_map( 'intval', (array) $post_ids ) ) ) );

		if ( empty( $post_ids ) ) {
			return array();
		}

		$placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );

		$sql = $wpdb->prepare(
			"SELECT post_id, score, analysis_date
			 FROM {$this->getTableName()}
			 WHERE post_id IN ( {$placeholders} )
			 ORDER BY analysis_date ASC",
			$post_ids
		);

		$rows = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $sql is the prepared statement built above; only %d placeholders are interpolated.

		if ( empty( $rows ) ) {
			return array();
		}

		$scores = array();

		// Ascending order means the newest analysis of a post overwrites the
		// older ones, which mirrors the "ORDER BY analysis_date DESC LIMIT 1"
		// of the single-post read.
		foreach ( $rows as $row ) {
			$scores[ (int) $row['post_id'] ] = maybe_unserialize( $row['score'] );
		}

		return $scores;
	}

	/**
	 * The getContentAnalysis function.
	 *
	 * @param int   $post_id The post id.
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function getContentAnalysis( $post_id, $columns = array( '*' ) ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		global $wpdb;

		// A column name cannot be a bound parameter, so the caller's list is
		// matched against the columns this repository knows about. Every caller
		// passes a hardcoded list today; this keeps that true for the next one.
		$allowed = array_merge( array( 'id', 'post_id' ), $this->getAuthorizedInsertValues() );
		$columns = array_values( array_intersect( (array) $columns, $allowed ) );

		if ( empty( $columns ) ) {
			$columns = array( '*' );
		}

		$str_columns = implode( ', ', $columns );
		$sql         = $wpdb->prepare(
			"SELECT {$str_columns}
             FROM {$this->getTableName()}
             WHERE post_id = %d
             ORDER BY analysis_date DESC
             LIMIT 1",
			$post_id
		);

		$result = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- $sql is the prepared statement built above, columns are matched against an allow-list.

		if ( empty( $result ) ) {
			return null;
		}

		return array_map( 'maybe_unserialize', $result[0] );
	}
}

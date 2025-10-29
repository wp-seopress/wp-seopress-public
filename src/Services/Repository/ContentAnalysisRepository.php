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

		$sql = $wpdb->prepare( "SELECT id FROM {$table_name} WHERE post_id = %d", $post_id );

		$result = $wpdb->get_results( $sql );

		return ! empty( $result );
	}

	/**
	 * The insertContentAnalysis function.
	 *
	 * @param array $data The data.
	 */
	public function insertContentAnalysis( $data ) {

		global $wpdb;
		$sql  = $this->getInsertInstruction( $data );
		$sql .= $this->getInsertValuesInstruction( $data );

		try {
			return $wpdb->query( $sql );
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

		$sql  = $this->getUpdateInstruction( $data );
		$sql .= $this->getUpdateValues( $data );
		$sql .= " WHERE post_id = {$post_id}";

		try {
			return $wpdb->query( $sql );
		} catch ( \Exception $e ) {
			return null;
		}
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
		$str_columns = implode( ', ', $columns );
		$sql         = $wpdb->prepare(
			"SELECT {$str_columns}
             FROM {$this->getTableName()}
             WHERE post_id = %d
             ORDER BY analysis_date DESC
             LIMIT 1",
			$post_id
		);

		$result = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $result ) ) {
			return null;
		}

		return array_map( 'maybe_unserialize', $result[0] );
	}
}

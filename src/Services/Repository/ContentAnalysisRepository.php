<?php

namespace SEOPress\Services\Repository;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\AbstractRepository;

class ContentAnalysisRepository extends AbstractRepository {

    public function __construct(){
        $tables = seopress_get_service('TableList')->getTables();
        seopress_get_service('TableManager')->createTablesIfNeeded($tables);
        $this->table = seopress_get_service('TableList')->getTableContentAnalysis();
    }

	protected function getAuthorizedInsertValues(): array
	{
		return [
			"post_id",
            "title",
            "description",
            "og_title",
            "og_description",
            "og_image",
            "og_url",
            "og_site_name",
            "twitter_title",
            "twitter_description",
            "twitter_image",
            "twitter_image_src",
            "canonical",
            "h1",
            "h2",
            "h3",
            "images",
            "meta_robots",
            "meta_google",
            "outbound_links",
            "internal_links",
            "json_schemas",
            "links_no_follow",
            "keywords",
            "data",
            "score",
            "permalink",
            "analysis_date"
		];
	}

	protected function getAuthorizedUpdateValues(): array
	{
		return [
            "title",
            "description",
            "og_title",
            "og_description",
            "og_image",
            "og_url",
            "og_site_name",
            "twitter_title",
            "twitter_description",
            "twitter_image",
            "twitter_image_src",
            "canonical",
            "h1",
            "h2",
            "h3",
            "images",
            "meta_robots",
            "meta_google",
            "outbound_links",
            "internal_links",
            "json_schemas",
            "links_no_follow",
            "keywords",
            "data",
            "score",
            "permalink",
            "analysis_date"
		];
	}

    public function analysisAlreadyExistForPostId($postId){
        global $wpdb;

        $postId = absint($postId);

        $tableName = esc_sql($this->getTableName());

        $sql = $wpdb->prepare("SELECT id FROM {$tableName} WHERE post_id = %d", $postId);

        $result = $wpdb->get_results($sql);

        return !empty($result);
    }

    /**
     * @param array $data
     */
    public function insertContentAnalysis($data){

        global $wpdb;
		$sql = $this->getInsertInstruction($data);
		$sql .= $this->getInsertValuesInstruction($data);

        try {
            return $wpdb->query($sql);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateContentAnalysis($postId, $data){
        global $wpdb;

        $postId = absint($postId);

        $sql = $this->getUpdateInstruction($data);
		$sql .= $this->getUpdateValues($data);
        $sql .= " WHERE post_id = {$postId}";

        try {
            return $wpdb->query($sql);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getContentAnalysis($postId, $columns = ["*"]){

        global $wpdb;
        $strColumns = implode(', ', $columns);
        $sql = $wpdb->prepare(
            "SELECT {$strColumns}
             FROM {$this->getTableName()}
             WHERE post_id = %d
             ORDER BY analysis_date DESC
             LIMIT 1",
            $postId
        );
        
        $result = $wpdb->get_results($sql, ARRAY_A);

        if(empty($result)){
            return null;
        }

        return array_map("maybe_unserialize", $result[0]);
    }

}

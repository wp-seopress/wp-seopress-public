<?php

namespace SEOPress\Services\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableInterface;
use SEOPress\Core\Table\TableFactory;
use SEOPress\Models\Table\TableStructure;
use SEOPress\Models\Table\TableColumn;
use SEOPress\Models\Table\Table;


class TableList {

    public function getTableContentAnalysis(){
        $tableStructure = new TableStructure([
            new TableColumn('id', [
                'type' => 'bigint(20)',
                'primaryKey' => true
            ]),
            new TableColumn('post_id', [
                'type' => 'bigint(20)',
                'index' => true,
            ]),
            new TableColumn('title', [
                'type' => 'longtext',
            ]),
            new TableColumn('description', [
                'type' => 'longtext',
            ]),
            new TableColumn('og_title', [
                'type' => 'longtext',
            ]),
            new TableColumn('og_description', [
                'type' => 'longtext',
            ]),
            new TableColumn('og_image', [
                'type' => 'longtext',
            ]),
            new TableColumn('og_url', [
                'type' => 'longtext',
            ]),
            new TableColumn('og_site_name', [
                'type' => 'longtext',
            ]),
            new TableColumn('twitter_title', [
                'type' => 'longtext',
            ]),
            new TableColumn('twitter_description', [
                'type' => 'longtext',
            ]),
            new TableColumn('twitter_image', [
                'type' => 'longtext',
            ]),
            new TableColumn('twitter_image_src', [
                'type' => 'longtext',
            ]),
            new TableColumn('canonical', [
                'type' => 'longtext',
            ]),
            new TableColumn('h1', [
                'type' => 'longtext',
            ]),
            new TableColumn('h2', [
                'type' => 'longtext',
            ]),
            new TableColumn('h3', [
                'type' => 'longtext',
            ]),
            new TableColumn('images', [
                'type' => 'longtext',
            ]),
            new TableColumn('meta_robots', [
                'type' => 'longtext',
            ]),
            new TableColumn('meta_google', [
                'type' => 'longtext',
            ]),
            new TableColumn('links_no_follow', [
                'type' => 'longtext',
            ]),
            new TableColumn('outbound_links', [
                'type' => 'longtext',
            ]),
            new TableColumn('internal_links', [
                'type' => 'longtext',
            ]),
            new TableColumn('json_schemas', [
                'type' => 'longtext',
            ]),
            new TableColumn('keywords', [
                'type' => 'text',
            ]),
            new TableColumn('permalink', [
                'type' => 'text',
            ]),
            new TableColumn('score', [
                'type' => 'text',
            ]),
            new TableColumn('analysis_date', [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP'
            ]),

        ]);

        return new Table("seopress_content_analysis", $tableStructure, 1);
    }

    public function getTables(){
        return [
            "seopress_content_analysis" => $this->getTableContentAnalysis(),
        ];
    }
}

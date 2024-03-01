<?php

namespace SEOPress\Services\Table;

defined( 'ABSPATH' ) || exit;

use SEOPress\Models\Table\TableInterface;
use SEOPress\Core\Table\QueryCreateTable;
use SEOPress\Core\Table\QueryExistTable;

class TableManager {

    protected $queryCreateTable;

    protected $queryExistTable;

    public function __construct(){
        $this->queryCreateTable = new QueryCreateTable();
        $this->queryExistTable = new QueryExistTable();
    }

    public function exist(TableInterface $table){
        return $this->queryExistTable->exist($table);
    }

    public function create(TableInterface $table){
        if($this->exist($table)){
            return;
        }

        $this->queryCreateTable->create($table);
    }

    public function createTablesIfNeeded($tables){
        foreach ($tables as $key => $table) {
            $this->create($table);
        }
    }

}

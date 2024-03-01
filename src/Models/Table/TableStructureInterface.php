<?php

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;


interface TableStructureInterface {


    /**
     * @return array
     */
	public function getColumns();

}

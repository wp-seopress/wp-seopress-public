<?php

namespace SEOPress\Models\Table;

defined( 'ABSPATH' ) || exit;


interface TableColumnInterface {


    /**
	 * @return int
	 */
	public function getType();

	/**
	 * @return string
	 */
	public function getName();

    /**
     * @return bool
     */
	public function getPrimaryKey();

}

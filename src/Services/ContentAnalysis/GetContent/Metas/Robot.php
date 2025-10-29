<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Robot
 */
class Robot {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$items = $xpath->query( '//meta[@name="robots"]/@content' );

		$data = array();
		foreach ( $items as $key => $item ) {
			$data[] = $item->nodeValue; // phpcs:ignore -- TODO: check if property is outside this class before renaming.
		}

		return $data;
	}
}

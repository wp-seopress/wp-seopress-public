<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hn
 */
class Hn {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array();
		if ( ! isset( $options['hn'] ) ) {
			return $data;
		}

		$items = $xpath->query( sprintf( '//%s', $options['hn'] ) );

		foreach ( $items as $key => $item ) {
			$data[] = $item->nodeValue; // phpcs:ignore -- TODO: check if property is outside this class before renaming.
		}

		return $data;
	}
}

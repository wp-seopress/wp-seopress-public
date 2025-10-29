<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent\OG;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sitename
 */
class Sitename {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$values = $xpath->query( '//meta[@property="og:site_name"]/@content' );

		$data = array();
		if ( empty( $values ) ) {
			return $data;
		}
		foreach ( $values as $key => $item ) {
			$data[] = $item->nodeValue; // phpcs:ignore -- TODO: check if property is outside this class before renaming.
		}

		return $data;
	}
}

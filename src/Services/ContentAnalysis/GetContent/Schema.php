<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schema
 */
class Schema {
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

		$items = $xpath->query( '//script[@type="application/ld+json"]' );
		foreach ( $items as $key => $node ) {
			$json = json_decode( $node->nodeValue, true ); // phpcs:ignore -- TODO: check if property is outside this class before renaming.
			if ( isset( $json['@type'] ) ) {
				$data[] = $json['@type'];
			}
		}

		return $data;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 */
class Description {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$metas = $xpath->query( '//meta[@name="description"]/@content' );

		$value = '';
		foreach ( $metas as $key => $item ) {
			$value = $item->nodeValue; // phpcs:ignore -- TODO: check if property is outside this class before renaming.
		}

		return $value;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * OutboundLinks
 */
class OutboundLinks {
	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data     = array();
		$site_url = wp_parse_url( get_home_url(), PHP_URL_HOST );
		$items    = $xpath->query( "//a[not(contains(@href, '" . $site_url . "'))]" );
		foreach ( $items as $key => $link ) {
			if ( ! empty( wp_parse_url( $link->getAttribute( 'href' ), PHP_URL_HOST ) ) ) {
				$data[] = array(
					'value' => $link->nodeValue, // phpcs:ignore -- TODO: check if property is outside this class before renaming.
					'url'   => $link->getAttribute( 'href' ),
				);
			}
		}

		return $data;
	}
}

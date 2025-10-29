<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Title
 */
class Title {
	/**
	 * The getDataByDom function.
	 *
	 * @param object $dom The dom.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByDom( $dom, $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$list = $dom->getElementsByTagName( 'title' );

		if ( 0 === $list->length ) {
			return '';
		}

		return $list->item( 0 )->textContent; // phpcs:ignore -- TODO: check if property is outside this class before renaming.
	}
}

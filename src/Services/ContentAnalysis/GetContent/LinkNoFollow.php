<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LinkNoFollow
 */
class LinkNoFollow {
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

		$items = $xpath->query( "//a[contains(@rel, 'nofollow') and not(contains(@rel, 'ugc'))]" );

		foreach ( $items as $link ) {
			if ( ! preg_match_all( '#\b(cancel-comment-reply-link)\b#iu', $link->getAttribute( 'id' ), $m ) && ! preg_match_all( '#\b(comment-reply-link)\b#iu', $link->getAttribute( 'class' ), $m ) ) {
				$data[] = array(
					'value' => $link->nodeValue, // phpcs:ignore -- TODO: check if property is outside this class before renaming.
					'url'   => $link->getAttribute( 'href' ),
				);
			}
		}

		return $data;
	}
}

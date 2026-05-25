<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ContentStructure
 *
 * Extracts the heading outline (ordered list of heading levels) and the
 * number of embedded videos from the rendered source code. Used by the
 * content quality / structure checks aligned with Google's AI optimization
 * guidelines (https://developers.google.com/search/docs/fundamentals/ai-optimization-guide).
 */
class ContentStructure {

	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) {
		$data = array(
			'outline' => array(),
			'videos'  => 0,
		);

		// Restrict the scan to the main content area when the theme exposes
		// one, so headings from the header/footer/sidebar don't pollute the
		// hierarchy analysis. Fall back to the whole document otherwise.
		$scopes = array( '//main', '//article', '//*[@role="main"]' );
		$prefix = '';
		foreach ( $scopes as $scope ) {
			$nodes = $xpath->query( $scope );
			if ( $nodes && $nodes->length > 0 ) {
				$prefix = $scope;
				break;
			}
		}

		$heading_query = '';
		for ( $level = 1; $level <= 6; $level++ ) {
			$heading_query .= ( '' === $heading_query ? '' : '|' ) . $prefix . '//h' . $level;
		}

		$headings = $xpath->query( $heading_query );
		if ( $headings ) {
			foreach ( $headings as $heading ) {
				if ( '' === trim( (string) $heading->nodeValue ) ) { // phpcs:ignore -- DOM property.
					continue;
				}

				$data['outline'][] = (int) substr( $heading->nodeName, 1 ); // phpcs:ignore -- DOM property.
			}
		}

		$video_query = $prefix . '//video|' . $prefix . "//iframe[contains(@src,'youtube') or contains(@src,'youtu.be') or contains(@src,'vimeo') or contains(@src,'dailymotion') or contains(@src,'wistia') or contains(@src,'loom')]";
		$videos      = $xpath->query( $video_query );
		if ( $videos ) {
			$data['videos'] = $videos->length;
		}

		return $data;
	}
}

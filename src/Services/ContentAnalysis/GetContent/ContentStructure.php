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
		// hierarchy analysis.
		//
		// We only adopt a scope that actually contains subheadings (h2-h6).
		// Some themes and page builders expose a <main>/<article>/[role=main]
		// wrapper that does not hold the article body, which previously made
		// the scan report zero headings even though the page was full of them.
		// When no scoped container holds subheadings, fall back to the whole
		// document so the outline is never falsely empty.
		$scopes = array( '//main', '//article', '//*[@role="main"]' );
		$prefix = '';
		foreach ( $scopes as $scope ) {
			$probe = $xpath->query( $scope . '//h2|' . $scope . '//h3|' . $scope . '//h4|' . $scope . '//h5|' . $scope . '//h6' );
			if ( $probe && $probe->length > 0 ) {
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

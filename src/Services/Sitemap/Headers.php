<?php // phpcs:ignore

namespace SEOPress\Services\Sitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Headers
 */
class Headers {
	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'SitemapHeaders';

	/**
	 * The printHeaders function.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function printHeaders() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$headers = array(
			'Content-type' => 'text/xml',
			'x-robots-tag' => 'noindex, follow',
		);
		$headers = apply_filters( 'seopress_sitemaps_headers', $headers );
		if ( empty( $headers ) ) {
			return;
		}

		foreach ( $headers as $key => $header ) {
			header( $key . ':' . $header );
		}
	}
}

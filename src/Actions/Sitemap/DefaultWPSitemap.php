<?php // phpcs:ignore

namespace SEOPress\Actions\Sitemap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Default WPSitemap
 */
class DefaultWPSitemap implements ExecuteHooks {
	/**
	 * The Default WPSitemap hooks.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		/*
		 * Remove default WP XML sitemaps.
		 */
		if ( '1' == seopress_get_toggle_option( 'xml-sitemap' ) ) { // phpcs:ignore -- TODO: null comparison check.
				remove_action( 'init', 'wp_sitemaps_get_server' );
		}
	}
}

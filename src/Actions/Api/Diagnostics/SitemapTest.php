<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Diagnostics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoint backing the in-admin "Test your XML sitemap" tool.
 *
 * POST /seopress/v1/diagnostics/sitemap-test — runs the sitemap diagnostic
 * against the site's own sitemap and returns a structured report.
 *
 * The endpoint only ever tests the local site (the URL is derived server-side
 * from home_url()), so it does not accept an arbitrary URL and carries no SSRF
 * surface.
 *
 * @since 10.0.0
 */
class SitemapTest implements ExecuteHooks {

	const RATE_LIMIT_WINDOW_SEC = 5;
	const RATE_LIMIT_TRANSIENT  = 'seopress_sitemap_test_rl_';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check: users allowed to manage the sitemap settings.
	 *
	 * @return boolean
	 */
	public function permissionCheck() {
		return current_user_can( seopress_capability( 'manage_options', 'xml_html_sitemap' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/diagnostics/sitemap-test',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * Run the diagnostic.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function process() {
		$allowed = $this->checkRateLimit();
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$diagnostic = seopress_get_service( 'SitemapDiagnostic' );

		if ( ! $diagnostic ) {
			return new \WP_Error(
				'unavailable',
				__( 'The sitemap diagnostic is unavailable.', 'wp-seopress' ),
				array( 'status' => 500 )
			);
		}

		return new \WP_REST_Response( $diagnostic->run(), 200 );
	}

	/**
	 * Per-user rate limit so the loopback requests cannot be spammed.
	 *
	 * @return true|\WP_Error
	 */
	private function checkRateLimit() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return true;
		}

		$key = self::RATE_LIMIT_TRANSIENT . $user_id;
		if ( false !== get_transient( $key ) ) {
			return new \WP_Error(
				'rate_limited',
				__( 'Please wait a few seconds before testing again.', 'wp-seopress' ),
				array( 'status' => 429 )
			);
		}

		set_transient( $key, 1, self::RATE_LIMIT_WINDOW_SEC );
		return true;
	}
}

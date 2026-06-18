<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Diagnostics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoint applying one-click fixes surfaced by the sitemap diagnostic.
 *
 * POST /seopress/v1/diagnostics/sitemap-fix — runs a single allow-listed
 * remediation and returns a confirmation message. The frontend re-runs the
 * diagnostic afterwards to reflect the new state.
 *
 * Only a fixed set of safe, intentional actions is exposed; the `fix`
 * parameter is validated against that allow-list.
 *
 * @since 10.0.0
 */
class SitemapFix implements ExecuteHooks {

	const RATE_LIMIT_WINDOW_SEC = 3;
	const RATE_LIMIT_TRANSIENT  = 'seopress_sitemap_fix_rl_';

	/**
	 * Allow-listed fix ids.
	 *
	 * @var string[]
	 */
	const ALLOWED_FIXES = array(
		'flush_permalinks',
	);

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
			'/diagnostics/sitemap-fix',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'fix' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
						'validate_callback' => function ( $param ) {
							return in_array( $param, self::ALLOWED_FIXES, true );
						},
					),
				),
			)
		);
	}

	/**
	 * Apply the requested fix.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function process( \WP_REST_Request $request ) {
		$allowed = $this->checkRateLimit();
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$fix = $request->get_param( 'fix' );

		switch ( $fix ) {
			case 'flush_permalinks':
				flush_rewrite_rules( false );
				$message = __( 'Permalinks flushed. Re-testing…', 'wp-seopress' );
				break;

			default:
				return new \WP_Error(
					'unknown_fix',
					__( 'Unknown fix.', 'wp-seopress' ),
					array( 'status' => 400 )
				);
		}

		return new \WP_REST_Response(
			array(
				'code'    => 'fixed',
				'fix'     => $fix,
				'message' => $message,
			),
			200
		);
	}

	/**
	 * Per-user rate limit.
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
				__( 'Please wait a moment before trying again.', 'wp-seopress' ),
				array( 'status' => 429 )
			);
		}

		set_transient( $key, 1, self::RATE_LIMIT_WINDOW_SEC );
		return true;
	}
}

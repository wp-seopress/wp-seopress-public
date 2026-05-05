<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoints backing the admin command palette.
 *
 * - GET/POST /seopress/v1/commands/recent — per-user most-recent command names
 *   (stored in user meta, capped at 8).
 * - POST /seopress/v1/commands/flush-rewrite-rules — one-click rewrite flush.
 * - POST /seopress/v1/commands/clear-cache — delete SEOPress transients.
 *
 * @since 9.8.0
 */
class Commands implements ExecuteHooks {

	const USER_META_RECENT      = 'seopress_command_palette_recent';
	const MAX_RECENT            = 8;
	const RATE_LIMIT_WINDOW_SEC = 5;
	const RATE_LIMIT_TRANSIENT  = 'seopress_cmd_rl_';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check: any user that can manage SEO options.
	 *
	 * @return boolean
	 */
	public function permissionCheck() {
		return current_user_can( seopress_capability( 'manage_options', 'dashboard' ) );
	}

	/**
	 * Per-user rate limit for destructive endpoints.
	 *
	 * Uses a short-lived transient keyed by user id + action so a single
	 * authenticated user cannot spam the endpoint faster than once per
	 * RATE_LIMIT_WINDOW_SEC seconds.
	 *
	 * @param string $action Unique action id (e.g. "clear-cache").
	 * @return true|\WP_Error True when the call is allowed, WP_Error otherwise.
	 */
	private function check_rate_limit( $action ) {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return true;
		}
		$key = self::RATE_LIMIT_TRANSIENT . $user_id . '_' . $action;
		if ( false !== get_transient( $key ) ) {
			return new \WP_Error(
				'rate_limited',
				__( 'Please wait a few seconds before trying again.', 'wp-seopress' ),
				array( 'status' => 429 )
			);
		}
		set_transient( $key, 1, self::RATE_LIMIT_WINDOW_SEC );
		return true;
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/commands/recent',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'processGetRecent' ),
					'permission_callback' => array( $this, 'permissionCheck' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'processSaveRecent' ),
					'permission_callback' => array( $this, 'permissionCheck' ),
					'args'                => array(
						'name' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && '' !== trim( $param );
							},
						),
					),
				),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/commands/flush-rewrite-rules',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processFlushRewrite' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);

		register_rest_route(
			'seopress/v1',
			'/commands/clear-cache',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'processClearCache' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * GET /seopress/v1/commands/recent — return most recent command names.
	 *
	 * @return \WP_REST_Response
	 */
	public function processGetRecent() {
		$user_id = get_current_user_id();
		$recent  = get_user_meta( $user_id, self::USER_META_RECENT, true );

		if ( ! is_array( $recent ) ) {
			$recent = array();
		}

		return new \WP_REST_Response( array_values( $recent ) );
	}

	/**
	 * POST /seopress/v1/commands/recent — push a command name to the head of the list.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processSaveRecent( \WP_REST_Request $request ) {
		$name    = trim( (string) $request->get_param( 'name' ) );
		$user_id = get_current_user_id();
		$recent  = get_user_meta( $user_id, self::USER_META_RECENT, true );

		if ( ! is_array( $recent ) ) {
			$recent = array();
		}

		// Remove if present, then unshift, then cap.
		$recent = array_values( array_diff( $recent, array( $name ) ) );
		array_unshift( $recent, $name );
		$recent = array_slice( $recent, 0, self::MAX_RECENT );

		update_user_meta( $user_id, self::USER_META_RECENT, $recent );

		return new \WP_REST_Response(
			array(
				'code' => 'saved',
				'data' => $recent,
			)
		);
	}

	/**
	 * POST /seopress/v1/commands/flush-rewrite-rules — flush WP rewrite rules.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processFlushRewrite() {
		$allowed = $this->check_rate_limit( 'flush-rewrite' );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		flush_rewrite_rules( false );

		return new \WP_REST_Response(
			array(
				'code'    => 'flushed',
				/* translators: confirmation message shown in a snackbar after flushing rewrite rules */
				'message' => __( 'Rewrite rules flushed.', 'wp-seopress' ),
			)
		);
	}

	/**
	 * POST /seopress/v1/commands/clear-cache — delete SEOPress transients.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function processClearCache() {
		$allowed = $this->check_rate_limit( 'clear-cache' );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		global $wpdb;

		$deleted = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			"DELETE FROM {$wpdb->options}
			WHERE option_name LIKE '_transient_seopress_%'
			   OR option_name LIKE '_transient_timeout_seopress_%'
			   OR option_name LIKE '_site_transient_seopress_%'
			   OR option_name LIKE '_site_transient_timeout_seopress_%'"
		);

		if ( false === $deleted ) {
			$deleted = 0;
		}

		// Approximate transient count: each transient produces 2 rows (value + timeout).
		$count = (int) ceil( $deleted / 2 );

		return new \WP_REST_Response(
			array(
				'code'    => 'cleared',
				'count'   => $count,
				/* translators: %d: number of cache entries cleared */
				'message' => sprintf( _n( '%d SEOPress cache entry cleared.', '%d SEOPress cache entries cleared.', $count, 'wp-seopress' ), $count ),
			)
		);
	}
}

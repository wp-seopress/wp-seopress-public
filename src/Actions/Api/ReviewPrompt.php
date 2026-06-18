<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\InstallDate;

/**
 * REST endpoint backing the "Rate SEOPress 5 stars" prompt.
 *
 * The prompt itself is a React notice rendered inside the SEOPress settings
 * layout, so it only ever appears on our own screens. This endpoint persists a
 * per-user choice (reviewed / no thanks / maybe later) so the notice never nags.
 *
 * @since 10.0.0
 */
class ReviewPrompt implements ExecuteHooks {

	/**
	 * User meta flag set once the prompt is permanently dismissed.
	 *
	 * @var string
	 */
	const META_DISMISSED = 'seopress_review_prompt_dismissed';

	/**
	 * User meta holding the timestamp until which the prompt stays hidden.
	 *
	 * @var string
	 */
	const META_SNOOZE = 'seopress_review_prompt_snooze';

	/**
	 * How long "Maybe later" hides the prompt (14 days, in seconds).
	 *
	 * @var int
	 */
	const SNOOZE_SECONDS = 1209600;

	/**
	 * Minimum number of days the user must be eligible before the prompt shows.
	 *
	 * @var int
	 */
	const MIN_INSTALL_DAYS = 7;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check — settings-level capability.
	 *
	 * @return bool
	 */
	public function permissionCheck() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register the REST route.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/review-prompt',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'action' => array(
						'required'          => true,
						'type'              => 'string',
						'enum'              => array( 'reviewed', 'dismiss', 'snooze' ),
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);
	}

	/**
	 * Persist the user's choice.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response
	 */
	public function process( \WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$action  = (string) $request->get_param( 'action' );

		if ( 'snooze' === $action ) {
			update_user_meta( $user_id, self::META_SNOOZE, time() + self::SNOOZE_SECONDS );
		} else {
			// 'reviewed' and 'dismiss' both hide it for good.
			update_user_meta( $user_id, self::META_DISMISSED, '1' );
			delete_user_meta( $user_id, self::META_SNOOZE );
		}

		return new \WP_REST_Response( array( 'action' => $action ) );
	}

	/**
	 * Whether the review prompt should be shown to the current user.
	 *
	 * Read server-side and passed to the settings app, so the notice is gated
	 * before it ever renders.
	 *
	 * @return bool
	 */
	public static function should_show() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$user_id = get_current_user_id();

		// Permanently dismissed (reviewed or "no thanks").
		if ( '1' === get_user_meta( $user_id, self::META_DISMISSED, true ) ) {
			return false;
		}

		// Snoozed and still within the window.
		$snooze = (int) get_user_meta( $user_id, self::META_SNOOZE, true );
		if ( $snooze && time() < $snooze ) {
			return false;
		}

		// Let the user actually use the plugin before asking.
		if ( ! self::is_old_enough() ) {
			return false;
		}

		/**
		 * Filter whether the 5-star review prompt is shown. PRO White Label
		 * (or any site owner) can hook this to suppress it entirely.
		 *
		 * @since 10.0.0
		 *
		 * @param bool $show    Whether to show the prompt.
		 * @param int  $user_id Current user ID.
		 */
		return (bool) apply_filters( 'seopress_show_review_prompt', true, $user_id );
	}

	/**
	 * Whether enough time has passed since install to show the prompt.
	 *
	 * Uses the shared, persistent install timestamp (see InstallDate) rather
	 * than `seopress_activated`, which is a one-shot activation flag deleted on
	 * the first admin load, not an install date.
	 *
	 * @return bool
	 */
	private static function is_old_enough() {
		return InstallDate::daysSince() >= self::MIN_INSTALL_DAYS;
	}
}

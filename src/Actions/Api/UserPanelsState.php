<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoint for saving user panel expand/collapse preferences.
 *
 * @since 9.8.0
 */
class UserPanelsState implements ExecuteHooks {

	/**
	 * User meta key for storing panels state.
	 *
	 * @var string
	 */
	const META_KEY = 'seopress_panels_state';

	/**
	 * Allowed tab keys.
	 *
	 * @var array
	 */
	const ALLOWED_TABS = array( 'post_types', 'taxonomies', 'archives' );

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check — must be able to manage options.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return bool
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'titles_metas' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/user/panels-state',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'processGet' ),
					'permission_callback' => array( $this, 'permissionCheck' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'processPost' ),
					'permission_callback' => array( $this, 'permissionCheck' ),
					'args'                => array(
						'tab' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => array( $this, 'validateTab' ),
						),
						'expanded' => array(
							'required'          => true,
							'type'              => 'boolean',
						),
					),
				),
			)
		);
	}

	/**
	 * Validate that tab is an allowed value.
	 *
	 * @param string $value The tab value.
	 *
	 * @return bool
	 */
	public function validateTab( $value ) {
		return in_array( $value, self::ALLOWED_TABS, true );
	}

	/**
	 * GET — return current user's panels state.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processGet( \WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$state   = get_user_meta( $user_id, self::META_KEY, true );

		if ( ! is_array( $state ) ) {
			$state = array();
		}

		return new \WP_REST_Response( $state );
	}

	/**
	 * POST — save expanded/collapsed preference for a given tab.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response
	 */
	public function processPost( \WP_REST_Request $request ) {
		$user_id  = get_current_user_id();
		$tab      = $request->get_param( 'tab' );
		$expanded = (bool) $request->get_param( 'expanded' );

		$state = get_user_meta( $user_id, self::META_KEY, true );

		if ( ! is_array( $state ) ) {
			$state = array();
		}

		$state[ $tab ] = $expanded;

		update_user_meta( $user_id, self::META_KEY, $state );

		return new \WP_REST_Response( $state );
	}
}

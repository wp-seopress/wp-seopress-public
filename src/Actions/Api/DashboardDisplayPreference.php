<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoint for the dashboard "Display" panel toggles.
 *
 * Owns the six "Hide X" preferences that used to be saved by the legacy
 * jQuery click handlers in seopress-dashboard.js. Replaces the
 * wp_ajax_seopress_display + wp_ajax_seopress_hide_notices calls for the
 * dashboard block visibility toggles.
 *
 * @since 9.9.0
 */
class DashboardDisplayPreference implements ExecuteHooks {

	/**
	 * Toggles that map to keys in the seopress_notices option.
	 *
	 * @var array<string,string>
	 */
	const NOTICE_TOGGLES = array(
		'notice-get-started'  => 'notice-get-started',
		'notice-tasks'        => 'notice-tasks',
		'notice-integrations' => 'notice-integrations',
	);

	/**
	 * Toggles that map to keys in the seopress_advanced_option_name option.
	 *
	 * @var array<string,string>
	 */
	const ADVANCED_TOGGLES = array(
		'seopress-advanced-seo-tools'     => 'seopress_advanced_appearance_seo_tools',
		'seopress-advanced-notifications' => 'seopress_advanced_appearance_notifications',
	);

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check — must hold the dashboard capability.
	 *
	 * @return bool
	 */
	public function permissionCheck() {
		return current_user_can( seopress_capability( 'manage_options', 'dashboard' ) );
	}

	/**
	 * Register the REST route.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/dashboard/display-preference',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'key'   => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validateKey' ),
					),
					'value' => array(
						'required' => true,
						'type'     => 'boolean',
					),
				),
			)
		);
	}

	/**
	 * Validate that the key is one of the known toggles.
	 *
	 * @param string $value The key.
	 * @return bool
	 */
	public function validateKey( $value ) {
		return isset( self::NOTICE_TOGGLES[ $value ] )
			|| isset( self::ADVANCED_TOGGLES[ $value ] );
	}

	/**
	 * Persist the preference.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response
	 */
	public function process( \WP_REST_Request $request ) {
		$key   = (string) $request->get_param( 'key' );
		$value = (bool) $request->get_param( 'value' );

		if ( isset( self::NOTICE_TOGGLES[ $key ] ) ) {
			$notices = get_option( 'seopress_notices', array() );
			if ( ! is_array( $notices ) ) {
				$notices = array();
			}
			$notices[ self::NOTICE_TOGGLES[ $key ] ] = $value ? '1' : '0';
			update_option( 'seopress_notices', $notices, false );
		} elseif ( isset( self::ADVANCED_TOGGLES[ $key ] ) ) {
			$advanced = get_option( 'seopress_advanced_option_name', array() );
			if ( ! is_array( $advanced ) ) {
				$advanced = array();
			}
			if ( $value ) {
				$advanced[ self::ADVANCED_TOGGLES[ $key ] ] = '1';
			} else {
				unset( $advanced[ self::ADVANCED_TOGGLES[ $key ] ] );
			}
			update_option( 'seopress_advanced_option_name', $advanced, false );
		}

		return new \WP_REST_Response(
			array(
				'key'   => $key,
				'value' => $value,
			)
		);
	}
}

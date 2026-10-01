<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

defined( 'ABSPATH' ) || exit;

use SEOPress\Actions\Abilities\McpSettingsData;
use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Serve the MCP settings tab its payload.
 *
 * Kept off the page bootstrap data on purpose: the readiness checks call this
 * site's own endpoint, and that loopback request has no business running on
 * every settings page load. The tab asks for it when it opens, and when the
 * site owner presses "Run the checks again".
 *
 * @since 10.3.0
 */
class McpStatus implements ExecuteHooks {

	/**
	 * Register hooks.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Register the route.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/mcp/status',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
			)
		);
	}

	/**
	 * Only whoever may edit the Advanced settings may read this.
	 *
	 * The payload names every tool an AI client would be handed, which is a map
	 * of what this site can be made to change.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public function permissionCheck() { // phpcs:ignore -- matches the naming used by the other REST actions.
		return current_user_can( seopress_capability( 'manage_options', 'advanced' ) );
	}

	/**
	 * Return the tab payload.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_REST_Response
	 */
	public function process() {
		return new \WP_REST_Response( McpSettingsData::get_data(), 200 );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

defined( 'ABSPATH' ) || exit;

use SEOPress\Actions\Abilities\McpOauthStore;
use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Let a signed-in user cut off an AI client that holds a token for their account.
 *
 * The route only ever touches the records of whoever is making the request. It
 * takes no user id and reads none from the request, so there is no shape of
 * call that revokes somebody else's connection, and no capability that would
 * widen it into one.
 *
 * Revoking is deliberately open to any signed-in account, not only to whoever
 * administers the Advanced settings: a subscriber who once approved a client
 * has to be able to take that back, and it is their own token.
 *
 * @since 10.3.0
 */
class McpConnections implements ExecuteHooks {

	/**
	 * REST namespace of the route.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'seopress/v1';

	/**
	 * REST route, inside self::REST_NAMESPACE.
	 *
	 * @var string
	 */
	const REST_ROUTE = '/mcp/connections/(?P<uuid>[A-Za-z0-9-]+)';

	/**
	 * The path the settings tab builds its request from.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function rest_path() {
		return '/' . self::REST_NAMESPACE . '/mcp/connections/';
	}

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
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'uuid' => array(
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Anybody signed in may revoke, but only their own connections.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public function permissionCheck() { // phpcs:ignore -- matches the naming used by the other REST actions.
		return is_user_logged_in();
	}

	/**
	 * Revoke one connection of the current account.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function process( $request ) {
		$uuid = (string) $request->get_param( 'uuid' );

		$revoked = McpOauthStore::revoke_connection( get_current_user_id(), $uuid );
		if ( null === $revoked ) {
			return new \WP_Error( 'seopress_mcp_connection_busy', __( 'The connection could not be revoked. Please retry.', 'wp-seopress' ), array( 'status' => 503 ) );
		}
		if ( ! $revoked ) {
			return new \WP_Error(
				'seopress_mcp_connection_not_found',
				__( 'This connection does not belong to your account, or it was already revoked.', 'wp-seopress' ),
				array( 'status' => 404 )
			);
		}

		return new \WP_REST_Response(
			array(
				'revoked'     => true,
				'connections' => McpOauthStore::connections_for_user( get_current_user_id() ),
			),
			200
		);
	}
}

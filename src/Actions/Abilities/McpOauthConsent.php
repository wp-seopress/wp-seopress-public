<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * The authorization endpoint of the SEOPress MCP OAuth server, and the screen
 * where a WordPress user decides whether an AI client may act as them.
 *
 * This is the only part of the flow a human ever sees, so it is a normal
 * WordPress screen rather than a REST route: it needs the login cookie, the
 * login redirect, and a nonce on the form. It is served from admin-post.php,
 * which means an anonymous visitor is sent through wp_login_url() and comes
 * back to the same authorization request once signed in.
 *
 * What the screen has to make plain, and does:
 *
 * - which site is being connected, by name and by URL;
 * - which client is asking, with the caveat that the name is the client's own
 *   claim, because RFC 7591 registration is open and nothing verifies it;
 * - which WordPress account the token would act as, and what that account can
 *   already do, because that, and nothing else, is what the token can do;
 * - what refusing looks like, as clearly as what approving looks like.
 *
 * The consent grants no capability of its own. It mints an authorization code
 * bound to the user who approved it; every later call runs as that user and is
 * decided by each ability's own permission_callback.
 *
 * @since 10.3.0
 */
class McpOauthConsent implements ExecuteHooks {

	/**
	 * The admin-post action this endpoint answers on.
	 *
	 * @var string
	 */
	const ACTION = 'seopress_mcp_authorize';

	/**
	 * The nonce action protecting the consent form.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'seopress_mcp_oauth_consent';

	/**
	 * The form field carrying the decision.
	 *
	 * @var string
	 */
	const DECISION_FIELD = 'seopress_mcp_decision';

	/**
	 * The authorization request parameters carried across the consent form.
	 *
	 * @var string[]
	 */
	const REQUEST_PARAMS = array(
		'response_type',
		'client_id',
		'redirect_uri',
		'scope',
		'state',
		'code_challenge',
		'code_challenge_method',
		'resource',
	);

	/**
	 * Register the hooks.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	/**
	 * The form field carrying the access the person picked.
	 *
	 * @since 10.3.0
	 * @var string
	 */
	const ACCESS_FIELD = 'seopress_mcp_access';

	public function hooks() {
		if ( ! seopress_abilities_api_available() ) {
			return;
		}

		add_action( 'admin_post_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( $this, 'handle_anonymous' ) );
	}

	/**
	 * The absolute URL of the authorization endpoint.
	 *
	 * It carries a query component, which RFC 6749 Section 3.1 allows as long
	 * as a client adds its own parameters to it rather than replacing it.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function authorize_url() {
		return admin_url( 'admin-post.php' ) . '?action=' . self::ACTION;
	}

	/* ---------------------------------------------------------------------
	 * Validation
	 * ------------------------------------------------------------------ */

	/**
	 * Strip a value down to something safe to echo and to hand back.
	 *
	 * The state and the code challenge are opaque to this server and have to
	 * come back to the client byte for byte, so they are not sanitized into
	 * something else: only control characters are removed, and the length is
	 * capped.
	 *
	 * @since 10.3.0
	 *
	 * @param mixed $value The raw parameter.
	 *
	 * @return string
	 */
	public static function clean_opaque( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = preg_replace( '/[\x00-\x1F\x7F]/', '', $value );

		return substr( (string) $value, 0, 2048 );
	}

	/**
	 * Read the authorization request parameters out of a request array.
	 *
	 * @since 10.3.0
	 *
	 * @param array $source Usually $_GET or $_POST, already unslashed.
	 *
	 * @return array
	 */
	public static function collect_params( $source ) {
		$params = array();

		foreach ( self::REQUEST_PARAMS as $key ) {
			$params[ $key ] = isset( $source[ $key ] ) ? self::clean_opaque( $source[ $key ] ) : '';
		}

		return $params;
	}

	/**
	 * Refuse an authorization request, saying whether the client can be told.
	 *
	 * RFC 6749 Section 4.1.2.1 is precise about this: when the client or the
	 * redirect URI is the thing that is wrong, the user must be told on the
	 * server's own page, because redirecting would mean sending the answer to
	 * an address that was never proven to belong to the client. Everything
	 * else goes back to the client as an error redirect.
	 *
	 * @since 10.3.0
	 *
	 * @param string $error        The OAuth error code.
	 * @param string $description  What the site owner or developer should do.
	 * @param string $redirect_uri Where to send the error, empty to show it here.
	 * @param string $state        The client's state, echoed back with the error.
	 *
	 * @return \WP_Error
	 */
	protected static function refuse( $error, $description, $redirect_uri = '', $state = '' ) {
		return new \WP_Error(
			'seopress_mcp_oauth_' . $error,
			$description,
			array(
				'status'       => 400,
				'oauth_error'  => $error,
				'redirect_uri' => $redirect_uri,
				'state'        => $state,
			)
		);
	}

	/**
	 * Validate an authorization request.
	 *
	 * @since 10.3.0
	 *
	 * @param array $params The authorization request parameters.
	 *
	 * @return array|\WP_Error The validated request, or the refusal.
	 */
	public static function validate_request( $params ) {
		if ( ! McpOauth::is_enabled() ) {
			return self::refuse( 'invalid_request', McpOauth::disabled_message() );
		}

		if ( ! McpOauth::request_is_secure() ) {
			return self::refuse( 'invalid_request', McpOauth::insecure_transport_message() );
		}

		$state = isset( $params['state'] ) ? (string) $params['state'] : '';

		$client_id = isset( $params['client_id'] ) ? (string) $params['client_id'] : '';
		$client    = McpOauthStore::get_client( $client_id );

		if ( '' === $client_id || null === $client ) {
			return self::refuse(
				'invalid_client',
				__( 'This client is not registered on this site, so there is nothing to authorize. Remove the connector in your AI client and add it again, which registers it afresh.', 'wp-seopress' )
			);
		}

		$registered = isset( $client['redirect_uris'] ) && is_array( $client['redirect_uris'] )
			? $client['redirect_uris']
			: array();

		$redirect_uri = isset( $params['redirect_uri'] ) ? (string) $params['redirect_uri'] : '';

		// RFC 6749 lets the parameter be left out when exactly one URI was
		// registered. Anything else has to match a registered value exactly.
		if ( '' === $redirect_uri && 1 === count( $registered ) ) {
			$redirect_uri = (string) $registered[0];
		}

		if ( '' === $redirect_uri || ! in_array( $redirect_uri, $registered, true ) ) {
			return self::refuse(
				'invalid_request',
				__( 'The redirect address this client asked for is not one it registered, so the authorization was stopped here rather than sent on. Remove the connector in your AI client and add it again.', 'wp-seopress' )
			);
		}

		$response_type = isset( $params['response_type'] ) ? (string) $params['response_type'] : '';

		if ( 'code' !== $response_type ) {
			return self::refuse(
				'unsupported_response_type',
				__( 'This authorization server only issues authorization codes.', 'wp-seopress' ),
				$redirect_uri,
				$state
			);
		}

		$challenge_method = isset( $params['code_challenge_method'] ) ? (string) $params['code_challenge_method'] : '';

		if ( 'S256' !== $challenge_method ) {
			return self::refuse(
				'invalid_request',
				__( 'PKCE with the S256 challenge method is required on every authorization.', 'wp-seopress' ),
				$redirect_uri,
				$state
			);
		}

		$challenge = isset( $params['code_challenge'] ) ? (string) $params['code_challenge'] : '';

		if ( 1 !== preg_match( '/^[A-Za-z0-9\-._~]{43,128}$/', $challenge ) ) {
			return self::refuse(
				'invalid_request',
				__( 'The PKCE code challenge is missing or malformed.', 'wp-seopress' ),
				$redirect_uri,
				$state
			);
		}

		$scope = isset( $params['scope'] ) ? trim( (string) $params['scope'] ) : '';

		if ( '' !== $scope ) {
			foreach ( preg_split( '/\s+/', $scope ) as $requested ) {
				if ( McpOauthStore::SCOPE !== $requested ) {
					return self::refuse(
						'invalid_scope',
						sprintf(
							/* translators: %s: the only scope this server issues. */
							__( 'This server issues one scope only: %s.', 'wp-seopress' ),
							McpOauthStore::SCOPE
						),
						$redirect_uri,
						$state
					);
				}
			}
		}

		$resource = isset( $params['resource'] ) ? (string) $params['resource'] : '';

		if ( '' !== $resource && ! McpOauth::resources_match( $resource, McpOauth::canonical_resource() ) ) {
			return self::refuse(
				'invalid_target',
				sprintf(
					/* translators: %s: the canonical URI of this site's MCP server. */
					__( 'This server only authorizes access to %s.', 'wp-seopress' ),
					McpOauth::canonical_resource()
				),
				$redirect_uri,
				$state
			);
		}

		return array(
			'client'                => $client,
			'client_id'             => $client_id,
			'redirect_uri'          => $redirect_uri,
			'response_type'         => 'code',
			'scope'                 => McpOauthStore::SCOPE,
			'state'                 => $state,
			'code_challenge'        => $challenge,
			'code_challenge_method' => 'S256',
			'resource'              => McpOauth::canonical_resource(),
		);
	}

	/* ---------------------------------------------------------------------
	 * Outcomes
	 * ------------------------------------------------------------------ */

	/**
	 * Build a redirect back to the client.
	 *
	 * Built by hand rather than with add_query_arg(): that function does not
	 * encode the values it is handed, and a state or an error description that
	 * happens to contain an ampersand would otherwise split into two parameters.
	 *
	 * @since 10.3.0
	 *
	 * @param string $redirect_uri The client's redirect URI, already validated.
	 * @param array  $args         The query parameters to add.
	 *
	 * @return string
	 */
	public static function build_redirect( $redirect_uri, $args ) {
		$args['iss'] = McpOauth::issuer();

		$args = array_filter(
			$args,
			function ( $value ) {
				return '' !== $value && null !== $value;
			}
		);

		$separator = ( false === strpos( $redirect_uri, '?' ) ) ? '?' : '&';

		return $redirect_uri . $separator . http_build_query( $args, '', '&', PHP_QUERY_RFC3986 );
	}

	/**
	 * Mint an authorization code and say where to send the browser.
	 *
	 * @since 10.3.0
	 *
	 * @param array  $validated The validated authorization request.
	 * @param int    $user_id   The user who approved it.
	 * @param string $access    "full" for every tool, anything else for read only.
	 *
	 * @return string The redirect URL.
	 */
	public static function approve( $validated, $user_id, $access = 'full' ) {
		$code = McpOauthStore::create_authorization_code(
			array(
				'user_id'        => (int) $user_id,
				'client_id'      => $validated['client_id'],
				'redirect_uri'   => $validated['redirect_uri'],
				'code_challenge' => $validated['code_challenge'],
				'resource'       => McpOauth::canonical_resource(),
				// Only the word this screen offers grants the full set. Anything
				// else, including a field that never arrived, reads only.
				'scope'          => 'full' === $access ? McpOauthStore::SCOPE : McpOauthStore::SCOPE_READ,
			)
		);

		McpOauthStore::touch_client( $validated['client_id'] );

		return self::build_redirect(
			$validated['redirect_uri'],
			array(
				'code'  => $code,
				'state' => $validated['state'],
			)
		);
	}

	/**
	 * Say where to send the browser when the user refuses.
	 *
	 * @since 10.3.0
	 *
	 * @param array $validated The validated authorization request.
	 *
	 * @return string The redirect URL.
	 */
	public static function deny( $validated ) {
		return self::build_redirect(
			$validated['redirect_uri'],
			array(
				'error'             => 'access_denied',
				'error_description' => __( 'The WordPress user refused this connection.', 'wp-seopress' ),
				'state'             => $validated['state'],
			)
		);
	}

	/**
	 * Say where to send the browser for a refusal the client may be told about.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Error $error The refusal.
	 *
	 * @return string Empty when the client must not be redirected to.
	 */
	public static function error_redirect( $error ) {
		$data = $error->get_error_data();

		if ( ! is_array( $data ) || empty( $data['redirect_uri'] ) ) {
			return '';
		}

		return self::build_redirect(
			$data['redirect_uri'],
			array(
				'error'             => isset( $data['oauth_error'] ) ? $data['oauth_error'] : 'invalid_request',
				'error_description' => $error->get_error_message(),
				'state'             => isset( $data['state'] ) ? $data['state'] : '',
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Request handling
	 * ------------------------------------------------------------------ */

	/**
	 * Send an anonymous visitor to the login screen and back here afterwards.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function handle_anonymous() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- The start of an OAuth authorization is a cross-site GET by design; the decision itself is nonce protected.
		$params = self::collect_params( wp_unslash( $_GET ) );

		$return_to = self::authorize_url();

		foreach ( $params as $key => $value ) {
			if ( '' === $value ) {
				continue;
			}

			$return_to .= '&' . rawurlencode( $key ) . '=' . rawurlencode( $value );
		}

		wp_safe_redirect( wp_login_url( $return_to ) );

		$this->finish();
	}

	/**
	 * End the request.
	 *
	 * Every exit of this endpoint goes through here so the whole handler,
	 * including its redirects, can be driven by a test that replaces this one
	 * method instead of ending the PHP process.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function finish() {
		exit;
	}

	/**
	 * Serve the authorization endpoint to a signed-in user.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function handle() {
		$is_post = isset( $_SERVER['REQUEST_METHOD'] )
			&& 'POST' === strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) );

		if ( $is_post ) {
			$this->handle_decision();

			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- The start of an OAuth authorization is a cross-site GET by design; the decision itself is nonce protected.
		$params    = self::collect_params( wp_unslash( $_GET ) );
		$validated = self::validate_request( $params );

		if ( is_wp_error( $validated ) ) {
			$this->finish_with_error( $validated );

			return;
		}

		$this->render_consent( $validated );

		$this->finish();
	}

	/**
	 * Handle the consent form submission.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function handle_decision() {
		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			$this->render_error(
				__( 'This form has expired', 'wp-seopress' ),
				__( 'The consent form was not submitted from this site, or it sat open for too long. Start the connection again from your AI client.', 'wp-seopress' )
			);

			$this->finish();

			return;
		}

		$params    = self::collect_params( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- The nonce is verified immediately above.
		$validated = self::validate_request( $params );

		if ( is_wp_error( $validated ) ) {
			$this->finish_with_error( $validated );

			return;
		}

		$decision = isset( $_POST[ self::DECISION_FIELD ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- The nonce is verified above.
			? sanitize_key( wp_unslash( $_POST[ self::DECISION_FIELD ] ) )
			: '';

		$access = isset( $_POST[ self::ACCESS_FIELD ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- The nonce is verified above.
			? sanitize_key( wp_unslash( $_POST[ self::ACCESS_FIELD ] ) )
			: '';

		$url = 'approve' === $decision
			? self::approve( $validated, get_current_user_id(), $access )
			: self::deny( $validated );

		$this->redirect_to_client( $url );
	}

	/**
	 * Either redirect the refusal to the client, or show it here.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Error $error The refusal.
	 *
	 * @return void
	 */
	protected function finish_with_error( $error ) {
		$url = self::error_redirect( $error );

		if ( '' !== $url ) {
			$this->redirect_to_client( $url );

			return;
		}

		$this->render_error( __( 'This connection cannot be authorized', 'wp-seopress' ), $error->get_error_message() );

		$this->finish();
	}

	/**
	 * Send the browser back to the client.
	 *
	 * wp_safe_redirect() cannot be used: the whole point of a redirect URI is
	 * that it is off this site. It is safe because it was matched, character
	 * for character, against an address the client registered beforehand.
	 *
	 * @since 10.3.0
	 *
	 * @param string $url Where to send the browser.
	 *
	 * @return void
	 */
	protected function redirect_to_client( $url ) {
		wp_redirect( $url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- An OAuth redirect URI is off site by definition, and was matched against a pre-registered value.

		$this->finish();
	}

	/* ---------------------------------------------------------------------
	 * Rendering
	 * ------------------------------------------------------------------ */

	/**
	 * The stylesheet of the consent screen.
	 *
	 * Inline and self-contained: this page is served outside the admin shell,
	 * so it cannot rely on an enqueued stylesheet being there. The accent is
	 * the WordPress admin theme colour, with core's own default behind it.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	protected static function styles() {
		return '
:root { --seopress-oauth-accent: var(--wp-admin-theme-color, #2271b1); }
* { box-sizing: border-box; }
body { margin: 0; padding: 24px 16px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; font-size: 14px; line-height: 1.6; color: #1e1e1e; background: #f0f0f1; }
.seopress-oauth { max-width: 540px; margin: 0 auto; background: #fff; border: 1px solid #dcdcde; border-radius: 4px; padding: 32px; }
.seopress-oauth h1 { font-size: 22px; line-height: 1.3; margin: 0 0 8px; font-weight: 600; }
.seopress-oauth h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #646970; margin: 24px 0 8px; }
.seopress-oauth p { margin: 0 0 12px; }
.seopress-oauth dl { margin: 0; }
.seopress-oauth dt { font-weight: 600; margin-top: 8px; }
.seopress-oauth dd { margin: 0; color: #50575e; word-break: break-word; }
.seopress-oauth ul { margin: 0; padding-left: 20px; }
.seopress-oauth li { margin-bottom: 4px; }
.seopress-oauth-note { background: #f6f7f7; border-left: 4px solid #dba617; padding: 12px 16px; margin: 16px 0; }
.seopress-oauth-actions { display: flex; gap: 12px; margin-top: 28px; flex-wrap: wrap; }
.seopress-oauth-button { font: inherit; font-weight: 500; padding: 8px 18px; border-radius: 3px; cursor: pointer; border: 1px solid var(--seopress-oauth-accent); }
.seopress-oauth-button--approve { background: var(--seopress-oauth-accent); color: #fff; }
.seopress-oauth-choice { display: grid; gap: 8px; margin: 0 0 20px; }
.seopress-oauth-choice label { display: grid; grid-template-columns: auto 1fr; gap: 4px 8px; align-items: start; padding: 12px 14px; border: 1px solid #dcdcde; border-radius: 4px; cursor: pointer; }
.seopress-oauth-choice input { margin: 3px 0 0; grid-row: span 2; }
.seopress-oauth-choice span { font-size: 13px; color: #50575e; }
.seopress-oauth-choice label:has(input:checked) { border-color: var(--seopress-oauth-accent); box-shadow: 0 0 0 1px var(--seopress-oauth-accent); }
.seopress-oauth-button--deny { background: #fff; color: var(--seopress-oauth-accent); }
.seopress-oauth-button:focus { outline: 2px solid var(--seopress-oauth-accent); outline-offset: 1px; }
.seopress-oauth-access { font-size: 12px; color: #646970; }
.seopress-oauth-footer { margin-top: 24px; font-size: 12px; color: #646970; }
@media (prefers-color-scheme: dark) {
	body { background: #1d2327; color: #f0f0f1; }
	.seopress-oauth { background: #2c3338; border-color: #3c434a; }
	.seopress-oauth dd, .seopress-oauth h2, .seopress-oauth-access, .seopress-oauth-footer { color: #c3c4c7; }
	.seopress-oauth-note { background: #32373c; }
	.seopress-oauth-button--deny { background: #2c3338; color: #f0f0f1; border-color: #8c8f94; }
}
';
	}

	/**
	 * Open the standalone page the consent screen and its errors are drawn on.
	 *
	 * @since 10.3.0
	 *
	 * @param string $title The document title.
	 *
	 * @return void
	 */
	protected function open_page( $title ) {
		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
			// A consent screen must never be framed by the site asking for consent.
			header( 'X-Frame-Options: DENY' );
			header( 'Content-Security-Policy: frame-ancestors \'none\'' );
		}

		printf(
			'<!DOCTYPE html><html %1$s><head><meta charset="%2$s"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>%3$s</title><style>%4$s</style></head><body><main class="seopress-oauth">',
			get_language_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds this attribute string.
			esc_attr( get_bloginfo( 'charset' ) ),
			esc_html( $title ),
			self::styles() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static stylesheet defined above.
		);
	}

	/**
	 * Close the standalone page.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function close_page() {
		echo '</main></body></html>';
	}

	/**
	 * Draw a refusal that cannot be sent back to the client.
	 *
	 * @since 10.3.0
	 *
	 * @param string $title   The heading.
	 * @param string $message What went wrong, and what to do about it.
	 *
	 * @return void
	 */
	public function render_error( $title, $message ) {
		$this->open_page( $title );

		printf(
			'<h1>%1$s</h1><p>%2$s</p><p class="seopress-oauth-footer"><a href="%3$s">%4$s</a></p>',
			esc_html( $title ),
			esc_html( $message ),
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) )
		);

		$this->close_page();
	}

	/**
	 * Draw the consent screen.
	 *
	 * @since 10.3.0
	 *
	 * @param array $validated The validated authorization request.
	 *
	 * @return void
	 */
	public function render_consent( $validated ) {
		$user   = wp_get_current_user();
		$client = $validated['client'];

		$client_name = isset( $client['client_name'] ) ? (string) $client['client_name'] : '';
		$site_name   = get_bloginfo( 'name' );

		$title = sprintf(
			/* translators: 1: the name of the AI client asking for access. 2: the name of this WordPress site. */
			__( 'Connect %1$s to %2$s', 'wp-seopress' ),
			$client_name,
			$site_name
		);

		$this->open_page( $title );

		printf( '<h1>%s</h1>', esc_html( $title ) );

		printf(
			'<p>%s</p>',
			esc_html(
				sprintf(
					/* translators: 1: the name of the AI client. 2: the WordPress account name. */
					__( '%1$s is asking to use the SEOPress tools on this site as %2$s. It will be able to do exactly what that account can already do, and nothing more.', 'wp-seopress' ),
					$client_name,
					$user->display_name
				)
			)
		);

		printf( '<h2>%s</h2><dl>', esc_html__( 'What is being connected', 'wp-seopress' ) );

		printf(
			'<dt>%1$s</dt><dd>%2$s</dd>',
			esc_html__( 'Site', 'wp-seopress' ),
			esc_html( $site_name . ' (' . home_url() . ')' )
		);

		printf(
			'<dt>%1$s</dt><dd>%2$s</dd>',
			esc_html__( 'Client', 'wp-seopress' ),
			esc_html( $client_name )
		);

		if ( ! empty( $client['client_uri'] ) ) {
			printf(
				'<dt>%1$s</dt><dd>%2$s</dd>',
				esc_html__( 'Client address', 'wp-seopress' ),
				esc_html( $client['client_uri'] )
			);
		}

		printf(
			'<dt>%1$s</dt><dd>%2$s</dd>',
			esc_html__( 'Sends you back to', 'wp-seopress' ),
			esc_html( $validated['redirect_uri'] )
		);

		printf(
			'<dt>%1$s</dt><dd>%2$s</dd>',
			esc_html__( 'Acting as', 'wp-seopress' ),
			esc_html( $user->display_name . ' (' . $user->user_login . ')' )
		);

		printf(
			'<dt>%1$s</dt><dd>%2$s</dd>',
			esc_html__( 'MCP server', 'wp-seopress' ),
			esc_html( McpOauth::canonical_resource() )
		);

		echo '</dl>';

		$this->render_tool_list();

		printf(
			'<div class="seopress-oauth-note"><p>%s</p></div>',
			esc_html__( 'The client chose its own name when it registered, and nothing here checks that it is who it says it is. Only approve this if you just started the connection yourself.', 'wp-seopress' )
		);

		$this->render_form( $validated );

		printf(
			'<p class="seopress-oauth-footer">%s</p>',
			esc_html__( 'You can revoke this connection at any time in SEO, Advanced, MCP.', 'wp-seopress' )
		);

		$this->close_page();
	}

	/**
	 * List the tools the client would be handed.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function render_tool_list() {
		$abilities = McpExposure::exposed_abilities();

		printf(
			'<h2>%s</h2>',
			esc_html(
				sprintf(
					/* translators: %d: how many SEOPress tools would be handed to the client. */
					_n( 'The %d tool it would be handed', 'The %d tools it would be handed', count( $abilities ), 'wp-seopress' ),
					count( $abilities )
				)
			)
		);

		if ( empty( $abilities ) ) {
			printf(
				'<p>%s</p>',
				esc_html__( 'No tool is exposed on this site right now, so this connection would be able to do nothing at all.', 'wp-seopress' )
			);

			return;
		}

		echo '<ul>';

		foreach ( $abilities as $ability ) {
			$access = McpSettingsData::ability_access( $ability );

			printf(
				'<li>%1$s <span class="seopress-oauth-access">%2$s</span></li>',
				esc_html( (string) $ability->get_label() ),
				'read' === $access
					? esc_html__( 'reads only', 'wp-seopress' )
					: esc_html__( 'can change data', 'wp-seopress' )
			);
		}

		echo '</ul>';
	}

	/**
	 * Let the person narrow what this one connection may do.
	 *
	 * The MCP annotations already tell a client which tools only read, and a
	 * well behaved one asks before using the others. Not every client does:
	 * measured against a real ChatGPT connector, it deleted a redirection
	 * without asking anything. An annotation is advice, and advice is not a
	 * boundary, so the choice is made here and stored on the token, where the
	 * client has no say in it.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function render_access_choice() {
		$abilities = McpExposure::exposed_abilities();
		$writers   = 0;

		foreach ( $abilities as $ability ) {
			if ( ! McpExposure::is_read_ability( $ability ) ) {
				++$writers;
			}
		}

		printf( '<h2>%s</h2>', esc_html__( 'What it may do', 'wp-seopress' ) );

		printf(
			'<div class="seopress-oauth-choice"><label><input type="radio" name="%1$s" value="full" checked="checked" /> <strong>%2$s</strong><span>%3$s</span></label>',
			esc_attr( self::ACCESS_FIELD ),
			esc_html__( 'Read and change', 'wp-seopress' ),
			esc_html(
				sprintf(
					/* translators: %d: how many of the tools can change data. */
					_n(
						'Every tool above, including the %d that changes data on this site.',
						'Every tool above, including the %d that change data on this site.',
						$writers,
						'wp-seopress'
					),
					$writers
				)
			)
		);

		printf(
			'<label><input type="radio" name="%1$s" value="read" /> <strong>%2$s</strong><span>%3$s</span></label></div>',
			esc_attr( self::ACCESS_FIELD ),
			esc_html__( 'Read only', 'wp-seopress' ),
			esc_html__( 'The tools that only read. The others are not served to this connection at all, and are refused if it asks for one by name.', 'wp-seopress' )
		);
	}

	/**
	 * Draw the approve and refuse form.
	 *
	 * @since 10.3.0
	 *
	 * @param array $validated The validated authorization request.
	 *
	 * @return void
	 */
	protected function render_form( $validated ) {
		printf( '<form method="post" action="%s">', esc_url( admin_url( 'admin-post.php' ) ) );

		printf( '<input type="hidden" name="action" value="%s" />', esc_attr( self::ACTION ) );

		$fields = array(
			'response_type'         => $validated['response_type'],
			'client_id'             => $validated['client_id'],
			'redirect_uri'          => $validated['redirect_uri'],
			'scope'                 => $validated['scope'],
			'state'                 => $validated['state'],
			'code_challenge'        => $validated['code_challenge'],
			'code_challenge_method' => $validated['code_challenge_method'],
			'resource'              => $validated['resource'],
		);

		foreach ( $fields as $name => $value ) {
			printf(
				'<input type="hidden" name="%1$s" value="%2$s" />',
				esc_attr( $name ),
				esc_attr( $value )
			);
		}

		$this->render_access_choice();

		wp_nonce_field( self::NONCE_ACTION );

		printf(
			'<div class="seopress-oauth-actions"><button type="submit" class="seopress-oauth-button seopress-oauth-button--approve" name="%1$s" value="approve">%2$s</button><button type="submit" class="seopress-oauth-button seopress-oauth-button--deny" name="%1$s" value="deny">%3$s</button></div>',
			esc_attr( self::DECISION_FIELD ),
			esc_html__( 'Allow this connection', 'wp-seopress' ),
			esc_html__( 'Refuse', 'wp-seopress' )
		);

		echo '</form>';
	}
}

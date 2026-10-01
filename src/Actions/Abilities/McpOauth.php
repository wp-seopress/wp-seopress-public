<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * The OAuth 2.1 half of the SEOPress MCP server.
 *
 * The MCP specification makes an MCP server an OAuth 2.1 resource server. There
 * is no external authorization server to point at on a WordPress site, so this
 * class is both: it publishes the two discovery documents, registers clients,
 * issues and refreshes tokens, revokes them, and validates the bearer token the
 * MCP endpoint is called with.
 *
 * What it implements, and why each piece is not optional:
 *
 * - RFC 9728 Protected Resource Metadata, at /.well-known/oauth-protected-
 *   resource. Its authorization_servers member is what tells a client where to
 *   authenticate; the specification requires at least one entry.
 * - RFC 8414 Authorization Server Metadata, at /.well-known/oauth-authorization
 *   -server, so the client can find the authorization, token, registration and
 *   revocation endpoints without being told them by hand.
 * - RFC 7591 Dynamic Client Registration. claude.ai registers itself with no
 *   human step; without this, connecting a site would mean typing a client id
 *   and a secret into a dialog.
 * - RFC 8707 resource indicators. The client names the MCP server it wants a
 *   token for, that value is validated when the token is issued, stored on the
 *   token, and checked again on every call. A token minted for another resource
 *   is refused: the specification is explicit that an MCP server must validate
 *   that a token was issued for it, and must never accept or forward one that
 *   was not.
 * - PKCE with S256, on every authorization. No plain, no omission.
 *
 * What it deliberately does not implement: scopes beyond the single "mcp" one,
 * third-party authorization servers, the device flow and token introspection.
 *
 * A token never carries permissions of its own. It carries a WordPress user id,
 * the request runs as that user, and every ability's own permission_callback
 * then decides what that user may do. An editor's token gets an editor's reach,
 * a subscriber's token gets a subscriber's.
 *
 * @since 10.3.0
 */
class McpOauth implements ExecuteHooks {

	/**
	 * REST route of the dynamic client registration endpoint.
	 *
	 * @var string
	 */
	const REGISTER_ROUTE = '/v1/register';

	/**
	 * REST route of the token endpoint.
	 *
	 * @var string
	 */
	const TOKEN_ROUTE = '/v1/token';

	/**
	 * REST route of the revocation endpoint.
	 *
	 * @var string
	 */
	const REVOKE_ROUTE = '/v1/revoke';

	/**
	 * REST alias of the protected resource metadata document.
	 *
	 * The canonical location is /.well-known/oauth-protected-resource, which is
	 * served outside the REST API because RFC 9728 fixes that path. This alias
	 * exists so the same document can be fetched on a site whose server does
	 * not route /.well-known to WordPress, and so it can be asserted on.
	 *
	 * @var string
	 */
	const PROTECTED_RESOURCE_ROUTE = '/v1/oauth-protected-resource';

	/**
	 * REST alias of the authorization server metadata document.
	 *
	 * @var string
	 */
	const AUTHORIZATION_SERVER_ROUTE = '/v1/oauth-authorization-server';

	/**
	 * Well-known path of the protected resource metadata, per RFC 9728.
	 *
	 * @var string
	 */
	const WELL_KNOWN_PROTECTED_RESOURCE = '/.well-known/oauth-protected-resource';

	/**
	 * Well-known path of the authorization server metadata, per RFC 8414.
	 *
	 * @var string
	 */
	const WELL_KNOWN_AUTHORIZATION_SERVER = '/.well-known/oauth-authorization-server';

	/**
	 * Register the hooks.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! seopress_abilities_api_available() ) {
			return;
		}

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'parse_request', array( $this, 'maybe_serve_well_known' ), 0 );
	}

	/* ---------------------------------------------------------------------
	 * State of the feature
	 * ------------------------------------------------------------------ */

	/**
	 * Whether the OAuth server answers at all.
	 *
	 * OAuth is never a second switch: it follows the MCP server itself. Turning
	 * the MCP endpoint off turns every endpoint below off with it.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled = seopress_abilities_api_available()
			&& seopress_abilities_api_rest_enabled()
			&& function_exists( 'wp_fast_hash' )
			&& function_exists( 'wp_verify_fast_hash' );

		/**
		 * Filter whether the SEOPress MCP OAuth server answers.
		 *
		 * @since 10.3.0
		 *
		 * @param bool $enabled Whether the OAuth endpoints are served.
		 */
		return (bool) apply_filters( 'seopress_mcp_oauth_enabled', $enabled );
	}

	/**
	 * Whether this site may serve OAuth over the scheme it is published on.
	 *
	 * OAuth 2.1 requires every authorization endpoint to be served over HTTPS.
	 * Loopback is the one exception every implementation makes, because it is
	 * how a developer runs a site locally, and it is the same exception that
	 * lets a redirect URI be http://localhost.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function transport_is_secure() {
		$url   = McpServer::endpoint_url();
		$parts = wp_parse_url( $url );

		$scheme = ( is_array( $parts ) && ! empty( $parts['scheme'] ) ) ? strtolower( $parts['scheme'] ) : '';
		$host   = ( is_array( $parts ) && ! empty( $parts['host'] ) ) ? strtolower( $parts['host'] ) : '';

		$secure = self::transport_allows_oauth( $scheme, $host, wp_get_environment_type() );

		/**
		 * Filter whether the site is considered to be served securely enough for OAuth.
		 *
		 * @since 10.3.0
		 *
		 * @param bool   $secure Whether HTTPS, or loopback, was detected.
		 * @param string $url    The MCP endpoint URL that was read.
		 */
		return (bool) apply_filters( 'seopress_mcp_oauth_transport_is_secure', $secure, $url );
	}

	/**
	 * Require a secure request as well as a secure published endpoint.
	 *
	 * A trusted TLS proxy must configure WordPress's HTTPS server state before
	 * bootstrap. Forwarded headers alone are client input, not proof of TLS.
	 * The transport filter only controls the published endpoint check.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function request_is_secure() {
		if ( ! self::transport_is_secure() ) {
			return false;
		}

		$host = strtolower( (string) wp_parse_url( self::canonical_resource(), PHP_URL_HOST ) );

		return is_ssl() || self::is_loopback_host( $host );
	}

	/**
	 * The rule itself, given the three things it depends on.
	 *
	 * Taken apart from the site it reads so every combination can be tested:
	 * wp_get_environment_type() caches its answer on the first call, so a test
	 * cannot move the site from one environment to another, and a rule that
	 * cannot be tested in both directions is a rule nobody has checked.
	 *
	 * WordPress has already made this call for the credential it ships.
	 * wp_is_application_passwords_available() allows an Application Password
	 * over plain HTTP on a local environment, which is a bearer credential by
	 * any other name. Refusing OAuth there while core hands out that one sent
	 * every Local, Valet, DDEV and Studio install, all of them on a plain
	 * .local or .test name, down a path they do not need: their clients run on
	 * the same machine as the site.
	 *
	 * @since 10.3.0
	 *
	 * @param string $scheme      The scheme of the endpoint URL.
	 * @param string $host        The host of the endpoint URL, lowercased.
	 * @param string $environment What wp_get_environment_type() answers.
	 *
	 * @return bool
	 */
	public static function transport_allows_oauth( $scheme, $host, $environment ) {
		if ( 'https' === $scheme ) {
			return true;
		}

		if ( self::is_loopback_host( $host ) ) {
			return true;
		}

		return 'local' === $environment;
	}

	/**
	 * Whether a host name is the machine the request is running on.
	 *
	 * @since 10.3.0
	 *
	 * @param string $host The host name, already lowercased.
	 *
	 * @return bool
	 */
	public static function is_loopback_host( $host ) {
		$host = trim( (string) $host, '[]' );

		if ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
			return true;
		}

		// Reserved by RFC 6761 for the local machine, so ".localhost" names
		// resolve to loopback the same way "localhost" does.
		return (bool) preg_match( '/\.localhost$/', $host );
	}

	/* ---------------------------------------------------------------------
	 * Identifiers
	 * ------------------------------------------------------------------ */

	/**
	 * The issuer identifier of this authorization server.
	 *
	 * The site root, because that is where /.well-known/oauth-authorization-
	 * server is served from, and RFC 8414 derives one from the other.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function issuer() {
		return untrailingslashit( home_url() );
	}

	/**
	 * The canonical URI of the MCP server, as RFC 8707 means it.
	 *
	 * This is the audience every token is issued for and checked against.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function canonical_resource() {
		return McpServer::endpoint_url();
	}

	/**
	 * Reduce a resource URI to a comparable form.
	 *
	 * Scheme and host are case insensitive, a default port is not part of the
	 * identity, and a trailing slash does not change which server is meant.
	 * Everything else, the path above all, is compared as it was written.
	 *
	 * @since 10.3.0
	 *
	 * @param string $uri The resource URI.
	 *
	 * @return string Empty when the URI carries no usable host.
	 */
	public static function normalize_resource( $uri ) {
		$parts = wp_parse_url( (string) $uri );

		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
			return '';
		}

		$scheme = strtolower( $parts['scheme'] );
		$host   = strtolower( $parts['host'] );

		$normalized = $scheme . '://' . $host;

		$default_ports = array(
			'http'  => 80,
			'https' => 443,
		);

		if ( ! empty( $parts['port'] )
			&& ( ! isset( $default_ports[ $scheme ] ) || (int) $parts['port'] !== $default_ports[ $scheme ] ) ) {
			$normalized .= ':' . (int) $parts['port'];
		}

		if ( ! empty( $parts['path'] ) ) {
			$normalized .= untrailingslashit( $parts['path'] );
		}

		if ( ! empty( $parts['query'] ) ) {
			$normalized .= '?' . $parts['query'];
		}

		return $normalized;
	}

	/**
	 * Whether two resource URIs name the same MCP server.
	 *
	 * @since 10.3.0
	 *
	 * @param string $left  One resource URI.
	 * @param string $right The other.
	 *
	 * @return bool
	 */
	public static function resources_match( $left, $right ) {
		$left  = self::normalize_resource( $left );
		$right = self::normalize_resource( $right );

		if ( '' === $left || '' === $right ) {
			return false;
		}

		return $left === $right;
	}

	/**
	 * Absolute URL of one of the OAuth REST routes.
	 *
	 * @since 10.3.0
	 *
	 * @param string $route One of the *_ROUTE constants.
	 *
	 * @return string
	 */
	public static function rest_endpoint_url( $route ) {
		return rest_url( McpServer::REST_NAMESPACE . $route );
	}

	/**
	 * Absolute URL of the protected resource metadata document.
	 *
	 * RFC 9728 puts the resource path after the well-known segment, so a host
	 * serving several protected resources can describe each of them. The bare
	 * path is served as well, and both answer the same document.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function protected_resource_metadata_url() {
		$path = wp_parse_url( self::canonical_resource(), PHP_URL_PATH );
		$path = is_string( $path ) ? untrailingslashit( $path ) : '';

		return self::issuer() . self::WELL_KNOWN_PROTECTED_RESOURCE . $path;
	}

	/**
	 * Absolute URL of the authorization server metadata document.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function authorization_server_metadata_url() {
		return self::issuer() . self::WELL_KNOWN_AUTHORIZATION_SERVER;
	}

	/* ---------------------------------------------------------------------
	 * Metadata documents
	 * ------------------------------------------------------------------ */

	/**
	 * The RFC 9728 protected resource metadata document.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function protected_resource_metadata() {
		$document = array(
			'resource'                 => self::canonical_resource(),
			'authorization_servers'    => array( self::issuer() ),
			'scopes_supported'         => array( McpOauthStore::SCOPE ),
			'bearer_methods_supported' => array( 'header' ),
			'resource_name'            => sprintf(
				/* translators: %s: the name of this WordPress site. */
				__( 'SEOPress MCP server on %s', 'wp-seopress' ),
				get_bloginfo( 'name' )
			),
		);

		/**
		 * Filter the protected resource metadata document.
		 *
		 * @since 10.3.0
		 *
		 * @param array $document The document as SEOPress builds it.
		 */
		return (array) apply_filters( 'seopress_mcp_protected_resource_metadata', $document );
	}

	/**
	 * The RFC 8414 authorization server metadata document.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function authorization_server_metadata() {
		$auth_methods = array( 'none', 'client_secret_basic', 'client_secret_post' );

		$document = array(
			'issuer'                                        => self::issuer(),
			'authorization_endpoint'                        => McpOauthConsent::authorize_url(),
			'token_endpoint'                                => self::rest_endpoint_url( self::TOKEN_ROUTE ),
			'registration_endpoint'                         => self::rest_endpoint_url( self::REGISTER_ROUTE ),
			'revocation_endpoint'                           => self::rest_endpoint_url( self::REVOKE_ROUTE ),
			'scopes_supported'                              => array( McpOauthStore::SCOPE ),
			'response_types_supported'                      => array( 'code' ),
			'response_modes_supported'                      => array( 'query' ),
			'grant_types_supported'                         => array( 'authorization_code', 'refresh_token' ),
			'token_endpoint_auth_methods_supported'         => $auth_methods,
			'revocation_endpoint_auth_methods_supported'    => $auth_methods,
			'code_challenge_methods_supported'              => array( 'S256' ),
			'authorization_response_iss_parameter_supported' => true,
		);

		/**
		 * Filter the authorization server metadata document.
		 *
		 * @since 10.3.0
		 *
		 * @param array $document The document as SEOPress builds it.
		 */
		return (array) apply_filters( 'seopress_mcp_authorization_server_metadata', $document );
	}

	/* ---------------------------------------------------------------------
	 * Routing
	 * ------------------------------------------------------------------ */

	/**
	 * Register the OAuth REST routes.
	 *
	 * Every one of them is open to an unauthenticated caller by design: they
	 * are how a client becomes authenticated in the first place. Each callback
	 * does its own gating.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			McpServer::REST_NAMESPACE,
			self::REGISTER_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_register' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			McpServer::REST_NAMESPACE,
			self::TOKEN_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_token' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			McpServer::REST_NAMESPACE,
			self::REVOKE_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_revoke' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			McpServer::REST_NAMESPACE,
			self::PROTECTED_RESOURCE_ROUTE,
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'handle_protected_resource_metadata' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			McpServer::REST_NAMESPACE,
			self::AUTHORIZATION_SERVER_ROUTE,
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'handle_authorization_server_metadata' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Which well-known document, if any, a request path asks for.
	 *
	 * @since 10.3.0
	 *
	 * @param string $path The path component of the request.
	 *
	 * @return string One of "protected_resource", "authorization_server", or "".
	 */
	public static function well_known_document_for_path( $path ) {
		$path = '/' . ltrim( (string) $path, '/' );

		// A WordPress install in a subdirectory only ever sees its own prefix,
		// so the prefix is stripped before the well-known path is matched.
		$home_path = wp_parse_url( home_url(), PHP_URL_PATH );
		$home_path = is_string( $home_path ) ? untrailingslashit( $home_path ) : '';

		if ( '' !== $home_path && '/' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
			$path = substr( $path, strlen( $home_path ) );
		}

		$documents = array(
			self::WELL_KNOWN_PROTECTED_RESOURCE   => 'protected_resource',
			self::WELL_KNOWN_AUTHORIZATION_SERVER => 'authorization_server',
		);

		foreach ( $documents as $base => $document ) {
			if ( $path === $base || 0 === strpos( $path, $base . '/' ) ) {
				return $document;
			}
		}

		return '';
	}

	/**
	 * Serve a well-known document when the current request asks for one.
	 *
	 * RFC 9728 and RFC 8414 both fix their document at a path under
	 * /.well-known, which is above the REST API prefix, so the request is
	 * caught while WordPress is still working out what was asked for.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function maybe_serve_well_known() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$path = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );

		$document = self::well_known_document_for_path( is_string( $path ) ? $path : '' );

		if ( '' === $document || ! self::is_enabled() ) {
			return;
		}

		$payload = 'protected_resource' === $document
			? self::protected_resource_metadata()
			: self::authorization_server_metadata();

		if ( ! headers_sent() ) {
			nocache_headers();
			status_header( 200 );
			header( 'Content-Type: application/json; charset=utf-8' );
			// A discovery document is public by definition, and a client
			// fetching it from a browser needs to be allowed to read the answer.
			header( 'Access-Control-Allow-Origin: *' );
		}

		echo wp_json_encode( $payload ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON document, encoded above.

		$this->finish();
	}

	/**
	 * End the request once a well-known document has been printed.
	 *
	 * Kept as its own method so the serving can be driven by a test that
	 * replaces it, instead of ending the PHP process.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	protected function finish() {
		exit;
	}

	/**
	 * Serve the protected resource metadata over the REST alias.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_protected_resource_metadata() {
		if ( ! self::is_enabled() ) {
			return self::oauth_error( 'invalid_request', self::disabled_message(), 404 );
		}

		return new \WP_REST_Response( self::protected_resource_metadata(), 200 );
	}

	/**
	 * Serve the authorization server metadata over the REST alias.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_authorization_server_metadata() {
		if ( ! self::is_enabled() ) {
			return self::oauth_error( 'invalid_request', self::disabled_message(), 404 );
		}

		return new \WP_REST_Response( self::authorization_server_metadata(), 200 );
	}

	/* ---------------------------------------------------------------------
	 * Error shapes
	 * ------------------------------------------------------------------ */

	/**
	 * An OAuth error response, in the shape RFC 6749 Section 5.2 fixes.
	 *
	 * Not a WP_Error: WordPress would serialize it as {code, message, data},
	 * which no OAuth client reads. The body has to be {error, error_description}.
	 *
	 * @since 10.3.0
	 *
	 * @param string $error       The OAuth error code.
	 * @param string $description A sentence saying what to do about it.
	 * @param int    $status      The HTTP status code.
	 *
	 * @return \WP_REST_Response
	 */
	public static function oauth_error( $error, $description, $status = 400 ) {
		$response = new \WP_REST_Response(
			array(
				'error'             => (string) $error,
				'error_description' => (string) $description,
			),
			(int) $status
		);

		$response->header( 'Cache-Control', 'no-store' );
		$response->header( 'Pragma', 'no-cache' );

		if ( 401 === (int) $status ) {
			$response->header( 'WWW-Authenticate', 'Basic realm="SEOPress MCP"' );
		}

		return $response;
	}

	/**
	 * The sentence every endpoint gives when the MCP server is switched off.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function disabled_message() {
		return __( 'The SEOPress MCP server is switched off on this site, so there is nothing to authorize. Turn on "Expose abilities to AI agents and external tools" in SEO, Advanced, and save.', 'wp-seopress' );
	}

	/**
	 * The sentence every endpoint gives on a site that is not served over HTTPS.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function insecure_transport_message() {
		return sprintf(
			/* translators: %s: the MCP endpoint URL of this site. */
			__( 'OAuth requires HTTPS for the MCP endpoint (%s) and the incoming request. Enable HTTPS for WordPress and configure any trusted reverse proxy to report secure requests correctly.', 'wp-seopress' ),
			McpServer::endpoint_url()
		);
	}

	/**
	 * Every parameter of a request, whether it arrived as form data or as JSON.
	 *
	 * OAuth fixes application/x-www-form-urlencoded for the token endpoint, and
	 * JSON for registration, but clients in the wild send both to both.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return array
	 */
	public static function request_params( $request ) {
		$params = array();

		$json = $request->get_json_params();
		if ( is_array( $json ) ) {
			$params = $json;
		}

		$body = $request->get_body_params();
		if ( is_array( $body ) ) {
			$params = array_merge( $params, $body );
		}

		return $params;
	}

	/**
	 * One string parameter, trimmed, or an empty string.
	 *
	 * @since 10.3.0
	 *
	 * @param array  $params The parameters.
	 * @param string $key    Which one to read.
	 *
	 * @return string
	 */
	public static function param( $params, $key ) {
		if ( ! isset( $params[ $key ] ) || ! is_string( $params[ $key ] ) ) {
			return '';
		}

		return trim( $params[ $key ] );
	}

	/* ---------------------------------------------------------------------
	 * Dynamic client registration, RFC 7591
	 * ------------------------------------------------------------------ */

	/**
	 * Whether a redirect URI may be registered.
	 *
	 * HTTPS, or HTTP on the machine the client runs on. Nothing else: a plain
	 * HTTP redirect on a remote host hands the authorization code to anybody on
	 * the path, and a fragment would be dropped by the browser anyway.
	 *
	 * @since 10.3.0
	 *
	 * @param string $uri The redirect URI the client registered.
	 *
	 * @return bool
	 */
	public static function is_valid_redirect_uri( $uri ) {
		if ( ! is_string( $uri ) || '' === $uri ) {
			return false;
		}

		$parts = wp_parse_url( $uri );

		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
			return false;
		}

		if ( ! empty( $parts['fragment'] ) ) {
			return false;
		}

		// The stored value is compared character for character at authorization
		// time, so a URI that sanitizing would rewrite is refused here rather
		// than stored in a form the client would never send back.
		if ( esc_url_raw( $uri, array( 'http', 'https' ) ) !== $uri ) {
			return false;
		}

		$scheme = strtolower( $parts['scheme'] );

		if ( 'https' === $scheme ) {
			return true;
		}

		return 'http' === $scheme && self::is_loopback_host( strtolower( $parts['host'] ) );
	}

	/**
	 * Handle POST on the registration endpoint.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_register( $request ) {
		if ( ! self::is_enabled() ) {
			return self::oauth_error( 'access_denied', self::disabled_message(), 403 );
		}

		if ( ! self::request_is_secure() ) {
			return self::oauth_error( 'invalid_client_metadata', self::insecure_transport_message(), 400 );
		}

		if ( strlen( $request->get_body() ) > 16384 ) {
			return self::oauth_error( 'invalid_client_metadata', __( 'Client registration must not exceed 16 KB.', 'wp-seopress' ), 413 );
		}

		$params = self::request_params( $request );

		foreach ( array( 'client_name', 'client_uri', 'logo_uri', 'policy_uri', 'tos_uri', 'software_id' ) as $field ) {
			$limit = in_array( $field, array( 'client_name', 'software_id' ), true ) ? 200 : 2048;
			if ( isset( $params[ $field ] ) && ( ! is_string( $params[ $field ] ) || strlen( $params[ $field ] ) > $limit ) ) {
				return self::oauth_error( 'invalid_client_metadata', __( 'Client metadata contains an invalid or oversized field.', 'wp-seopress' ) );
			}
		}
		// Also bound pre-parsed REST requests and form submissions.
		if ( strlen( (string) wp_json_encode( $params ) ) > 16384 ) {
			return self::oauth_error( 'invalid_client_metadata', __( 'Client registration must not exceed 16 KB.', 'wp-seopress' ), 413 );
		}

		$redirect_uris = isset( $params['redirect_uris'] ) ? $params['redirect_uris'] : null;

		if ( ! is_array( $redirect_uris ) || empty( $redirect_uris ) || count( $redirect_uris ) > 10 ) {
			return self::oauth_error(
				'invalid_redirect_uri',
				__( 'A client registration must carry between 1 and 10 redirect URIs in "redirect_uris".', 'wp-seopress' ),
				400
			);
		}

		$clean = array();

		foreach ( $redirect_uris as $uri ) {
			if ( ! is_string( $uri ) || strlen( $uri ) > 2048 || ! self::is_valid_redirect_uri( $uri ) ) {
				return self::oauth_error(
					'invalid_redirect_uri',
					sprintf(
						/* translators: %s: the redirect URI the client sent. */
						__( 'The redirect URI %s cannot be registered. A redirect URI must use https, or http on localhost, and must carry no fragment.', 'wp-seopress' ),
						is_string( $uri ) ? $uri : ''
					),
					400
				);
			}

			$clean[] = $uri;
		}

		$clean = array_values( array_unique( $clean ) );

		$auth_method = self::param( $params, 'token_endpoint_auth_method' );
		if ( '' === $auth_method ) {
			$auth_method = 'client_secret_basic';
		}

		if ( ! in_array( $auth_method, array( 'none', 'client_secret_basic', 'client_secret_post' ), true ) ) {
			return self::oauth_error(
				'invalid_client_metadata',
				sprintf(
					/* translators: %s: the authentication method the client asked for. */
					__( 'This server does not support the token endpoint authentication method %s.', 'wp-seopress' ),
					$auth_method
				),
				400
			);
		}

		$grant_types = isset( $params['grant_types'] ) && is_array( $params['grant_types'] )
			? $params['grant_types']
			: array( 'authorization_code', 'refresh_token' );

		foreach ( $grant_types as $grant_type ) {
			if ( ! in_array( $grant_type, array( 'authorization_code', 'refresh_token' ), true ) ) {
				return self::oauth_error(
					'invalid_client_metadata',
					sprintf(
						/* translators: %s: the grant type the client asked for. */
						__( 'This server does not support the %s grant type.', 'wp-seopress' ),
						is_string( $grant_type ) ? $grant_type : ''
					),
					400
				);
			}
		}

		$address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( true !== McpOauthStore::allow_registration( $address ) ) {
			$response = self::oauth_error( 'temporarily_unavailable', __( 'Too many client registrations. Please wait a minute and try again.', 'wp-seopress' ), 429 );
			$response->header( 'Retry-After', '60' );
			return $response;
		}

		$client_id  = 'seopress-' . McpOauthStore::random( 16 );
		$has_secret = 'none' !== $auth_method;
		$secret     = $has_secret ? McpOauthStore::random( 32 ) : '';

		$client_name = self::param( $params, 'client_name' );
		if ( '' === $client_name ) {
			$client_name = __( 'Unnamed client', 'wp-seopress' );
		}

		$client = array(
			'client_id'                  => $client_id,
			'client_name'                => sanitize_text_field( $client_name ),
			'client_uri'                 => esc_url_raw( self::param( $params, 'client_uri' ), array( 'http', 'https' ) ),
			'logo_uri'                   => esc_url_raw( self::param( $params, 'logo_uri' ), array( 'http', 'https' ) ),
			'policy_uri'                 => esc_url_raw( self::param( $params, 'policy_uri' ), array( 'http', 'https' ) ),
			'tos_uri'                    => esc_url_raw( self::param( $params, 'tos_uri' ), array( 'http', 'https' ) ),
			'software_id'                => sanitize_text_field( self::param( $params, 'software_id' ) ),
			'redirect_uris'              => $clean,
			'grant_types'                => array_values( array_unique( array_map( 'strval', $grant_types ) ) ),
			'response_types'             => array( 'code' ),
			'token_endpoint_auth_method' => $auth_method,
			'scope'                      => McpOauthStore::SCOPE,
			'secret_hash'                => $has_secret ? McpOauthStore::hash_secret( $secret ) : '',
			'created'                    => time(),
			'last_used'                  => 0,
			'connections'                => array(),
		);

		if ( true !== McpOauthStore::put_client( $client ) ) {
			return self::oauth_error( 'temporarily_unavailable', __( 'The client registry is full or busy. Revoke unused connections in SEO, Advanced, MCP, then try again.', 'wp-seopress' ), 503 );
		}

		$body = array(
			'client_id'                  => $client['client_id'],
			'client_id_issued_at'        => $client['created'],
			'client_name'                => $client['client_name'],
			'redirect_uris'              => $client['redirect_uris'],
			'grant_types'                => $client['grant_types'],
			'response_types'             => $client['response_types'],
			'token_endpoint_auth_method' => $client['token_endpoint_auth_method'],
			'scope'                      => $client['scope'],
		);

		if ( $has_secret ) {
			// Returned once, here, and never again: only its hash was stored.
			$body['client_secret']            = $secret;
			$body['client_secret_expires_at'] = 0;
		}

		$response = new \WP_REST_Response( $body, 201 );
		$response->header( 'Cache-Control', 'no-store' );
		$response->header( 'Pragma', 'no-cache' );

		return $response;
	}

	/* ---------------------------------------------------------------------
	 * Token endpoint
	 * ------------------------------------------------------------------ */

	/**
	 * Identify the client behind a token or revocation request.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 * @param array            $params  The request parameters.
	 *
	 * @return array|\WP_REST_Response The client record, or the refusal to send back.
	 */
	public static function authenticate_client( $request, $params ) {
		$client_id     = self::param( $params, 'client_id' );
		$client_secret = self::param( $params, 'client_secret' );

		$authorization = (string) $request->get_header( 'authorization' );

		if ( 1 === preg_match( '/^Basic\s+(.+)$/i', $authorization, $matches ) ) {
			$decoded = base64_decode( $matches[1], true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- HTTP Basic credentials, not obfuscation.

			if ( is_string( $decoded ) && false !== strpos( $decoded, ':' ) ) {
				list( $basic_id, $basic_secret ) = explode( ':', $decoded, 2 );

				$client_id     = urldecode( $basic_id );
				$client_secret = urldecode( $basic_secret );
			}
		}

		if ( '' === $client_id ) {
			return self::oauth_error(
				'invalid_client',
				__( 'This request carries no client identifier.', 'wp-seopress' ),
				401
			);
		}

		$client = McpOauthStore::get_client( $client_id );

		if ( null === $client ) {
			return self::oauth_error(
				'invalid_client',
				__( 'This client is not registered on this site. Register again, then start a new authorization.', 'wp-seopress' ),
				401
			);
		}

		if ( ! empty( $client['secret_hash'] ) ) {
			if ( '' === $client_secret || ! McpOauthStore::verify_secret( $client_secret, $client['secret_hash'] ) ) {
				return self::oauth_error(
					'invalid_client',
					__( 'The client secret does not match the one this client registered with.', 'wp-seopress' ),
					401
				);
			}
		}

		return $client;
	}

	/**
	 * Handle POST on the token endpoint.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_token( $request ) {
		if ( ! self::is_enabled() ) {
			return self::oauth_error( 'invalid_request', self::disabled_message(), 403 );
		}

		if ( ! self::request_is_secure() ) {
			return self::oauth_error( 'invalid_request', self::insecure_transport_message(), 400 );
		}

		$params = self::request_params( $request );

		$client = self::authenticate_client( $request, $params );
		if ( $client instanceof \WP_REST_Response ) {
			return $client;
		}

		$grant_type = self::param( $params, 'grant_type' );

		if ( 'authorization_code' === $grant_type ) {
			return $this->grant_authorization_code( $client, $params );
		}

		if ( 'refresh_token' === $grant_type ) {
			return $this->grant_refresh_token( $client, $params );
		}

		return self::oauth_error(
			'unsupported_grant_type',
			__( 'This server issues tokens for the authorization_code and refresh_token grants only.', 'wp-seopress' ),
			400
		);
	}

	/**
	 * Whether a PKCE code verifier answers a code challenge.
	 *
	 * RFC 7636 Section 4.6: the challenge is the base64url of the SHA-256 of
	 * the verifier, with no padding. Compared with hash_equals(), so the
	 * comparison does not leak where two values start to differ.
	 *
	 * @since 10.3.0
	 *
	 * @param string $verifier  The code verifier the client sent.
	 * @param string $challenge The code challenge stored with the code.
	 *
	 * @return bool
	 */
	public static function verify_pkce( $verifier, $challenge ) {
		if ( ! is_string( $verifier ) || ! is_string( $challenge ) || '' === $challenge ) {
			return false;
		}

		if ( 1 !== preg_match( '/^[A-Za-z0-9\-._~]{43,128}$/', $verifier ) ) {
			return false;
		}

		$computed = rtrim( strtr( base64_encode( hash( 'sha256', $verifier, true ) ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- base64url encoding required by RFC 7636.

		return hash_equals( $challenge, $computed );
	}

	/**
	 * Exchange an authorization code for a pair of tokens.
	 *
	 * @since 10.3.0
	 *
	 * @param array $client The authenticated client.
	 * @param array $params The request parameters.
	 *
	 * @return \WP_REST_Response
	 */
	protected function grant_authorization_code( $client, $params ) {
		$code = self::param( $params, 'code' );

		$payload = McpOauthStore::consume_authorization_code( $code );

		if ( null === $payload ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'This authorization code is unknown, already used, or older than a minute. Start the authorization again.', 'wp-seopress' ),
				400
			);
		}

		if ( ! isset( $payload['client_id'] ) || $payload['client_id'] !== $client['client_id'] ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'This authorization code was issued to a different client.', 'wp-seopress' ),
				400
			);
		}

		$redirect_uri = self::param( $params, 'redirect_uri' );

		if ( ! isset( $payload['redirect_uri'] ) || $payload['redirect_uri'] !== $redirect_uri ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'The redirect URI does not match the one the authorization was granted for.', 'wp-seopress' ),
				400
			);
		}

		if ( ! self::verify_pkce( self::param( $params, 'code_verifier' ), isset( $payload['code_challenge'] ) ? $payload['code_challenge'] : '' ) ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'The PKCE code verifier does not answer the code challenge this authorization was started with.', 'wp-seopress' ),
				400
			);
		}

		$requested_resource = self::param( $params, 'resource' );

		if ( '' !== $requested_resource && ! self::resources_match( $requested_resource, self::canonical_resource() ) ) {
			return self::oauth_error(
				'invalid_target',
				sprintf(
					/* translators: %s: the canonical URI of this site's MCP server. */
					__( 'This server only issues tokens for %s.', 'wp-seopress' ),
					self::canonical_resource()
				),
				400
			);
		}

		$user_id = isset( $payload['user_id'] ) ? (int) $payload['user_id'] : 0;

		if ( $user_id <= 0 || false === get_userdata( $user_id ) ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'The WordPress account this authorization was granted by no longer exists.', 'wp-seopress' ),
				400
			);
		}

		// The scope travels on the authorization code, because it is the choice
		// the person made on the consent screen and nothing the client sends
		// may widen it afterwards.
		$issued = McpOauthStore::issue_tokens(
			$user_id,
			$client['client_id'],
			isset( $client['client_name'] ) ? $client['client_name'] : '',
			self::canonical_resource(),
			isset( $payload['scope'] ) ? $payload['scope'] : McpOauthStore::SCOPE
		);

		return self::token_response( $issued );
	}

	/**
	 * Exchange a refresh token for a fresh pair, rotating the refresh token.
	 *
	 * @since 10.3.0
	 *
	 * @param array $client The authenticated client.
	 * @param array $params The request parameters.
	 *
	 * @return \WP_REST_Response
	 */
	protected function grant_refresh_token( $client, $params ) {
		$found = McpOauthStore::find_refresh_token( self::param( $params, 'refresh_token' ) );

		if ( null === $found ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'This refresh token is unknown. It may have been rotated, revoked, or issued by another site.', 'wp-seopress' ),
				400
			);
		}

		$record = $found['record'];

		if ( ! isset( $record['client_id'] ) || $record['client_id'] !== $client['client_id'] ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'This refresh token was issued to a different client.', 'wp-seopress' ),
				400
			);
		}

		$expires = isset( $record['refresh_expires'] ) ? (int) $record['refresh_expires'] : 0;

		if ( $expires <= 0 || $expires <= time() ) {
			McpOauthStore::revoke_connection( $found['user_id'], $record['uuid'] );

			return self::oauth_error(
				'invalid_grant',
				__( 'This refresh token has expired. Connect this site again from your client.', 'wp-seopress' ),
				400
			);
		}

		if ( ! self::resources_match( isset( $record['resource'] ) ? $record['resource'] : '', self::canonical_resource() ) ) {
			return self::oauth_error(
				'invalid_target',
				sprintf(
					/* translators: %s: the canonical URI of this site's MCP server. */
					__( 'This refresh token was issued for another resource, not %s.', 'wp-seopress' ),
					self::canonical_resource()
				),
				400
			);
		}

		$requested_resource = self::param( $params, 'resource' );

		if ( '' !== $requested_resource && ! self::resources_match( $requested_resource, self::canonical_resource() ) ) {
			return self::oauth_error(
				'invalid_target',
				sprintf(
					/* translators: %s: the canonical URI of this site's MCP server. */
					__( 'This server only issues tokens for %s.', 'wp-seopress' ),
					self::canonical_resource()
				),
				400
			);
		}

		if ( false === get_userdata( $found['user_id'] ) ) {
			McpOauthStore::revoke_connection( $found['user_id'], $record['uuid'] );

			return self::oauth_error(
				'invalid_grant',
				__( 'The WordPress account this token belongs to no longer exists.', 'wp-seopress' ),
				400
			);
		}

		$issued = McpOauthStore::rotate_tokens( $found['user_id'], $record['uuid'], self::param( $params, 'refresh_token' ) );

		if ( null === $issued ) {
			return self::oauth_error(
				'invalid_grant',
				__( 'This connection was revoked, or its refresh token was already used. Connect again to authorize a new connection.', 'wp-seopress' ),
				400
			);
		}

		return self::token_response( $issued );
	}

	/**
	 * The RFC 6749 Section 5.1 successful token response.
	 *
	 * @since 10.3.0
	 *
	 * @param array $issued What the store handed back.
	 *
	 * @return \WP_REST_Response
	 */
	protected static function token_response( $issued ) {
		if ( null === $issued ) {
			return self::oauth_error( 'temporarily_unavailable', __( 'The connection could not be saved. Please reconnect and try again.', 'wp-seopress' ), 503 );
		}
		$response = new \WP_REST_Response(
			array(
				'access_token'  => $issued['access_token'],
				'token_type'    => 'Bearer',
				'expires_in'    => $issued['expires_in'],
				'refresh_token' => $issued['refresh_token'],
				'scope'         => isset( $issued['record']['scope'] )
					? (string) $issued['record']['scope']
					: McpOauthStore::SCOPE,
			),
			200
		);

		$response->header( 'Cache-Control', 'no-store' );
		$response->header( 'Pragma', 'no-cache' );

		return $response;
	}

	/* ---------------------------------------------------------------------
	 * Revocation endpoint, RFC 7009
	 * ------------------------------------------------------------------ */

	/**
	 * Handle POST on the revocation endpoint.
	 *
	 * RFC 7009 Section 2.2: a token that is already invalid is not an error, so
	 * this answers 200 either way and never says whether the token existed.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_revoke( $request ) {
		if ( ! self::is_enabled() ) {
			return self::oauth_error( 'invalid_request', self::disabled_message(), 403 );
		}

		if ( ! self::request_is_secure() ) {
			return self::oauth_error( 'invalid_request', self::insecure_transport_message(), 400 );
		}

		$params = self::request_params( $request );

		$client = self::authenticate_client( $request, $params );
		if ( $client instanceof \WP_REST_Response ) {
			return $client;
		}

		$token = self::param( $params, 'token' );

		if ( '' !== $token ) {
			$found = McpOauthStore::find_access_token( $token );

			if ( null === $found ) {
				$found = McpOauthStore::find_refresh_token( $token );
			}

			// A client may only revoke what was issued to it.
			if ( null !== $found
				&& isset( $found['record']['client_id'] )
				&& $found['record']['client_id'] === $client['client_id'] ) {
				if ( null === McpOauthStore::revoke_connection( $found['user_id'], $found['record']['uuid'] ) ) {
					return self::oauth_error( 'temporarily_unavailable', __( 'The connection could not be revoked. Please retry.', 'wp-seopress' ), 503 );
				}
			}
		}

		$response = new \WP_REST_Response( null, 200 );
		$response->header( 'Cache-Control', 'no-store' );

		return $response;
	}

	/* ---------------------------------------------------------------------
	 * Resource server: bearer token validation
	 * ------------------------------------------------------------------ */

	/**
	 * The bearer token of a request, if it carries one.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return string Empty when the request is not bearer authenticated.
	 */
	public static function bearer_token( $request ) {
		$authorization = (string) $request->get_header( 'authorization' );

		if ( 1 !== preg_match( '/^Bearer\s+(\S+)\s*$/i', $authorization, $matches ) ) {
			return '';
		}

		return $matches[1];
	}

	/**
	 * Validate a bearer token and say which WordPress user it acts as.
	 *
	 * Four things have to hold, and every one of them is a refusal on its own:
	 * the token exists, it has not expired, it was issued for this MCP server
	 * and no other, and the account it belongs to still exists. The audience
	 * check is the one the MCP specification is most explicit about: a server
	 * must never accept a token that was issued for somebody else.
	 *
	 * @since 10.3.0
	 *
	 * @param string $token The bearer token presented by the client.
	 *
	 * @return int|\WP_Error The user id, or the refusal.
	 */
	public static function authenticate_bearer( $token ) {
		if ( ! self::is_enabled() ) {
			return new \WP_Error(
				'seopress_mcp_oauth_disabled',
				self::disabled_message(),
				array( 'status' => 401 )
			);
		}

		if ( ! self::request_is_secure() ) {
			return new \WP_Error( 'seopress_mcp_insecure_transport', self::insecure_transport_message(), array( 'status' => 401 ) );
		}

		$found = McpOauthStore::find_access_token( $token );

		if ( null === $found ) {
			return new \WP_Error(
				'seopress_mcp_invalid_token',
				__( 'This access token is not valid on this site.', 'wp-seopress' ),
				array( 'status' => 401 )
			);
		}

		$record = $found['record'];

		$expires = isset( $record['access_expires'] ) ? (int) $record['access_expires'] : 0;

		if ( $expires <= 0 || $expires <= time() ) {
			return new \WP_Error(
				'seopress_mcp_expired_token',
				__( 'This access token has expired. Refresh it with the refresh token, or connect again.', 'wp-seopress' ),
				array( 'status' => 401 )
			);
		}

		if ( ! self::resources_match( isset( $record['resource'] ) ? $record['resource'] : '', self::canonical_resource() ) ) {
			return new \WP_Error(
				'seopress_mcp_invalid_audience',
				__( 'This access token was issued for another resource, so this MCP server will not accept it.', 'wp-seopress' ),
				array( 'status' => 401 )
			);
		}

		if ( false === get_userdata( $found['user_id'] ) ) {
			return new \WP_Error(
				'seopress_mcp_invalid_token',
				__( 'The WordPress account this access token belongs to no longer exists.', 'wp-seopress' ),
				array( 'status' => 401 )
			);
		}

		McpOauthStore::record_usage( $found['user_id'], $record['uuid'] );

		// What this token was granted decides what the rest of the request may
		// see and call, so publish it before anything reads the tool list.
		McpExposure::set_granted_scope(
			isset( $record['scope'] ) ? $record['scope'] : McpOauthStore::SCOPE
		);

		return (int) $found['user_id'];
	}

	/**
	 * The WWW-Authenticate value a 401 from the MCP endpoint must carry.
	 *
	 * RFC 9728 Section 5.1: the challenge points the client at the protected
	 * resource metadata, which is how it finds the authorization server without
	 * being configured by hand.
	 *
	 * @since 10.3.0
	 *
	 * @param string $error       The OAuth error code, empty for a bare challenge.
	 * @param string $description The human readable reason, empty to omit it.
	 *
	 * @return string
	 */
	public static function www_authenticate_value( $error = '', $description = '' ) {
		$parts = array( 'realm="SEOPress MCP"' );

		if ( '' !== $error ) {
			$parts[] = 'error="' . self::quote_safe( $error ) . '"';
		}

		if ( '' !== $description ) {
			$parts[] = 'error_description="' . self::quote_safe( $description ) . '"';
		}

		$parts[] = 'resource_metadata="' . self::quote_safe( self::protected_resource_metadata_url() ) . '"';

		return 'Bearer ' . implode( ', ', $parts );
	}

	/**
	 * Make a value safe to put inside a quoted HTTP header parameter.
	 *
	 * @since 10.3.0
	 *
	 * @param string $value The value.
	 *
	 * @return string
	 */
	protected static function quote_safe( $value ) {
		$value = str_replace( array( "\r", "\n", '"', '\\' ), ' ', (string) $value );

		return trim( preg_replace( '/\s+/', ' ', $value ) );
	}
}

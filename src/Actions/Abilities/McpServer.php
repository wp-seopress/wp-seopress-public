<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Serve the registered SEOPress abilities to MCP clients.
 *
 * Implements the Model Context Protocol "Streamable HTTP" transport
 * (protocol revision 2025-06-18) on a single WordPress REST route:
 *
 *     POST /wp-json/seopress/mcp/v1
 *
 * Only the JSON-RPC methods needed to expose tools are implemented:
 * initialize, notifications/initialized, tools/list, tools/call and ping.
 * There is no SSE stream, no session, no resources and no prompts, so GET
 * on the route answers 405 as the specification allows.
 *
 * Authentication is plain WordPress REST authentication, which means
 * Application Passwords work without any extra code. Authorization is never
 * decided here: every tool call is routed back through the ability's own
 * permission_callback.
 *
 * @since 10.3.0
 */
class McpServer implements ExecuteHooks {

	/**
	 * REST namespace of the MCP transport.
	 *
	 * Kept out of the plugin's own "seopress/v1" namespace: that one is the
	 * internal admin API and its route index should not advertise a remote
	 * control endpoint.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'seopress/mcp';

	/**
	 * REST route of the MCP transport, inside self::REST_NAMESPACE.
	 *
	 * The version sits on the endpoint itself so a future protocol revision
	 * can be served side by side on /v2 without moving the namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE = '/v1';

	/**
	 * Latest MCP protocol revision this server implements.
	 *
	 * @var string
	 */
	const LATEST_PROTOCOL_VERSION = '2025-06-18';

	/**
	 * Revision assumed when the client sends no MCP-Protocol-Version header.
	 *
	 * Mandated by the specification for backwards compatibility.
	 *
	 * @var string
	 */
	const DEFAULT_PROTOCOL_VERSION = '2025-03-26';

	/**
	 * Separator between the ability namespace and the ability slug in a tool name.
	 *
	 * Ability names match ^[a-z0-9-]+/[a-z0-9-]+$, so they never contain an
	 * underscore. Swapping the forward slash (which MCP clients reject in a
	 * tool name) for a double underscore is therefore lossless and reversible.
	 *
	 * @var string
	 */
	const TOOL_NAME_SEPARATOR = '__';

	/**
	 * Protocol revisions accepted in the MCP-Protocol-Version header.
	 *
	 * @since 10.3.0
	 *
	 * @return string[]
	 */
	public static function supported_protocol_versions() {
		return array( '2025-06-18', '2025-03-26', '2024-11-05' );
	}

	/**
	 * Absolute URL of the MCP endpoint, for the settings screen and the docs.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function endpoint_url() {
		return rest_url( self::REST_NAMESPACE . self::REST_ROUTE );
	}

	/**
	 * Internal REST route path, as WP_REST_Request::get_route() reports it.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function route_path() {
		return '/' . self::REST_NAMESPACE . self::REST_ROUTE;
	}

	/**
	 * Register the hooks.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Without the Abilities API there is nothing to serve as a tool.
		if ( ! seopress_abilities_api_available() ) {
			return;
		}

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'rest_request_before_callbacks', array( $this, 'catch_invalid_json' ), 10, 3 );
		add_filter( 'rest_request_after_callbacks', array( $this, 'add_authenticate_challenge' ), 10, 3 );
		add_filter( 'rest_pre_serve_request', array( $this, 'serve_empty_accepted_body' ), 10, 3 );
	}

	/**
	 * Register the single MCP route.
	 *
	 * @since 10.3.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_post' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
				array(
					'methods'             => 'GET, DELETE',
					'callback'            => array( $this, 'handle_unsupported_method' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			)
		);
	}

	/**
	 * The checks that depend on the site, not on who is calling.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return true|\WP_Error
	 */
	protected function check_site_gate( $request ) {
		if ( ! seopress_abilities_api_rest_enabled() ) {
			return new \WP_Error(
				'seopress_mcp_disabled',
				__( 'The SEOPress MCP endpoint is disabled. Enable it in SEO, Advanced, Abilities API.', 'wp-seopress' ),
				array( 'status' => 403 )
			);
		}

		$origin = $request->get_header( 'origin' );
		if ( null !== $origin && '' !== $origin && ! $this->is_allowed_origin( $origin ) ) {
			return new \WP_Error(
				'seopress_mcp_invalid_origin',
				__( 'The Origin header of this request is not allowed.', 'wp-seopress' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Gate the endpoint: opt-in option, Origin, protocol revision, then identity.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return true|\WP_Error
	 */
	public function check_permission( $request ) {
		$site = $this->check_site_gate( $request );

		if ( is_wp_error( $site ) ) {
			return $site;
		}

		// Trimmed, because a header a proxy rewrote to a single space means the
		// client sent nothing, and the spec asks a server with no revision to
		// assume the older default rather than refuse the call.
		$protocol_version = trim( (string) $request->get_header( 'mcp-protocol-version' ) );
		if ( '' === $protocol_version ) {
			$protocol_version = self::DEFAULT_PROTOCOL_VERSION;
		}

		// Only a malformed revision is refused, never merely a newer one.
		//
		// Measured against the real claude.ai connector: it probes with a
		// revision newer than anything this release knows about. Refusing that
		// with 400 answered before authentication did, so the client never saw
		// the 401 that carries the OAuth challenge, and the connector could not
		// be set up at all. A server that fails closed on a revision it has
		// simply not heard of breaks on every future MCP release.
		//
		// The spec settles this at `initialize`, where the server answers with
		// a revision it does support. That negotiation is what handles an
		// unknown revision, not a transport-level refusal.
		if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $protocol_version ) ) {
			return new \WP_Error(
				'seopress_mcp_unsupported_protocol_version',
				sprintf(
					/* translators: %s: the MCP protocol revision sent by the client. */
					__( 'Malformed MCP protocol version: %s.', 'wp-seopress' ),
					$protocol_version
				),
				array( 'status' => 400 )
			);
		}

		// Start every request with no scope in hand. The scope is per token, and
		// a static that outlived the request it was set in would hand the next
		// caller whatever the previous one was granted.
		McpExposure::set_granted_scope( null );

		// An OAuth bearer token, when there is one, decides on its own: it names
		// the WordPress user the rest of this request runs as. Anything the
		// request was already authenticated as is replaced, so a stray cookie
		// can never widen what a token was issued for.
		$bearer = McpOauth::bearer_token( $request );

		if ( '' !== $bearer ) {
			$user_id = McpOauth::authenticate_bearer( $bearer );

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}

			wp_set_current_user( $user_id );

			return true;
		}

		// No bearer token: plain WordPress authentication, which is what makes
		// Application Passwords work for Claude Code, Cursor and anything else
		// that can send an Authorization: Basic header.
		if ( ! is_user_logged_in() ) {
			return new \WP_Error(
				'seopress_mcp_unauthorized',
				__( 'You must be authenticated to use the SEOPress MCP endpoint.', 'wp-seopress' ),
				array( 'status' => 401 )
			);
		}

		return true;
	}

	/**
	 * Put the RFC 9728 challenge on every 401 this endpoint answers with.
	 *
	 * RFC 9728 Section 5.1 requires the WWW-Authenticate header of a 401 to
	 * carry resource_metadata, the URL of the protected resource metadata
	 * document. That header is the whole of discovery: without it a client that
	 * was only given the MCP URL has no way to find out where to authenticate,
	 * which is exactly the situation a custom connector on claude.ai starts in.
	 *
	 * Hooked on rest_request_after_callbacks rather than rest_post_dispatch so
	 * it runs inside WP_REST_Server::dispatch(), which is what both a real
	 * request and rest_do_request() go through.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Response|\WP_Error|mixed $response The current response.
	 * @param array                             $handler  The matched route handler.
	 * @param \WP_REST_Request                  $request  The incoming request.
	 *
	 * @return \WP_REST_Response|\WP_Error|mixed
	 */
	public function add_authenticate_challenge( $response, $handler, $request ) {
		if ( ! $request instanceof \WP_REST_Request || self::route_path() !== $request->get_route() ) {
			return $response;
		}

		$error_code = '';

		if ( is_wp_error( $response ) ) {
			$data = $response->get_error_data();

			if ( ! is_array( $data ) || ! isset( $data['status'] ) || 401 !== (int) $data['status'] ) {
				return $response;
			}

			$error_code = $response->get_error_code();
			$message    = $response->get_error_message();
			$response   = rest_convert_error_to_response( $response );
		} elseif ( $response instanceof \WP_REST_Response && 401 === $response->get_status() ) {
			$message = '';
		} else {
			return $response;
		}

		if ( ! $response instanceof \WP_REST_Response ) {
			return $response;
		}

		$response->header(
			'WWW-Authenticate',
			McpOauth::www_authenticate_value(
				'seopress_mcp_unauthorized' === $error_code ? '' : 'invalid_token',
				$message
			)
		);

		return $response;
	}

	/**
	 * Whether an Origin header may talk to this endpoint.
	 *
	 * The specification requires Origin validation to prevent DNS rebinding:
	 * a page on another host must not be able to drive the endpoint through a
	 * logged-in browser.
	 *
	 * @since 10.3.0
	 *
	 * @param string $origin The raw Origin header value.
	 *
	 * @return bool
	 */
	protected function is_allowed_origin( $origin ) {
		$normalized = $this->normalize_origin( $origin );
		if ( '' === $normalized ) {
			return false;
		}

		$allowed = array( home_url(), site_url() );

		/**
		 * Filter the origins allowed to reach the SEOPress MCP endpoint.
		 *
		 * @since 10.3.0
		 *
		 * @param string[] $allowed Allowed origins, as absolute URLs.
		 */
		$allowed = apply_filters( 'seopress_mcp_allowed_origins', $allowed );

		if ( ! is_array( $allowed ) ) {
			return false;
		}

		foreach ( $allowed as $candidate ) {
			if ( ! is_string( $candidate ) ) {
				continue;
			}

			if ( $normalized === $this->normalize_origin( $candidate ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Reduce a URL to a comparable "scheme://host[:port]" string.
	 *
	 * @since 10.3.0
	 *
	 * @param string $url The URL to normalize.
	 *
	 * @return string Empty string when the URL carries no usable host.
	 */
	protected function normalize_origin( $url ) {
		$parts = wp_parse_url( $url );

		if ( ! is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['scheme'] ) ) {
			return '';
		}

		$normalized = strtolower( $parts['scheme'] ) . '://' . strtolower( $parts['host'] );

		$default_ports = array(
			'http'  => 80,
			'https' => 443,
		);

		$scheme = strtolower( $parts['scheme'] );
		if ( ! empty( $parts['port'] )
			&& ( ! isset( $default_ports[ $scheme ] ) || (int) $parts['port'] !== $default_ports[ $scheme ] ) ) {
			$normalized .= ':' . (int) $parts['port'];
		}

		return $normalized;
	}

	/**
	 * Answer 405 on the HTTP methods this transport does not offer.
	 *
	 * The specification allows a server with no SSE stream to refuse GET.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Error
	 */
	public function handle_unsupported_method() {
		// Only an authenticated client reaches this: an anonymous GET is
		// refused by check_permission() with the 401 that carries the OAuth
		// challenge, and that challenge is what a browser connector bootstraps
		// its whole authorization from. Measured against a real ChatGPT
		// connector: answering 405 to an anonymous GET, correct though it is
		// for a server with no SSE stream, left it with nothing to discover
		// and it never started the flow at all.
		return new \WP_Error(
			'seopress_mcp_method_not_allowed',
			__( 'This MCP endpoint only accepts POST requests.', 'wp-seopress' ),
			array( 'status' => 405 )
		);
	}

	/**
	 * Handle one JSON-RPC message posted to the endpoint.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_REST_Request $request The incoming request.
	 *
	 * @return \WP_REST_Response
	 */
	public function handle_post( $request ) {
		$message = $request->get_json_params();

		if ( ! is_array( $message ) || empty( $message ) ) {
			return $this->rpc_error( null, -32700, __( 'Parse error: the request body is not a JSON object.', 'wp-seopress' ) );
		}

		// A JSON array is a JSON-RPC batch. Batching was removed from MCP in
		// revision 2025-06-18, so one message per POST is the contract.
		if ( array_keys( $message ) === range( 0, count( $message ) - 1 ) ) {
			return $this->rpc_error( null, -32600, __( 'Invalid Request: batched messages are not supported.', 'wp-seopress' ) );
		}

		if ( ! isset( $message['jsonrpc'] ) || '2.0' !== $message['jsonrpc'] ) {
			return $this->rpc_error( null, -32600, __( 'Invalid Request: the "jsonrpc" member must be "2.0".', 'wp-seopress' ) );
		}

		$has_id = array_key_exists( 'id', $message );

		// A notification carries no id; a response carries an id plus a result
		// or an error. Both are accepted and acknowledged with 202, no body.
		$is_response = $has_id && ( array_key_exists( 'result', $message ) || array_key_exists( 'error', $message ) );

		if ( ! $has_id || $is_response ) {
			return new \WP_REST_Response( null, 202 );
		}

		$id = $message['id'];
		if ( null === $id || ( ! is_string( $id ) && ! is_int( $id ) ) ) {
			return $this->rpc_error( null, -32600, __( 'Invalid Request: "id" must be a string or a number.', 'wp-seopress' ) );
		}

		if ( ! isset( $message['method'] ) || ! is_string( $message['method'] ) || '' === $message['method'] ) {
			return $this->rpc_error( $id, -32600, __( 'Invalid Request: "method" is missing.', 'wp-seopress' ) );
		}

		$params = array();
		if ( array_key_exists( 'params', $message ) ) {
			if ( ! is_array( $message['params'] ) ) {
				return $this->rpc_error( $id, -32602, __( 'Invalid params: "params" must be an object.', 'wp-seopress' ) );
			}

			$params = $message['params'];
		}

		switch ( $message['method'] ) {
			case 'initialize':
				return $this->rpc_result( $id, $this->handle_initialize( $params ) );

			case 'ping':
				return $this->rpc_result( $id, new \stdClass() );

			case 'tools/list':
				return $this->rpc_result( $id, array( 'tools' => $this->list_tools() ) );

			case 'tools/call':
				return $this->handle_tools_call( $id, $params );
		}

		return $this->rpc_error(
			$id,
			-32601,
			sprintf(
				/* translators: %s: the JSON-RPC method name sent by the client. */
				__( 'Method not found: %s.', 'wp-seopress' ),
				$message['method']
			)
		);
	}

	/**
	 * Build the "initialize" result.
	 *
	 * @since 10.3.0
	 *
	 * @param array $params The JSON-RPC params member.
	 *
	 * @return array
	 */
	protected function handle_initialize( $params ) {
		$requested = isset( $params['protocolVersion'] ) && is_string( $params['protocolVersion'] )
			? $params['protocolVersion']
			: '';

		$negotiated = in_array( $requested, self::supported_protocol_versions(), true )
			? $requested
			: self::LATEST_PROTOCOL_VERSION;

		return array(
			'protocolVersion' => $negotiated,
			'capabilities'    => array(
				'tools' => array( 'listChanged' => false ),
			),
			'serverInfo'      => self::server_info(),
		);
	}

	/**
	 * Who this server says it is.
	 *
	 * The icon, the description and the site address are additive metadata:
	 * MCP defines them on Implementation as of revision 2025-11-25, and a
	 * client that has not heard of them ignores fields it does not know. They
	 * are served whatever revision is negotiated rather than gated behind one
	 * this server has not verified it complies with in full.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function server_info() {
		$info = array(
			'name'        => 'seopress',
			'title'       => 'SEOPress',
			'version'     => defined( 'SEOPRESS_VERSION' ) ? SEOPRESS_VERSION : '',
			'description' => __( 'The SEO tools of this WordPress site, as an AI client can use them.', 'wp-seopress' ),
			'websiteUrl'  => home_url( '/' ),
		);

		if ( defined( 'SEOPRESS_URL_ASSETS' ) ) {
			// On the scheme of the endpoint the client is already talking to.
			// The asset URL is built from the request that happens to be
			// running, so on a site behind a TLS terminating proxy it can come
			// out as http while the server answers on https, and a client
			// would refuse to load it.
			$scheme = wp_parse_url( self::endpoint_url(), PHP_URL_SCHEME );

			$info['icons'] = array(
				array(
					'src'      => set_url_scheme(
						SEOPRESS_URL_ASSETS . '/img/logo-seopress.svg',
						'https' === $scheme ? 'https' : 'http'
					),
					'mimeType' => 'image/svg+xml',
					'sizes'    => array( 'any' ),
				),
			);
		}

		return $info;
	}

	/**
	 * Build the "tools/list" payload from the abilities that opted in.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected function list_tools() {
		$tools = array();

		foreach ( $this->get_exposed_abilities() as $ability ) {
			$tool = array(
				'name'        => $this->tool_name( $ability->get_name() ),
				'title'       => $ability->get_label(),
				'description' => $ability->get_description(),
				'inputSchema' => $this->prepare_input_schema( $ability->get_input_schema() ),
			);

			$output_schema = self::output_schema_for_mcp( $ability->get_output_schema() );
			if ( ! empty( $output_schema ) ) {
				$tool['outputSchema'] = $this->schema_to_object( $output_schema );
			}

			$annotations = $this->tool_annotations( $ability );
			if ( ! empty( $annotations ) ) {
				$tool['annotations'] = $annotations;
			}

			$tools[] = $tool;
		}

		return $tools;
	}

	/**
	 * The abilities that explicitly opted in to MCP exposure as a tool.
	 *
	 * Nothing is exposed implicitly, on two levels. An ability has to carry
	 * meta.mcp.public === true and meta.mcp.type === 'tool', and its namespace
	 * has to be one McpExposure allows: the Abilities API is a site-wide
	 * registry, so serving whatever it holds would let a SEOPress setting hand
	 * out another plugin's write tools.
	 *
	 * @since 10.3.0
	 *
	 * @return \WP_Ability[] Keyed by ability name.
	 */
	protected function get_exposed_abilities() {
		return McpExposure::exposed_abilities();
	}

	/**
	 * Handle "tools/call".
	 *
	 * A tool that fails is not a JSON-RPC error: the failure is reported inside
	 * a normal result with isError set to true, so the model can read it.
	 *
	 * @since 10.3.0
	 *
	 * @param string|int $id     The JSON-RPC request id.
	 * @param array      $params The JSON-RPC params member.
	 *
	 * @return \WP_REST_Response
	 */
	protected function handle_tools_call( $id, $params ) {
		if ( ! isset( $params['name'] ) || ! is_string( $params['name'] ) || '' === $params['name'] ) {
			return $this->rpc_error( $id, -32602, __( 'Invalid params: "name" is required.', 'wp-seopress' ) );
		}

		$ability = $this->resolve_tool( $params['name'] );
		// Check scope before normalization or any ability callback, even when an
		// extension supplies its own tool list.
		$scope_denied = null === $ability
			? $this->is_tool_held_back_by_scope( $params['name'] )
			: McpExposure::is_read_only() && ! McpExposure::is_read_ability( $ability );

		if ( $scope_denied ) {
			return $this->rpc_result(
				$id,
				$this->tool_error(
					sprintf(
						/* translators: %s: the tool name sent by the client. */
						__( 'This connection was approved for reading only, so it cannot use the "%s" tool. Connect again and choose to read and change if you need it.', 'wp-seopress' ),
						$params['name']
					)
				)
			);
		}

		if ( null === $ability ) {
			return $this->rpc_error(
				$id,
				-32602,
				sprintf(
					/* translators: %s: the tool name sent by the client. */
					__( 'Unknown tool: %s.', 'wp-seopress' ),
					$params['name']
				)
			);
		}

		$arguments = array();
		if ( array_key_exists( 'arguments', $params ) ) {
			if ( ! is_array( $params['arguments'] ) ) {
				return $this->rpc_error( $id, -32602, __( 'Invalid params: "arguments" must be an object.', 'wp-seopress' ) );
			}

			$arguments = $params['arguments'];
		}

		// An ability without an input schema only accepts null input.
		$input = empty( $ability->get_input_schema() ) ? null : $arguments;

		$normalized = $ability->normalize_input( $input );
		if ( is_wp_error( $normalized ) ) {
			return $this->rpc_result( $id, $this->tool_error( $normalized->get_error_message() ) );
		}

		$valid = $ability->validate_input( $normalized );
		if ( is_wp_error( $valid ) ) {
			return $this->rpc_result( $id, $this->tool_error( $valid->get_error_message() ) );
		}

		// The ability decides, never this transport. Checking here as well as
		// inside execute() stops a denied call before any execute_callback side
		// effect.
		//
		// An ability that returns a WP_Error has chosen what to say, and the
		// reason is often not a permission at all: a post ID that matches
		// nothing is the caller's own input, and answering "you do not have
		// permission" sends them to look at roles instead of at the ID. A bare
		// false gave no reason, so the generic sentence stands.
		$permitted = $ability->check_permissions( $normalized );

		if ( is_wp_error( $permitted ) ) {
			return $this->rpc_result( $id, $this->tool_error( $permitted->get_error_message() ) );
		}

		if ( true !== $permitted ) {
			return $this->rpc_result(
				$id,
				$this->tool_error(
					sprintf(
						/* translators: %s: the tool name sent by the client. */
						__( 'You do not have permission to use the "%s" tool.', 'wp-seopress' ),
						$params['name']
					)
				)
			);
		}

		$result = $ability->execute( $normalized );
		if ( is_wp_error( $result ) ) {
			return $this->rpc_result( $id, $this->tool_error( $result->get_error_message() ) );
		}

		return $this->rpc_result( $id, $this->tool_result( $ability, $result ) );
	}

	/**
	 * Build a successful "tools/call" result.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability $ability The executed ability.
	 * @param mixed       $result  Whatever the ability returned.
	 *
	 * @return array
	 */
	protected function tool_result( $ability, $result ) {
		$serialized = wp_json_encode( $result );

		$payload = array(
			'content' => array(
				array(
					'type' => 'text',
					'text' => false === $serialized ? '' : $serialized,
				),
			),
			'isError' => false,
		);

		if ( is_array( $result ) ) {
			$structured = self::structured_content_for_mcp( $ability->get_output_schema(), $result );

			if ( null !== $structured ) {
				$payload['structuredContent'] = $structured;
			}
		}

		return $payload;
	}

	/**
	 * The name a list is served under once it has to live inside an object.
	 *
	 * @since 10.3.0
	 * @var string
	 */
	const LIST_WRAPPER = 'items';

	/**
	 * An ability's output schema, in the shape MCP requires of one.
	 *
	 * The spec says a tool's outputSchema is a JSON Schema object, and two of
	 * these abilities answer with a list, so their schema said "array". Claude
	 * and ChatGPT accepted it. Cursor validates the field and rejected the
	 * whole listing over it, which does not cost two tools, it costs all of
	 * them: a client that cannot parse tools/list has no tools at all.
	 *
	 * So a list is wrapped rather than dropped, and the caller gets structured
	 * output for those two tools instead of text it has to parse itself.
	 *
	 * @since 10.3.0
	 *
	 * @param array $schema The ability's own output schema.
	 *
	 * @return array
	 */
	public static function output_schema_for_mcp( $schema ) {
		if ( ! is_array( $schema ) || empty( $schema ) ) {
			return array();
		}

		if ( isset( $schema['type'] ) && 'object' === $schema['type'] ) {
			return $schema;
		}

		return array(
			'type'                 => 'object',
			'properties'           => array(
				self::LIST_WRAPPER => $schema,
			),
			'required'             => array( self::LIST_WRAPPER ),
			'additionalProperties' => false,
		);
	}

	/**
	 * The structured result to serve, wrapped the same way its schema was.
	 *
	 * @since 10.3.0
	 *
	 * @param array $schema The ability's own output schema.
	 * @param array $result What the ability returned.
	 *
	 * @return array|\stdClass|null Null when nothing structured should be sent.
	 */
	public static function structured_content_for_mcp( $schema, $result ) {
		if ( ! is_array( $schema ) || empty( $schema ) ) {
			return null;
		}

		if ( isset( $schema['type'] ) && 'object' === $schema['type'] ) {
			return empty( $result ) ? new \stdClass() : $result;
		}

		// The schema was wrapped, so the payload has to be wrapped with it or
		// a client validating one against the other is right to refuse it.
		return array( self::LIST_WRAPPER => $result );
	}

	/**
	 * Build a failed "tools/call" result.
	 *
	 * @since 10.3.0
	 *
	 * @param string $message The message handed to the model.
	 *
	 * @return array
	 */
	protected function tool_error( $message ) {
		return array(
			'content' => array(
				array(
					'type' => 'text',
					'text' => $message,
				),
			),
			'isError' => true,
		);
	}

	/**
	 * Turn an ability name into an MCP tool name.
	 *
	 * @since 10.3.0
	 *
	 * @param string $ability_name The registered ability name.
	 *
	 * @return string
	 */
	public function tool_name( $ability_name ) {
		return str_replace( '/', self::TOOL_NAME_SEPARATOR, $ability_name );
	}

	/**
	 * Find the exposed ability behind an MCP tool name.
	 *
	 * The lookup walks the exposed abilities and compares computed tool names,
	 * so an ability that is registered but not exposed can never be reached,
	 * whatever the client sends.
	 *
	 * @since 10.3.0
	 *
	 * @param string $tool_name The tool name sent by the client.
	 *
	 * @return \WP_Ability|null
	 */
	public function resolve_tool( $tool_name ) {
		foreach ( $this->get_exposed_abilities() as $ability ) {
			if ( $this->tool_name( $ability->get_name() ) === $tool_name ) {
				return $ability;
			}
		}

		return null;
	}

	/**
	 * Would this tool name have resolved if the connection were not read-only.
	 *
	 * @since 10.3.0
	 *
	 * @param string $tool_name The tool name sent by the client.
	 *
	 * @return bool
	 */
	protected function is_tool_held_back_by_scope( $tool_name ) {
		if ( ! McpExposure::is_read_only() ) {
			return false;
		}

		foreach ( McpExposure::abilities_in_scope_of_namespace() as $ability ) {
			if ( $this->tool_name( $ability->get_name() ) === $tool_name ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Map the ability annotations onto the MCP tool annotation hints.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability $ability The ability to read.
	 *
	 * @return array
	 */
	protected function tool_annotations( $ability ) {
		$meta = $ability->get_meta();

		if ( empty( $meta['annotations'] ) || ! is_array( $meta['annotations'] ) ) {
			return array();
		}

		$map = array(
			'readonly'    => 'readOnlyHint',
			'destructive' => 'destructiveHint',
			'idempotent'  => 'idempotentHint',
		);

		$annotations = array();
		foreach ( $map as $source => $target ) {
			if ( array_key_exists( $source, $meta['annotations'] ) ) {
				$annotations[ $target ] = (bool) $meta['annotations'][ $source ];
			}
		}

		return $annotations;
	}

	/**
	 * An MCP inputSchema is always a JSON Schema object, even with no property.
	 *
	 * @since 10.3.0
	 *
	 * @param array $schema The ability input schema.
	 *
	 * @return array
	 */
	protected function prepare_input_schema( $schema ) {
		if ( empty( $schema ) ) {
			return array(
				'type'       => 'object',
				'properties' => new \stdClass(),
			);
		}

		return $this->schema_to_object( $schema );
	}

	/**
	 * Keep an empty "properties" map encoded as {} instead of [].
	 *
	 * @since 10.3.0
	 *
	 * @param array $schema A JSON Schema fragment.
	 *
	 * @return array
	 */
	protected function schema_to_object( $schema ) {
		if ( isset( $schema['properties'] ) && is_array( $schema['properties'] ) && empty( $schema['properties'] ) ) {
			$schema['properties'] = new \stdClass();
		}

		return $schema;
	}

	/**
	 * Wrap a payload in a JSON-RPC success envelope.
	 *
	 * @since 10.3.0
	 *
	 * @param string|int $id     The JSON-RPC request id.
	 * @param mixed      $result The result member.
	 *
	 * @return \WP_REST_Response
	 */
	protected function rpc_result( $id, $result ) {
		return new \WP_REST_Response(
			array(
				'jsonrpc' => '2.0',
				'id'      => $id,
				'result'  => $result,
			),
			200
		);
	}

	/**
	 * Wrap a protocol failure in a JSON-RPC error envelope.
	 *
	 * @since 10.3.0
	 *
	 * @param string|int|null $id      The JSON-RPC request id, null when unknown.
	 * @param int             $code    The JSON-RPC error code.
	 * @param string          $message The error message.
	 *
	 * @return \WP_REST_Response
	 */
	protected function rpc_error( $id, $code, $message ) {
		return new \WP_REST_Response(
			array(
				'jsonrpc' => '2.0',
				'id'      => $id,
				'error'   => array(
					'code'    => $code,
					'message' => $message,
				),
			),
			200
		);
	}

	/**
	 * Turn WordPress's own "invalid JSON body" refusal into a JSON-RPC parse error.
	 *
	 * WP_REST_Server rejects a malformed JSON body before the route callback
	 * runs, so this filter is the only place where the client can still be
	 * answered in its own protocol.
	 *
	 * @since 10.3.0
	 *
	 * @param mixed            $response The current response or WP_Error.
	 * @param array            $handler  The matched route handler.
	 * @param \WP_REST_Request $request  The incoming request.
	 *
	 * @return mixed
	 */
	public function catch_invalid_json( $response, $handler, $request ) {
		if ( ! $request instanceof \WP_REST_Request || self::route_path() !== $request->get_route() ) {
			return $response;
		}

		if ( ! is_wp_error( $response ) || 'rest_invalid_json' !== $response->get_error_code() ) {
			return $response;
		}

		return $this->rpc_error( null, -32700, __( 'Parse error: the request body is not valid JSON.', 'wp-seopress' ) );
	}

	/**
	 * Send a genuinely empty body with the 202 acknowledgement.
	 *
	 * @since 10.3.0
	 *
	 * @param bool              $served  Whether the request has already been served.
	 * @param \WP_REST_Response $result  The response about to be serialized.
	 * @param \WP_REST_Request  $request The incoming request.
	 *
	 * @return bool
	 */
	public function serve_empty_accepted_body( $served, $result, $request ) {
		if ( true === $served ) {
			return $served;
		}

		if ( ! $request instanceof \WP_REST_Request || self::route_path() !== $request->get_route() ) {
			return $served;
		}

		if ( ! $result instanceof \WP_REST_Response || 202 !== $result->get_status() ) {
			return $served;
		}

		return true;
	}
}

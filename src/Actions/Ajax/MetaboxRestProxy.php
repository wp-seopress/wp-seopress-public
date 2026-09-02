<?php // phpcs:ignore

namespace SEOPress\Actions\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;

/**
 * MetaboxRestProxy
 *
 * Serves the universal metabox REST endpoints over admin-ajax.php.
 *
 * Some hosts run a WAF (o2switch Tiger Protect, Sucuri...) that challenges the
 * REST API path (/wp-json) by rule — the browser gets a JS "security check"
 * interstitial instead of JSON — while leaving admin-ajax.php alone, because
 * blocking it would break core WordPress admin. The classic metabox fetched
 * the preview/content analysis over admin-ajax before 9.8, so it worked on
 * every host; the React metabox moved to /wp-json and regressed behind such
 * WAFs.
 *
 * This handler dispatches the same request to the existing REST controllers
 * with rest_do_request(), so there is no logic duplication and every
 * permission_callback still runs. The React side only flips the transport when
 * a SEOPress REST call would otherwise be challenged.
 */
class MetaboxRestProxy implements ExecuteHooksBackend {

	/**
	 * How many routes a single batch may carry.
	 *
	 * A tab never mounts more than a handful of hooks in one commit, so this
	 * is well above what the metabox asks for. It is there so a malformed or
	 * hostile client cannot turn one request into an unbounded loop of REST
	 * dispatches.
	 */
	const MAX_BATCH_ROUTES = 20;

	/**
	 * The MetaboxRestProxy hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_seopress_metabox_proxy', array( $this, 'proxy' ) );
	}

	/**
	 * Dispatch a same-site SEOPress REST route received over admin-ajax.
	 *
	 * @return void
	 */
	public function proxy() {
		check_ajax_referer( 'seopress_metabox_proxy', '_ajax_nonce' );

		// Baseline gate; each route still enforces its own permission_callback
		// (e.g. edit_post on the specific id) through rest_do_request().
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
		}

		// Several routes asked for at once: answer them from this bootstrap
		// instead of making the browser pay for one per route (see #1851).
		if ( isset( $_REQUEST['routes'] ) ) {
			$routes = wp_unslash( $_REQUEST['routes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each entry is validated against the allow-list in dispatch_read().

			if ( ! is_array( $routes ) ) {
				wp_send_json_error( array( 'message' => 'invalid_routes' ), 400 );
			}

			$this->proxy_batch( $routes );
		}

		$route = isset( $_REQUEST['route'] ) ? (string) wp_unslash( $_REQUEST['route'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated below against a strict allow-list.

		$parts = explode( '?', $route, 2 );
		$path  = '/' . ltrim( $parts[0], '/' );

		if ( ! $this->is_allowed_path( $path ) ) {
			wp_send_json_error( array( 'message' => 'invalid_route' ), 400 );
		}

		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
		if ( ! in_array( $method, array( 'GET', 'POST' ), true ) ) {
			$method = 'GET';
		}

		$request = new \WP_REST_Request( $method, $path );

		// Query string travels in the route tail (e.g. ?target_keywords=...).
		if ( isset( $parts[1] ) && '' !== $parts[1] ) {
			$query = array();
			wp_parse_str( $parts[1], $query );
			$request->set_query_params( $query );
		}

		// Forward the JSON body for writes (score save, ignore toggle...).
		if ( 'POST' === $method ) {
			// php://input is the request body, not a filesystem path, so
			// WP_Filesystem does not apply here.
			$body = file_get_contents( 'php://input' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( ! empty( $body ) ) {
				$decoded = json_decode( $body, true );
				if ( is_array( $decoded ) ) {
					$request->set_header( 'Content-Type', 'application/json' );
					$request->set_body_params( $decoded );
				}
			}
		}

		$response = rest_do_request( $request );
		$server   = rest_get_server();
		$data     = $server->response_to_data( $response, false );

		wp_send_json( $data, $response->get_status() );
	}

	/**
	 * Answer several read routes from a single request.
	 *
	 * Dispatching costs a few milliseconds; what costs is everything that
	 * happens before this method is reached — WordPress loading every active
	 * plugin, the theme and `init` — and the browser used to pay for it once
	 * per route. A tab that mounts three hooks paid three bootstraps for
	 * three sets of post meta.
	 *
	 * Each entry still goes through rest_do_request(), so every route keeps
	 * running its own permission_callback: the baseline edit_posts check above
	 * gates the batch, it never stands in for a route's own authorisation.
	 *
	 * The response is a list in the order the routes were asked for, each
	 * entry carrying its own status, so one route answering 403 or 404 leaves
	 * the others intact.
	 *
	 * @param array $routes Routes to dispatch, each with its optional query string.
	 * @return void
	 */
	private function proxy_batch( $routes ) {
		if ( count( $routes ) > self::MAX_BATCH_ROUTES ) {
			wp_send_json_error( array( 'message' => 'too_many_routes' ), 400 );
		}

		$results = array();

		foreach ( $routes as $route ) {
			// routes[][]=… would otherwise reach the cast below as an array.
			$results[] = is_string( $route )
				? $this->dispatch_read( $route )
				: array(
					'route'  => '',
					'status' => 400,
					'body'   => array(
						'code'    => 'invalid_route',
						'message' => 'invalid_route',
					),
				);
		}

		wp_send_json( $results, 200 );
	}

	/**
	 * Run one GET route through the REST server and describe the outcome.
	 *
	 * Never sends anything itself: a route that is rejected comes back as an
	 * entry with its own status so the rest of the batch still answers.
	 *
	 * @param string $route Route with its optional query string, as asked for.
	 * @return array{route: string, status: int, body: mixed}
	 */
	private function dispatch_read( $route ) {
		$parts = explode( '?', $route, 2 );
		$path  = '/' . ltrim( $parts[0], '/' );

		if ( ! $this->is_allowed_path( $path ) ) {
			return array(
				'route'  => $route,
				'status' => 400,
				'body'   => array(
					'code'    => 'invalid_route',
					'message' => 'invalid_route',
				),
			);
		}

		$request = new \WP_REST_Request( 'GET', $path );

		// Query string travels in the route tail (e.g. ?target_keywords=...).
		if ( isset( $parts[1] ) && '' !== $parts[1] ) {
			$query = array();
			wp_parse_str( $parts[1], $query );
			$request->set_query_params( $query );
		}

		$response = rest_do_request( $request );
		$server   = rest_get_server();

		return array(
			'route'  => $route,
			'status' => (int) $response->get_status(),
			'body'   => $server->response_to_data( $response, false ),
		);
	}

	/**
	 * Hard allow-list: only this plugin's own namespace, only safe path
	 * characters. Never proxy an arbitrary route.
	 *
	 * @param string $path Route path, leading slash included, query stripped.
	 * @return bool
	 */
	private function is_allowed_path( $path ) {
		return 1 === preg_match( '#^/seopress/v[0-9]+/[A-Za-z0-9/_-]+$#', $path );
	}
}

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

		$route = isset( $_REQUEST['route'] ) ? (string) wp_unslash( $_REQUEST['route'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated below against a strict allow-list.

		$parts = explode( '?', $route, 2 );
		$path  = '/' . ltrim( $parts[0], '/' );

		// Hard allow-list: only this plugin's own namespace, only safe path
		// characters. Never proxy an arbitrary route.
		if ( ! preg_match( '#^/seopress/v[0-9]+/[A-Za-z0-9/_-]+$#', $path ) ) {
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
}

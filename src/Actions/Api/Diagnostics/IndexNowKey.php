<?php
namespace SEOPress\Actions\Api\Diagnostics;
defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooks;

/** Check the saved IndexNow key at the same URL used for submissions. */
class IndexNowKey implements ExecuteHooks {
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}
	public function permission_check() {
		return current_user_can( seopress_capability( 'manage_options', 'instant_indexing' ) );
	}
	public function register() {
		register_rest_route( 'seopress/v1', '/diagnostics/indexnow-key', array(
			'methods' => 'POST',
			'callback' => array( $this, 'process' ),
			'permission_callback' => array( $this, 'permission_check' ),
		) );
	}
	public function process() {
		if ( ! $this->permission_check() ) {
			return new \WP_Error( 'rest_forbidden', __( 'You cannot manage Instant Indexing settings.', 'wp-seopress' ), array( 'status' => 403 ) );
		}
		require_once SEOPRESS_PLUGIN_DIR_PATH . 'inc/functions/options-instant-indexing.php';
		$options = get_option( 'seopress_instant_indexing_option_name', array() );
		$key = seopress_instant_indexing_get_api_key( $options['seopress_instant_indexing_bing_api_key'] ?? '' );
		$result = array( 'status' => 'warning', 'url' => '', 'http_code' => 0 );
		if ( '' === $key ) {
			$result['message'] = __( 'Save a valid IndexNow key before checking its file.', 'wp-seopress' );
			return rest_ensure_response( $result );
		}
		$result['url'] = trailingslashit( get_home_url() ) . $key . '.txt';
		if ( '1' !== seopress_get_toggle_option( 'instant-indexing' ) ) {
			$result['message'] = __( 'Enable Instant Indexing so WordPress can serve the key file.', 'wp-seopress' );
			return rest_ensure_response( $result );
		}
		$rate_key = 'seopress_indexnow_check_' . get_current_user_id();
		if ( get_transient( $rate_key ) ) {
			return new \WP_Error( 'rate_limited', __( 'Please wait a few seconds before checking again.', 'wp-seopress' ), array( 'status' => 429 ) );
		}
		set_transient( $rate_key, 1, 5 );
		$response = wp_safe_remote_get( $result['url'], array( 'timeout' => 10, 'redirection' => 3, 'limit_response_size' => 1024 ) );
		if ( is_wp_error( $response ) ) {
			$result['message'] = __( 'The server could not fetch the key file. Open its URL to check access and ask your host to check loopback requests, firewall rules and TLS.', 'wp-seopress' );
		} else {
			$result['http_code'] = (int) wp_remote_retrieve_response_code( $response );
			if ( 200 !== $result['http_code'] ) {
				/* translators: %d: HTTP status returned by the key file URL. */
				$result['message'] = sprintf( __( 'The key file returned HTTP %d. Check server rules for .txt files, caching and access restrictions. WordPress must receive this request.', 'wp-seopress' ), $result['http_code'] );
			} elseif ( trim( wp_remote_retrieve_body( $response ) ) !== $key ) {
				$result['message'] = __( 'The URL responds, but its contents do not match the saved key. Check your cache or an existing file at this URL.', 'wp-seopress' );
			} else {
				$result['status'] = 'success';
				$result['message'] = __( 'The key file is reachable and contains the saved key.', 'wp-seopress' );
			}
		}
		return rest_ensure_response( $result );
	}
}

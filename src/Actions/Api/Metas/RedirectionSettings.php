<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\RedirectionSettings as RedirectionSettingsHelper;

/**
 * Redirection Settings
 */
class RedirectionSettings implements ExecuteHooks {
	/**
	 * The Redirection Settings hooks.
	 *
	 * @since 5.0.0
	 */

	/**
	 * The Redirection Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Redirection Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/redirection-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processGet' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);

		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/redirection-settings',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'processPut' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					$post_id = $request['id'];
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);

		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/redirection-test',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'processTest' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					return current_user_can( 'edit_post', (int) $request['id'] );
				},
			)
		);
	}

	/**
	 * The Redirection Settings process test.
	 *
	 * Checks the saved destination URL of the redirection with a real HTTP
	 * request and returns its status code (or the error when the host can't be
	 * reached) so the user can tell a valid target from a dead one. Also
	 * returns the source URL (the page that performs the redirect) so the UI
	 * can offer an "open in a new tab" link for a real browser test.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 9.9.0
	 *
	 * @return \WP_REST_Response
	 */
	public function processTest( \WP_REST_Request $request ) {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( null === $post ) {
			return new \WP_REST_Response(
				array(
					'code'    => 'error',
					'message' => __( 'Redirection not found.', 'wp-seopress' ),
				),
				404
			);
		}

		// The source URL is always rebuilt from the post itself (the site's own
		// URL), never from the request body, to avoid any SSRF surface.
		if ( 'seopress_404' === $post->post_type ) {
			$parse_url = wp_parse_url( get_home_url() );
			$home_url  = get_home_url();
			if ( ! empty( $parse_url['scheme'] ) && ! empty( $parse_url['host'] ) ) {
				$home_url = $parse_url['scheme'] . '://' . $parse_url['host'];
			}
			$source_url = $home_url . '/' . ltrim( $post->post_title, '/' );
		} else {
			$source_url = get_permalink( $id );
		}

		$response = array(
			'code'       => 'success',
			'source_url' => $source_url,
		);

		$type        = (string) get_post_meta( $id, '_seopress_redirections_type', true );
		$destination = (string) get_post_meta( $id, '_seopress_redirections_value', true );

		// 410 / 451 are terminal: there is no destination to reach.
		if ( in_array( $type, array( '410', '451' ), true ) ) {
			$response['terminal'] = $type;
			return new \WP_REST_Response( $response );
		}

		if ( '' === $destination ) {
			$response['error'] = __( 'No destination URL to test. Save your redirection first.', 'wp-seopress' );
			return new \WP_REST_Response( $response );
		}

		// Resolve a relative destination against the site home URL.
		$destination_url = $destination;
		if ( ! preg_match( '#^https?://#i', $destination_url ) ) {
			$destination_url = home_url( '/' . ltrim( $destination_url, '/' ) );
		}
		$response['destination_url'] = $destination_url;

		// The destination is user-provided, so use the safe HTTP API (it blocks
		// requests to private / loopback hosts). Redirects are followed so the
		// reported status reflects where the target actually ends up.
		$http = wp_safe_remote_get(
			$destination_url,
			array(
				'timeout'   => 10,
				'sslverify' => false,
				'headers'   => array(
					'Cache-Control' => 'no-cache',
				),
			)
		);

		if ( is_wp_error( $http ) ) {
			$response['error'] = $http->get_error_message();
			return new \WP_REST_Response( $response );
		}

		$response['status_code'] = (int) wp_remote_retrieve_response_code( $http );

		return new \WP_REST_Response( $response );
	}

	/**
	 * The Redirection Settings process put.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processPut( \WP_REST_Request $request ) {

		$id     = $request->get_param( 'id' );
		$metas  = RedirectionSettingsHelper::getMetaKeys( $id );
		$params = $request->get_params();

		try {
			$data_keys_save = array(
				'_seopress_redirections_value',
				'_seopress_redirections_enabled',
				'_seopress_redirections_enabled_regex',
				'_seopress_redirections_logged_status',
				'_seopress_redirections_param',
				'_seopress_redirections_type',
			);

			foreach ( $metas as $key => $value ) {
				if ( ! isset( $params[ $value['key'] ] ) ) {
					continue;
				}

				$item = $params[ $value['key'] ];

				if ( ! in_array( $value['key'], $data_keys_save, true ) ) {
					continue;
				}

				if ( '_seopress_redirections_value' === $value['key'] ) {
					$item = sanitize_url( $item );
				}

				if ( '_seopress_redirections_enabled' === $value['key'] || '_seopress_redirections_enabled_regex' === $value['key'] ) {
					$item = sanitize_text_field( $item );
				}

				if ( '_seopress_redirections_logged_status' === $value['key'] ) {
					$logged_status = sanitize_text_field( $item );

					$allowed_options = array(
						'both',
						'only_logged_in',
						'only_not_logged_in',
					);

					if ( in_array( $logged_status, $allowed_options, true ) ) {
						$item = $logged_status;
					}
				}

				if ( '_seopress_redirections_param' === $value['key'] ) {
					$redirections_param = sanitize_text_field( $item );

					$allowed_options = array(
						'exact_match',
						'without_param',
						'with_ignored_param',
					);

					if ( in_array( $redirections_param, $allowed_options, true ) ) {
						$item = $redirections_param;
					}
				}

				if ( '_seopress_redirections_type' === $value['key'] ) {
					$redirection_type = intval( $item );

					$allowed_options = array(
						301,
						302,
						307,
					);

					if ( in_array( $redirection_type, $allowed_options, true ) ) {
						$item = $redirection_type;
					}
				}

				if ( ! empty( $item ) ) {
					update_post_meta( $id, $value['key'], $item );
				} else {
					delete_post_meta( $id, $value['key'] );
				}
			}

			return new \WP_REST_Response(
				array(
					'code' => 'success',
				)
			);
		} catch ( \Exception $e ) {
			return new \WP_REST_Response(
				array(
					'code'         => 'error',
					'code_message' => 'execution_failed',
				),
				403
			);
		}
	}

	/**
	 * The Redirection Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processGet( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$metas = RedirectionSettingsHelper::getMetaKeys( $id );

		$data = array();
		foreach ( $metas as $key => $value ) {
			if ( isset( $value['use_default'] ) && $value['use_default'] ) {
				$data[] = array_merge(
					$value,
					array(
						'can_modify' => false,
						'value'      => $value['default'],
					)
				);
			} else {
				$result = get_post_meta( $id, $value['key'], true );
				$data[] = array_merge(
					$value,
					array(
						'can_modify' => true,
						'value'      => 'checkbox' === $value['type'] ? ( $result ? true : false ) : $result,
					)
				);
			}
		}

		return new \WP_REST_Response( $data );
	}
}

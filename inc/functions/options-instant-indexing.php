<?php
/**
 * Options instant indexing
 *
 * @package Functions
 */

use SEOPress\Helpers\PagesAdmin;

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Create the virtual Instant Indexing API key txt file
 *
 * @return void
 */
function seopress_instant_indexing_api_key_txt() {
	// Is instant indexing enabled?
	if ( '1' !== seopress_get_toggle_option( 'instant-indexing' ) ) {
		return;
	}

	$options = get_option( 'seopress_instant_indexing_option_name' );
	$api_key = isset( $options['seopress_instant_indexing_bing_api_key'] ) ? esc_attr( $options['seopress_instant_indexing_bing_api_key'] ) : null;

	if ( null === $api_key ) {
		return;
	}

	if ( seopress_is_base64_string( $api_key ) === false ) {
		return;
	}

	$api_key = base64_decode( $api_key );

	global $wp;
	$current_url = home_url( $wp->request );

	if ( isset( $current_url ) && trailingslashit( get_home_url() ) . $api_key . '.txt' === $current_url ) {
		header( 'Content-Type: text/plain' );
		header( 'X-Robots-Tag: noindex' );
		status_header( 200 );
		echo esc_html( $api_key );

		exit();
	}
}
add_action( 'template_redirect', 'seopress_instant_indexing_api_key_txt', 0 );

/**
 * Batch Instant Indexing
 *
 * @param bool   $is_manual_submission Is manual submission.
 * @param string $permalink Permalink.
 *
 * @return void
 */
function seopress_instant_indexing_fn( $is_manual_submission = true, $permalink = null ) {
	// Is instant indexing enabled?
	if ( '1' !== seopress_get_toggle_option( 'instant-indexing' ) ) {
		return;
	}

	if ( true === $is_manual_submission ) {
		$options = get_option( 'seopress_instant_indexing_option_name' );

		// Update options.
		if ( isset( $_POST['urls_to_submit'] ) ) {
			$options['seopress_instant_indexing_manual_batch'] = sanitize_textarea_field( wp_unslash( $_POST['urls_to_submit'] ) );
		}

		if ( isset( $_POST['indexnow_api'] ) ) {
			$options['seopress_instant_indexing_bing_api_key'] = sanitize_text_field( wp_unslash( $_POST['indexnow_api'] ) );
		}

		if ( isset( $_POST['google_api'] ) ) {
			$options['seopress_instant_indexing_google_api_key'] = sanitize_textarea_field( wp_unslash( $_POST['google_api'] ) );
		}

		if ( isset( $_POST['google'] ) ) {
			if ( 'true' === $_POST['google'] ) {
				$options['engines']['google'] = '1';
			} elseif ( 'false' === $_POST['google'] ) {
				unset( $options['engines']['google'] );
			}
		}

		if ( isset( $_POST['bing'] ) ) {
			if ( 'true' === $_POST['bing'] ) {
				$options['engines']['bing'] = '1';
			} elseif ( 'false' === $_POST['bing'] ) {
				unset( $options['engines']['bing'] );
			}
		}

		if ( isset( $_POST['automatic_submission'] ) ) {
			if ( 'true' === $_POST['automatic_submission'] ) {
				$options['seopress_instant_indexing_automate_submission'] = '1';
			} elseif ( 'false' === $_POST['automatic_submission'] ) {
				unset( $options['seopress_instant_indexing_automate_submission'] );
			}
		}

		if ( isset( $_POST['update_action'] ) && isset( $_POST['delete_action'] ) ) {
			if ( 'URL_UPDATED' === $_POST['update_action'] ) {
				$options['seopress_instant_indexing_google_action'] = 'URL_UPDATED';
			} elseif ( 'URL_DELETED' === $_POST['delete_action'] ) {
				$options['seopress_instant_indexing_google_action'] = 'URL_DELETED';
			} else {
				$options['seopress_instant_indexing_google_action'] = 'URL_UPDATED';
			}
		}

		update_option( 'seopress_instant_indexing_option_name', $options );
	}

	$options = get_option( 'seopress_instant_indexing_option_name' );

	$engines        = isset( $options['engines'] ) ? $options['engines'] : null;
	$actions        = isset( $options['seopress_instant_indexing_google_action'] ) ? esc_attr( $options['seopress_instant_indexing_google_action'] ) : 'URL_UPDATED';
	$urls           = isset( $options['seopress_instant_indexing_manual_batch'] ) ? esc_attr( $options['seopress_instant_indexing_manual_batch'] ) : '';
	$google_api_key = isset( $options['seopress_instant_indexing_google_api_key'] ) ? $options['seopress_instant_indexing_google_api_key'] : '';
	$bing_api_key   = isset( $options['seopress_instant_indexing_bing_api_key'] ) ? esc_attr( $options['seopress_instant_indexing_bing_api_key'] ) : '';
	$bing_url       = 'https://api.indexnow.org/indexnow/';
	$google_url     = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

	// Clean logs.
	delete_option( 'seopress_instant_indexing_log_option_name' );

	// Check we have URLs to submit.
	if ( '' === $urls && true === $is_manual_submission ) {
		$log['error'] = __( 'No URLs to submit', 'wp-seopress' );
		update_option( 'seopress_instant_indexing_log_option_name', $log, false );
		return;
	}

	// Check we have at least one search engine selected.
	if ( empty( $engines ) ) {
		$log['error'] = __( 'No search engines selected', 'wp-seopress' );
		update_option( 'seopress_instant_indexing_log_option_name', $log, false );
		return;
	}

	// Check we have setup at least one API key.
	if ( '' === $google_api_key && '' === $bing_api_key ) {
		$log['error'] = __( 'No API key defined from the settings tab', 'wp-seopress' );
		update_option( 'seopress_instant_indexing_log_option_name', $log, false );
		return;
	}

	// Prepare the URLS.
	if ( true === $is_manual_submission ) {
		$urls          = preg_split( '/\r\n|\r|\n/', $urls );
		$x_source_info = 'https://www.seopress.org/9.3.0.2/true';

		$urls = array_slice( $urls, 0, 100 );
	} elseif ( false === $is_manual_submission && ! empty( $permalink ) ) {
		$urls          = null;
		$urls[]        = $permalink;
		$x_source_info = 'https://www.seopress.org/9.3.0.2/false';
	}

	// Bing API.
	if ( isset( $bing_api_key ) && ! empty( $bing_api_key ) && ! empty( $engines['bing'] ) && '1' === $engines['bing'] ) {
		if ( seopress_is_base64_string( $bing_api_key ) === true ) {
			$bing_api_key = base64_decode( $bing_api_key );

			$host = wp_parse_url( get_home_url(), PHP_URL_HOST );

			$body = array(
				'host'        => $host,
				'key'         => $bing_api_key,
				'keyLocation' => trailingslashit( get_home_url() ) . $bing_api_key . '.txt',
				'urlList'     => $urls,
			);

			$body = wp_json_encode( $body );

			if ( false !== $body ) {
				// Build the POST request.
				$args = array(
					'body'    => $body,
					'timeout' => 30,
					'headers' => array(
						'Content-Type'  => 'application/json',
						'X-Source-Info' => $x_source_info,
					),
				);
				$args = apply_filters( 'seopress_instant_indexing_post_request_args', $args );

				// IndexNow (Bing).
				$response = wp_remote_post( $bing_url, $args );

				// Standardize Bing response log.
				if ( is_wp_error( $response ) ) {
					$log['bing']['response'] = array(
						'error' => array(
							'code'    => $response->get_error_code(),
							'message' => $response->get_error_message(),
						),
					);
				} else {
					$code                    = wp_remote_retrieve_response_code( $response );
					$body                    = wp_remote_retrieve_body( $response );
					$log['bing']['response'] = array(
						'code' => $code,
						'body' => $body,
					);
					// If Bing returns an error in the body, try to parse and store it.
					$json = json_decode( $body, true );
					if ( is_array( $json ) && isset( $json['error'] ) ) {
						$log['bing']['response']['error'] = $json['error'];
					} else {
						unset( $log['bing']['response']['error'] );
					}
				}
			} else {
				$log['bing']['response'] = array(
					'error' => array(
						'code'    => 400,
						'message' => __( 'Bad request: Invalid format', 'wp-seopress' ),
					),
				);
			}
		} else {
			$log['bing']['response'] = array(
				'error' => array(
					'code'    => 400,
					'message' => __( 'Bad request: Invalid key format', 'wp-seopress' ),
				),
			);
		}
	} elseif ( ! empty( $engines['bing'] ) && '1' === $engines['bing'] ) {
		$log['bing']['response'] = array(
			'error' => array(
				'code'    => 401,
				'message' => __( 'Bing API key is missing', 'wp-seopress' ),
			),
		);
	}

	// Google API.
	if ( true === $is_manual_submission ) {
		if ( isset( $google_api_key ) && ! empty( $google_api_key ) && '1' === $engines['google'] ) {
			try {
				$client = new Google_Client();

				$client->setAuthConfig( json_decode( $google_api_key, true ) );
				$client->setScopes( Google_Service_Indexing::INDEXING );

				$client->setUseBatch( true );

				$service = new Google_Service_Indexing( $client );
				$batch   = $service->createBatch();

				$post_body = new Google_Service_Indexing_UrlNotification();

				foreach ( $urls as $url ) {
					$post_body->setUrl( $url );
					$post_body->setType( $actions );
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$batch->add( $service->urlNotifications->publish( $post_body ) );
				}
				$results = $batch->execute();
			} catch ( \Exception $e ) {
				$results = $e->getMessage();
			}

			$log['google']['response'] = $results;
		} elseif ( '1' === $engines['google'] ) {
			$log['google']['response']['error'] = array(
				'code'    => 401,
				'message' => __( 'Google API key is missing', 'wp-seopress' ),
			);
		}
	}

	// Log URLs submitted.
	$log['log']['urls'] = $urls;
	$log['log']['date'] = current_time( 'F j, Y, g:i a' );

	update_option( 'seopress_instant_indexing_log_option_name', $log, false );

	if ( true === $is_manual_submission ) {
		exit();
	}
}

/**
 * Ajax Batch Instant Indexing
 *
 * @return void
 */
function seopress_instant_indexing_post() {
	check_ajax_referer( 'seopress_instant_indexing_post_nonce' );
	require_once WP_PLUGIN_DIR . '/wp-seopress/vendor/autoload.php';
	if ( current_user_can( seopress_capability( 'manage_options', PagesAdmin::INSTANT_INDEXING ) ) && is_admin() ) {
		seopress_instant_indexing_fn();
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_seopress_instant_indexing_post', 'seopress_instant_indexing_post' );

/**
 * Ajax Generate Instant Indexing API Key
 *
 * @return void
 */
function seopress_instant_indexing_generate_api_key() {
	check_ajax_referer( 'seopress_instant_indexing_generate_api_key_nonce' );
	if ( current_user_can( seopress_capability( 'manage_options', PagesAdmin::INSTANT_INDEXING ) ) && is_admin() ) {
		seopress_instant_indexing_generate_api_key_fn();
	}

	wp_safe_redirect( admin_url( 'admin.php?page=seopress-instant-indexing' ) );
	exit();
}
add_action( 'wp_ajax_seopress_instant_indexing_generate_api_key', 'seopress_instant_indexing_generate_api_key' );

/**
 * Automatic submission
 *
 * @param string $new_status New status.
 * @param string $old_status Old status.
 * @param object $post Post.
 *
 * @return void
 */
function seopress_instant_indexing_on_post_publish( $new_status, $old_status, $post ) {
	$options = get_option( 'seopress_instant_indexing_option_name' );

	// Is instant indexing enabled?
	if ( '1' !== seopress_get_toggle_option( 'instant-indexing' ) ) {
		return;
	}

	// Is automatic submission enabled?
	if ( ! isset( $options['seopress_instant_indexing_automate_submission'] ) ) {
		return;
	}

	$do_submit = false;

	// Check post status.
	$type = 'add';
	if ( 'publish' === $old_status && 'publish' === $new_status ) {
		$do_submit = true;
		$type      = 'update';
	} elseif ( 'publish' !== $old_status && 'publish' === $new_status ) {
		$do_submit = true;
		$type      = 'add';
	} elseif ( 'publish' === $old_status && 'trash' === $new_status ) {
		$do_submit = true;
		$type      = 'delete';
	}

	// do submission.
	if ( $do_submit ) {
		$permalink = get_permalink( $post );

		// clean permalink if trashed post.
		if ( strpos( $permalink, '__trashed' ) > 0 ) {
			$permalink = substr( $permalink, 0, strlen( $permalink ) - 10 ) . '/';
		}
		if ( empty( $permalink ) ) {
			return;
		}

		// is it a public post type?
		if ( function_exists( 'is_post_publicly_viewable' ) ) {
			$is_public_post = is_post_publicly_viewable( $post );

			if ( ! $is_public_post && 'delete' !== $type ) {
				return;
			}

			if ( ! $is_public_post ) {
				return;
			}

			// Check if the post type is supported by Instant Indexing.
			$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();

			unset(
				$post_types['seopress_rankings'],
				$post_types['seopress_backlinks'],
				$post_types['seopress_404'],
				$post_types['elementor_library'],
				$post_types['fl-builder-template'],
				$post_types['editor-template'],
				$post_types['editor-form-entry'],
				$post_types['breakdance_form_res'],
				$post_types['customer_discount'],
				$post_types['cuar_private_file'],
				$post_types['cuar_private_page'],
				$post_types['vc_grid_item'],
				$post_types['zion_template'],
				$post_types['tbuilder_layout'],
				$post_types['tbuilder_layout_part'],
				$post_types['tb_cf'],
				$post_types['ct_template'],
				$post_types['oxy_user_library'],
				$post_types['bricks_template']
			);

			if ( ! in_array( $post->post_type, array_keys( $post_types ), true ) ) {
				return;
			}

			$permalink = apply_filters( 'seopress_instant_indexing_permalink', $permalink, $post );

			return seopress_instant_indexing_fn( false, $permalink );
		}
	}
}
add_action( 'transition_post_status', 'seopress_instant_indexing_on_post_publish', 10, 3 );

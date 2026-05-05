<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\RobotSettings as MetaRobotSettingsHelper;

/**
 * Robot Settings
 */
class RobotSettings implements ExecuteHooks {

	/**
	 * The Robot Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Robot Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/meta-robot-settings',
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
			'/posts/(?P<id>\d+)/meta-robot-settings',
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
	}

	/**
	 * The Robot Settings process put.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processPut( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$metas = MetaRobotSettingsHelper::getMetaKeys( $id );

		$params = $request->get_params();

		try {
			$data_keys_save = array(
				'_seopress_robots_index',
				'_seopress_robots_follow',
				'_seopress_robots_imageindex',
				'_seopress_robots_snippet',
				'_seopress_robots_canonical',
				'_seopress_robots_primary_cat',
				'_seopress_robots_breadcrumbs',
				'_seopress_robots_freeze_modified_date',
				'_seopress_robots_custom_modified_date',
			);

			foreach ( $metas as $key => $value ) {
				if ( ! isset( $params[ $value['key'] ] ) ) {
					continue;
				}
				$item = $params[ $value['key'] ];

				if ( ! in_array( $value['key'], $data_keys_save, true ) ) {
					continue;
				}

				if ( '_seopress_robots_canonical' === $value['key'] ) {
					$item = sanitize_url( $item );
				}

				if (
					'_seopress_robots_primary_cat' === $value['key']
					|| '_seopress_robots_index' === $value['key']
					|| '_seopress_robots_follow' === $value['key']
					|| '_seopress_robots_imageindex' === $value['key']
					|| '_seopress_robots_snippet' === $value['key']
					|| '_seopress_robots_breadcrumbs' === $value['key']
					|| '_seopress_robots_freeze_modified_date' === $value['key']
					|| '_seopress_robots_custom_modified_date' === $value['key']
				) {
					$item = sanitize_text_field( $item );
				}

				if ( ! empty( $item ) ) {
					update_post_meta( $id, $value['key'], $item );
				} else {
					delete_post_meta( $id, $value['key'] );
				}
			}

			// If a custom modified date is set, update post_modified directly.
			$custom_date = isset( $params['_seopress_robots_custom_modified_date'] )
				? sanitize_text_field( $params['_seopress_robots_custom_modified_date'] )
				: '';

			if ( ! empty( $custom_date ) ) {
				$timestamp = strtotime( $custom_date );
				if ( $timestamp ) {
					// Use date() instead of gmdate() since post_modified stores site-local time.
				$date_local = date( 'Y-m-d H:i:s', $timestamp );
					$date_gmt   = get_gmt_from_date( $date_local );

					global $wpdb;
					$wpdb->update(
						$wpdb->posts,
						array(
							'post_modified'     => $date_local,
							'post_modified_gmt' => $date_gmt,
						),
						array( 'ID' => $id ),
						array( '%s', '%s' ),
						array( '%d' )
					);
					clean_post_cache( $id );
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
	 * The Robot Settings process get.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 5.0.0
	 */
	public function processGet( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$metas = MetaRobotSettingsHelper::getMetaKeys( $id );

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
						'value'      => 'checkbox' === $value['type'] ? ( true === $result || $result === 'yes' ? 'yes' : '' ) : $result, // phpcs:ignore
					)
				);
			}
		}

		// Append post modified dates as extra data.
		$post = get_post( $id );
		if ( $post ) {
			$wp_date_format = get_option( 'date_format' );
			$wp_time_format = get_option( 'time_format' );

			$data[] = array(
				'key'   => 'post_modified',
				'value' => $post->post_modified,
			);
			$data[] = array(
				'key'   => 'post_modified_gmt',
				'value' => $post->post_modified_gmt,
			);
			$data[] = array(
				'key'   => 'post_modified_formatted',
				'value' => mysql2date( $wp_date_format . ' ' . $wp_time_format, $post->post_modified ),
			);
		}

		return new \WP_REST_Response( $data );
	}
}

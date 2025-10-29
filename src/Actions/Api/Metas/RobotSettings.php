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
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Robot Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
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
					$post_id      = $request['id'];
					$current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

					if ( ! user_can( $current_user, 'edit_post', $post_id ) ) {
						return false;
					}

					return true;
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

			// Elementor sync.
			$elementor = get_post_meta( $id, '_elementor_page_settings', true );

			$data_keys_save = array(
				'_seopress_robots_index',
				'_seopress_robots_follow',
				'_seopress_robots_imageindex',
				'_seopress_robots_snippet',
				'_seopress_robots_canonical',
				'_seopress_robots_primary_cat',
				'_seopress_robots_breadcrumbs',
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
				) {
					$item = sanitize_text_field( $item );
				}

				if ( ! empty( $item ) ) {
					update_post_meta( $id, $value['key'], $item );
				} else {
					delete_post_meta( $id, $value['key'] );
				}

				if ( ! empty( $elementor ) ) {
					$elementor[ $value['key'] ] = $item;
				}
			}

			if ( ! empty( $elementor ) ) {
				update_post_meta( $id, '_elementor_page_settings', $elementor );
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

		return new \WP_REST_Response( $data );
	}
}

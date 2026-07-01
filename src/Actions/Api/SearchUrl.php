<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\ManualHooks\ApiHeader;

/**
 * Search Url
 */
class SearchUrl implements ExecuteHooks {
	/**
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Search Url hooks.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Search Url register.
	 *
	 * @since 4.7.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/search-url',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'process' ),
				// Admin-only autocomplete: it enumerates post titles/permalinks,
				// so require the same capability needed to edit content.
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'url' => array(
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_string( $param ) && ! empty( $param );
						},
						// This param is a free-text search keyword, not a URL.
						// esc_url_raw() prepends "http://" to schemeless terms
						// (e.g. "blog" => "http://blog"), which breaks the
						// post_name / post_title LIKE search below.
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * The Search Url process.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 4.7.0
	 */
	public function process( \WP_REST_Request $request ) {

		$url = $request->get_param( 'url' );

		$data = seopress_get_service( 'SearchUrl' )->searchByPostName( $url );

		return new \WP_REST_Response( $data );
	}
}

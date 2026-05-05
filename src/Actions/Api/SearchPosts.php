<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Search Posts — lightweight endpoint for post/page autocomplete.
 *
 * @since 9.8.0
 */
class SearchPosts implements ExecuteHooks {

	/**
	 * Register hooks.
	 *
	 * @since 9.8.0
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check — admin only.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 9.8.0
	 *
	 * @return bool
	 */
	public function permissionCheck( \WP_REST_Request $request ) {
		return current_user_can( seopress_capability( 'manage_options', 'dashboard' ) );
	}

	/**
	 * Register the REST route.
	 *
	 * @since 9.8.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/search-posts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'search' => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						},
						'sanitize_callback' => 'sanitize_text_field',
					),
					'ids' => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						},
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Process the search request.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 9.8.0
	 *
	 * @return \WP_REST_Response
	 */
	public function process( \WP_REST_Request $request ) {
		$search = $request->get_param( 'search' );
		$ids    = $request->get_param( 'ids' );

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		// Resolve specific IDs (used on initial load to display titles).
		if ( ! empty( $ids ) ) {
			$id_list = array_filter( array_map( 'absint', explode( ',', $ids ) ) );

			if ( empty( $id_list ) ) {
				return new \WP_REST_Response( array() );
			}

			$posts = get_posts(
				array(
					'post__in'       => $id_list,
					'post_type'      => array_values( $post_types ),
					'post_status'    => array( 'publish', 'draft', 'private' ),
					'posts_per_page' => count( $id_list ),
					'orderby'        => 'post__in',
				)
			);

			return new \WP_REST_Response( $this->format_results( $posts ) );
		}

		// Search by title / ID.
		if ( empty( $search ) ) {
			return new \WP_REST_Response( array() );
		}

		$args = array(
			'post_type'      => array_values( $post_types ),
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 20,
			'orderby'        => 'relevance',
		);

		// If the search term is numeric, also look up by ID.
		if ( is_numeric( $search ) ) {
			$by_id = get_post( absint( $search ) );

			if ( $by_id && in_array( $by_id->post_type, $post_types, true ) ) {
				$args['post__not_in'] = array( $by_id->ID );
			}

			$args['s'] = $search;
			$posts     = get_posts( $args );

			if ( $by_id && in_array( $by_id->post_type, $post_types, true ) ) {
				array_unshift( $posts, $by_id );
			}
		} else {
			$args['s'] = $search;
			$posts     = get_posts( $args );
		}

		return new \WP_REST_Response( $this->format_results( $posts ) );
	}

	/**
	 * Format post results for the REST response.
	 *
	 * @param \WP_Post[] $posts The posts.
	 *
	 * @since 9.8.0
	 *
	 * @return array
	 */
	private function format_results( $posts ) {
		$results = array();

		foreach ( $posts as $post ) {
			$post_type_obj = get_post_type_object( $post->post_type );
			$type_label    = $post_type_obj ? $post_type_obj->labels->singular_name : $post->post_type;

			$results[] = array(
				'id'        => $post->ID,
				'title'     => $post->post_title ? $post->post_title : __( '(no title)', 'wp-seopress' ),
				'post_type' => $type_label,
				'status'    => $post->post_status,
			);
		}

		return $results;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Preview Title Description Meta
 */
class PreviewTitleDescriptionMeta implements ExecuteHooks {
	/**
	 * The current user.
	 *
	 * @var int|null
	 */
	private $current_user;

	/**
	 * The Preview Title Description Meta hooks.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {
		$this->current_user = wp_get_current_user()->ID;
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * The Preview Title Description Meta register.
	 *
	 * @since 4.7.0
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/posts/(?P<id>\d+)/preview-title-description-metas',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'preview' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) { // phpcs:ignore
							return is_numeric( $param );
						},
					),
				),
				'permission_callback' => function ( $request ) {
					$post_id = $request['id'];

					if ( ! user_can( $this->current_user, 'edit_post', $post_id ) ) {
						return false;
					}

					return true;
				},
			)
		);
	}

	/**
	 * The Preview Title Description Meta process preview.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @since 4.7.0
	 */
	public function preview( \WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		$title          = $request->get_param( 'title' );
		$description    = $request->get_param( 'description' );
		$post_thumbnail = get_the_post_thumbnail_url( $id, 'full' ) ? get_the_post_thumbnail_url( $id, 'full' ) : '';

		$post_date = '';
		if ( seopress_get_service( 'TitleOption' )->getSingleCptDate( $id ) ) {
			$post_date = get_the_modified_date( 'M j, Y', $id ) ? get_the_modified_date( 'M j, Y', $id ) : '';
		}

		if ( null === $title && null === $description ) {
			return new \WP_REST_Response(
				array(
					'code'         => 'error',
					'code_message' => 'missing_parameters',
				),
				401
			);
		}

		$context_page = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( $id );

		$context_page->setPostById( $id );
		$context_page->setIsSingle( true );

		$terms = get_the_terms( $id, 'post_tag' );

		if ( ! empty( $terms ) ) {
			$context_page->setHasTag( true );
		}

		$categories = get_the_terms( $id, 'category' );
		if ( ! empty( $categories ) ) {
			$context_page->setHasCategory( true );
		}

		$title       = seopress_get_service( 'TagsToString' )->replace( $title, $context_page->getContext() );
		$description = seopress_get_service( 'TagsToString' )->replace( $description, $context_page->getContext() );

		return new \WP_REST_Response(
			array(
				'title'          => $title,
				'description'    => $description,
				'post_thumbnail' => $post_thumbnail,
				'post_date'      => $post_date,
			)
		);
	}
}

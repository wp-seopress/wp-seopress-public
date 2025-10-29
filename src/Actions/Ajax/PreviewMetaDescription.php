<?php // phpcs:ignore

namespace SEOPress\Actions\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;

/**
 * Preview Meta Description
 */
class PreviewMetaDescription implements ExecuteHooksBackend {
	/**
	 * The Preview Meta Description hooks.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_get_preview_meta_description', array( $this, 'get' ) );
	}

	/**
	 * The Preview Meta Description get.
	 *
	 * @since 4.4.0
	 *
	 * @return array
	 */
	public function get() {
        if ( ! isset($_GET['template'])) { //phpcs:ignore
			wp_send_json_error();
			return;
		}

		$template = stripcslashes( $_GET['template'] ); // phpcs:ignore
		$post_id  = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : null;
		$home_id  = isset( $_GET['home_id'] ) ? (int) $_GET['home_id'] : null;
		$term_id  = isset( $_GET['term_id'] ) ? (int) $_GET['term_id'] : null;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$context_page = seopress_get_service( 'ContextPage' )->buildContextWithCurrentId( (int) $_GET['post_id'] );
		if ( $post_id ) {
			$context_page->setPostById( (int) $_GET['post_id'] );
			$context_page->setIsSingle( true );

			$terms = get_the_terms( $post_id, 'post_tag' );

			if ( ! empty( $terms ) ) {
				$context_page->setHasTag( true );
			}

			$categories = get_the_terms( $post_id, 'category' );
			if ( ! empty( $categories ) ) {
				$context_page->setHasCategory( true );
			}
		}

		if ( $post_id === $home_id && null !== $home_id ) {
			$context_page->setIsHome( true );
		}

		if ( $post_id === $term_id && null !== $term_id ) {
			$context_page->setIsCategory( true );
			$context_page->setTermId( $term_id );
		}

		$value = seopress_get_service( 'TagsToString' )->replace( $template, $context_page->getContext() );

		wp_send_json_success( $value );
	}
}

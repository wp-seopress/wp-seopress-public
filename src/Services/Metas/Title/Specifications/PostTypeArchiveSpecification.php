<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title\Specifications;

/**
 * PostTypeArchiveSpecification
 */
class PostTypeArchiveSpecification {


	/**
	 * The getValue function.
	 *
	 * @param array $params The params.
	 *
	 * @example [
	 *     'context' => array
	 *
	 * ]
	 * @return string
	 */
	public function getValue( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];

		$post_type = isset( $context['post'] ) ? $context['post']->post_type : null;
		$value     = seopress_get_service( 'TitleOption' )->getArchiveCptTitle( $post_type );

		if ( empty( $value ) || ! $value ) {
			return '';
		}

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
	}



	/**
	 * The isSatisfyBy function.
	 *
	 * @param array $params The params.
	 *
	 * @example [
	 *     'post' => \WP_Post
	 *     'title' => string
	 *     'context' => array
	 *
	 * ]
	 * @return boolean
	 */
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];

		if ( $context['is_post_type_archive'] && ! $context['is_tax'] ) {
			$post_type = isset( $context['post'] ) ? $context['post']->post_type : null;
			$value     = seopress_get_service( 'TitleOption' )->getArchiveCptTitle( $post_type );

			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

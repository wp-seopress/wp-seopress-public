<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title\Specifications;

/**
 * StaticHomepageSpecification
 */
class StaticHomepageSpecification {


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
		$title = seopress_get_service( 'TitleOption' )->getHomeSiteTitle();
		if ( empty( $title ) || ! $title ) {
			return '';
		}

		$context = $params['context'];

		return seopress_get_service( 'TagsToString' )->replace( $title, $context );
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
		$title_value = $params['title'];
		$context     = $params['context'];
		$post        = $params['post'];

		if ( $context['is_front_page'] && $post && empty( $title_value ) ) {
			$home_title = seopress_get_service( 'TitleOption' )->getHomeSiteTitle();

			if ( ! empty( $home_title ) ) {
				return true;
			}
		}

		return false;
	}
}

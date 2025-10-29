<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title\Specifications;

/**
 * AuthorSpecification
 */
class AuthorSpecification {


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
		$value = seopress_get_service( 'TitleOption' )->getArchivesAuthorTitle();

		if ( empty( $value ) || ! $value ) {
			return '';
		}

		$context = $params['context'];

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
		$post    = $params['post'];

		if ( $context['is_author'] ) {
			$value = seopress_get_service( 'TitleOption' )->getArchivesAuthorTitle();
			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

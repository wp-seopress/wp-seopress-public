<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * AuthorSpecification
 */
class AuthorSpecification {


	const NAME_SERVICE = 'AuthorDescriptionSpecification';

	/**
	 * The getValue function.
	 *
	 * @param array $params The params.
	 *
	 * @return string
	 */
	public function getValue( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value = seopress_get_service( 'TitleOption' )->getArchivesAuthorDescription();

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
	 *     'description' => string
	 *     'context' => array
	 *
	 * ]
	 * @return boolean
	 */
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];
		$post    = $params['post'];

		if ( $context['is_author'] ) {
			$value = seopress_get_service( 'TitleOption' )->getArchivesAuthorDescription();
			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

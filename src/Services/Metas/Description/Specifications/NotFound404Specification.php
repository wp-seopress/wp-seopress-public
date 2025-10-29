<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * NotFound404Specification
 */
class NotFound404Specification {

	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'NotFound404DescriptionSpecification';

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
		$title = seopress_get_service( 'TitleOption' )->getArchives404Desc();
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
	 *     'description' => string
	 *     'context' => array
	 *
	 * ]
	 * @return boolean
	 */
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];

		if ( $context['is_404'] ) {
			$value = seopress_get_service( 'TitleOption' )->getArchives404Desc();

			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

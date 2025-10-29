<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * DateSpecification
 */
class DateSpecification {


	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'DateDescriptionSpecification';

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
		$value = seopress_get_service( 'TitleOption' )->getArchivesDateDesc();

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

		if ( ! $context['is_date'] ) {
			return false;
		}

		$value = seopress_get_service( 'TitleOption' )->getArchivesDateDesc();

		if ( empty( $value ) ) {
			return false;
		}

		return true;
	}
}

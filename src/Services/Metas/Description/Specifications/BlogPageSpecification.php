<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * BlogPageSpecification
 */
class BlogPageSpecification {

	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'BlogPageDescriptionSpecification';

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
		$value = isset( $params['description'] ) ? $params['description'] : '';

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
		$description_value = $params['description'];
		$context           = $params['context'];
		$post              = $params['post'];

		if ( $context['is_home'] && ! empty( $description_value ) ) {
			return true;
		}

		return false;
	}
}

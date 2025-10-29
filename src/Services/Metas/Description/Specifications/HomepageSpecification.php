<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * HomepageSpecification
 */
class HomepageSpecification {

	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'HomepageDescriptionSpecification';

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
		$value = seopress_get_service( 'TitleOption' )->getHomeDescriptionTitle();

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

		if ( $context['is_front_page'] && $context['is_home'] && isset( $post ) && empty( $description_value ) ) { // HOMEPAGE.

			$value = seopress_get_service( 'TitleOption' )->getHomeDescriptionTitle();
			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

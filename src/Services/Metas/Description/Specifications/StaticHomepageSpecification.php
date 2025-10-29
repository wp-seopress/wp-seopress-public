<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

/**
 * StaticHomepageSpecification
 */
class StaticHomepageSpecification {

	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'StaticHomepageDescriptionSpecification';

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
		$title = seopress_get_service( 'TitleOption' )->getHomeDescriptionTitle();
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
		$description_value = $params['description'];
		$context           = $params['context'];
		$post              = $params['post'];

		if ( $context['is_front_page'] && $post && empty( $description_value ) ) {
			$value = seopress_get_service( 'TitleOption' )->getHomeDescriptionTitle();

			if ( ! empty( $value ) ) {
				return true;
			}
		}

		return false;
	}
}

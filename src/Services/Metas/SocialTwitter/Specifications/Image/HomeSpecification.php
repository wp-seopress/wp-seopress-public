<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;

/**
 * HomeSpecification
 */
class HomeSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'HomeImageSocialTwitterSpecification';

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
	public function getValue( $params ) {

		return $this->applyFilter(
			array(
				'url' => seopress_get_service( 'SocialOption' )->getTwitterImageHome(),
			),
			$params
		);
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
	public function isSatisfyBy( $params ) {
		$context = $params['context'];

		if ( $context['is_home'] && ! empty( $this->getValue() ) ) {
			return true;
		}

		return false;
	}
}

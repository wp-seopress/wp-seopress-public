<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

use SEOPress\Services\Metas\SocialFacebook\Specifications\Image\AbstractImageSpecification;

/**
 * HomeSpecification
 */
class HomeSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'HomeImageSocialFacebookSpecification';

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

		$value = seopress_get_service( 'SocialOption' )->getFacebookImageHomeOption();

		return $this->applyFilter(
			array(
				'url' => seopress_get_service( 'TagsToString' )->replace( $value, $context ),
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

		if ( $context['is_home'] && ! empty( seopress_get_service( 'SocialOption' )->getFacebookImageHomeOption() ) ) {
				return true;
		}

		return false;
	}
}

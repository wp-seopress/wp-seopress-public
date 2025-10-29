<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;

/**
 * SingularApplyAllSpecification
 */
class SingularApplyAllSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'SingularImageApplyAllSocialTwitterSpecification';

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

		$context = $params['context'];

		$post            = $params['post'];
		$GLOBALS['post'] = $post; // phpcs:ignore

		return $this->applyFilter(
			array(
				'url' => seopress_get_service( 'SocialOption' )->getSocialTwitterImg(),
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

		if ( $context['is_singular'] ) {
			$post = $params['post'];

			if ( ! empty( seopress_get_service( 'SocialOption' )->getSocialTwitterImg() ) ) {
				return true;
			}
		}

		return false;
	}
}

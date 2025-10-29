<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;

/**
 * FeaturedImageSpecification
 */
class FeaturedImageSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'FeaturedImageSocialTwitterSpecification';

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

		$post            = $params['post'];
		$GLOBALS['post'] = $post; // phpcs:ignore

		return $this->applyFilter(
			array(
				'url' => get_the_post_thumbnail_url( $post->ID, 'large' ),
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

			if ( has_post_thumbnail( $post->ID ) ) {
				return true;
			}
		}

		return false;
	}
}

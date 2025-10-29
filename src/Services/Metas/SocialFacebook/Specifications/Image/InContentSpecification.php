<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

use SEOPress\Services\Metas\SocialFacebook\Specifications\Image\AbstractImageSpecification;
use SEOPress\Services\Metas\GetImageInContent;

/**
 * InContentSpecification
 */
class InContentSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'InContentSocialFacebookSpecification';


	/**
	 * The getThumbnailInContent function.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string
	 */
	protected function getThumbnailInContent( $post_id ) {
		$manager = new GetImageInContent();
		return $manager->getThumbnailInContentByPostId( $post_id );
	}

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
				'url' => $this->getThumbnailInContent( $post->ID ),
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

			if ( ! empty( $this->getThumbnailInContent( $post->ID ) ) ) {
				return true;
			}
		}

		return false;
	}
}

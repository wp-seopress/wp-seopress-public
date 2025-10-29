<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Title\AbstractTitleSpecification;

/**
 * SingularSpecification
 */
class SingularSpecification extends AbstractTitleSpecification {

	const NAME_SERVICE = 'SingularSocialFacebookSpecification';

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

		$post = $params['post'];

		$value = seopress_get_service( 'SocialOption' )->getFacebookTitlePostOption( $post->ID );

		return $this->applyFilter( seopress_get_service( 'TagsToString' )->replace( $value, $context ) );
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
			if ( ! empty( seopress_get_service( 'SocialOption' )->getFacebookTitlePostOption( $post->ID ) ) ) {
				return true;
			}
		}

		return false;
	}
}

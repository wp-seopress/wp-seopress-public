<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Title\AbstractTitleSpecification;

/**
 * DefaultSocialFacebookSpecification
 */
class DefaultSocialFacebookSpecification extends AbstractTitleSpecification {


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
		$post    = $params['post'];

		$value = get_the_title( $post->ID );
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
		$post = $params['post'];

		return ! empty( get_the_title( $post->ID ) );
	}
}

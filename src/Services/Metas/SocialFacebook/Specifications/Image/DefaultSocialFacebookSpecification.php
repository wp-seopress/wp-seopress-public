<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Image\AbstractImageSpecification;

/**
 * DefaultSocialFacebookSpecification
 */
class DefaultSocialFacebookSpecification extends AbstractImageSpecification {

	const NAME_SERVICE = 'DefaultImageSocialFacebookSpecification';

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

		$site_icon = wp_get_attachment_url( get_option( 'site_icon' ) );

		return $this->applyFilter(
			array(
				'url' => $site_icon,
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

		return ! empty( get_option( 'site_icon' ) );
	}
}

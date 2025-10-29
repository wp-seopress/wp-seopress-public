<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Description;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Description\AbstractDescriptionSpecification;
use SEOPress\Services\Metas\Description\DescriptionMeta;

/**
 * HomeSpecification
 */
class HomeSpecification extends AbstractDescriptionSpecification {

	const NAME_SERVICE = 'HomeDescriptionSocialTwitterSpecification';

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
		$value   = seopress_get_service( 'SocialMeta' )->getFacebookHomeDescription();

		if ( empty( $value ) ) {
			$description_meta = new DescriptionMeta();
			$result           = $description_meta->getValue( $params );
			if ( ! empty( $result ) ) {
				$value = $result;
			}
		}

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
		if ( ! parent::isSatisfyBy( $params ) ) {
			return false;
		}

		$context = $params['context'];
		$post    = $params['post'];

		if ( $context['is_home'] ) {
				return true;
		}

		return false;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook;

use SEOPress\Helpers\Metas\SocialSettings;

/**
 * SocialFacebookMeta
 */
class SocialFacebookMeta {

	/**
	 * The specifications property.
	 *
	 * @var array
	 */
	protected $specifications = array();

	/**
	 * The __construct function.
	 */
	public function __construct() {
		$this->createDefaultSpecifications();
	}

	/**
	 * The createDefaultSpecifications function.
	 */
	protected function createDefaultSpecifications() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$this->specifications = array(
			'title'       => array(
				seopress_get_service( 'TaxonomySocialFacebookSpecification' ),
				seopress_get_service( 'SingularSocialFacebookSpecification' ),
				seopress_get_service( 'WithTitleSocialFacebookSpecification' ),
				seopress_get_service( 'DefaultSocialFacebookSpecification' ), // Don't move, it's the last one, always "yes" for isSatisfyBy.
			),
			'description' => array(
				seopress_get_service( 'HomeDescriptionSocialFacebookSpecification' ),
				seopress_get_service( 'TaxonomyDescriptionSocialFacebookSpecification' ),
				seopress_get_service( 'SingularDescriptionSocialFacebookSpecification' ),
				seopress_get_service( 'DefaultDescriptionSocialFacebookSpecification' ),
			),
			'image'       => array(
				seopress_get_service( 'HomeImageSocialFacebookSpecification' ),
				seopress_get_service( 'FeaturedImageSocialFacebookSpecification' ),
				seopress_get_service( 'InContentSocialFacebookSpecification' ),
				seopress_get_service( 'SingularImageSocialFacebookSpecification' ),
				seopress_get_service( 'SingularImageApplyAllSocialFacebookSpecification' ),
				seopress_get_service( 'DefaultImageSocialFacebookSpecification' ),
			),
		);
	}

	/**
	 * The getMetasForPost function.
	 *
	 * @param array $context The context.
	 *
	 * @return array
	 */
	protected function getMetasForPost( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		header( 'Content-Type: text/html; charset=utf-8' );
		$variables = apply_filters( 'seopress_dyn_variables_fn', array() );

		$post = $variables['post'] ?? $context['post'];

		$data = array(
			'title'         => '',
			'description'   => '',
			'image'         => '',
			'attachment_id' => '',
			'image_width'   => '',
			'image_height'  => '',
		);

		if ( seopress_get_service( 'SocialOption' )->getSocialFacebookOGEnable() !== '1' ) {
			return $data;
		}

		$metas = SocialSettings::getMetaKeysFacebook();

		foreach ( $metas as $key => $value ) {
			$key_social = seopress_get_service( 'SocialMeta' )->getKeySocial( $value['key'] );

			if ( ! isset( $this->specifications[ $key_social ] ) ) {
				continue;
			}

			foreach ( $this->specifications[ $key_social ] as $key_specification => $specification ) {
				if ( ! $specification->isSatisfyBy(
					array(
						'post'    => $post,
						'context' => $context,
					)
				) ) {
					continue;
				}

				$item = $specification->getValue(
					array(
						'post'    => $post,
						'context' => $context,
					)
				);

				if ( 'image' === $key_social ) {
					$data[ $key_social ]   = $item['url'];
					$data['attachment_id'] = isset( $item['attachment_id'] ) ? $item['attachment_id'] : '';
					$data['image_width']   = isset( $item['image_width'] ) ? $item['image_width'] : '';
					$data['image_height']  = isset( $item['image_height'] ) ? $item['image_height'] : '';
				} else {
					$data[ $key_social ] = $item;
				}
				break;
			}
		}

		return $data;
	}

	/**
	 * The getTitleForTaxonomy function.
	 *
	 * @param array $context The context.
	 *
	 * @return string
	 */
	protected function getTitleForTaxonomy( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return '';
	}

	/**
	 * The getValue function.
	 *
	 * @param array $context The context.
	 *
	 * @return string|null
	 */
	public function getValue( $context ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value = null;

		if ( isset( $context['post'] ) && null !== $context['post'] ) {
			$value = $this->getMetasForPost( $context );
		}
		if ( isset( $context['term_id'] ) ) {
			$value = $this->getTitleForTaxonomy( $context );
		}

		if ( null === $value ) {
			return $value;
		}

		return $value;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Title;

/**
 * AbstractTitleSpecification
 */
abstract class AbstractTitleSpecification {


	/**
	 * The applyFilter function.
	 *
	 * @param string $value The value.
	 *
	 * @return string
	 */
	public function applyFilter( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( has_filter( 'seopress_social_og_title' ) ) {
			return apply_filters( 'seopress_social_og_title', $value );
		}

		return $value;
	}
}

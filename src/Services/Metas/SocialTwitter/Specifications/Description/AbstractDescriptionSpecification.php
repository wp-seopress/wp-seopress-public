<?php // phpcs:ignore

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Description;

/**
 * AbstractDescriptionSpecification
 */
abstract class AbstractDescriptionSpecification {

	/**
	 * The applyFilter function.
	 *
	 * @param string $value The value.
	 *
	 * @return string
	 */
	public function applyFilter( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( has_filter( 'seopress_social_og_desc' ) ) {
			return apply_filters( 'seopress_social_og_desc', $value );
		}

		return $value;
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
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];
		if ( $context['is_search'] ) {
			return false;
		}

		if ( function_exists( 'wc_memberships_is_post_content_restricted' ) && wc_memberships_is_post_content_restricted() ) {
			return false;
		}

		return true;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

use SEOPress\Constants\MetasDefaultValues;

/**
 * SingularSpecification
 */
class SingularSpecification {


	/**
	 * The name service.
	 *
	 * @var string
	 */
	const NAME_SERVICE = 'SingularDescriptionSpecification';


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
	public function getValue( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		$post    = $params['post'];
		$value   = $params['description'];
		$context = $params['context'];

		if ( $post ) {
			$context['user_id'] = $post->post_author;
		}

		if ( empty( $value ) || ! $value ) {
			// Global.
			$global_cpt = seopress_get_service( 'TitleOption' )->getSingleCptDesc( $post->ID );
			if ( ! empty( $global_cpt ) ) {
				$value = $global_cpt;
			}
		}

		if ( empty( $value ) || ! $value ) {
			// Default excerpt or content.
			$value = MetasDefaultValues::getPostTypeDescriptionValue();
		}

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
	}


	/**
	 * The isSatisfyBy function.
	 *
	 * @param array $params The params.
	 *
	 * @example [
	 *     'post' => \WP_Post
	 *     'description' => string
	 *     'context' => array
	 *
	 * ]
	 * @return boolean
	 */
	public function isSatisfyBy( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$context = $params['context'];

		if ( $context['is_singular'] ) {
			return true;
		}

		return false;
	}
}

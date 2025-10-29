<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Title\Specifications;

/**
 * BuddyPressSpecification
 */
class BuddyPressSpecification {


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
		$value = seopress_get_service( 'TitleOption' )->getTitleBpGroups();

		if ( empty( $value ) || ! $value ) {
			return '';
		}

		$context = $params['context'];

		return seopress_get_service( 'TagsToString' )->replace( $value, $context );
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
		if ( ! function_exists( 'bp_is_group' ) ) {
			return false;
		}

		if ( ! bp_is_group() ) {
			return false;
		}

		$value = seopress_get_service( 'TitleOption' )->getTitleBpGroups();
		if ( empty( $value ) ) {
			return false;
		}

		return true;
	}
}

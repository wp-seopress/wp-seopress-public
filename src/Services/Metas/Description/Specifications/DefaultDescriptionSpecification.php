<?php // phpcs:ignore

namespace SEOPress\Services\Metas\Description\Specifications;

use SEOPress\Constants\MetasDefaultValues;

/**
 * DefaultDescriptionSpecification
 */
class DefaultDescriptionSpecification {


	/**
	 * The getValue function.
	 *
	 * @param array $params The params.
	 *
	 * @example[
	 *     'context' => array
	 *
	 * ]
	 * @return string
	 */
	public function getValue( $params ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value   = MetasDefaultValues::getPostTypeDescriptionValue();
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
		return true;
	}
}

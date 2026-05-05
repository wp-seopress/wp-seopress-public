<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Social Knowledge Employees
 */
class SocialKnowledgeEmployees implements GetTagValue {

	const NAME = 'social_knowledge_employees';

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialKnowledgeEmployees();

		return apply_filters( 'seopress_get_tag_schema_social_knowledge_employees', $value, $context );
	}
}

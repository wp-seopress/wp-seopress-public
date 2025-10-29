<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Social Knowledge Name
 */
class SocialKnowledgeName implements GetTagValue {
	const NAME = 'social_knowledge_name';

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialKnowledgeName();
		$type  = seopress_get_service( 'SocialOption' )->getSocialKnowledgeType();

		if ( empty( $value ) && 'none' !== $type ) {
			$value = get_bloginfo( 'name' );
		}

		return apply_filters( 'seopress_get_tag_schema_social_knowledge_name', $value, $context );
	}
}

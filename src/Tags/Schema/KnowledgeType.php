<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Knowledge Type
 */
class KnowledgeType implements GetTagValue {
	const NAME = 'knowledge_type';

	/**
	 * Get value
	 *
	 * @since 4.5.0
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialKnowledgeType();

		if ( empty( $value ) ) {
			$value = 'Organization';
		}

		return apply_filters( 'seopress_get_tag_schema_knowledge_type', $value, $context );
	}
}

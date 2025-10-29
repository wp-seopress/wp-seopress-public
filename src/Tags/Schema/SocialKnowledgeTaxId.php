<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Social Knowledge Tax ID
 */
class SocialKnowledgeTaxId implements GetTagValue {

	const NAME = 'social_knowledge_tax_id';

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialKnowledgeTaxID();

		return apply_filters( 'seopress_get_tag_schema_organization_tax_id', $value, $context );
	}
}

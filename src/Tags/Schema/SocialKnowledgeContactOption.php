<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Social Knowledge Contact Option
 */
class SocialKnowledgeContactOption implements GetTagValue {

	const NAME = 'social_knowledge_contact_option';

	/**
	 * Get value
	 *
	 * @param array $args The args.
	 *
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialKnowledgeContactOption();
		if ( 'None' === $value ) {
			$value = '';
		}

		return apply_filters( 'seopress_get_tag_schema_social_knowledge_contact_option', $value, $context );
	}
}

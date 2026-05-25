<?php // phpcs:ignore

namespace SEOPress\Tags\Schema\Date;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Post Date
 */
class PostDate implements GetTagValue {
	const NAME = 'schema_post_date';

	const ALIAS = array( 'post_date_time', 'post_date_c', 'post_date_iso8601', 'post_date_formatted' );

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = '';

		if ( isset( $context['post'] ) ) {
			$value = get_the_date( 'c', $context['post']->ID );
		}

		return apply_filters( 'seopress_get_tag_schema_post_date_value', $value, $context );
	}
}

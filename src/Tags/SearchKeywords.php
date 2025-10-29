<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Search Keywords
 */
class SearchKeywords implements GetTagValue {
	const NAME = 'search_keywords';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Search Keywords', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = get_search_query();

		if ( ! empty( $value ) ) {
			$value = esc_attr( '"' . $value . '"' );
		} else {
			$value = esc_attr( '" "' );
		}

		/**
		 * Filter Search Keywords
		 *
		 * @deprecated 4.4.0
		 * Please use seopress_get_tag_search_keywords_value
		 */
		$value = apply_filters( 'seopress_get_search_query', $value );

		return apply_filters( 'seopress_get_tag_search_keywords_value', $value, $context );
	}
}

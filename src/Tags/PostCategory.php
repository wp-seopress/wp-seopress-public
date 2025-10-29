<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Post Category
 */
class PostCategory implements GetTagValue {
	const NAME = 'post_category';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Post Category', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = '';

		if ( ! $context ) {
			return $value;
		}

		if ( $context['is_single'] && $context['has_category'] && isset( $context['post']->ID ) ) {
			$terms = get_the_terms( $context['post']->ID, 'category' );
			$value = $terms[0]->name;
			/**
			 * Filter Post Category
			 *
			 * @deprecated 4.4.0
			 * Please use seopress_get_tag_post_category_value
			 */
			$value = apply_filters( 'seopress_titles_cat', $value );
		}

		return apply_filters( 'seopress_get_tag_post_category_value', $value, $context );
	}
}

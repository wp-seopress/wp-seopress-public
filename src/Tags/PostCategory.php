<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\Metas\PrimaryTerm;
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
			// The primary category, else the first one, like %%_ct_category%%.
			$term  = PrimaryTerm::get_display_term( $context['post']->ID, 'category' );
			$value = $term instanceof \WP_Term ? $term->name : '';
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

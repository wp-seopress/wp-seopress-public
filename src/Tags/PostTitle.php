<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Post Title
 */
class PostTitle implements GetTagValue {
	const NAME = 'post_title';

	const ALIAS = array( 'title' );

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Post Title', 'wp-seopress' );
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

		if (
			( isset( $context['is_home'] ) || isset( $context['is_single'] ) )
			&& isset( $context['post'] ) && $context['post'] ) {
			$value = get_post_field( 'post_title', $context['post']->ID );
			$value = str_replace( '<br>', ' ', $value );
			$value = esc_attr( wp_strip_all_tags( $value ) );
		}

		return apply_filters( 'seopress_get_tag_post_title_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Post URL
 */
class PostUrl implements GetTagValue {
	const NAME = 'post_url';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Post URL', 'wp-seopress' );
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

		if ( $context['is_single'] && ! empty( $context['post'] ) ) {
			$value = esc_url( get_permalink( $context['post'] ) );
			/**
			 * Filter Post URL
			 *
			 * @deprecated 4.4.0
			 * Please use seopress_get_tag_post_url_value
			 */
			$value = apply_filters( 'seopress_titles_post_url', $value );
		}

		return apply_filters( 'seopress_get_tag_post_url_value', $value, $context );
	}
}

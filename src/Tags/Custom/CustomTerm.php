<?php // phpcs:ignore

namespace SEOPress\Tags\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

/**
 * Custom Term
 */
class CustomTerm extends AbstractCustomTagValue implements GetTagValue {
	const CUSTOM_FORMAT = '_ct_';
	const NAME          = '_ct_your_custom_taxonomy_slug';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Custom Term', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$tag     = isset( $args[1] ) ? $args[1] : null;
		$value   = '';
		if ( null === $tag || ! $context || ! is_array( $context ) ) {
			return $value;
		}

		// The context can be partial (schemas generated without a page context
		// for example), so never assume the key is set.
		$post = isset( $context['post'] ) ? $context['post'] : null;

		if ( ! isset( $post->ID ) ) {
			return $value;
		}

		$regex = $this->buildRegex( self::CUSTOM_FORMAT );

		preg_match( $regex, $tag, $matches );

		if ( empty( $matches ) || ! array_key_exists( 'field', $matches ) ) {
			return $value;
		}

		$field = $matches['field'];

		$terms = wp_get_post_terms( $post->ID, $field );
		if ( is_wp_error( $terms ) ) {
			return $value;
		}

		$value = isset( $terms[0] ) ? esc_attr( $terms[0]->name ) : '';

		/**
		 * Filter to change the value of the custom term.
		 *
		 * @deprecated 4.4.0 Use seopress_get_tag' . $tag . '_value
		 */
		$value = apply_filters( 'seopress_titles_custom_tax', $value, $field );

		return apply_filters( 'seopress_get_tag' . $tag . '_value', $value, $context );
	}
}

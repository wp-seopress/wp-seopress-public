<?php // phpcs:ignore

namespace SEOPress\Tags\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

/**
 * Custom Post Meta
 */
class CustomPostMeta extends AbstractCustomTagValue implements GetTagValue {
	const CUSTOM_FORMAT = '_cf_';
	const NAME          = '_cf_your_custom_field_name';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Custom fields (replace your_custom_field_name by the name of your custom field)', 'wp-seopress' );
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

		// Support both post and term contexts.
		// The context can be partial (schemas generated without a page context
		// for example), so never assume the keys are set.
		$post    = isset( $context['post'] ) ? $context['post'] : null;
		$term_id = isset( $context['term_id'] ) ? $context['term_id'] : null;

		if ( empty( $post ) && empty( $term_id ) ) {
			return $value;
		}
		$regex = $this->buildRegex( self::CUSTOM_FORMAT );

		preg_match( $regex, $tag, $matches );

		if ( empty( $matches ) || ! array_key_exists( 'field', $matches ) ) {
			return $value;
		}

		$field = $matches['field'];

		$length = 50;
		$length = apply_filters( 'seopress_excerpt_length', $length );

		// Get meta value based on context type
		if ( isset( $post->ID ) ) {
			$raw_value = get_post_meta( $post->ID, $field, true );
		} elseif ( $term_id ) {
			$raw_value = get_term_meta( $term_id, $field, true );
		} else {
			$raw_value = '';
		}

		$stored = $raw_value;

		// A custom field can hold an array: ACF Relationship, Post Object with
		// several values, Checkbox, Select multiple, Gallery, Repeater. Render
		// the entries that can be rendered instead of dropping the value, and
		// never hand anything but a string to strip_shortcodes(), which fatals
		// on an array.
		$raw_value = seopress_custom_field_to_string( $raw_value );

		$value = wp_trim_words( esc_attr( stripslashes_deep( wp_filter_nohtml_kses( wp_strip_all_tags( strip_shortcodes( $raw_value ) ) ) ) ), $length );

		/**
		 * Filter the resolved value of a single custom-field variable.
		 *
		 * The stored value is passed as the third argument so a site can build
		 * a structured result, a list of schema @id references for instance,
		 * without reading the meta a second time. It is whatever the database
		 * holds: a string, an array, or anything else a plugin stored there.
		 *
		 * @param string $value     Rendered value.
		 * @param array  $context   Resolution context.
		 * @param mixed  $stored    Stored meta value, before rendering.
		 */
		return apply_filters( 'seopress_get_tag_' . $tag . '_value', $value, $context, $stored );
	}
}

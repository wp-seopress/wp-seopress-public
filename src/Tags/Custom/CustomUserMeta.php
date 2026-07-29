<?php // phpcs:ignore

namespace SEOPress\Tags\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\AbstractCustomTagValue;
use SEOPress\Models\GetTagValue;

/**
 * Custom User Meta
 */
class CustomUserMeta extends AbstractCustomTagValue implements GetTagValue {
	const CUSTOM_FORMAT = '_ucf_';
	const NAME          = '_ucf_your_user_meta';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Custom User Meta', 'wp-seopress' );
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

		if ( empty( $post ) ) {
			return $value;
		}
		$regex = $this->buildRegex( self::CUSTOM_FORMAT );

		preg_match( $regex, $tag, $matches );

		if ( empty( $matches ) || ! array_key_exists( 'field', $matches ) ) {
			return $value;
		}

		$field = $matches['field'];

		$user_id = $context['user_id'] ?? get_current_user_id();

		if ( ! $user_id || intval( $user_id ) === 0 ) {
			if ( isset( $context['is_author'] ) && isset( $context['author']->ID ) ) {
				$author_id = $context['author']->ID;
			}
			if ( isset( $post->post_author ) ) {
				$author_id = $post->post_author;
			}
		}

		$raw_value = get_user_meta( $user_id, $field, true );

		// A user meta can hold an array (multi value meta, ACF repeater...).
		// esc_attr() would cast it and output the literal string "Array".
		$value = is_scalar( $raw_value ) ? esc_attr( (string) $raw_value ) : '';

		return apply_filters( 'seopress_get_tag_' . $tag . '_value', $value, $context );
	}
}

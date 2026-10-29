<?php // phpcs:ignore

namespace SEOPress\Tags\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\Metas\PrimaryTerm;
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

		// An unknown taxonomy leaves the value untouched, as before.
		if ( ! taxonomy_exists( $field ) ) {
			return $value;
		}

		// The primary term when the author picked one, the first otherwise,
		// through the same helper as the published title and description.
		$term  = PrimaryTerm::get_display_term( $post->ID, $field );
		$value = null !== $term ? esc_attr( $term->name ) : '';

		/**
		 * Filter to change the value of the custom term.
		 *
		 * @deprecated 4.4.0 Use seopress_get_tag_{$tag}_value, e.g. seopress_get_tag__ct_category_value.
		 */
		$value = apply_filters( 'seopress_titles_custom_tax', $value, $field );

		// Until 10.4.0 this hook missed the underscore after "tag", unlike every
		// other variable (seopress_get_tag_ct_category_value). Callbacks on the
		// old name keep running.
		$value = apply_filters_deprecated(
			'seopress_get_tag' . $tag . '_value',
			array( $value, $context ),
			'10.4.0',
			'seopress_get_tag_' . $tag . '_value'
		);

		/**
		 * Filter the resolved value of a single custom-term variable.
		 *
		 * The tag keeps its leading underscore, hence the double underscore:
		 * %%_ct_category%% is filtered by seopress_get_tag__ct_category_value,
		 * like %%_cf_price%% by seopress_get_tag__cf_price_value.
		 *
		 * @since 10.4.0
		 *
		 * @param string $value   Rendered value.
		 * @param array  $context Resolution context.
		 */
		return apply_filters( 'seopress_get_tag_' . $tag . '_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * WooCommerce Single Tag
 */
class SingleTag implements GetTagValue {
	const NAME = 'wc_single_tag';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Product Tag', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		if ( ! seopress_get_service( 'WooCommerceActivate' )->isActive() ) {
			return '';
		}

		$value = '';

		if ( ! $context ) {
			return $value;
		}

		if ( is_singular( array( 'product' ) ) || $context['is_product'] ) {
			$terms = get_the_terms( $context['post']->ID, 'product_tag' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				$single_tag = array();

				foreach ( $terms as $term ) {
					$single_tag[ $term->term_id ] = $term->name;
				}

				/**
				 * Filter WooCommerce Single Tag
				 *
				 * @deprecated 4.4.0
				 * Please use seopress_get_tag_wc_single_tag_value
				 */
				$single_tag = apply_filters( 'seopress_titles_product_tag', $single_tag );

				$value = stripslashes_deep( wp_filter_nohtml_kses( join( ', ', $single_tag ) ) );
			}
		}

		return apply_filters( 'seopress_get_tag_wc_single_tag_value', $value, $context );
	}
}

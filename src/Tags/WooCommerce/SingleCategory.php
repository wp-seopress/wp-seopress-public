<?php // phpcs:ignore

namespace SEOPress\Tags\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * WooCommerce Single Category
 */
class SingleCategory implements GetTagValue {
	const NAME = 'wc_single_cat';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Product Category', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = '';

		if ( ! seopress_get_service( 'WooCommerceActivate' )->isActive() ) {
			return $value;
		}

		if ( ! $context ) {
			return $value;
		}

		if ( is_singular( array( 'product' ) ) || $context['is_product'] ) {
			$terms = get_the_terms( $context['post']->ID, 'product_cat' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				$woo_single_cat = array();
				foreach ( $terms as $term ) {
					$woo_single_cat[ $term->term_id ] = $term->name;
				}

				/**
				 * Filter WooCommerce Single Category
				 *
				 * @deprecated 4.4.0
				 * Please use seopress_get_tag_wc_single_cat_value
				 */
				$woo_single_cat = apply_filters( 'seopress_titles_product_cat', $woo_single_cat );

				$value = stripslashes_deep( wp_filter_nohtml_kses( join( ', ', $woo_single_cat ) ) );
			}
		}

		return apply_filters( 'seopress_get_tag_wc_single_cat_value', $value, $context );
	}
}

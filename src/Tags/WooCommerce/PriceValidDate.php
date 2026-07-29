<?php // phpcs:ignore

namespace SEOPress\Tags\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * WooCommerce Price Valid Date
 */
class PriceValidDate implements GetTagValue {
	const NAME = 'wc_price_valid_date';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Product Date On Sale To', 'wp-seopress' );
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

		if ( ( is_singular( array( 'product' ) ) || $context['is_product'] ) && isset( $context['post']->ID ) ) {
			$product = wc_get_product( $context['post']->ID );
			$date    = $product->get_date_on_sale_to();

			// WooCommerce restores the regular price when a sale ends but keeps the sale end
			// date on the product, so this returns a past date long after the sale is over.
			// Google drops the product snippet when priceValidUntil is in the past, so only
			// expose the date while it is still ahead.
			if ( $date && $date->getTimestamp() > time() ) {
				// Google expects priceValidUntil in ISO 8601 (YYYY-MM-DD); the US m-d-Y
				// format was reported as an invalid date in Search Console.
				$value = $date->date( 'Y-m-d' );
			}
		}

		return apply_filters( 'seopress_get_tag_wc_price_valid_date', $value, $context );
	}
}

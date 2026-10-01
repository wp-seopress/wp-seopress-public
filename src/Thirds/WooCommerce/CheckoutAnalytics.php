<?php
/** Checkout-start tracking inside the consent-controlled GA4 payload. */
namespace SEOPress\Thirds\WooCommerce;

defined( 'ABSPATH' ) || exit;

class CheckoutAnalytics {
	/**
	 * Append checkout data only on the initial checkout page.
	 *
	 * @param string $script Existing tracking code.
	 * @return string
	 */
	public function append_event( $script ) {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() || is_wc_endpoint_url( 'order-pay' ) ) {
			return $script;
		}

		$cart = WC()->cart;
		if ( ! $cart ) {
			return $script;
		}

		$items = array();
		$value = 0;
		foreach ( $cart->get_cart() as $line ) {
			$product  = isset( $line['data'] ) ? $line['data'] : null;
			$quantity = isset( $line['quantity'] ) ? (float) $line['quantity'] : 0;
			if ( ! $product instanceof \WC_Product || $quantity <= 0 || ! isset( $line['line_total'] ) ) {
				continue;
			}

			// WooCommerce line totals are after discounts and exclude tax and shipping.
			$total   = max( 0, (float) $line['line_total'] );
			$sku     = $product->get_sku();
			$items[] = array(
				'item_id'   => '' !== $sku ? $sku : (string) $product->get_id(),
				'item_name' => $product->get_name(),
				'quantity'  => $quantity,
				'price'     => $total / $quantity,
			);
			$value += $total;
		}

		if ( empty( $items ) ) {
			return $script;
		}

		$payload = array(
			'currency' => get_woocommerce_currency(),
			'value'    => $value,
			'items'    => $items,
		);
		$event = "\ngtag('event', 'begin_checkout', " . wp_json_encode( $payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ');';

		return $script . apply_filters( 'seopress_gtag_ec_begin_checkout_ev', $event );
	}
}

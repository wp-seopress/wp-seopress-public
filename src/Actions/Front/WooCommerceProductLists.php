<?php
/**
 * GA4 product-list tracking for WooCommerce archives.
 *
 * @package SEOPress
 */
namespace SEOPress\Actions\Front;

defined( 'ABSPATH' ) || exit;

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class WooCommerceProductLists implements ExecuteHooksFrontend {
	public function hooks() {
		if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'print_marker' ) );
		add_filter( 'render_block_core/post-title', array( $this, 'append_block_marker' ), 10, 3 );
		add_filter( 'render_block_woocommerce/product-image', array( $this, 'append_block_marker' ), 10, 3 );
		add_filter( 'seopress_gtag_before_closing_script', array( $this, 'activate_tracking' ) );
	}

	/** Only the supported product archives, with explicitly enabled tracking. */
	private function is_enabled() {
		if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
			return false;
		}
		$options = seopress_get_service( 'GoogleAnalyticsOption' );
		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'is_shop' )
			|| '1' !== seopress_get_toggle_option( 'google-analytics' )
			|| '1' !== $options->getEnableOption() || empty( $options->getGA4() ) ) {
			return false;
		}
		if ( '1' !== $options->searchOptionByKey( 'seopress_google_analytics_view_item_list' )
			&& '1' !== $options->searchOptionByKey( 'seopress_google_analytics_select_item' ) ) {
			return false;
		}
		$excluded = $options->getRoles();
		if ( is_user_logged_in() && is_array( $excluded )
			&& array_intersect( wp_get_current_user()->roles, array_keys( $excluded ) ) ) {
			return false;
		}
		return is_shop() || is_tax( array( 'product_cat', 'product_tag' ) )
			|| ( is_search() && in_array( 'product', (array) get_query_var( 'post_type' ), true ) );
	}

	public function enqueue() {
		if ( $this->is_enabled() ) {
			wp_enqueue_script( 'seopress-product-lists', SEOPRESS_URL_ASSETS . '/js/seopress-product-lists.js', array(), SEOPRESS_VERSION, true );
		}
	}

	/** Inert product data follows the actual rendered card, including AJAX cards. */
	public function print_marker() {
		global $product;
		if ( $this->is_enabled() ) {
			echo $this->marker( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in marker().
		}
	}

	/** Product image blocks receive the current product ID from Product Template. */
	public function append_block_marker( $content, $parsed_block, $block ) {
		if ( ! $this->is_enabled() || empty( $block->context['postId'] ) || 'product' !== get_post_type( $block->context['postId'] ) ) {
			return $content;
		}
		return $content . $this->marker( wc_get_product( absint( $block->context['postId'] ) ) );
	}

	private function marker( $product ) {
		if ( ! $product instanceof \WC_Product || 'publish' !== $product->get_status() ) {
			return '';
		}
		if ( is_tax() ) {
			$term = get_queried_object();
			$list_id = $term->taxonomy . '_' . $term->term_id;
			$list_name = $term->name;
		} elseif ( is_search() ) {
			$list_id = 'product_search';
			$list_name = __( 'Product search results', 'wp-seopress' );
		} else {
			$list_id = 'shop';
			$list_name = get_the_title( wc_get_page_id( 'shop' ) );
		}
		$item = array(
			'item_id' => (string) seopress_get_service( 'WooCommerceAnalyticsService' )->getProductSku( $product ),
			'item_name' => wp_strip_all_tags( $product->get_name() ),
			'quantity' => 1,
		);
		if ( '' !== $product->get_price() ) {
			$item['price'] = (float) $product->get_price();
		}
		$categories = get_the_terms( $product->get_id(), 'product_cat' );
		if ( is_array( $categories ) ) {
			foreach ( array_slice( $categories, 0, 5 ) as $index => $category ) {
				$item[ 0 === $index ? 'item_category' : 'item_category' . ( $index + 1 ) ] = wp_strip_all_tags( $category->name );
			}
		}
		$data = array(
			'url' => get_permalink( $product->get_id() ),
			'currency' => get_woocommerce_currency(),
			'item_list_id' => $list_id,
			'item_list_name' => wp_strip_all_tags( $list_name ),
			'item' => $item,
		);
		return '<span hidden class="seopress-product-list-data" data-seopress-item="' . esc_attr( wp_json_encode( $data ) ) . '"></span>';
	}

	/** This startup code runs only when the existing Analytics payload runs. */
	public function activate_tracking( $script ) {
		if ( ! $this->is_enabled() ) {
			return $script;
		}
		$options = seopress_get_service( 'GoogleAnalyticsOption' );
		$config = array(
			'view' => '1' === $options->searchOptionByKey( 'seopress_google_analytics_view_item_list' ),
			'select' => '1' === $options->searchOptionByKey( 'seopress_google_analytics_select_item' ),
			'requiresConsent' => '1' === $options->getDisable() && '1' !== $options->getHalfDisable(),
		);
		$startup = "\nwindow.seopressProductListTracking = " . wp_json_encode( $config ) . ';';
		foreach ( array( 'view' => 'view_item_list', 'select' => 'select_item' ) as $key => $event ) {
			if ( $config[ $key ] ) {
				$event_script = 'window.seopressProductListTracking.' . $key . ' = function(data) { gtag("event", "' . $event . '", data); };';
				$startup .= apply_filters( 'seopress_gtag_ec_' . $event . '_ev', $event_script );
			}
		}
		return $script . $startup . 'window.dispatchEvent(new Event("seopress:product-list-tracking"));';
	}
}

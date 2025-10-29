<?php // phpcs:ignore

namespace SEOPress\Thirds\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Analytics Service
 */
class WooCommerceAnalyticsService {

	/**
	 * Function to convert an array to a JavaScript object using wp_json_encode
	 *
	 * @param array $array The array to convert.
	 * @return string
	 */
	public function arrayToJs( $array ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$json_object = wp_json_encode( $array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		return substr( $json_object, 1, -1 ); // Remove curly braces from the JSON.
	}

	/**
	 * Get product categories
	 *
	 * @param object $product The product object.
	 * @return array
	 */
	public function getProductCategories( $product ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$categories_out = array();

		$categories = get_the_terms( $product->get_id(), 'product_cat' );

		if ( $categories && ! is_wp_error( $categories ) ) {
			$i = 1;
			foreach ( $categories as $category ) {
				$cat_key = 'item_category_' . $i;
				if ( 1 === $i ) {
					$cat_key = 'item_category';
				}
				$categories_out[ $cat_key ] = esc_js( $category->name );
				++$i;
			}
		}

		return $categories_out;
	}

	/**
	 * Get product SKU, fallback Post ID
	 *
	 * @param object $product The product object.
	 * @return float
	 */
	public function getProductSku( $product ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$sku = $product->get_sku() ? $product->get_sku() : $product->get_id();

		return $sku;
	}

	/**
	 * Add to cart
	 *
	 * @return void
	 */
	public function addToCart() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Get current product.
		global $product;

		// Set data.
		$items_purchased['item_id']   = esc_js( $this->getProductSku( $product ) );
		$items_purchased['item_name'] = esc_js( $product->get_title() );
		$items_purchased['list_name'] = esc_js( get_the_title() );
		$items_purchased['quantity']  = (float) esc_js( 1 );
		$items_purchased['price']     = (float) esc_js( $product->get_price() );
		$items_purchased              = array_merge( $items_purchased, $this->getProductCategories( $product ) );

		$js = "
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                function getParameterByName(name, url) {
                    name = name.replace(/[\[\]]/g, '\\$&');
                    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                        results = regex.exec(url);
                    if (!results) return null;
                    if (!results[2]) return '';
                    return decodeURIComponent(results[2].replace(/\+/g, ' '));
                }

                document.addEventListener('click', function(event){
                    const namedItem = event.target.attributes.getNamedItem('data-product_id')
                    if(!event.target.matches('.ajax_add_to_cart')){
                        return;
                    }

                    var item_id = null;

                    if(!namedItem){
                        try{
                            item_id = getParameterByName('add-to-cart', new URL(event.target.href).search)
                        }
                        catch(e){}
                    }
                    else{
                        item_id = namedItem.value
                    }

                    if(item_id != " . $items_purchased['item_id'] . "){
                        return;
                    }

                    gtag('event', 'add_to_cart', {'items': [ " . wp_json_encode( $items_purchased ) . ' ]});
                })

            });
        </script>
        ';
		$js = apply_filters( 'seopress_gtag_ec_add_to_cart_archive_ev', $js );

		echo $js;
	}

	/**
	 * Single add to cart
	 *
	 * @return void
	 */
	public function singleAddToCart() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Get current product.
		global $product;

		$js = "
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                document.addEventListener('click', function(event){
                    if(!event.target.matches('.single_add_to_cart_button')){
                        return;
                    }

                    const quantity = document.querySelector('input.qty').value || '1';
                    const formProductVariation = document.querySelector('form[data-product_variations]')
                    const variationItem = document.querySelector('.variation_id')

                    let price = " . (float) esc_js( $product->get_price() ) . "
                    if(formProductVariation && variationItem){
                        try{
                            const variations = JSON.parse(formProductVariation.dataset.product_variations)
                            const variationId = variationItem.value
                            for(const variation of variations){
                                if(variation.variation_id == Number(variationId)){
                                    price = variation.display_price
                                }
                            }
                        }
                        catch{
                        }
                    }

                    gtag('event', 'add_to_cart', {
                        'items': [ {
                            'item_id':'" . esc_js( $this->getProductSku( $product ) ) . "',
                            'item_name': '" . esc_js( $product->get_title() ) . "',
                            'list_name': '" . esc_js( get_the_title() ) . "',
                            'quantity': quantity,
                            'price': price,
                            " . $this->arrayToJs( $this->getProductCategories( $product ) ) . '
                        }]
                    });
                })
            });
        </script>
        ';

		$js = apply_filters( 'seopress_gtag_ec_add_to_cart_single_ev', $js );

		echo $js;
	}

	/**
	 * Remove from cart
	 *
	 * @param string $sprintf The sprintf.
	 * @param string $cart_item_key The cart item key.
	 *
	 * @return string
	 */
	public function removeFromCart( $sprintf, $cart_item_key ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Extract cart and get current product data.
		global $woocommerce;
		foreach ( $woocommerce->cart->get_cart() as $key => $item ) {
			if ( $key === $cart_item_key ) {
				$product                     = wc_get_product( $item['product_id'] );
				$items_purchased['quantity'] = (float) $item['quantity'];
			}
		}

		// Get current product.
		if ( $product ) {
			// Set data.
			$items_purchased['item_id']   = esc_js( $this->getProductSku( $product ) );
			$items_purchased['item_name'] = esc_js( $product->get_title() );
			$items_purchased['list_name'] = esc_js( get_the_title() );
			$items_purchased['price']     = (float) esc_js( $product->get_price() );
			$items_purchased              = array_merge( $items_purchased, $this->getProductCategories( $product ) );

			$sprintf .= "
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    document.addEventListener('click', function(event){
                        if(!event.target.matches('.product-remove .remove')){
                            return;
                        }

                        gtag('event', 'remove_from_cart', {'items': [ " . wp_json_encode( $items_purchased ) . ' ]});
                    })

                });
            </script>
            ';
		}

		$sprintf = apply_filters( 'seopress_gtag_ec_remove_from_cart_ev', $sprintf );

		return $sprintf;
	}

	/**
	 * Update cart or checkout
	 *
	 * @return void
	 */
	public function updateCartOrCheckout() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Extract cart.
		global $woocommerce;
		foreach ( $woocommerce->cart->get_cart() as $key => $item ) {
			$product = wc_get_product( $item['product_id'] );
			// Get current product.
			if ( $product ) {
				// Set data.
				$items_purchased['item_id']   = esc_js( $this->getProductSku( $product ) );
				$items_purchased['item_name'] = esc_js( $product->get_title() );
				$items_purchased['list_name'] = esc_js( get_the_title() );
				$items_purchased['quantity']  = (float) esc_js( $item['quantity'] );
				$items_purchased['price']     = (float) esc_js( $product->get_price() );

				$items_purchased = array_merge( $items_purchased, $this->getProductCategories( $product ) );
			}

			$final[] = $items_purchased;
		}

		$js = "
        <script>
        document.addEventListener('DOMContentLoaded', function(){

            document.addEventListener('click', function(event){
                if(!event.target.matches('.actions .button')){
                    return;
                }

                gtag('event', 'remove_from_cart', {'items': " . wp_json_encode( $final ) . '});
            })

        });
        </script>';

		$js = apply_filters( 'seopress_gtag_ec_remove_from_cart_checkout_ev', $js, $final );

		echo $js;
	}

	/**
	 * Single view items details
	 */
	public function singleViewItemsDetails() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		// Get current product.
		global $product;

		$js = "
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                gtag('event', 'view_item', {
                    'items': [{
                        'item_id': '" . esc_js( $this->getProductSku( $product ) ) . "',
                        'item_name': '" . esc_js( $product->get_title() ) . "',
                        'price': " . (float) esc_js( $product->get_price() ) . ",
                        'quantity': 1,
                        " . $this->arrayToJs( $this->getProductCategories( $product ) ) . '
                    }]
                });
            });
        </script>
        ';

		$js = apply_filters( 'seopress_gtag_ec_single_view_details_ev', $js );

		echo $js;
	}
}

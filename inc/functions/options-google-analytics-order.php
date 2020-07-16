<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Google Analytics E-commerce
//=================================================================================================
function seopress_google_analytics_order_received() {
	$seopress_google_analytics_html ='';
	global $woocommerce;
    foreach( WC()->cart->get_cart() as $cart_item ){
        
        $product = wc_get_product($cart_item['product_id']);
        $product_cat = get_the_terms($cart_item['product_id'], 'product_cat');

        $seopress_google_analytics_html .= "
            ga('ec:addProduct', {
                'id': '".$cart_item['product_id']."',
                'name': '".$product->get_name()."',";

            if ($product_cat !='') {
                $seopress_google_analytics_html .= "
                'category': '".$product_cat[0]->name."',";
            }

            if ($product->get_price() !='') {
                $seopress_google_analytics_html .= "
                'price': '".$product->get_price()."',";
            }

            if ($cart_item['quantity'] !='') {
                $seopress_google_analytics_html .= "
                'quantity': ".$cart_item['quantity'];
            }
            $seopress_google_analytics_html .= "
            });
        ";
    }

	$seopress_google_analytics_html .= "\n";
        
    $seopress_google_analytics_html .= "
    ga('ec:setAction', 'purchase', {
	  'id': 'daf9276a-3fa2-45a3-a591-495154662f7f',
	  'revenue': ".$woocommerce->cart->get_cart_total().",
	  'tax': 5,
	  'shipping': 5
	});
    ";

    $seopress_google_analytics_html .= "\n";

    if (function_exists('wc_enqueue_js')) {
    	wc_enqueue_js($seopress_google_analytics_html);
	}
}
add_action('woocommerce_checkout_order_processed', 'seopress_google_analytics_order_received');

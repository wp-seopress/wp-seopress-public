<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Google Analytics E-commerce
//=================================================================================================
//Measuring an Addition from Cart
function seopress_google_analytics_js2($cart_item_key, $product_id, $quantity) {
	if (seopress_google_analytics_ecommerce_enable_option() =='1') {

		$seopress_google_analytics_html = "ga('require', 'ec');";
		$seopress_google_analytics_html .= "\n";

		//If WC enabled
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
            $product = wc_get_product($product_id);

            $product_cat = get_the_terms($product_id, 'product_cat');

            $seopress_google_analytics_html = "
                ga('ec:addProduct', {
                    'id': ".$product_id.",
                    'name': '".$product->get_name()."',";

                if ($product_cat !='') {
                    $seopress_google_analytics_html .= "
                    'category': '".$product_cat[0]->name."',";
                }

                if ($product->get_price() !='') {
                    $seopress_google_analytics_html .= "
                    'price': '".$product->get_price()."',";
                }

                if ($quantity !='') {
                    $seopress_google_analytics_html .= "
                    'quantity': ".$quantity;
                }
            $seopress_google_analytics_html .= "
                });
            ";
            $seopress_google_analytics_html .= "\n";
            
            $seopress_google_analytics_html .= "ga('ec:setAction', 'add');";
            $seopress_google_analytics_html .= "\n";
            
            $seopress_google_analytics_html .= "ga('send', 'event', 'UX', 'click', 'add to cart');";
            $seopress_google_analytics_html .= "\n";
		
			$seopress_google_analytics_html .= "\n";

			if (seopress_google_analytics_enable_option() =='1' && seopress_google_analytics_ua_option() !='') {
				if (is_user_logged_in()) {
					global $wp_roles;
						
					//Get current user role
					if(isset(wp_get_current_user()->roles[0])) {
						$seopress_user_role = wp_get_current_user()->roles[0];
						//If current user role matchs values from SEOPress GA settings then apply
						if (function_exists('seopress_google_analytics_roles_option') && seopress_google_analytics_roles_option() !='') {
							if( array_key_exists( $seopress_user_role, seopress_google_analytics_roles_option())) {
								//do nothing
							} else {
								if (function_exists('wc_enqueue_js')) {
						        	wc_enqueue_js($seopress_google_analytics_html);
						    	}
							}
						} else {
							if (function_exists('wc_enqueue_js')) {
					        	wc_enqueue_js($seopress_google_analytics_html);
					    	}
						}
					}
				} else {
					if (function_exists('wc_enqueue_js')) {
			        	wc_enqueue_js($seopress_google_analytics_html);
			    	}
				}
			}
		}
	}
}
add_action('woocommerce_add_to_cart', 'seopress_google_analytics_js2', 20, 3);		



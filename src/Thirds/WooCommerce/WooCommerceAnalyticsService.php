<?php

namespace SEOPress\Thirds\WooCommerce;

if ( ! defined('ABSPATH')) {
    exit;
}

class WooCommerceAnalyticsService {
    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function measurePurchase() {
        if ( ! function_exists('is_order_received_page') || ! is_order_received_page()) {
            return;
        }
        global $wp;
        $order_id = isset($wp->query_vars['order-received']) ? $wp->query_vars['order-received'] : 0;

        if (0 < $order_id && 1 != get_post_meta($order_id, '_seopress_ga_tracked', true)) {
            $order = wc_get_order($order_id);

            //Check order status
            if (method_exists($order, 'get_status') && ('processing' === $order->get_status()) || 'completed' === $order->get_status()) {
                $items_purchased = [];
                foreach ($order->get_items() as $item) {
                    // Get Product object
                    $_product = wc_get_product($item->get_product_id());

                    if ( ! is_a($_product, 'WC_Product')) {
                        continue;
                    }

                    // init vars
                    $item_id        = $_product->get_id();
                    $variation_id   = 0;
                    $variation_data = null;
                    $categories_js  = null;
                    $categories_out = [];
                    $variant_js     = null;

                    // Set data
                    $items_purchased['id']       = esc_js($item_id);
                    $items_purchased['name']     = esc_js($item->get_name());
                    $items_purchased['quantity'] = (float) esc_js($item->get_quantity());
                    $items_purchased['price']    = (float) esc_js($order->get_item_total($item));

                    // Categories and Variations
                    $categories = get_the_terms($item_id, 'product_cat');
                    if ($item->get_variation_id()) {
                        $variation_id   = $item->get_variation_id();
                        $variation_data = wc_get_product_variation_attributes($variation_id);
                    }

                    // Variations
                    if (is_array($variation_data) && ! empty($variation_data)) {
                        $variant_js = esc_js(wc_get_formatted_variation($variation_data, true));
                        $categories = get_the_terms($item_id, 'product_cat');
                        $item_id    = $variation_id;

                        $items_purchased['variant'] = esc_js($variant_js);
                    }
                    // Categories
                    if ($categories) {
                        foreach ($categories as $category) {
                            $categories_out[] = $category->name;
                        }
                        $categories_js = esc_js(implode('/', $categories_out));

                        $items_purchased['category'] = esc_js($categories_js);
                    }

                    $final[] = $items_purchased;
                }

                $global_purchase = [
                            'transaction_id' => esc_js($order_id),
                            'affiliation'    => esc_js(get_bloginfo('name')),
                            'value'          => (float) esc_js($order->get_total()),
                            'currency'       => esc_js($order->get_currency()),
                            'tax'            => (float) esc_js($order->get_total_tax()),
                            'shipping'       => (float) esc_js($order->get_shipping_total()),
                            'items'          => $final,
                        ];

                $seopress_google_analytics_click_event['purchase_tracking'] = 'gtag(\'event\', \'purchase\',';
                $seopress_google_analytics_click_event['purchase_tracking'] .= json_encode($global_purchase);
                $seopress_google_analytics_click_event['purchase_tracking'] .= ');';
                $seopress_google_analytics_click_event['purchase_tracking'] = apply_filters('seopress_gtag_ec_purchases_ev', $seopress_google_analytics_click_event['purchase_tracking']);

                update_post_meta($order_id, '_seopress_ga_tracked', true);
            }
        }
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function addToCart() {
        // Get current product
        global $product;

        // Set data
        $items_purchased['id']        = esc_js($product->get_id());
        $items_purchased['name']      = esc_js($product->get_title());
        $items_purchased['list_name'] = esc_js(get_the_title());
        $items_purchased['quantity']  = (float) esc_js(1);
        $items_purchased['price']     = (float) esc_js($product->get_price());

        // Extract categories
        $categories = get_the_terms($product->get_id(), 'product_cat');
        if ($categories) {
            foreach ($categories as $category) {
                $categories_out[] = $category->name;
            }
            $categories_js               = esc_js(implode('/', $categories_out));
            $items_purchased['category'] = esc_js($categories_js);
        }

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

                    var id = null

                    if(!namedItem){
                        try{
                            id = getParameterByName('add-to-cart', new URL(event.target.href).search)
                        }
                        catch(e){}
                    }
                    else{
                        console.log('named item')
                        id = namedItem.value
                    }

                    if(id != " . $items_purchased['id'] . "){
                        return;
                    }

                    gtag('event', 'add_to_cart', {'items': [ " . json_encode($items_purchased) . ' ]});
                })

            });
        </script>
        ';
        $js = apply_filters('seopress_gtag_ec_add_to_cart_archive_ev', $js);

        echo $js;
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function singleAddToCart() {
        // Get current product
        global $product;

        // Extract categories
        $categories               = get_the_terms($product->get_id(), 'product_cat');
        $items_purchased_category = '';
        if ($categories) {
            foreach ($categories as $category) {
                $categories_out[] = $category->name;
            }
            $categories_js               = esc_js(implode('/', $categories_out));
            $items_purchased_category    = esc_js($categories_js);
        }

        $js = "
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                document.addEventListener('click', function(event){
                    if(!event.target.matches('.single_add_to_cart_button')){
                        return;
                    }

                    const quantity = document.querySelector('input.qty').value || '1';

                    gtag('event', 'add_to_cart', {
                        'items': [ {
                            'id':'" . esc_js($product->get_id()) . "',
                            'name': '" . esc_js($product->get_title()) . "',
                            'list_name': '" . esc_js(get_the_title()) . "',
                            'quantity': quantity,
                            'price': " . (float) esc_js($product->get_price()) . ",
                            'category': " . json_encode($items_purchased_category) . '
                        }]
                    });
                })

            });
        </script>
        ';

        $js = apply_filters('seopress_gtag_ec_add_to_cart_single_ev', $js);

        echo $js;
    }

    /**
     * @since 4.4.0
     *
     * @param string $sprintf
     * @param string $cart_item_key
     *
     * @return void
     */
    public function removeFromCart($sprintf, $cart_item_key) {
        // Extract cart and get current product data
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $key => $item) {
            if ($key == $cart_item_key) {
                $product                     = wc_get_product($item['product_id']);
                $items_purchased['quantity'] = (float) $item['quantity'];
            }
        }

        // Get current product
        if ($product) {
            // Set data
            $items_purchased['id']        = esc_js($product->get_id());
            $items_purchased['name']      = esc_js($product->get_title());
            $items_purchased['list_name'] = esc_js(get_the_title());
            $items_purchased['price']     = (float) esc_js($product->get_price());

            // Extract categories
            $categories = get_the_terms($product->get_id(), 'product_cat');
            if ($categories) {
                foreach ($categories as $category) {
                    if (is_object($category) && property_exists($category, 'name')) {
                        $categories_out[] = $category->name;
                    } elseif (is_array($category) && isset($category['name'])) {
                        $categories_out[] = $category['name'];
                    }
                }
                $categories_js               = esc_js(implode('/', $categories_out));
                $items_purchased['category'] = esc_js($categories_js);
            }

            $sprintf .= "
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    document.addEventListener('click', function(event){
                        if(!event.target.matches('.product-remove .remove')){
                            return;
                        }

                        gtag('event', 'remove_from_cart', {'items': [ " . json_encode($items_purchased) . ' ]});
                    })

                });
            </script>
            ';
        }

        $sprintf = apply_filters('seopress_gtag_ec_remove_from_cart_ev', $sprintf);

        return $sprintf;
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function updateCartOrCheckout() {
        // Extract cart
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $key => $item) {
            $product = wc_get_product($item['product_id']);
            // Get current product
            if ($product) {
                // Set data
                $items_purchased['id']        = esc_js($product->get_id());
                $items_purchased['name']      = esc_js($product->get_title());
                $items_purchased['list_name'] = esc_js(get_the_title());
                $items_purchased['quantity']  = (float) esc_js($item['quantity']);
                $items_purchased['price']     = (float) esc_js($product->get_price());

                // Extract categories
                $categories = get_the_terms($product->get_id(), 'product_cat');
                if ($categories) {
                    foreach ($categories as $category) {
                        $categories_out[] = $category->name;
                    }
                    $categories_js               = esc_js(implode('/', $categories_out));
                    $items_purchased['category'] = esc_js($categories_js);
                }
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

                gtag('event', 'remove_from_cart', {'items': " . json_encode($final) . "'});
            })

        });
        </script>";

        $js = apply_filters('seopress_gtag_ec_remove_from_cart_checkout_ev', $js, $final);

        echo $js;
    }
}

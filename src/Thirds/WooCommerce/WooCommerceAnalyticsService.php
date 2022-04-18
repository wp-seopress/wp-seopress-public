<?php

namespace SEOPress\Thirds\WooCommerce;

if (! defined('ABSPATH')) {
    exit;
}

class WooCommerceAnalyticsService
{
    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function addToCart()
    {
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

                    var id = null;

                    if(!namedItem){
                        try{
                            id = getParameterByName('add-to-cart', new URL(event.target.href).search)
                        }
                        catch(e){}
                    }
                    else{
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
    public function singleAddToCart()
    {
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
                    const formProductVariation = document.querySelector('form[data-product_variations]')
                    const variationItem = document.querySelector('.variation_id')

                    let price = " . (float) esc_js($product->get_price()) . "
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
                            'id':'" . esc_js($product->get_id()) . "',
                            'name': '" . esc_js($product->get_title()) . "',
                            'list_name': '" . esc_js(get_the_title()) . "',
                            'quantity': quantity,
                            'price': price,
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
    public function removeFromCart($sprintf, $cart_item_key)
    {
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
    public function updateCartOrCheckout()
    {
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

                gtag('event', 'remove_from_cart', {'items': " . json_encode($final) . '});
            })

        });
        </script>';

        $js = apply_filters('seopress_gtag_ec_remove_from_cart_checkout_ev', $js, $final);

        echo $js;
    }
}

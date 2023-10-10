<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

if (is_plugin_active('woocommerce/woocommerce.php')) {
    // Measure Purchases
    $purchasesOptions = seopress_get_service('GoogleAnalyticsOption')->getPurchases();
    if (!$purchasesOptions) {
        return;
    }

    if (function_exists('is_order_received_page') && is_order_received_page()) {
        global $wp;
        $order_id = isset($wp->query_vars['order-received']) ? $wp->query_vars['order-received'] : 0;

        if (0 < $order_id && 1 != get_post_meta($order_id, '_seopress_ga_tracked', true)) {
            $order = wc_get_order($order_id);

            //Check it's a real order
            if (is_bool($order)) {
                return;
            }

            //Check order status
            $status = ['completed', 'processing'];
            $status = apply_filters('seopress_gtag_ec_status', $status);

            if (method_exists($order, 'get_status') && (in_array($order->get_status(), $status))) {
                $items_purchased = [];
                foreach ($order->get_items() as $item) {
                    // Get Product object
                    $_product = wc_get_product($item->get_product_id());

                    if ( ! is_a($_product, 'WC_Product')) {
                        continue;
                    }

                    // init vars
                    $item_id        = $_product->get_sku() ? $_product->get_sku() : $_product->get_id();
                    $variation_id   = 0;
                    $variation_data = null;
                    $categories_js  = null;
                    $categories_out = [];
                    $variant_js     = null;

                    // Set data
                    $items_purchased['item_id']       = esc_js($item_id);
                    $items_purchased['item_name']     = esc_js($item->get_name());
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

                    $items_purchased = array_merge($items_purchased, seopress_get_service('WooCommerceAnalyticsService')->getProductCategories($_product));


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
}

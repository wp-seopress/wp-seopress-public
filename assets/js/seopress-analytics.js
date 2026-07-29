//GA Enhanced Ecommerce
jQuery(document).ready(function ($) {
    // WooCommerce AJAX carts (FunnelKit, XootiX Side Cart, native AJAX add to
    // cart) re-fire updated_cart_totals / added_to_cart on every cart-fragment
    // refresh, including background ones. Without debouncing, a single user
    // action floods admin-ajax.php with seopress_after_update_cart requests
    // (sustained high CPU) and can send duplicate GA4 events. Coalesce a burst
    // of events into a single request on the trailing edge.
    var seopressCartUpdateTimer = null;
    var seopressCartDebounceMs = 500;

    function seopressSendCartEvent() {
        $.ajax({
            method: 'GET',
            url: seopressAjaxAnalytics.seopress_analytics,
            data: {
                action: 'seopress_after_update_cart',
                _ajax_nonce: seopressAjaxAnalytics.seopress_nonce,
            },
            success: function (data) {
                jQuery('body').append(data.data);
            },
        });
    }

    jQuery(document.body).on('updated_cart_totals wc_cart_emptied removed_from_cart added_to_cart', function () {
        if (seopressCartUpdateTimer) {
            clearTimeout(seopressCartUpdateTimer);
        }
        seopressCartUpdateTimer = setTimeout(seopressSendCartEvent, seopressCartDebounceMs);
    });
});

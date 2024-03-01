<?php

namespace SEOPress\Actions\Front;


use SEOPress\Core\Hooks\ExecuteHooksFrontend;
use SEOPress\ManualHooks\Thirds\WooCommerce\WooCommerceAnalytics;

class AMP implements ExecuteHooksFrontend {
    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        if (is_plugin_active('wp-seopress-pro/seopress-pro.php') && defined('SEOPRESS_PRO_VERSION') && version_compare(SEOPRESS_PRO_VERSION, '5.4', '<')) { //Quick fix to prevent fatal error for SEOPress < 5.4
            return;
        }

        add_action('wp', [$this, 'amp_compatibility_wp'], 0);
        add_action('wp_head', [$this, 'amp_compatibility_wp_head'], 0);

    }

    /**
     * AMP Compatibility - wp action hook
     *
     * @since 5.9.0
     *
     * @return void
     */

    public function amp_compatibility_wp() {

        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            wp_dequeue_script( 'seopress-accordion' );

            remove_filter( 'seopress_google_analytics_html', 'seopress_google_analytics_js', 10);

            remove_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);

            remove_action('wp_enqueue_scripts', 'seopress_google_analytics_cookies_js', 20, 1);

            remove_action( 'wp_head', 'seopress_load_google_analytics_options', 0 );
        }
    }

    /**
     * AMP Compatibility - wp_head action hook
     *
     * @since 5.9.0
     *
     * @return void
     */

    public function amp_compatibility_wp_head() {
        if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
            wp_dequeue_script( 'seopress-accordion' );
        }
    }
}

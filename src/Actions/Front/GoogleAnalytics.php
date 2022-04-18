<?php

namespace SEOPress\Actions\Front;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;
use SEOPress\ManualHooks\Thirds\WooCommerce\WooCommerceAnalytics;

class GoogleAnalytics implements ExecuteHooksFrontend {
    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        add_action('seopress_google_analytics_html', [$this, 'analytics'], 10, 1);
    }

    public function analytics($echo) {
        if (('' != seopress_google_analytics_ua_option() || '' != seopress_google_analytics_ga4_option()) && '1' == seopress_google_analytics_enable_option()) {
            if (seopress_get_service('WooCommerceActivate')->isActive()) {
                $woocommerceAnalyticsHook = new WooCommerceAnalytics();
                $woocommerceAnalyticsHook->hooks();
            }
        }
    }
}

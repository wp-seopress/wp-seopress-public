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
        if ('1' === seopress_get_service('GoogleAnalyticsOption')->getHalfDisable() || (((isset($_COOKIE['seopress-user-consent-accept']) && '1' == $_COOKIE['seopress-user-consent-accept']) && '1' === seopress_get_service('GoogleAnalyticsOption')->getDisable()) || ('1' !== seopress_get_service('GoogleAnalyticsOption')->getDisable()))) { //User consent cookie OK
            if (is_user_logged_in()) {
                global $wp_roles;

                //Get current user role
                if (isset(wp_get_current_user()->roles[0])) {
                    $seopress_user_role = wp_get_current_user()->roles[0];
                    //If current user role matchs values from SEOPress GA settings then apply
                    if (!empty(seopress_get_service('GoogleAnalyticsOption')->getRoles())) {
                        if (array_key_exists($seopress_user_role, seopress_get_service('GoogleAnalyticsOption')->getRoles())) {
                            //do nothing
                        } else {
                            add_action('init', [$this, 'analytics'], 10, 1);
                        }
                    } else {
                        add_action('init', [$this, 'analytics'], 10, 1);
                    }
                }
            } else {
                add_action('init', [$this, 'analytics'], 10, 1);
            }
        }
    }

    public function analytics($echo) {
        if ('' !== seopress_get_service('GoogleAnalyticsOption')->getGA4() && '1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption()) {
            if (seopress_get_service('WooCommerceActivate')->isActive()) {
                $woocommerceAnalyticsHook = new WooCommerceAnalytics();
                $woocommerceAnalyticsHook->hooks();
            }
        }
    }
}

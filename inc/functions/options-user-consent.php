<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

if ('1' === seopress_get_service('GoogleAnalyticsOption')->getHalfDisable() || (((isset($_COOKIE['seopress-user-consent-accept']) && '1' == $_COOKIE['seopress-user-consent-accept']) && '1' === seopress_get_service('GoogleAnalyticsOption')->getDisable()) || ('1' !== seopress_get_service('GoogleAnalyticsOption')->getDisable()))) { //User consent cookie OK

    $addToCartOption = seopress_get_service('GoogleAnalyticsOption')->getAddToCart();
    $removeFromCartOption = seopress_get_service('GoogleAnalyticsOption')->getRemoveFromCart();
    $getViewItemsDetails = seopress_get_service('GoogleAnalyticsOption')->getViewItemsDetails();

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
                    if ('1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption() && '' !== seopress_get_service('GoogleAnalyticsOption')->getGA4()) {
                        add_action('wp_head', 'seopress_google_analytics_js_arguments', 929, 1);
                        add_action('wp_head', 'seopress_custom_tracking_hook', 900, 1);
                    }
                    if ('1' === seopress_get_service('GoogleAnalyticsOption')->getMatomoEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoId() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId()) {
                        add_action('wp_head', 'seopress_matomo_js_arguments', 960, 1);
                        add_action('wp_body_open', 'seopress_matomo_nojs', 960, 1);
                    }
                    if ('1' === seopress_get_service('GoogleAnalyticsOption')->getClarityEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId()) {
                        add_action('wp_head', 'seopress_clarity_js_arguments', 970, 1);
                    }
                    add_action('wp_head', 'seopress_custom_tracking_head_hook', 980, 1);
                    add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
                    add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

                    //ecommerce
                    $purchasesOptions = seopress_get_service('GoogleAnalyticsOption')->getPurchases();
                    if ('1' === $purchasesOptions || '1' === $addToCartOption || '1' === $removeFromCartOption || '1' === $getViewItemsDetails) {
                        add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
                    }
                }
            } else {
                if ('1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption() && '' !== seopress_get_service('GoogleAnalyticsOption')->getGA4()) {
                    add_action('wp_head', 'seopress_google_analytics_js_arguments', 929, 1);
                    add_action('wp_head', 'seopress_custom_tracking_hook', 900, 1);
                }
                if ('1' === seopress_get_service('GoogleAnalyticsOption')->getMatomoEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoId() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId()) {
                    add_action('wp_head', 'seopress_matomo_js_arguments', 960, 1);
                    add_action('wp_body_open', 'seopress_matomo_nojs', 960, 1);
                }
                if ('1' === seopress_get_service('GoogleAnalyticsOption')->getClarityEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId()) {
                    add_action('wp_head', 'seopress_clarity_js_arguments', 970, 1);
                }
                add_action('wp_head', 'seopress_custom_tracking_head_hook', 980, 1); //Oxygen: if prioriry >= 990, nothing will be outputed
                add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
                add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

                //ecommerce
                $purchasesOptions = seopress_get_service('GoogleAnalyticsOption')->getPurchases();
                if ('1' === $purchasesOptions || '1' === $addToCartOption || '1' === $removeFromCartOption || '1' === $getViewItemsDetails) {
                    add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
                }
            }
        }
    } else {
        if ('1' === seopress_get_service('GoogleAnalyticsOption')->getEnableOption() && '' !== seopress_get_service('GoogleAnalyticsOption')->getGA4()) {
            add_action('wp_head', 'seopress_google_analytics_js_arguments', 929, 1);
            add_action('wp_head', 'seopress_custom_tracking_hook', 900, 1);
        }
        if ('1' === seopress_get_service('GoogleAnalyticsOption')->getMatomoEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoId() && '' !== seopress_get_service('GoogleAnalyticsOption')->getMatomoSiteId()) {
            add_action('wp_head', 'seopress_matomo_js_arguments', 960, 1);
            add_action('wp_body_open', 'seopress_matomo_nojs', 960, 1);
        }
        if ('1' === seopress_get_service('GoogleAnalyticsOption')->getClarityEnable() && '' !== seopress_get_service('GoogleAnalyticsOption')->getClarityProjectId()) {
            add_action('wp_head', 'seopress_clarity_js_arguments', 970, 1);
        }
        add_action('wp_head', 'seopress_custom_tracking_head_hook', 980, 1);
        add_action('wp_body_open', 'seopress_custom_tracking_body_hook', 1020, 1);
        add_action('wp_footer', 'seopress_custom_tracking_footer_hook', 1030, 1);

        //ecommerce
        $purchasesOptions = seopress_get_service('GoogleAnalyticsOption')->getPurchases();
        if ('1' === $purchasesOptions || '1' === $addToCartOption || '1' === $removeFromCartOption || '1' === $getViewItemsDetails) {
            add_action('wp_enqueue_scripts', 'seopress_google_analytics_ecommerce_js', 20, 1);
        }
    }
}

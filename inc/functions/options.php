<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Permalink structure for TrailingSlash
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_advanced_advanced_trailingslash_option()
{
    $seopress_advanced_advanced_trailingslash_option = get_option('seopress_advanced_option_name');
    if (! empty($seopress_advanced_advanced_trailingslash_option)) {
        foreach ($seopress_advanced_advanced_trailingslash_option as $key => $seopress_advanced_advanced_trailingslash_value) {
            $options[$key] = $seopress_advanced_advanced_trailingslash_value;
        }
        if (isset($seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'])) {
            return $seopress_advanced_advanced_trailingslash_option['seopress_advanced_advanced_trailingslash'];
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Core
///////////////////////////////////////////////////////////////////////////////////////////////////
// Instant Indexing
if ('1' == seopress_get_toggle_option('instant-indexing')) {
    require_once dirname(__FILE__) . '/options-instant-indexing.php';
}

//Import / Export tool
add_action('init', 'seopress_enable', 999);
function seopress_enable()
{
    if (is_admin()) {
        require_once dirname(__FILE__) . '/options-import-export.php'; //Import Export
    }
}

//Front END
if ('1' == seopress_get_toggle_option('titles')) {
    //Author archive Disabled
    function seopress_titles_archives_author_disable_option()
    {
        $seopress_titles_archives_author_disable_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_archives_author_disable_option)) {
            foreach ($seopress_titles_archives_author_disable_option as $key => $seopress_titles_archives_author_disable_value) {
                $options[$key] = $seopress_titles_archives_author_disable_value;
            }
            if (isset($seopress_titles_archives_author_disable_option['seopress_titles_archives_author_disable'])) {
                return $seopress_titles_archives_author_disable_option['seopress_titles_archives_author_disable'];
            }
        }
    }

    //Date archive Disabled
    function seopress_titles_archives_date_disable_option()
    {
        $seopress_titles_archives_date_disable_option = get_option('seopress_titles_option_name');
        if (! empty($seopress_titles_archives_date_disable_option)) {
            foreach ($seopress_titles_archives_date_disable_option as $key => $seopress_titles_archives_date_disable_value) {
                $options[$key] = $seopress_titles_archives_date_disable_value;
            }
            if (isset($seopress_titles_archives_date_disable_option['seopress_titles_archives_date_disable'])) {
                return $seopress_titles_archives_date_disable_option['seopress_titles_archives_date_disable'];
            }
        }
    }

    function seopress_titles_disable_archives()
    {
        global $wp_query;

        if ('1' == seopress_titles_archives_author_disable_option() && $wp_query->is_author && ! is_feed()) {
            wp_redirect(get_home_url(), '301');
            exit;
        }
        if ('1' == seopress_titles_archives_date_disable_option() && $wp_query->is_date && ! is_feed()) {
            wp_redirect(get_home_url(), '301');
            exit;
        }

        return false;
    }

    //SEO metaboxes
    function seopress_hide_metaboxes()
    {
        if (is_admin()) {
            global $typenow;
            global $pagenow;

            //Post type?
            if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                function seopress_titles_single_enable_option()
                {
                    global $post;
                    $seopress_get_current_cpt = get_post_type($post);

                    $seopress_titles_single_enable_option = get_option('seopress_titles_option_name');
                    if (! empty($seopress_titles_single_enable_option)) {
                        foreach ($seopress_titles_single_enable_option as $key => $seopress_titles_single_enable_value) {
                            $options[$key] = $seopress_titles_single_enable_value;
                        }
                        if (isset($seopress_titles_single_enable_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['enable'])) {
                            return $seopress_titles_single_enable_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['enable'];
                        }
                    }
                }
                function seopress_titles_single_enable_metabox($seopress_get_post_types)
                {
                    global $post;
                    if (1 == seopress_titles_single_enable_option() && '' != get_post_type($post)) {
                        unset($seopress_get_post_types[get_post_type($post)]);
                    }

                    return $seopress_get_post_types;
                }
                add_filter('seopress_metaboxe_seo', 'seopress_titles_single_enable_metabox');
                add_filter('seopress_metaboxe_content_analysis', 'seopress_titles_single_enable_metabox');
                add_filter('seopress_pro_metaboxe_sdt', 'seopress_titles_single_enable_metabox');
            }

            //Taxonomy?
            if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
                if (! empty($_GET['taxonomy'])) {
                    $seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));

                    function seopress_tax_single_enable_option($seopress_get_current_tax)
                    {
                        $seopress_tax_single_enable_option = get_option('seopress_titles_option_name');
                        if (! empty($seopress_tax_single_enable_option)) {
                            foreach ($seopress_tax_single_enable_option as $key => $seopress_tax_single_enable_value) {
                                $options[$key] = $seopress_tax_single_enable_value;
                            }
                            if (isset($seopress_tax_single_enable_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['enable'])) {
                                return $seopress_tax_single_enable_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['enable'];
                            }
                        }
                    }

                    function seopress_tax_single_enable_metabox($seopress_get_taxonomies)
                    {
                        $seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));
                        if (1 == seopress_tax_single_enable_option($seopress_get_current_tax) && '' != $seopress_get_current_tax) {
                            unset($seopress_get_taxonomies[$seopress_get_current_tax]);
                        }

                        return $seopress_get_taxonomies;
                    }
                    add_filter('seopress_metaboxe_term_seo', 'seopress_tax_single_enable_metabox');
                }
            }
        }
    }
    add_action('after_setup_theme', 'seopress_hide_metaboxes');

    //Titles and metas
    add_action('template_redirect', 'seopress_titles_disable_archives', 0);
    add_action('wp_head', 'seopress_load_titles_options', 0);
    function seopress_load_titles_options()
    {
        if (! is_admin()) {
            if ((function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && Ecwid_Store_Page::is_store_page())) {//disable on wpForo pages to avoid conflicts
                //do nothing
            } else {
                require_once dirname(__FILE__) . '/options-titles-metas.php'; //Titles & metas
            }
        }
    }
}
if ('1' == seopress_get_toggle_option('social')) {
    add_action('init', 'seopress_load_oembed_options');
    function seopress_load_oembed_options()
    {
        if (! is_admin()) {
            require_once dirname(__FILE__) . '/options-oembed.php'; //Oembed
        }
    }

    add_action('wp_head', 'seopress_load_social_options', 0);
    function seopress_load_social_options()
    {
        if (! is_admin()) {
            //disable on wpForo, LifterLMS private area, Ecwid store pages to avoid conflicts
            if ((function_exists('is_llms_private_area') && is_llms_private_area()) || (function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && Ecwid_Store_Page::is_store_page())) {
                //do nothing
            } else {
                require_once dirname(__FILE__) . '/options-social.php'; //Social
            }
        }
    }
}
if ('1' == seopress_get_toggle_option('google-analytics')) {
    //Enabled
    function seopress_google_analytics_enable_option()
    {
        $seopress_google_analytics_enable_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_enable_option)) {
            foreach ($seopress_google_analytics_enable_option as $key => $seopress_google_analytics_enable_value) {
                $options[$key] = $seopress_google_analytics_enable_value;
            }
            if (isset($seopress_google_analytics_enable_option['seopress_google_analytics_enable'])) {
                return $seopress_google_analytics_enable_option['seopress_google_analytics_enable'];
            }
        }
    }

    //UA
    function seopress_google_analytics_ua_option()
    {
        $seopress_google_analytics_ua_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_ua_option)) {
            foreach ($seopress_google_analytics_ua_option as $key => $seopress_google_analytics_ua_value) {
                $options[$key] = $seopress_google_analytics_ua_value;
            }
            if (isset($seopress_google_analytics_ua_option['seopress_google_analytics_ua'])) {
                return $seopress_google_analytics_ua_option['seopress_google_analytics_ua'];
            }
        }
    }

    //GA4 (measurement ID)
    function seopress_google_analytics_ga4_option()
    {
        $seopress_google_analytics_ga4_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_ga4_option)) {
            foreach ($seopress_google_analytics_ga4_option as $key => $seopress_google_analytics_ga4_value) {
                $options[$key] = $seopress_google_analytics_ga4_value;
            }
            if (isset($seopress_google_analytics_ga4_option['seopress_google_analytics_ga4'])) {
                return $seopress_google_analytics_ga4_option['seopress_google_analytics_ga4'];
            }
        }
    }

    //User roles
    function seopress_google_analytics_roles_option()
    {
        $seopress_google_analytics_roles_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_roles_option)) {
            foreach ($seopress_google_analytics_roles_option as $key => $seopress_google_analytics_roles_value) {
                $options[$key] = $seopress_google_analytics_roles_value;
            }
            if (isset($seopress_google_analytics_roles_option['seopress_google_analytics_roles'])) {
                return $seopress_google_analytics_roles_option['seopress_google_analytics_roles'];
            }
        }
    }

    //Ecommerce enabled
    function seopress_google_analytics_ecommerce_enable_option()
    {
        $seopress_google_analytics_ecommerce_enable_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_ecommerce_enable_option)) {
            foreach ($seopress_google_analytics_ecommerce_enable_option as $key => $seopress_google_analytics_ecommerce_enable_value) {
                $options[$key] = $seopress_google_analytics_ecommerce_enable_value;
            }
            if (isset($seopress_google_analytics_ecommerce_enable_option['seopress_google_analytics_e_commerce_enable'])) {
                return $seopress_google_analytics_ecommerce_enable_option['seopress_google_analytics_e_commerce_enable'];
            }
        }
    }

    //Disable Tracking
    function seopress_google_analytics_disable_option()
    {
        $seopress_google_analytics_disable_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_disable_option)) {
            foreach ($seopress_google_analytics_disable_option as $key => $seopress_google_analytics_disable_value) {
                $options[$key] = $seopress_google_analytics_disable_value;
            }
            if (isset($seopress_google_analytics_disable_option['seopress_google_analytics_disable'])) {
                return $seopress_google_analytics_disable_option['seopress_google_analytics_disable'];
            }
        }
    }

    //Auto accept user consent
    function seopress_google_analytics_half_disable_option()
    {
        $seopress_google_analytics_half_disable_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_half_disable_option)) {
            foreach ($seopress_google_analytics_half_disable_option as $key => $seopress_google_analytics_half_disable_value) {
                $options[$key] = $seopress_google_analytics_half_disable_value;
            }
            if (isset($seopress_google_analytics_half_disable_option['seopress_google_analytics_half_disable'])) {
                return $seopress_google_analytics_half_disable_option['seopress_google_analytics_half_disable'];
            }
        }
    }

    //Disable Tracking - Message
    function seopress_google_analytics_opt_out_msg_option()
    {
        $seopress_google_analytics_opt_out_msg_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_opt_out_msg_option)) {
            foreach ($seopress_google_analytics_opt_out_msg_option as $key => $seopress_google_analytics_opt_out_msg_value) {
                $options[$key] = $seopress_google_analytics_opt_out_msg_value;
            }
            if (isset($seopress_google_analytics_opt_out_msg_option['seopress_google_analytics_opt_out_msg'])) {
                return $seopress_google_analytics_opt_out_msg_option['seopress_google_analytics_opt_out_msg'];
            }
        }
    }

    //Cookie expiration date
    function seopress_google_analytics_cb_exp_date_option()
    {
        $seopress_google_analytics_cb_exp_date_option = get_option('seopress_google_analytics_option_name');
        if (! empty($seopress_google_analytics_cb_exp_date_option)) {
            foreach ($seopress_google_analytics_cb_exp_date_option as $key => $seopress_google_analytics_cb_exp_date_value) {
                $options[$key] = $seopress_google_analytics_cb_exp_date_value;
            }
            if (isset($seopress_google_analytics_cb_exp_date_option['seopress_google_analytics_cb_exp_date'])) {
                return $seopress_google_analytics_cb_exp_date_option['seopress_google_analytics_cb_exp_date'];
            }
        }
    }

    //User Consent JS
    function seopress_google_analytics_cookies_js()
    {
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_register_script('seopress-cookies', plugins_url('assets/js/seopress-cookies' . $prefix . '.js', dirname(dirname(__FILE__))), [], SEOPRESS_VERSION, true);
        wp_enqueue_script('seopress-cookies');

        wp_enqueue_script('seopress-cookies-ajax', plugins_url('assets/js/seopress-cookies-ajax' . $prefix . '.js', dirname(dirname(__FILE__))), ['jquery', 'seopress-cookies'], SEOPRESS_VERSION, true);

        $days = 30;

        if (seopress_google_analytics_cb_exp_date_option()) {
            $days = seopress_google_analytics_cb_exp_date_option();
        }
        $days = apply_filters('seopress_cookies_expiration_days', $days);

        $seopress_cookies_user_consent = [
            'seopress_nonce'                   => wp_create_nonce('seopress_cookies_user_consent_nonce'),
            'seopress_cookies_user_consent'    => admin_url('admin-ajax.php'),
            'seopress_cookies_expiration_days' => $days,
        ];
        wp_localize_script('seopress-cookies-ajax', 'seopressAjaxGAUserConsent', $seopress_cookies_user_consent);
    }

    //Triggers WooCommerce JS
    function seopress_google_analytics_ecommerce_js()
    {
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('seopress-analytics', plugins_url('assets/js/seopress-analytics' . $prefix . '.js', dirname(dirname(__FILE__))), [], SEOPRESS_VERSION, true);

        $seopress_analytics = [
            'seopress_nonce'                => wp_create_nonce('seopress_analytics_nonce'),
            'seopress_analytics' 			        => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-analytics', 'seopressAjaxAnalytics', $seopress_analytics);
    }

    //Ecommerce
    function seopress_after_update_cart()
    {
        check_ajax_referer('seopress_analytics_nonce');

        $items_purchased = [];
        $final           = [];
        // Extract cart
        global $woocommerce;
        foreach ($woocommerce->cart->get_cart() as $key => $item) {
            $product = wc_get_product($item['product_id']);
            // Get current product
            if ($product) {
                // Set data
                $items_purchased['id']       = esc_js($product->get_id());
                $items_purchased['name']     = esc_js($product->get_title());
                $items_purchased['quantity'] = (float) esc_js($item['quantity']);
                $items_purchased['price']    = (float) esc_js($product->get_price());

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

        $html = "<script>gtag('event', 'add_to_cart', {'items': " . json_encode($final) . ' });</script>';

        $html = apply_filters('seopress_gtag_ec_add_to_cart_checkout_ev', $html);

        wp_send_json_success($html);
    }
    add_action('wp_ajax_seopress_after_update_cart', 'seopress_after_update_cart');
    add_action('wp_ajax_nopriv_seopress_after_update_cart', 'seopress_after_update_cart');

    if ('1' == seopress_google_analytics_disable_option()) {
        if (is_user_logged_in()) {
            global $wp_roles;

            //Get current user role
            if (isset(wp_get_current_user()->roles[0])) {
                $seopress_user_role = wp_get_current_user()->roles[0];
                //If current user role matchs values from SEOPress GA settings then apply
                if (function_exists('seopress_google_analytics_roles_option') && '' != seopress_google_analytics_roles_option()) {
                    if (array_key_exists($seopress_user_role, seopress_google_analytics_roles_option())) {
                        //do nothing
                    } else {
                        add_action('wp_enqueue_scripts', 'seopress_google_analytics_cookies_js', 20, 1);
                    }
                } else {
                    add_action('wp_enqueue_scripts', 'seopress_google_analytics_cookies_js', 20, 1);
                }
            } else {
                add_action('wp_enqueue_scripts', 'seopress_google_analytics_cookies_js', 20, 1);
            }
        } else {
            add_action('wp_enqueue_scripts', 'seopress_google_analytics_cookies_js', 20, 1);
        }
    }

    add_action('wp_head', 'seopress_load_google_analytics_options', 0);
    function seopress_load_google_analytics_options()
    {
        require_once dirname(__FILE__) . '/options-google-analytics.php'; //Google Analytics + Matomo
    }

    function seopress_cookies_user_consent()
    {
        //check_ajax_referer( 'seopress_cookies_user_consent_nonce', $_GET['_ajax_nonce'], true );
        if ('1' == seopress_google_analytics_half_disable_option()) {//no user consent required
            wp_send_json_success();
        } else {
            if (is_user_logged_in()) {
                global $wp_roles;

                //Get current user role
                if (isset(wp_get_current_user()->roles[0])) {
                    $seopress_user_role = wp_get_current_user()->roles[0];
                    //If current user role matchs values from SEOPress GA settings then apply
                    if (function_exists('seopress_google_analytics_roles_option') && '' != seopress_google_analytics_roles_option()) {
                        if (array_key_exists($seopress_user_role, seopress_google_analytics_roles_option())) {
                            //do nothing
                        } else {
                            include_once dirname(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                            $data 					          = [];
                            $data['gtag_js'] 		  = seopress_google_analytics_js(false);
                            $data['matomo_js'] 		= seopress_matomo_js(false);
                            $data['body_js'] 		  = seopress_google_analytics_body_code(false);
                            $data['head_js'] 		  = seopress_google_analytics_head_code(false);
                            $data['footer_js'] 		= seopress_google_analytics_footer_code(false);
                            $data['custom'] 		   = '';
                            $data['custom'] 		   = apply_filters('seopress_custom_tracking', $data['custom']);
                            wp_send_json_success($data);
                        }
                    } else {
                        include_once dirname(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                        $data 					          = [];
                        $data['gtag_js'] 		  = seopress_google_analytics_js(false);
                        $data['matomo_js'] 		= seopress_matomo_js(false);
                        $data['body_js'] 		  = seopress_google_analytics_body_code(false);
                        $data['head_js'] 		  = seopress_google_analytics_head_code(false);
                        $data['footer_js'] 		= seopress_google_analytics_footer_code(false);
                        $data['custom'] 		   = '';
                        $data['custom'] 		   = apply_filters('seopress_custom_tracking', $data['custom']);
                        wp_send_json_success($data);
                    }
                }
            } else {
                include_once dirname(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                $data 					          = [];
                $data['gtag_js'] 		  = seopress_google_analytics_js(false);
                $data['matomo_js'] 		= seopress_matomo_js(false);
                $data['body_js'] 		  = seopress_google_analytics_body_code(false);
                $data['head_js'] 		  = seopress_google_analytics_head_code(false);
                $data['footer_js'] 		= seopress_google_analytics_footer_code(false);
                $data['custom'] 		   = '';
                $data['custom'] 		   = apply_filters('seopress_custom_tracking', $data['custom']);
                wp_send_json_success($data);
            }
        }
    }
    add_action('wp_ajax_seopress_cookies_user_consent', 'seopress_cookies_user_consent');
    add_action('wp_ajax_nopriv_seopress_cookies_user_consent', 'seopress_cookies_user_consent');
}

add_action('wp', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options()
{
    if (function_exists('is_plugin_active') && is_plugin_active('thrive-visual-editor/thrive-visual-editor.php') && is_admin()) {
        return;
    }
    if (! is_admin()) {
        require_once dirname(__FILE__) . '/options-redirections.php'; //Redirections
    }
}

if ('1' == seopress_get_toggle_option('xml-sitemap')) {
    add_action('init', 'seopress_load_sitemap', 999);
    function seopress_load_sitemap()
    {
        if (! is_admin()) {
            require_once dirname(__FILE__) . '/options-sitemap.php'; //XML / HTML Sitemap
        }
    }
}
if ('1' == seopress_get_toggle_option('advanced')) {
    //Remove comment author url
    function seopress_advanced_advanced_comments_author_url_option()
    {
        $seopress_advanced_advanced_comments_author_url_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_comments_author_url_option)) {
            foreach ($seopress_advanced_advanced_comments_author_url_option as $key => $seopress_advanced_advanced_comments_author_url_value) {
                $options[$key] = $seopress_advanced_advanced_comments_author_url_value;
            }
            if (isset($seopress_advanced_advanced_comments_author_url_option['seopress_advanced_advanced_comments_author_url'])) {
                return $seopress_advanced_advanced_comments_author_url_option['seopress_advanced_advanced_comments_author_url'];
            }
        }
    }
    if ('1' == seopress_advanced_advanced_comments_author_url_option()) {
        add_filter('get_comment_author_url', '__return_empty_string');
    }

    //Remove website field in comments
    function seopress_advanced_advanced_comments_website_option()
    {
        $seopress_advanced_advanced_comments_website_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_comments_website_option)) {
            foreach ($seopress_advanced_advanced_comments_website_option as $key => $seopress_advanced_advanced_comments_website_value) {
                $options[$key] = $seopress_advanced_advanced_comments_website_value;
            }
            if (isset($seopress_advanced_advanced_comments_website_option['seopress_advanced_advanced_comments_website'])) {
                return $seopress_advanced_advanced_comments_website_option['seopress_advanced_advanced_comments_website'];
            }
        }
    }
    if ('1' == seopress_advanced_advanced_comments_website_option()) {
        function seopress_advanced_advanced_comments_website_hook($fields)
        {
            unset($fields['url']);

            return $fields;
        }
        add_filter('comment_form_default_fields', 'seopress_advanced_advanced_comments_website_hook', 40);
    }

    add_action('wp_head', 'seopress_load_advanced_options', 0);
    function seopress_load_advanced_options()
    {
        if (! is_admin()) {
            require_once dirname(__FILE__) . '/options-advanced.php'; //Advanced
        }
    }
    add_action('init', 'seopress_load_advanced_admin_options', 11);
    function seopress_load_advanced_admin_options()
    {
        require_once dirname(__FILE__) . '/options-advanced-admin.php'; //Advanced (admin)
        //Admin bar
        function seopress_advanced_appearance_adminbar_option()
        {
            $seopress_advanced_appearance_adminbar_option = get_option('seopress_advanced_option_name');
            if (! empty($seopress_advanced_appearance_adminbar_option)) {
                foreach ($seopress_advanced_appearance_adminbar_option as $key => $seopress_advanced_appearance_adminbar_value) {
                    $options[$key] = $seopress_advanced_appearance_adminbar_value;
                }
                if (isset($seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'])) {
                    return $seopress_advanced_appearance_adminbar_option['seopress_advanced_appearance_adminbar'];
                }
            }
        }

        if ('' != seopress_advanced_appearance_adminbar_option()) {
            add_action('admin_bar_menu', 'seopress_advanced_appearance_adminbar_hook', 999);

            function seopress_advanced_appearance_adminbar_hook($wp_admin_bar)
            {
                $wp_admin_bar->remove_node('seopress_custom_top_level');
            }
        }
    }

    //Add nofollow noopener noreferrer to comments form link
    function seopress_advanced_advanced_comments_form_link_option()
    {
        $seopress_advanced_advanced_comments_form_link_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_comments_form_link_option)) {
            foreach ($seopress_advanced_advanced_comments_form_link_option as $key => $seopress_advanced_advanced_comments_form_link_value) {
                $options[$key] = $seopress_advanced_advanced_comments_form_link_value;
            }
            if (isset($seopress_advanced_advanced_comments_form_link_option['seopress_advanced_advanced_comments_form_link'])) {
                return $seopress_advanced_advanced_comments_form_link_option['seopress_advanced_advanced_comments_form_link'];
            }
        }
    }
    if ('1' == seopress_advanced_advanced_comments_form_link_option()) {
        /* Custom attributes on comment link */
        add_filter('comments_popup_link_attributes', 'seopress_comments_popup_link_attributes');
        function seopress_comments_popup_link_attributes($attr) {
            $attr = 'rel="nofollow noopener noreferrer"';
            return $attr;
        }
    }

    //primary category
    function seopress_titles_primary_cat_hook($cats_0, $cats, $post)
    {
        $primary_cat	= null;

        if ($post) {
            $_seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);
            if (isset($_seopress_robots_primary_cat) && '' != $_seopress_robots_primary_cat && 'none' != $_seopress_robots_primary_cat) {
                if (null != $post->post_type && 'post' == $post->post_type) {
                    $primary_cat = get_category($_seopress_robots_primary_cat);
                }
                if (! is_wp_error($primary_cat) && null != $primary_cat) {
                    return $primary_cat;
                }
            } else {
                //no primary cat
                return $cats_0;
            }
        } else {
            return $cats_0;
        }
    }
    add_filter('post_link_category', 'seopress_titles_primary_cat_hook', 10, 3);

    function seopress_titles_primary_wc_cat_hook($terms_0, $terms, $post)
    {
        $primary_cat	= null;

        $id = get_the_ID();

        if (function_exists('wc_get_product')) {
            $post		= wc_get_product($id);
        }
        if ($post) {
            $_seopress_robots_primary_cat = get_post_meta($id, '_seopress_robots_primary_cat', true);

            if (isset($_seopress_robots_primary_cat) && '' != $_seopress_robots_primary_cat && 'none' != $_seopress_robots_primary_cat) {
                if (null != $post->post_type && 'product' == $post->post_type) {
                    $primary_cat = get_term($_seopress_robots_primary_cat, 'product_cat');
                }
                if (! is_wp_error($primary_cat) && null != $primary_cat) {
                    return $primary_cat;
                }
            } else {
                //no primary cat
                return $terms_0;
            }
        } else {
            return $terms_0;
        }
    }
    add_filter('wc_product_post_type_link_product_cat', 'seopress_titles_primary_wc_cat_hook', 10, 3);

    //No /category/ in URL
    function seopress_advanced_advanced_category_url_option()
    {
        $seopress_advanced_advanced_category_url_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_category_url_option)) {
            foreach ($seopress_advanced_advanced_category_url_option as $key => $seopress_advanced_advanced_category_url_value) {
                $options[$key] = $seopress_advanced_advanced_category_url_value;
            }
            if (isset($seopress_advanced_advanced_category_url_option['seopress_advanced_advanced_category_url'])) {
                return $seopress_advanced_advanced_category_url_option['seopress_advanced_advanced_category_url'];
            }
        }
    }

    if ('' != seopress_advanced_advanced_category_url_option()) {
        //Flush permalinks when creating/editing/deleting post categories
        add_action('created_category', 'flush_rewrite_rules');
        add_action('delete_category', 'flush_rewrite_rules');
        add_action('edited_category', 'flush_rewrite_rules');

        //@credits : WordPress VIP
        add_filter('category_rewrite_rules', 'seopress_filter_category_rewrite_rules');
        function seopress_filter_category_rewrite_rules($rules)
        {
            if (class_exists('Sitepress')) {
                global $sitepress;
                remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
                $categories = get_categories(['hide_empty' => false]);
                add_filter('terms_clauses', [$sitepress, 'terms_clauses'], 10, 4);
            } else {
                $categories = get_categories(['hide_empty' => false]);
            }
            if (is_array($categories) && ! empty($categories)) {
                $slugs = [];

                foreach ($categories as $category) {
                    if (is_object($category) && ! is_wp_error($category)) {
                        if (0 == $category->category_parent) {
                            $slugs[] = $category->slug;
                        } else {
                            $slugs[] = trim(get_category_parents($category->term_id, false, '/', true), '/');
                        }
                    }
                }

                if (! empty($slugs)) {
                    $rules = [];

                    foreach ($slugs as $slug) {
                        $rules['(' . $slug . ')/feed/(feed|rdf|rss|rss2|atom)?/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                        $rules['(' . $slug . ')/(feed|rdf|rss|rss2|atom)/?$']       = 'index.php?category_name=$matches[1]&feed=$matches[2]';
                        $rules['(' . $slug . ')(/page/(\d+))?/?$']                  = 'index.php?category_name=$matches[1]&paged=$matches[3]';
                    }
                }
            }
            $rules = apply_filters('seopress_category_rewrite_rules', $rules);

            return $rules;
        }

        function seopress_remove_category_base($termlink, $term, $taxonomy)
        {
            if ('category' == $taxonomy) {
                $category_base = get_option('category_base');

                if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
                    $category_base = apply_filters('wpml_translate_single_string', 'category', 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE);
                }

                if ('' == $category_base) {
                    $category_base = 'category';
                }

                $category_base = apply_filters('seopress_remove_category_base', $category_base);

                if ('/' == substr($category_base, 0, 1)) {
                    $category_base = substr($category_base, 1);
                }
                $category_base .= '/';

                return preg_replace('`' . preg_quote($category_base, '`') . '`u', '', $termlink, 1);
            } else {
                return $termlink;
            }
        }
        add_filter('term_link', 'seopress_remove_category_base', 10, 3);

        add_action('template_redirect', 'seopress_category_redirect', 1);
        function seopress_category_redirect()
        {
            if (!is_category()) {
                return;
            }
            global $wp;

            if (seopress_advanced_advanced_trailingslash_option()) {
                $current_url = home_url(add_query_arg([], $wp->request));
            } else {
                $current_url = trailingslashit(home_url(add_query_arg([], $wp->request)));
            }

            $category_base = get_option('category_base');

            if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
                $category_base = apply_filters('wpml_translate_single_string', 'category', 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE);
            }

            $category_base = apply_filters('seopress_remove_category_base', $category_base);

            if ('' != $category_base) {
                $regex = sprintf('/\/%s\//', str_replace('/', '\/', $category_base));
                if (preg_match($regex, $current_url)) {
                    $new_url = str_replace('/' . $category_base, '', $current_url);
                    wp_redirect($new_url, 301);
                    exit();
                }
            } else {
                $category_base = 'category';
                $regex         = sprintf('/\/%s\//', str_replace('/', '\/', $category_base));
                if (preg_match($regex, $current_url)) {
                    $new_url = str_replace('/' . $category_base, '', $current_url);
                    wp_redirect($new_url, 301);
                    exit();
                }
            }
        }
    }

    //No /product-category/ in URL
    function seopress_advanced_advanced_product_category_url_option()
    {
        $seopress_advanced_advanced_product_category_url_option = get_option('seopress_advanced_option_name');
        if (! empty($seopress_advanced_advanced_product_category_url_option)) {
            foreach ($seopress_advanced_advanced_product_category_url_option as $key => $seopress_advanced_advanced_product_category_url_value) {
                $options[$key] = $seopress_advanced_advanced_product_category_url_value;
            }
            if (isset($seopress_advanced_advanced_product_category_url_option['seopress_advanced_advanced_product_cat_url'])) {
                return $seopress_advanced_advanced_product_category_url_option['seopress_advanced_advanced_product_cat_url'];
            }
        }
    }

    if ('' != seopress_advanced_advanced_product_category_url_option()) {
        //Flush permalinks when creating/editing/deleting product categories
        add_action('created_product_cat', 'flush_rewrite_rules');
        add_action('delete_product_cat', 'flush_rewrite_rules');
        add_action('edited_product_cat', 'flush_rewrite_rules');

        add_filter('product_cat_rewrite_rules', 'seopress_filter_product_category_rewrite_rules');
        function seopress_filter_product_category_rewrite_rules($rules)
        {
            if (class_exists('Sitepress')) {
                global $sitepress;
                remove_filter('terms_clauses', [$sitepress, 'terms_clauses']);
                $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
                add_filter('terms_clauses', [$sitepress, 'terms_clauses'], 10, 4);
            } else {
                $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
            }
            if (is_array($categories) && ! empty($categories)) {
                $slugs = [];

                foreach ($categories as $category) {
                    if (is_object($category) && ! is_wp_error($category)) {
                        if (0 == $category->parent) {
                            $slugs[] = $category->slug;
                        } else {
                            $slugs[] = trim(get_term_parents_list($category->term_id, 'product_cat', ['separator' => '/', 'link' => false]), '/');
                        }
                    }
                }

                if (! empty($slugs)) {
                    $rules = [];
                    foreach ($slugs as $slug) {
                        $rules['(' . $slug . ')(/page/(\d+))?/?$']                  = 'index.php?product_cat=$matches[1]&paged=$matches[3]';
                        $rules[$slug . '/(.+?)/page/?([0-9]{1,})/?$']                = 'index.php?product_cat=$matches[1]&paged=$matches[2]';
                        $rules[$slug . '/(.+?)/?$']                                  = 'index.php?product_cat=$matches[1]';

                        $rules[$slug . '/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                        $rules[$slug . '/(.+?)/(feed|rdf|rss|rss2|atom)/?$']      = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
                        $rules[$slug . '/(.+?)/embed/?$']                         = 'index.php?product_cat=$matches[1]&embed=true';
                    }
                }
            }
            $rules = apply_filters('seopress_product_category_rewrite_rules', $rules);

            return $rules;
        }

        function seopress_remove_product_category_base($termlink, $term, $taxonomy)
        {
            if ('product_cat' == $taxonomy) {
                $category_base = get_option('woocommerce_permalinks');
                $category_base = $category_base['category_base'];

                if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
                    $category_base = apply_filters('wpml_translate_single_string', 'product_cat', 'WordPress', 'URL product category tax slug', ICL_LANGUAGE_CODE);
                }

                if ('' == $category_base) {
                    $category_base = 'product-category';
                }

                $category_base = apply_filters('seopress_remove_category_base', $category_base);

                if ('/' == substr($category_base, 0, 1)) {
                    $category_base = substr($category_base, 1);
                }
                $category_base .= '/';

                return preg_replace('`' . preg_quote($category_base, '`') . '`u', '', $termlink, 1);
            } else {
                return $termlink;
            }
        }
        add_filter('term_link', 'seopress_remove_product_category_base', 10, 3);

        add_action('template_redirect', 'seopress_product_category_redirect', 1);
        function seopress_product_category_redirect()
        {
            global $wp;

            if (seopress_advanced_advanced_trailingslash_option()) {
                $current_url = home_url(add_query_arg([], $wp->request));
            } else {
                $current_url = trailingslashit(home_url(add_query_arg([], $wp->request)));
            }

            $category_base = get_option('woocommerce_permalinks');
            $category_base = $category_base['category_base'];

            if (class_exists('Sitepress') && defined('ICL_LANGUAGE_CODE')) {
                $category_base = apply_filters('wpml_translate_single_string', 'product_cat', 'WordPress', 'URL product category tax slug', ICL_LANGUAGE_CODE);
            }

            $category_base = apply_filters('seopress_remove_product_category_base', $category_base);

            if ('' != $category_base) {
                if (preg_match('/\/' . $category_base . '\//', $current_url)) {
                    $new_url = str_replace('/' . $category_base, '', $current_url);
                    wp_redirect($new_url, 301);
                    exit();
                }
            } else {
                $category_base = 'product-category';

                if (preg_match('/\/' . $category_base . '\//', $current_url)) {
                    $new_url = str_replace('/' . $category_base, '', $current_url);
                    wp_redirect($new_url, 301);
                    exit();
                }
            }
        }
    }
}

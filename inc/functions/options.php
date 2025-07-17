<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/* Instant Indexing */
require_once dirname(__FILE__) . '/options-instant-indexing.php';

/* Import / Export tool */
add_action('init', 'seopress_enable', 999);
function seopress_enable()
{
    if (is_admin()) {
        require_once dirname(__FILE__) . '/options-import-export.php';
    }
}

/* Front END */
if ('1' == seopress_get_toggle_option('titles')) {
    function seopress_titles_disable_archives()
    {
        global $wp_query;

        $url = apply_filters( 'seopress_disable_archives_redirect_url', get_home_url() );
        $status = apply_filters( 'seopress_disable_archives_redirect_status' , '301' );

        if ('1' === seopress_get_service('TitleOption')->getArchiveAuthorDisable() && $wp_query->is_author && ! is_feed()) {
            wp_redirect($url, $status);
            exit;
        }
        if ('1' === seopress_get_service('TitleOption')->getArchiveDateDisable() && $wp_query->is_date && ! is_feed()) {
            wp_redirect($url, $status);
            exit;
        }

        return false;
    }

    /* SEO metaboxes */
    function seopress_hide_metaboxes()
    {
        if (is_admin()) {
            global $typenow;
            global $pagenow;

            /* Post type? */
            if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
                function seopress_titles_single_enable_metabox($seopress_get_post_types)
                {
                    global $post;

                    if ('1' === seopress_get_service('TitleOption')->getSingleCptEnable($post->post_type) && isset($post->post_type)) {
                        unset($seopress_get_post_types[$post->post_type]);
                    }

                    return $seopress_get_post_types;
                }
                add_filter('seopress_metaboxe_seo', 'seopress_titles_single_enable_metabox');
                add_filter('seopress_metaboxe_content_analysis', 'seopress_titles_single_enable_metabox');
                add_filter('seopress_pro_metaboxe_sdt', 'seopress_titles_single_enable_metabox');
            }

            /* Taxonomy? */
            if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
                if (! empty($_GET['taxonomy'])) {
                    $seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));

                    function seopress_tax_single_enable_metabox($seopress_get_taxonomies)
                    {
                        $seopress_get_current_tax = sanitize_title(esc_attr($_GET['taxonomy']));
                        if ('1' === seopress_get_service('TitleOption')->getTaxEnable($seopress_get_current_tax) && '' !== $seopress_get_current_tax) {
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

    /* Titles and metas */
    add_action('template_redirect', 'seopress_titles_disable_archives', 0);
    add_action('wp_head', 'seopress_load_titles_options', 0);
    function seopress_load_titles_options()
    {
        if (! is_admin()) {
            /* disable on wpForo, Ecwid store pages to avoid conflicts */
            if ((function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && \Ecwid_Store_Page::is_store_page())) {
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
            if ((function_exists('is_llms_private_area') && is_llms_private_area()) || (function_exists('is_wpforo_page') && is_wpforo_page()) || (class_exists('Ecwid_Store_Page') && \Ecwid_Store_Page::is_store_page())) {
                //do nothing
            } else {
                require_once dirname(__FILE__) . '/options-social.php'; //Social
            }
        }
    }
}
if ('1' === seopress_get_toggle_option('google-analytics') && !isset($_GET['bricks'])) {
    //User Consent JS
    function seopress_google_analytics_cookies_js()
    {
        $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_register_script('seopress-cookies', plugins_url('assets/js/seopress-cookies' . $prefix . '.js', dirname(dirname(__FILE__))), [], SEOPRESS_VERSION, true);
        wp_enqueue_script('seopress-cookies');

        wp_enqueue_script('seopress-cookies-ajax', plugins_url('assets/js/seopress-cookies-ajax' . $prefix . '.js', dirname(dirname(__FILE__))), ['jquery', 'seopress-cookies'], SEOPRESS_VERSION, true);

        $days = 30;

        if (seopress_get_service('GoogleAnalyticsOption')->getCbExpDate()) {
            $days = seopress_get_service('GoogleAnalyticsOption')->getCbExpDate();
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
                $items_purchased['item_id']       = esc_js($product->get_sku() ? $product->get_sku() : $product->get_id());
                $items_purchased['item_name']     = esc_js($product->get_title());
                $items_purchased['list_name'] = esc_js(get_the_title());
                $items_purchased['quantity'] = (float) esc_js($item['quantity']);
                $items_purchased['price']    = (float) esc_js($product->get_price());
                $items_purchased = array_merge($items_purchased, seopress_get_service('WooCommerceAnalyticsService')->getProductCategories($product));
            }
            $final[] = $items_purchased;
        }

        $html = "<script>gtag('event', 'add_to_cart', {'items': " . wp_json_encode($final) . ' });</script>';

        $html = apply_filters('seopress_gtag_ec_add_to_cart_checkout_ev', $html);

        wp_send_json_success($html);
    }
    add_action('wp_ajax_seopress_after_update_cart', 'seopress_after_update_cart');
    add_action('wp_ajax_nopriv_seopress_after_update_cart', 'seopress_after_update_cart');

    if ('1' === seopress_get_service('GoogleAnalyticsOption')->getDisable()) {
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
        //Google Analytics
        require_once plugin_dir_path(__FILE__) . '/options-google-analytics.php';

        //MATOMO
        require_once plugin_dir_path(__FILE__) . '/options-matomo.php';

        //Clarity
        require_once plugin_dir_path(__FILE__) . '/options-clarity.php';

        //User Consent
        require_once plugin_dir_path(__FILE__) . '/options-user-consent.php';
    }

    function seopress_cookies_user_consent() {
        if ('1' === seopress_get_service('GoogleAnalyticsOption')->getHalfDisable()) {//no user consent required
            wp_send_json_success();
        } else {
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
                            require_once plugin_dir_path(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                            require_once plugin_dir_path(__FILE__) . '/options-matomo.php'; //Matomo
                            require_once plugin_dir_path(__FILE__) . '/options-clarity.php'; //Clarity
                            $data = [];
                            $data['gtag_js'] = seopress_google_analytics_js(false);
                            $data['matomo_js'] = seopress_matomo_js(false);
                            $data['clarity_js'] = seopress_clarity_js(false);
                            $data['body_js'] = seopress_google_analytics_body_code(false);
                            $data['matomo_body_js'] = seopress_matomo_body_js(false);
                            $data['head_js'] = seopress_google_analytics_head_code(false);
                            $data['footer_js'] = seopress_google_analytics_footer_code(false);
                            $data['custom'] = '';
                            $data['custom'] = apply_filters('seopress_custom_tracking', $data['custom']);
                            wp_send_json_success($data);
                        }
                    } else {
                        require_once plugin_dir_path(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                        require_once plugin_dir_path(__FILE__) . '/options-matomo.php'; //Matomo
                        require_once plugin_dir_path(__FILE__) . '/options-clarity.php'; //Clarity
                        $data 					          = [];
                        $data['gtag_js'] 		  = seopress_google_analytics_js(false);
                        $data['matomo_js'] 		= seopress_matomo_js(false);
                        $data['clarity_js'] 		= seopress_clarity_js(false);
                        $data['body_js'] 		  = seopress_google_analytics_body_code(false);
                        $data['matomo_body_js'] = seopress_matomo_body_js(false);
                        $data['head_js'] 		  = seopress_google_analytics_head_code(false);
                        $data['footer_js'] 		= seopress_google_analytics_footer_code(false);
                        $data['custom'] 		   = '';
                        $data['custom'] 		   = apply_filters('seopress_custom_tracking', $data['custom']);
                        wp_send_json_success($data);
                    }
                }
            } else {
                require_once plugin_dir_path(__FILE__) . '/options-google-analytics.php'; //Google Analytics
                require_once plugin_dir_path(__FILE__) . '/options-matomo.php'; //Matomo
                require_once plugin_dir_path(__FILE__) . '/options-clarity.php'; //Clarity
                $data 					          = [];
                $data['gtag_js'] 		  = seopress_google_analytics_js(false);
                $data['matomo_js'] 		= seopress_matomo_js(false);
                $data['clarity_js'] 		= seopress_clarity_js(false);
                $data['body_js'] 		  = seopress_google_analytics_body_code(false);
                $data['matomo_body_js'] = seopress_matomo_body_js(false);
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

    function seopress_cookies_user_consent_close() {
        require_once plugin_dir_path(__FILE__) . '/options-google-analytics.php'; //Google Analytics
        require_once plugin_dir_path(__FILE__) . '/options-clarity.php'; //Clarity

        $data = [];
        $data['gtag_consent_js'] = seopress_google_analytics_js(false);
        $data['clarity_consent_js'] = seopress_clarity_js(false);

        wp_send_json_success($data);
    }
    add_action('wp_ajax_seopress_cookies_user_consent_close', 'seopress_cookies_user_consent_close');
    add_action('wp_ajax_nopriv_seopress_cookies_user_consent_close', 'seopress_cookies_user_consent_close');
}

add_action('wp', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options()
{
    if (function_exists('is_plugin_active') && is_plugin_active('thrive-visual-editor/thrive-visual-editor.php') && is_admin()) {
        return;
    }
    if (! is_admin()) {
        require_once plugin_dir_path(__FILE__) . '/options-redirections.php'; //Redirections
    }
}

if ('1' == seopress_get_toggle_option('xml-sitemap')) {
    add_action('init', 'seopress_load_sitemap', 999);
    function seopress_load_sitemap() {
        if ('1' === seopress_get_service('SitemapOption')->getHtmlEnable()) {
            $htmlSitemapService = new \SEOPress\Services\HTMLSitemap\HTMLSitemapService(seopress_get_service('SitemapOption'));
            $htmlSitemapService->init();
        }
    }
}

//Robots
if ('1' == seopress_get_toggle_option('xml-sitemap')) {
    require_once plugin_dir_path(__FILE__) . '/options-robots-txt.php'; //Robots.txt
}

if ('1' === seopress_get_toggle_option('advanced')) {
    require_once plugin_dir_path(__FILE__) . '/options-advanced-rewriting.php'; //Advanced Rewriting
    
    if (! is_admin()) {
        // Remove comment author url
        if ('1' === seopress_get_service('AdvancedOption')->getAdvancedCommentsAuthorURLDisable()) {
            add_filter('get_comment_author_url', '__return_empty_string');
        }

        // Remove website field in comments
        if ('1' === seopress_get_service('AdvancedOption')->getAdvancedCommentsAuthorURLDisable()) {
            function seopress_advanced_advanced_comments_website_hook($fields)
            {
                unset($fields['url']);

                return $fields;
            }
            add_filter('comment_form_default_fields', 'seopress_advanced_advanced_comments_website_hook', 40);
        }

        // Add nofollow noopener noreferrer to comments form link
        if ('1' === seopress_get_service('AdvancedOption')->getAdvancedCommentsFormLinkDisable()) {
            /* Custom attributes on comment link */
            add_filter('comments_popup_link_attributes', 'seopress_comments_popup_link_attributes');
            function seopress_comments_popup_link_attributes($attr) {
                $attr = 'rel="nofollow noopener noreferrer"';
                return $attr;
            }
        }
    }
    
    add_action('init', 'seopress_load_advanced_options', 0);
    function seopress_load_advanced_options()
    {
        if (! is_admin()) {
            require_once plugin_dir_path(__FILE__) . '/options-advanced.php'; //Advanced
        }
    }
    add_action('init', 'seopress_load_advanced_admin_options', 11);
    function seopress_load_advanced_admin_options()
    {
        require_once plugin_dir_path(__FILE__) . '/options-advanced-admin.php'; //Advanced (admin)
        //Admin bar
        if ('1' === seopress_get_service('AdvancedOption')->getAppearanceAdminBar()) {
            add_action('admin_bar_menu', 'seopress_advanced_appearance_adminbar_hook', 999);

            function seopress_advanced_appearance_adminbar_hook($wp_admin_bar)
            {
                $wp_admin_bar->remove_node('seopress');
            }
        }
    }

    //primary category
    function seopress_titles_primary_cat_hook($cats_0, $cats, $post)
    {
        $primary_cat	= null;

        if ($post) {
            $_seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);
            if (isset($_seopress_robots_primary_cat) && '' != $_seopress_robots_primary_cat && 'none' != $_seopress_robots_primary_cat && '0' != $_seopress_robots_primary_cat) {
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

        if ($post) {
            $_seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);

            if (isset($_seopress_robots_primary_cat) && '' != $_seopress_robots_primary_cat && 'none' != $_seopress_robots_primary_cat && '0' != $_seopress_robots_primary_cat) {
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
}

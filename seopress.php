<?php
/*
Plugin Name: SEOPress
Plugin URI: https://www.seopress.org/
Description: The best plugin for SEO.
Version: 3.3.11.2
Author: Benjamin Denis
Author URI: https://www.seopress.org/
License: GPLv2
Text Domain: wp-seopress
Domain Path: /languages
*/

/*  Copyright 2016 - 2019 - Benjamin Denis  (email : contact@seopress.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// To prevent calling the plugin directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
	exit;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Hooks activation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_activation() {
	add_option( 'seopress_activated', 'yes' );
    flush_rewrite_rules();
    do_action('seopress_activation');
}
register_activation_hook(__FILE__, 'seopress_activation');

function seopress_deactivation() {
	delete_option( 'seopress_activated' );
    flush_rewrite_rules();
    do_action('seopress_deactivation');
}
register_deactivation_hook(__FILE__, 'seopress_deactivation');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Define
///////////////////////////////////////////////////////////////////////////////////////////////////
define( 'SEOPRESS_VERSION', '3.3.11.2' ); 
define( 'SEOPRESS_AUTHOR', 'Benjamin Denis' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS INIT = Admin + Core + API + Translation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_init($hook) {
    load_plugin_textdomain( 'wp-seopress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    global $pagenow;
    
    if ( is_admin() || is_network_admin() ) {
        require_once dirname( __FILE__ ) . '/inc/admin/admin.php';
        if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
            require_once dirname( __FILE__ ) . '/inc/admin/admin-metaboxes.php';
        }
        if ( $pagenow =='term.php' || $pagenow =='edit-tags.php') {
            require_once dirname( __FILE__ ) . '/inc/admin/admin-term-metaboxes.php';
        }
        require_once dirname( __FILE__ ) . '/inc/admin/ajax.php';
        if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
            //do not load the SEOPress admin header
        } else {
            require_once dirname( __FILE__ ) . '/inc/admin/admin-header.php';
        }
    }

    require_once dirname( __FILE__ ) . '/inc/functions/options.php';

    if(current_user_can('edit_posts')) {
        require_once dirname( __FILE__ ) . '/inc/admin/adminbar.php';
    }
    
    remove_action( 'wp_head', 'rel_canonical' ); //remove default WordPress Canonical
}
add_action('plugins_loaded', 'seopress_init', 999);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads the JS/CSS in admin
///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Options page
function seopress_add_admin_options_scripts($hook) {
    wp_register_style( 'seopress-admin', plugins_url('assets/css/seopress.min.css', __FILE__), array(), SEOPRESS_VERSION);
    wp_enqueue_style( 'seopress-admin' );
    
    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-network-option')) {
        wp_enqueue_script( 'seopress-network-tabs', plugins_url( 'assets/js/seopress-network-tabs.js', __FILE__ ), array( 'jquery' ), SEOPRESS_VERSION, true );
    }

    //Toggle / Notices JS
    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-option' || $_GET['page'] == 'seopress-network-option' || $_GET['page'] == 'seopress-titles' || $_GET['page'] == 'seopress-xml-sitemap' || $_GET['page'] == 'seopress-social' || $_GET['page'] == 'seopress-google-analytics' || $_GET['page'] == 'seopress-advanced' || $_GET['page'] == 'seopress-pro-page') ) {
        wp_enqueue_script( 'seopress-toggle-ajax', plugins_url( 'assets/js/seopress-dashboard.js', __FILE__ ), array( 'jquery' ), SEOPRESS_VERSION, true );

        //Features
        $seopress_toggle_features = array(
            'seopress_nonce' => wp_create_nonce('seopress_toggle_features_nonce'),
            'seopress_toggle_features' => admin_url( 'admin-ajax.php'),
        );
        wp_localize_script( 'seopress-toggle-ajax', 'seopressAjaxToggleFeatures', $seopress_toggle_features );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-option') ) {
        //Notices
        $seopress_hide_notices = array(
            'seopress_nonce' => wp_create_nonce('seopress_hide_notices_nonce'),
            'seopress_hide_notices' => admin_url( 'admin-ajax.php'),
        );
        wp_localize_script( 'seopress-toggle-ajax', 'seopressAjaxHideNotices', $seopress_hide_notices );

        //Admin Tabs
        wp_enqueue_script( 'seopress-reverse-ajax', plugins_url( 'assets/js/seopress-tabs7.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );

        //Reverse domains
        $seopress_request_reverse = array(
            'seopress_nonce' => wp_create_nonce('seopress_request_reverse_nonce'),
            'seopress_request_reverse' => admin_url( 'admin-ajax.php'),
        );
        wp_localize_script( 'seopress-reverse-ajax', 'seopressAjaxReverse', $seopress_request_reverse );

        $seopress_clear_reverse_cache = array(
            'seopress_nonce' => wp_create_nonce('seopress_clear_reverse_cache_nonce'),
            'seopress_clear_reverse_cache' => admin_url( 'admin-ajax.php'),
        );
        wp_localize_script( 'seopress-reverse-ajax', 'seopressAjaxClearReverseCache', $seopress_clear_reverse_cache );

        //Alexa Rank
        $seopress_request_alexa_rank = array(
            'seopress_nonce' => wp_create_nonce('seopress_request_alexa_rank_nonce'),
            'seopress_request_alexa_rank' => admin_url( 'admin-ajax.php'),
        );
        wp_localize_script( 'seopress-reverse-ajax', 'seopressAjaxAlexa', $seopress_request_alexa_rank );
    }

    //Migration
    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-option' || $_GET['page'] == 'seopress-import-export') ) {
        wp_enqueue_script( 'seopress-migrate-ajax', plugins_url( 'assets/js/seopress-yoast-migrate.js', __FILE__ ), array( 'jquery' ), SEOPRESS_VERSION, true );

        $seopress_migrate = array( 
            'seopress_aio_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_aio_migrate_nonce'),
                'seopress_aio_migration' => admin_url( 'admin-ajax.php'),
            ),
            'seopress_yoast_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_yoast_migrate_nonce'),
                'seopress_yoast_migration' => admin_url( 'admin-ajax.php'),
            ),
            'seopress_seo_framework_migrate' => array(
                'seopress_nonce' => wp_create_nonce('seopress_seo_framework_migrate_nonce'),
                'seopress_seo_framework_migration' => admin_url( 'admin-ajax.php'),
            ),
        );
        wp_localize_script( 'seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate );
    }

    //Tabs
    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-titles') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-xml-sitemap') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs4.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-xml-sitemap' || $_GET['page'] == 'seopress-pro-page' || $_GET['page'] == 'seopress-network-option' )) {
        wp_enqueue_script( 'seopress-xml-ajax', plugins_url( 'assets/js/seopress-sitemap-ajax.js', __FILE__ ), array( 'jquery' ), SEOPRESS_VERSION, true );

        $seopress_ajax_permalinks = array(
            'seopress_nonce' => wp_create_nonce('seopress_flush_permalinks_nonce'),
            'seopress_flush_permalinks' => admin_url('options-permalink.php'),
        );
        wp_localize_script( 'seopress-xml-ajax', 'seopressAjaxResetPermalinks', $seopress_ajax_permalinks ); 
    
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-google-analytics') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs6.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-advanced') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs5.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-social') ) {
        wp_enqueue_script( 'seopress-social-tabs-js', plugins_url( 'assets/js/seopress-tabs3.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
        wp_enqueue_script( 'seopress-cpt-tabs-js', plugins_url( 'assets/js/seopress-tabs2.js', __FILE__ ), array( 'jquery-ui-tabs' ), SEOPRESS_VERSION );
        wp_enqueue_script( 'seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader.js', __FILE__), array('jquery'), SEOPRESS_VERSION, false );
        wp_enqueue_media();
    }
}

add_action('admin_enqueue_scripts', 'seopress_add_admin_options_scripts', 10, 1);

//SEOPRESS Admin bar
function seopress_admin_bar_css() {
    if (is_user_logged_in() && function_exists('seopress_advanced_appearance_adminbar_option') && seopress_advanced_appearance_adminbar_option() !='1') {
        wp_register_style( 'seopress-admin-bar', plugins_url('assets/css/seopress-admin-bar.min.css', __FILE__), array(), SEOPRESS_VERSION);
        wp_enqueue_style( 'seopress-admin-bar' );
    }
}

add_action('init', 'seopress_admin_bar_css', 10, 1);

//Quick Edit
function seopress_add_admin_options_scripts_quick_edit() {
    wp_enqueue_script( 'seopress-quick-edit', plugins_url('assets/js/seopress-quick-edit.js', __FILE__), array('jquery', 'inline-edit-post'), SEOPRESS_VERSION, true );
}
add_action( 'admin_print_scripts-edit.php', 'seopress_add_admin_options_scripts_quick_edit' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Admin Body Class
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'admin_body_class', 'seopress_admin_body_class', 9 );
function seopress_admin_body_class( $classes ) {
    if ((isset($_GET['page']) && ($_GET['page'] == 'seopress-option'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-network-option'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-titles'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-xml-sitemap'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-social'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-google-analytics'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-advanced'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-import-export'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-pro-page'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-bot-batch'))
    || (isset($_GET['page']) && ($_GET['page'] == 'seopress-license'))) {
        return $classes."seopress-styles";
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//3rd plugins compatibility
///////////////////////////////////////////////////////////////////////////////////////////////////

//Jetpack
function seopress_compatibility_jetpack() {
    if ( function_exists('is_plugin_active')) {
        if (is_plugin_active( 'jetpack/jetpack.php' ) && !is_admin()) {
            add_filter( 'jetpack_enable_open_graph', '__return_false' );
        }
    }
}
add_action( 'wp_head', 'seopress_compatibility_jetpack', 0 );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Credits footer
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_custom_credits_footer() {
    return '<span id="seopress-footer-credits">
                <span class="dashicons dashicons-wordpress"></span>
                '.__( "You like SEOPress? Don't forget to rate it 5 stars!", "wp-seopress" ).'

                <span class="wporg-ratings rating-stars">
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=1#postform" data-rating="1" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=2#postform" data-rating="2" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=3#postform" data-rating="3" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=4#postform" data-rating="4" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform" data-rating="5" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>
                </span>
                <script>
                    jQuery(document).ready( function($) {
                        $(".rating-stars").find("a").hover(
                            function() {
                                $(this).nextAll("a").children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                $(this).prevAll("a").children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                $(this).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                            }, function() {
                                var rating = $("input#rating").val();
                                if (rating) {
                                    var list = $(".rating-stars a");
                                    list.children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                    list.slice(0, rating).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                }
                            }
                        );
                    });
                </script>
            </span>';
}
if (isset($_GET['page']) && ($_GET['page'] == 'seopress-option' || $_GET['page'] == 'seopress-network-option' || $_GET['page'] == 'seopress-titles' || $_GET['page'] == 'seopress-xml-sitemap' || $_GET['page'] == 'seopress-social' || $_GET['page'] == 'seopress-google-analytics' || $_GET['page'] == 'seopress-advanced' || $_GET['page'] == 'seopress-pro-page') ) {
    add_filter('admin_footer_text', 'seopress_custom_credits_footer');
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Shortcut settings page
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('plugin_action_links', 'seopress_plugin_action_links', 10, 2);

function seopress_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=seopress-option') . '">'.__("Settings","wp-seopress").'</a>';
        $website_link = '<a href="https://www.seopress.org/" target="_blank">'.__("SEOPress.org","wp-seopress").'</a>';
        if ( !is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
            $pro_link = '<a href="https://www.seopress.org/seopress-pro/" style="color:#a00;font-weight:bold" target="_blank">'.__("GO PRO!","wp-seopress").'</a>';
            array_unshift($links, $pro_link);
        }
        if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
            if ( array_key_exists( 'deactivate', $links ) && in_array( $file, array(
                'wp-seopress/seopress.php'
            )));
            unset( $links['deactivate'] );
        }
        array_unshift($links, $settings_link, $website_link);
    }

    return $links;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get all registered post types
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_post_types() {
    global $wp_post_types;

    $args = array(
        'show_ui' => true,
        'public' => true,
    );

    $output = 'objects'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator ); 
    unset($post_types['attachment'], $post_types['seopress_404'], $post_types['elementor_library']);    
    return $post_types;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get all registered custom taxonomies
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_taxonomies() {
    $args = array(
        'show_ui' => true,
        'public' => true,
    ); 
    $output = 'objects'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );  
    
    return $taxonomies;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Check SSL for schema.org
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_check_ssl() {
    if (is_ssl()) {
        return 'https://';
    } else {
        return 'http://';
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if a feature is ON
///////////////////////////////////////////////////////////////////////////////////////////////////

// Is Titles enable?
function seopress_get_toggle_titles_option() {
    $seopress_get_toggle_titles_option = get_option("seopress_toggle");
    if ( ! empty ( $seopress_get_toggle_titles_option ) ) {
        foreach ($seopress_get_toggle_titles_option as $key => $seopress_get_toggle_titles_value)
            $options[$key] = $seopress_get_toggle_titles_value;
         if (isset($seopress_get_toggle_titles_option['toggle-titles'])) { 
            return $seopress_get_toggle_titles_option['toggle-titles'];
         }
    }
}
// Is Social enable?
function seopress_get_toggle_social_option() {
    $seopress_get_toggle_social_option = get_option("seopress_toggle");
    if ( ! empty ( $seopress_get_toggle_social_option ) ) {
        foreach ($seopress_get_toggle_social_option as $key => $seopress_get_toggle_social_value)
            $options[$key] = $seopress_get_toggle_social_value;
         if (isset($seopress_get_toggle_social_option['toggle-social'])) { 
            return $seopress_get_toggle_social_option['toggle-social'];
         }
    }
}
// Is XML Sitemap enable?
function seopress_get_toggle_xml_sitemap_option() {
    $seopress_get_toggle_xml_sitemap_option = get_option("seopress_toggle");
    if ( ! empty ( $seopress_get_toggle_xml_sitemap_option ) ) {
        foreach ($seopress_get_toggle_xml_sitemap_option as $key => $seopress_get_toggle_xml_sitemap_value)
            $options[$key] = $seopress_get_toggle_xml_sitemap_value;
         if (isset($seopress_get_toggle_xml_sitemap_option['toggle-xml-sitemap'])) { 
            return $seopress_get_toggle_xml_sitemap_option['toggle-xml-sitemap'];
         }
    }
}
// Is Google Analytics enable?
function seopress_get_toggle_google_analytics_option() {
    $seopress_get_toggle_google_analytics_option = get_option("seopress_toggle");
    if ( ! empty ( $seopress_get_toggle_google_analytics_option ) ) {
        foreach ($seopress_get_toggle_google_analytics_option as $key => $seopress_get_toggle_google_analytics_value)
            $options[$key] = $seopress_get_toggle_google_analytics_value;
         if (isset($seopress_get_toggle_google_analytics_option['toggle-google-analytics'])) { 
            return $seopress_get_toggle_google_analytics_option['toggle-google-analytics'];
         }
    }
}
// Is Advanced enable?
function seopress_get_toggle_advanced_option() {
    $seopress_get_toggle_advanced_option = get_option("seopress_toggle");
    if ( ! empty ( $seopress_get_toggle_advanced_option ) ) {
        foreach ($seopress_get_toggle_advanced_option as $key => $seopress_get_toggle_advanced_value)
            $options[$key] = $seopress_get_toggle_advanced_value;
         if (isset($seopress_get_toggle_advanced_option['toggle-advanced'])) { 
            return $seopress_get_toggle_advanced_option['toggle-advanced'];
         }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Enable XML Sitemap
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_xml_sitemap_general_enable_option() {
    $seopress_xml_sitemap_general_enable_option = get_option("seopress_xml_sitemap_option_name");
    if ( ! empty ( $seopress_xml_sitemap_general_enable_option ) ) {
        foreach ($seopress_xml_sitemap_general_enable_option as $key => $seopress_xml_sitemap_general_enable_value)
            $options[$key] = $seopress_xml_sitemap_general_enable_value;
         if (isset($seopress_xml_sitemap_general_enable_option['seopress_xml_sitemap_general_enable'])) { 
            return $seopress_xml_sitemap_general_enable_option['seopress_xml_sitemap_general_enable'];
         }
    }
}

function seopress_xml_sitemap_post_types_list_option() {
    $seopress_xml_sitemap_post_types_list_option = get_option("seopress_xml_sitemap_option_name");
    if ( ! empty ( $seopress_xml_sitemap_post_types_list_option ) ) {
        foreach ($seopress_xml_sitemap_post_types_list_option as $key => $seopress_xml_sitemap_post_types_list_value)
            $options[$key] = $seopress_xml_sitemap_post_types_list_value;
         if (isset($seopress_xml_sitemap_post_types_list_option['seopress_xml_sitemap_post_types_list'])) { 
            return $seopress_xml_sitemap_post_types_list_option['seopress_xml_sitemap_post_types_list'];
         }
    }
}

function seopress_xml_sitemap_taxonomies_list_option() {
    $seopress_xml_sitemap_taxonomies_list_option = get_option("seopress_xml_sitemap_option_name");
    if ( ! empty ( $seopress_xml_sitemap_taxonomies_list_option ) ) {
        foreach ($seopress_xml_sitemap_taxonomies_list_option as $key => $seopress_xml_sitemap_taxonomies_list_value)
            $options[$key] = $seopress_xml_sitemap_taxonomies_list_value;
         if (isset($seopress_xml_sitemap_taxonomies_list_option['seopress_xml_sitemap_taxonomies_list'])) { 
            return $seopress_xml_sitemap_taxonomies_list_option['seopress_xml_sitemap_taxonomies_list'];
         }
    }
}

//Rewrite Rules for XML Sitemap
if (seopress_xml_sitemap_general_enable_option() =='1' && seopress_get_toggle_xml_sitemap_option() =='1') {
    add_action( 'init', 'seopress_xml_sitemap_rewrite' );
    add_action( 'query_vars', 'seopress_xml_sitemap_query_vars' );
    add_action( 'template_redirect', 'seopress_xml_sitemap_change_template', 1 );
    add_action( 'template_redirect', 'seopress_xml_sitemap_shortcut', 1);

    //WPML compatibility
    if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
        add_filter( 'request', 'seopress_wpml_block_secondary_languages' );
    }
    function seopress_wpml_block_secondary_languages( $q ) {
        $current_language = apply_filters( 'wpml_current_language', false );
        $default_language = apply_filters( 'wpml_default_language', false );
        if ( $current_language !== $default_language ) {
            unset( $q['seopress_sitemap'] );
            unset( $q['seopress_cpt'] );
            unset( $q['seopress_tax'] );
            unset( $q['seopress_sitemap_xsl'] );
        }
        return $q;
    }

    function seopress_xml_sitemap_shortcut() {
        //Redirect sitemap.xml to sitemaps.xml
        $get_current_url = get_home_url().$_SERVER['REQUEST_URI'];
        if (in_array($get_current_url,array(get_home_url().'/sitemap.xml/',get_home_url().'/sitemap.xml'))) {
            wp_safe_redirect(get_home_url().'/sitemaps.xml', 301);
            exit();
        }
    }

    function seopress_xml_sitemap_rewrite() {
        //XML Index
        add_rewrite_rule( '^sitemaps.xml$', 'index.php?seopress_sitemap=1', 'top' );

        //XSL Sitemap
        add_rewrite_rule( '^sitemaps_xsl.xsl$', 'index.php?seopress_sitemap_xsl=1', 'top' );

        //CPT
        if (seopress_xml_sitemap_post_types_list_option() !='') {
            foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if($_cpt_value =='1') {
                        add_rewrite_rule( 'sitemaps/'.$cpt_key.'.xml?$', 'index.php?seopress_cpt='.$cpt_key, 'top' );
                    }
                }
            }
        }

        //Taxonomies
        if (seopress_xml_sitemap_taxonomies_list_option() !='') {
            foreach (seopress_xml_sitemap_taxonomies_list_option() as $tax_key => $tax_value) {
                foreach ($tax_value as $_tax_key => $_tax_value) {
                    if($_tax_value =='1') {
                        add_rewrite_rule( 'sitemaps/'.$tax_key.'.xml?$', 'index.php?seopress_tax='.$tax_key, 'top' );
                    }
                }
            }
        }
    }


    function seopress_xml_sitemap_query_vars($vars) {
        $vars[] = 'seopress_sitemap';
        $vars[] = 'seopress_sitemap_xsl';
        $vars[] = 'seopress_cpt';
        $vars[] = 'seopress_tax';
        return $vars;
    }
    
    function seopress_xml_sitemap_change_template( $template ) {
        if( get_query_var( 'seopress_sitemap' ) === '1' ) {
            $seopress_sitemap = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps.php';
            if( file_exists( $seopress_sitemap ) ) {
                include $seopress_sitemap;
                exit;
            }
        }        
        if( get_query_var( 'seopress_sitemap_xsl' ) === '1' ) {
            $seopress_sitemap_xsl = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps-xsl.php';
            if( file_exists( $seopress_sitemap_xsl ) ) {
                include $seopress_sitemap_xsl;
                exit;
            }
        }
        if( get_query_var( 'seopress_cpt') !== '' ) {
            $seopress_cpt = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps-single.php';
            if( file_exists( $seopress_cpt ) ) {
                include $seopress_cpt;
                exit;
            }
        }
        if( get_query_var( 'seopress_tax') !== '' ) {
            $seopress_tax = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps-single-term.php';
            if( file_exists( $seopress_tax ) ) {
                include $seopress_tax;
                exit;
            }
        }
        return $template;
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Remove Admin Bar with Content Analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_remove_admin_bar() {
    if (isset($_GET['no_admin_bar']) && $_GET['no_admin_bar'] == 1) {
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action('plugins_loaded', 'seopress_remove_admin_bar');

///////////////////////////////////////////////////////////////////////////////////////////////////
// Test abolute URLs (return true if absolute)
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_is_absolute($url) {
    $pattern = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

    return (bool) preg_match($pattern, $url);
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Manage localized links
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_locale() {                    
    switch (get_user_locale(get_current_user_id())) {
        case "fr_FR":
            $locale_link = 'fr';
            break;
        case "fr_BE":
            $locale_link = 'fr';
            break;
        case "fr_CA":
            $locale_link = 'fr';
            break;
        case "fr_LU":
            $locale_link = 'fr';
            break;
        case "fr_MC":
            $locale_link = 'fr';
            break;
        case "fr_CH":
            $locale_link = 'fr';
            break;
        default:
            $locale_link = '';
    }
    return $locale_link;
}
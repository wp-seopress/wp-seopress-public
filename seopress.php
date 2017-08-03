<?php

/*
Plugin Name: SEOPress
Plugin URI: http://seopress.org/
Description: The best SEO plugin.
Version: 0.4
Author: Benjamin DENIS
Author URI: http://seopress.org/
License: GPLv2
Text Domain: wp-seopress
Domain Path: /languages
*/

/*  Copyright 2016 - Benjamin DENIS  (email : contact@seopress.org)

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

define( 'SEOPRESS_VERSION', '0.4' ); 
define( 'SEOPRESS_AUTHOR', 'Benjamin Denis' ); 

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS INIT = Admin + Core + Translation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_init() {
    load_plugin_textdomain( 'wp-seopress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    global $pagenow;
    
    if ( is_admin() ) {
        require_once dirname( __FILE__ ) . '/inc/admin/admin.php';
        require_once dirname( __FILE__ ) . '/inc/admin/admin-metaboxes.php';
    }

    require_once dirname( __FILE__ ) . '/inc/functions/options.php';
}
add_action('plugins_loaded', 'seopress_init', 999);


///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads the JS/CSS in admin
///////////////////////////////////////////////////////////////////////////////////////////////////

//SEOPRESS Options page
function seopress_add_admin_options_scripts($hook) {
    wp_register_style( 'seopress-admin', plugins_url('assets/css/seopress.css', __FILE__));
    wp_enqueue_style( 'seopress-admin' );
    
    //Tabs
    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-titles') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs.js', __FILE__ ), array( 'jquery-ui-tabs' ) );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-xml-sitemap') ) {
        wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs4.js', __FILE__ ), array( 'jquery-ui-tabs' ) );
    }

    if (isset($_GET['page']) && ($_GET['page'] == 'seopress-social') ) {
        wp_enqueue_script( 'seopress-social-tabs-js', plugins_url( 'assets/js/seopress-tabs3.js', __FILE__ ), array( 'jquery-ui-tabs' ) );
        wp_enqueue_script( 'seopress-cpt-tabs-js', plugins_url( 'assets/js/seopress-tabs2.js', __FILE__ ), array( 'jquery-ui-tabs' ) );
        wp_enqueue_script( 'seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader.js', __FILE__), array('jquery'), '', false );
        wp_enqueue_media();
    }

    global $post;
    
    if ( $hook == 'post-new.php' || $hook == 'post.php') {
        if ( 'attachment' !== $post->post_type) { 
            wp_enqueue_script( 'seopress-cpt-tabs-js', plugins_url( 'assets/js/seopress-tabs2.js', __FILE__ ), array( 'jquery-ui-tabs' ) );
            wp_enqueue_script( 'seopress-cpt-counters-js', plugins_url( 'assets/js/seopress-counters.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader.js', __FILE__), array('jquery'), '', false );
            wp_enqueue_media();

        }
    }
}

add_action('admin_enqueue_scripts', 'seopress_add_admin_options_scripts', 10, 1);

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
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=seopress-option">'.__("Settings","wp-seopress").'</a>';
        $website_link = '<a href="http://seopress.org/" target="_blank">'.__("SEOPress.org","wp-seopress").'</a>';
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
    );

    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $args, $output, $operator ); 
    unset($post_types['attachment']);    
    return $post_types;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get all registered post types
///////////////////////////////////////////////////////////////////////////////////////////////////

function seopress_get_taxonomies() {
    $args = array(
        'show_ui' => true,
        'public' => true,
      
    ); 
    $output = 'names'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );  
    
    return $taxonomies;
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
};

function seopress_xml_sitemap_post_types_list_option() {
    $seopress_xml_sitemap_post_types_list_option = get_option("seopress_xml_sitemap_option_name");
    if ( ! empty ( $seopress_xml_sitemap_post_types_list_option ) ) {
        foreach ($seopress_xml_sitemap_post_types_list_option as $key => $seopress_xml_sitemap_post_types_list_value)
            $options[$key] = $seopress_xml_sitemap_post_types_list_value;
         if (isset($seopress_xml_sitemap_post_types_list_option['seopress_xml_sitemap_post_types_list'])) { 
            return $seopress_xml_sitemap_post_types_list_option['seopress_xml_sitemap_post_types_list'];
         }
    }
};

function seopress_xml_sitemap_taxonomies_list_option() {
    $seopress_xml_sitemap_taxonomies_list_option = get_option("seopress_xml_sitemap_option_name");
    if ( ! empty ( $seopress_xml_sitemap_taxonomies_list_option ) ) {
        foreach ($seopress_xml_sitemap_taxonomies_list_option as $key => $seopress_xml_sitemap_taxonomies_list_value)
            $options[$key] = $seopress_xml_sitemap_taxonomies_list_value;
         if (isset($seopress_xml_sitemap_taxonomies_list_option['seopress_xml_sitemap_taxonomies_list'])) { 
            return $seopress_xml_sitemap_taxonomies_list_option['seopress_xml_sitemap_taxonomies_list'];
         }
    }
};

//Rewrite Rules for XML Sitemap
if (seopress_xml_sitemap_general_enable_option() =='1') {

    add_action( 'init', 'seopress_xml_sitemap_rewrite' );
    add_action( 'query_vars', 'seopress_xml_sitemap_query_vars' );
    add_action( 'template_include', 'seopress_xml_sitemap_change_template' );

    function seopress_xml_sitemap_rewrite() {

        //XML Index
        add_rewrite_rule( '^sitemaps$', 'index.php?seopress_sitemap=1', 'top' );

        //CPT
        if (seopress_xml_sitemap_post_types_list_option() !='') {
            foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
                foreach ($cpt_value as $_cpt_key => $_cpt_value) {
                    if($_cpt_value =='1') {
                        add_rewrite_rule( 'sitemaps/'.$cpt_key.'/?$', 'index.php?seopress_cpt='.$cpt_key, 'top' );
                    }
                }
            }
        }

        //Taxonomies
        if (seopress_xml_sitemap_taxonomies_list_option() !='') {
            foreach (seopress_xml_sitemap_taxonomies_list_option() as $tax_key => $tax_value) {
                foreach ($tax_value as $_tax_key => $_tax_value) {
                    if($_tax_value =='1') {
                        add_rewrite_rule( 'sitemaps/'.$tax_key.'/?$', 'index.php?seopress_tax='.$tax_key, 'top' );
                    }
                }
            }
        }
    }


    function seopress_xml_sitemap_query_vars($vars) {
        $vars[] = 'seopress_sitemap';
        $vars[] = 'seopress_cpt';
        $vars[] = 'seopress_tax';
        return $vars;
    }
    
    function seopress_xml_sitemap_change_template( $template ) {
        if( get_query_var( 'seopress_sitemap' ) === '1' ) {
            $seopress_sitemap = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps.php';
            if( file_exists( $seopress_sitemap ) ) {
                return $seopress_sitemap;
            }
        }
        if( get_query_var( 'seopress_cpt') !== '' ) {
            $seopress_cpt = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps-single.php';
            if( file_exists( $seopress_cpt ) ) {
                return $seopress_cpt;
            }
        }
        if( get_query_var( 'seopress_tax') !== '' ) {
            $seopress_tax = plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/template-xml-sitemaps-single-term.php';
            if( file_exists( $seopress_tax ) ) {
                return $seopress_tax;
            }
        }
        return $template;
    }
}




?>
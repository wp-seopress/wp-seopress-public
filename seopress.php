<?php
/*
Plugin Name: SEOPress
Plugin URI: https://www.seopress.org/
Description: One of the best SEO plugins for WordPress.
Version: 3.9.2
Author: SEOPress
Author URI: https://www.seopress.org/
License: GPLv2
Text Domain: wp-seopress
Domain Path: /languages
*/

/*  Copyright 2016 - 2020 - Benjamin Denis  (email : contact@seopress.org)

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
	deactivate_plugins( [ 'wp-seopress-pro/seopress-pro.php', 'wp-seopress-insights/seopress-insights.php' ] );

	delete_option( 'seopress_activated' );
	flush_rewrite_rules();
	do_action('seopress_deactivation');
}
register_deactivation_hook(__FILE__, 'seopress_deactivation');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Define
///////////////////////////////////////////////////////////////////////////////////////////////////
define( 'SEOPRESS_VERSION', '3.9.2' );
define( 'SEOPRESS_AUTHOR', 'Benjamin Denis' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS INIT = Admin + Core + API + Translation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_init($hook) {
	load_plugin_textdomain( 'wp-seopress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	global $pagenow;
	global $typenow;

	if ( is_admin() || is_network_admin() ) {
		require_once dirname( __FILE__ ) . '/inc/admin/plugin-upgrader.php';
		require_once dirname( __FILE__ ) . '/inc/admin/admin.php';

		if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {
			if ( 'seopress_schemas' != $typenow ) {
				require_once dirname( __FILE__ ) . '/inc/admin/admin-metaboxes.php';
			}
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

	require_once dirname( __FILE__ ) . '/inc/admin/adminbar.php';

	remove_action( 'wp_head', 'rel_canonical' ); //remove default WordPress Canonical

	//Setup/welcome
	if (!empty($_GET['page'])){
		switch ($_GET['page']){
			case 'seopress-setup':
				include_once dirname( __FILE__ ) . '/inc/admin/admin-wizard.php';
				break;
			default :
				break;
		}
	}

	//Elementor
	if ( did_action( 'elementor/loaded' ) ) {
		include_once dirname( __FILE__ ) . '/inc/admin/page-builders/elementor/elementor-addon.php';
	}
}
add_action('plugins_loaded', 'seopress_init', 999);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads dynamic variables for titles, metas, schemas...
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_dyn_variables_init($variables){
	$variables = include dirname( __FILE__ ) . '/inc/functions/variables/dynamic-variables.php';
	return $variables;
}
add_filter('seopress_dyn_variables_fn','seopress_dyn_variables_init');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads the JS/CSS in admin
///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Options page
function seopress_add_admin_options_scripts( $hook ) {
	$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style( 'seopress-admin', plugins_url( 'assets/css/seopress' . $prefix . '.css', __FILE__ ), [], SEOPRESS_VERSION );
	wp_enqueue_style( 'seopress-admin' );

	if ( ! isset( $_GET['page'] ) ) {
		return;
	}
	if ( 'seopress-network-option' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-network-tabs', plugins_url( 'assets/js/seopress-network-tabs' . $prefix . '.js', __FILE__ ), [ 'jquery' ], SEOPRESS_VERSION, true );
	}

	//Toggle / Notices JS
	$_pages = [ 'seopress-option' => true, 'seopress-network-option' => true, 'seopress-titles' => true, 'seopress-xml-sitemap' => true, 'seopress-social' => true, 'seopress-google-analytics' => true, 'seopress-pro-page' => true, 'seopress-advanced' => true ];
	if ( isset( $_pages[ $_GET['page'] ] ) ) {
		wp_enqueue_script( 'seopress-toggle-ajax', plugins_url( 'assets/js/seopress-dashboard' . $prefix . '.js', __FILE__ ), [ 'jquery' ], SEOPRESS_VERSION, true );

		//Features
		$seopress_toggle_features = [
			'seopress_nonce'           => wp_create_nonce( 'seopress_toggle_features_nonce' ),
			'seopress_toggle_features' => admin_url( 'admin-ajax.php'),
			'i18n'                     => __( 'has been successfully updated!', 'wp-seopress' )
		];
		wp_localize_script( 'seopress-toggle-ajax', 'seopressAjaxToggleFeatures', $seopress_toggle_features );
	}
	unset( $_pages );

	if ( 'seopress-option' === $_GET['page'] ) {
		//Notices
		$seopress_hide_notices = [
			'seopress_nonce'        => wp_create_nonce( 'seopress_hide_notices_nonce' ),
			'seopress_hide_notices' => admin_url( 'admin-ajax.php'),
		];
		wp_localize_script( 'seopress-toggle-ajax', 'seopressAjaxHideNotices', $seopress_hide_notices );

		//Admin Tabs
		wp_enqueue_script( 'seopress-reverse-ajax', plugins_url( 'assets/js/seopress-tabs7' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );

		//Reverse domains
		$seopress_request_reverse = [
			'seopress_nonce'           => wp_create_nonce( 'seopress_request_reverse_nonce' ),
			'seopress_request_reverse' => admin_url( 'admin-ajax.php'),
		];
		wp_localize_script( 'seopress-reverse-ajax', 'seopressAjaxReverse', $seopress_request_reverse );

		$seopress_clear_reverse_cache = [
			'seopress_nonce'               => wp_create_nonce( 'seopress_clear_reverse_cache_nonce' ),
			'seopress_clear_reverse_cache' => admin_url( 'admin-ajax.php'),
		];
		wp_localize_script( 'seopress-reverse-ajax', 'seopressAjaxClearReverseCache', $seopress_clear_reverse_cache );
	}

	//Migration
	if ( 'seopress-option' === $_GET['page'] || 'seopress-import-export' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-migrate-ajax', plugins_url( 'assets/js/seopress-migrate' . $prefix . '.js', __FILE__ ), [ 'jquery' ], SEOPRESS_VERSION, true );

		$seopress_migrate = [
			'seopress_aio_migrate'				=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_aio_migrate_nonce'),
				'seopress_aio_migration'				=> admin_url( 'admin-ajax.php'),
			],
			'seopress_yoast_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_yoast_migrate_nonce'),
				'seopress_yoast_migration'				=> admin_url( 'admin-ajax.php'),
			],
			'seopress_seo_framework_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_seo_framework_migrate_nonce'),
				'seopress_seo_framework_migration'		=> admin_url( 'admin-ajax.php'),
			],
			'seopress_rk_migrate'				=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_rk_migrate_nonce'),
				'seopress_rk_migration'					=> admin_url( 'admin-ajax.php'),
			],
			'seopress_squirrly_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_squirrly_migrate_nonce'),
				'seopress_squirrly_migration'			=> admin_url( 'admin-ajax.php'),
			],
			'seopress_seo_ultimate_migrate'		=> [
				'seopress_nonce' 						=> wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
				'seopress_seo_ultimate_migration'		=> admin_url( 'admin-ajax.php'),
			],
			'seopress_wp_meta_seo_migrate'		=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_meta_seo_migrate_nonce'),
				'seopress_wp_meta_seo_migration'		=> admin_url( 'admin-ajax.php'),
			],
			'seopress_premium_seo_pack_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
				'seopress_premium_seo_pack_migration'	=> admin_url( 'admin-ajax.php'),
			],
			'seopress_metadata_csv'				=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_export_csv_metadata_nonce'),
				'seopress_metadata_export'				=> admin_url( 'admin-ajax.php'),
			],
			'i18n'								=> [
				'migration'								=> __( 'Migration completed!', 'wp-seopress' ),
				'export'								=>__( 'Export completed!', 'wp-seopress' ),
			]
		];
		wp_localize_script( 'seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate );
	}

	//Tabs
	if ( 'seopress-titles' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
	}

	if ( 'seopress-xml-sitemap' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs4' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
	}

	if ( 'seopress-xml-sitemap' === $_GET['page'] || 'seopress-pro-page' === $_GET['page'] || 'seopress-network-option' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-xml-ajax', plugins_url( 'assets/js/seopress-sitemap-ajax' . $prefix . '.js', __FILE__ ), [ 'jquery' ], SEOPRESS_VERSION, true );

		$seopress_ajax_permalinks = [
			'seopress_nonce'            => wp_create_nonce('seopress_flush_permalinks_nonce'),
			'seopress_ajax_permalinks' 	=> admin_url( 'admin-ajax.php'),
		];
		wp_localize_script( 'seopress-xml-ajax', 'seopressAjaxResetPermalinks', $seopress_ajax_permalinks );

	}

	if ( 'seopress-google-analytics' === $_GET['page'] ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs6' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs', 'wp-color-picker' ], SEOPRESS_VERSION );
	}

	if ( 'seopress-advanced' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs5' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
	}

	if ( 'seopress-import-export' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-admin-tabs-js', plugins_url( 'assets/js/seopress-tabs8' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
	}

	if ( 'seopress-social' === $_GET['page'] ) {
		wp_enqueue_script( 'seopress-social-tabs-js', plugins_url( 'assets/js/seopress-tabs3' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
		wp_enqueue_script( 'seopress-cpt-tabs-js', plugins_url( 'assets/js/seopress-tabs2' . $prefix . '.js', __FILE__ ), [ 'jquery-ui-tabs' ], SEOPRESS_VERSION );
		wp_enqueue_script( 'seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), [ 'jquery' ], SEOPRESS_VERSION, false );
		wp_enqueue_media();
	}

	//CSV Importer
	if ( 'seopress_csv_importer' === $_GET['page'] ) {
		wp_enqueue_style( 'seopress-setup', plugins_url( 'assets/css/seopress-setup' . $prefix . '.css', __FILE__), [ 'dashicons' ], SEOPRESS_VERSION );
	}
}

add_action('admin_enqueue_scripts', 'seopress_add_admin_options_scripts', 10, 1);

//SEOPRESS Admin bar
function seopress_admin_bar_css() {
	$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	if ( is_user_logged_in() && function_exists( 'seopress_advanced_appearance_adminbar_option' ) && seopress_advanced_appearance_adminbar_option() != '1' ) {
		wp_register_style( 'seopress-admin-bar', plugins_url( 'assets/css/seopress-admin-bar' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION );
		wp_enqueue_style( 'seopress-admin-bar' );
	}
}
add_action('init', 'seopress_admin_bar_css', 10, 1);

//Quick Edit
function seopress_add_admin_options_scripts_quick_edit() {
	$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script( 'seopress-quick-edit', plugins_url('assets/js/seopress-quick-edit' . $prefix . '.js', __FILE__), [ 'jquery', 'inline-edit-post' ], SEOPRESS_VERSION, true );
}
add_action( 'admin_print_scripts-edit.php', 'seopress_add_admin_options_scripts_quick_edit' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Admin Body Class
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'admin_body_class', 'seopress_admin_body_class', 100 );
function seopress_admin_body_class( $classes ) {
	if ( ! isset($_GET['page'] ) ) {
		return $classes;
	}
	$_pages = [ 
		'seopress_csv_importer' => true, 
		'seopress-option' => true, 
		'seopress-network-option' => true, 
		'seopress-titles' => true, 
		'seopress-xml-sitemap' => true, 
		'seopress-social' => true, 
		'seopress-google-analytics' => true, 
		'seopress-advanced' => true, 
		'seopress-import-export' => true, 
		'seopress-pro-page' => true, 
		'seopress-bot-batch' => true, 
		'seopress-license' => true
	];
	if ( isset( $_pages[ $_GET['page'] ] ) ) {
		$classes .= " seopress-styles ";
	}
	return $classes;
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

/**
 * Remove default WC meta robots
 *
 * @since 3.8.1
 */
function seopress_compatibility_woocommerce() {
	if ( function_exists('is_plugin_active')) {
		if (is_plugin_active( 'woocommerce/woocommerce.php' ) && !is_admin()) {
			remove_action( 'wp_head', 'wc_page_noindex' );
		}
	}
}
add_action( 'wp_head', 'seopress_compatibility_woocommerce', 0 );

/**
 * Remove WPML home url filter
 *
 * @since 3.8.6
 */
function seopress_remove_wpml_home_url_filter( $home_url, $url, $path, $orig_scheme, $blog_id ) {
	return $url;
}

/**
 * Remove default WP XML sitemaps
 *
 * @since 3.8.8
 */
remove_action( 'init', 'wp_sitemaps_get_server' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//Credits footer
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_custom_credits_footer() {
	$html = '<span id="seopress-footer-credits">
				<span class="dashicons dashicons-wordpress"></span>
				'.__( "You like SEOPress? Don't forget to rate it 5 stars!", "wp-seopress" ).'<span class="wporg-ratings rating-stars">';
				for ($i=1; $i < 6; $i++) { 
					$html .= '<a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate='.$i.'#postform" data-rating="'.$i.'" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#FFDE24 !important;"></span></a>';
				}
				$html .= '</span>
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
	return $html;
}
if ((isset($_GET['page']) && (
	$_GET['page'] == 'seopress-option' 
	|| $_GET['page'] == 'seopress-network-option' 
	|| $_GET['page'] == 'seopress-titles' 
	|| $_GET['page'] == 'seopress-xml-sitemap' 
	|| $_GET['page'] == 'seopress-social' 
	|| $_GET['page'] == 'seopress-google-analytics' 
	|| $_GET['page'] == 'seopress-advanced' 
	|| $_GET['page'] == 'seopress-pro-page' 
	|| $_GET['page'] == 'seopress-import-export' 
	|| $_GET['page'] == 'seopress-bot-batch' 
	|| $_GET['page'] == 'seopress-insights' 
	|| $_GET['page'] == 'seopress-license'))
	|| (isset($_GET['post_type']) && (
	$_GET['post_type'] == 'seopress_404'
	|| $_GET['post_type'] == 'seopress_schemas'
	|| $_GET['post_type'] == 'seopress_bot'
	|| $_GET['post_type'] == 'seopress_backlinks'))) {
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
		$website_link = '<a href="https://www.seopress.org/support/" target="_blank">'.__("Docs","wp-seopress").'</a>';
		$wizard_link = '<a href="'.admin_url('admin.php?page=seopress-setup').'">'.__("Configuration Wizard","wp-seopress").'</a>';
		if ( !is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )) {
			$pro_link = '<a href="https://www.seopress.org/seopress-pro/" style="color:red;font-weight:bold" target="_blank">'.__("GO PRO!","wp-seopress").'</a>';
			array_unshift($links, $pro_link);
		}
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) || is_plugin_active( 'wp-seopress-insights/seopress-insights.php' )) {
			if ( array_key_exists( 'deactivate', $links ) && in_array( $file, array(
				'wp-seopress/seopress.php'
			)));
			unset( $links['deactivate'] );
		}
		array_unshift($links, $settings_link, $wizard_link, $website_link);
	}

	return $links;
}

/**
  * Get all registered post types
  *
  * @author Benjamin Denis
  *
  * @return (array) $wp_post_types
  **/
function seopress_get_post_types() {
	global $wp_post_types;

	$args = array(
		'show_ui' => true,
		'public' => true,
	);

	$output = 'objects'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'

	$post_types = get_post_types( $args, $output, $operator );
	unset(
		$post_types['attachment'],
		$post_types['seopress_rankings'],
		$post_types['seopress_backlinks'],
		$post_types['seopress_404'],
		$post_types['elementor_library'],
		$post_types['cuar_private_file'],
		$post_types['cuar_private_page'],
		$post_types['ct_template']
	);
	$post_types = apply_filters('seopress_post_types', $post_types);
	return $post_types;
}

/**
  * Get all registered custom taxonomies
  *
  * @author Benjamin Denis
  *
  * @param (bool) $with_terms
  * @return (array) $taxonomies
  **/
function seopress_get_taxonomies( $with_terms = false ) {
	$args = [
		'show_ui' => true,
		'public'  => true,
	];
	$args = apply_filters('seopress_get_taxonomies_args', $args);
	
	$output = 'objects'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$taxonomies = get_taxonomies( $args, $output, $operator );
	
	$taxonomies = apply_filters('seopress_get_taxonomies_list', $taxonomies);

	if ( ! $with_terms ) {
		return $taxonomies;
	}

	foreach ( $taxonomies as $_tax_slug => &$_tax ) {
		$_tax->terms = get_terms( [ 'taxonomy' => $_tax_slug ] );
	}

	return $taxonomies;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Get all custom fields (limit: 250)
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_custom_fields() {
	$cf_keys = wp_cache_get( 'seopress_get_custom_fields' );

	if ( false === $cf_keys ) {
		global $wpdb;

		$limit = (int) apply_filters( 'postmeta_form_limit', 250 );
		$cf_keys = $wpdb->get_col( $wpdb->prepare( "
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			HAVING meta_key NOT LIKE '\_%%'
			ORDER BY meta_key
			LIMIT %d", $limit ) );

		$cf_keys = apply_filters( 'seopress_get_custom_fields', $cf_keys );

		if ( $cf_keys ) {
			natcasesort( $cf_keys );
		};
		wp_cache_set( 'seopress_get_custom_fields', $cf_keys );
	}
	return $cf_keys;
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

/**
  * Get IP address
  *
  * @author Benjamin Denis
  * @return (string) $ip
  **/
function seopress_get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if a feature is ON
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Global check
 * @since 3.8
 * @param string $feature
 * @return string 1 if true
 * @author Benjamin
 */
function seopress_get_toggle_option($feature) {
	$seopress_get_toggle_option = get_option("seopress_toggle");
	if ( ! empty ( $seopress_get_toggle_option ) ) {
		foreach ($seopress_get_toggle_option as $key => $seopress_get_toggle_value) {
			$options[$key] = $seopress_get_toggle_value;
			if (isset($seopress_get_toggle_option['toggle-'.$feature])) {
				return $seopress_get_toggle_option['toggle-'.$feature];
			}
		}
	}
}

// Is Titles enable?
//@deprecated since version 3.8
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
//@deprecated since version 3.8
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
//@deprecated since version 3.8
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
//@deprecated since version 3.8
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
//@deprecated since version 3.8
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

function seopress_xml_sitemap_author_enable_option() {
	$seopress_xml_sitemap_author_enable_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_author_enable_option ) ) {
		foreach ($seopress_xml_sitemap_author_enable_option as $key => $seopress_xml_sitemap_author_enable_value)
			$options[$key] = $seopress_xml_sitemap_author_enable_value;
		 if (isset($seopress_xml_sitemap_author_enable_option['seopress_xml_sitemap_author_enable'])) {
			return $seopress_xml_sitemap_author_enable_option['seopress_xml_sitemap_author_enable'];
		 }
	}
}

function seopress_xml_sitemap_img_enable_option() {
	$seopress_xml_sitemap_img_enable_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_img_enable_option ) ) {
		foreach ($seopress_xml_sitemap_img_enable_option as $key => $seopress_xml_sitemap_img_enable_value)
			$options[$key] = $seopress_xml_sitemap_img_enable_value;
		 if (isset($seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'])) { 
		 	return $seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'];
		 }
	}
}

//Rewrite Rules for XML Sitemap
if (seopress_xml_sitemap_general_enable_option() =='1' && seopress_get_toggle_option('xml-sitemap') =='1') {
	add_action( 'init', 'seopress_xml_sitemap_rewrite' );
	add_action( 'query_vars', 'seopress_xml_sitemap_query_vars' );
	add_action( 'template_redirect', 'seopress_xml_sitemap_change_template', 1 );
	add_action( 'template_redirect', 'seopress_xml_sitemap_shortcut', 1);

	function seopress_sitemaps_headers() {
		$headers = array('Content-type' => 'text/xml', 'x-robots-tag' => 'noindex, follow');
		$headers = apply_filters( 'seopress_sitemaps_headers', $headers );
		if (!empty($headers)) {
			foreach($headers as $key => $header) {
				Header($key.':'.$header);
			}
		}
	}

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
			unset( $q['seopress_paged'] );
			unset( $q['seopress_author'] );
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

		//CPT / Taxonomies
		$urls = array();

		/*CPT*/
		if (seopress_xml_sitemap_post_types_list_option() !='') {
			foreach (seopress_xml_sitemap_post_types_list_option() as $cpt_key => $cpt_value) {
				foreach ($cpt_value as $_cpt_key => $_cpt_value) {
					if($_cpt_value =='1') {
						$urls[] = $cpt_key;
					}
				}
			}
		}

		/*Taxonomies*/
		if (seopress_xml_sitemap_taxonomies_list_option() !='') {
			foreach (seopress_xml_sitemap_taxonomies_list_option() as $tax_key => $tax_value) {
				foreach ($tax_value as $_tax_key => $_tax_value) {
					if($_tax_value =='1') {
						$urls[] = $tax_key;
					}
				}
			}
		}

		/*Urls*/
		if (!empty($urls)) {
			$matches[2] = '';
			foreach ($urls as $key => $value) {
				add_rewrite_rule( 'sitemaps/'.$value.'-sitemap([0-9]+)?.xml$', 'index.php?seopress_cpt='.$value.'&seopress_paged='.$matches[2], 'top' );
			}
		}

		//XML Author
		if (seopress_xml_sitemap_author_enable_option() == 1) {
			add_rewrite_rule( 'sitemaps/author.xml?$', 'index.php?seopress_author=1', 'top' );
		}
	}


	function seopress_xml_sitemap_query_vars($vars) {
		$vars[] = 'seopress_sitemap';
		$vars[] = 'seopress_sitemap_xsl';
		$vars[] = 'seopress_cpt';
		$vars[] = 'seopress_paged';
		$vars[] = 'seopress_author';
		return $vars;
	}

	function seopress_xml_sitemap_change_template( $template ) {
		if( get_query_var( 'seopress_sitemap' ) === '1' ) {
			$seopress_sitemap_file = 'template-xml-sitemaps.php';
		} elseif( get_query_var( 'seopress_sitemap_xsl' ) === '1' ) {
			$seopress_sitemap_file = 'template-xml-sitemaps-xsl.php';
		} elseif( get_query_var( 'seopress_author' ) === '1' ) {
			$seopress_sitemap_file = 'template-xml-sitemaps-author.php';
		} elseif (get_query_var( 'seopress_cpt') !== '' ) {
			if (function_exists('seopress_xml_sitemap_post_types_list_option')
				&& seopress_xml_sitemap_post_types_list_option() !=''
				&& array_key_exists(get_query_var('seopress_cpt'),seopress_xml_sitemap_post_types_list_option())) {
				$seopress_sitemap_file = 'template-xml-sitemaps-single.php';
			} elseif (function_exists('seopress_xml_sitemap_taxonomies_list_option')
				&& seopress_xml_sitemap_taxonomies_list_option() !=''
				&& array_key_exists(get_query_var('seopress_cpt'),seopress_xml_sitemap_taxonomies_list_option())) {
				$seopress_sitemap_file = 'template-xml-sitemaps-single-term.php';
			}
		}
		if ( isset( $seopress_sitemap_file ) && file_exists( plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/' . $seopress_sitemap_file ) ) {
			$return_true ='';
            $return_true = apply_filters( 'seopress_ob_end_flush_all', $return_true );

            if (has_filter('seopress_ob_end_flush_all') && $return_true == true) {
				wp_ob_end_flush_all();
				die();
			}

			include( plugin_dir_path( __FILE__ ) . 'inc/functions/sitemap/' . $seopress_sitemap_file );
			exit();
		}

		return $template;
	}
}
function seopress_disable_qm( $allcaps, $caps, $args ) {
	$allcaps['view_query_monitor'] = false; 
	return $allcaps;
}
///////////////////////////////////////////////////////////////////////////////////////////////////
// Remove Admin Bar with Content Analysis
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_clean_content_analysis() {
    if ( current_user_can( 'edit_posts' ) ) {
		if ( isset($_GET['no_admin_bar'] ) && '1' === $_GET['no_admin_bar'] ) {

			//Remove admin bar
			add_filter( 'show_admin_bar', '__return_false' );

			//Disable Query Monitor
			add_filter( 'user_has_cap', 'seopress_disable_qm', 10, 3);
			
			//Disable wptexturize
			add_filter('run_wptexturize', '__return_false');
			
			//Oxygen compatibility
			if (function_exists('ct_template_output')) { //disable for Oxygen
				add_action( 'template_redirect', 'seopress_get_oxygen_content' );
			}
		}
	}
}
add_action('plugins_loaded', 'seopress_clean_content_analysis');

///////////////////////////////////////////////////////////////////////////////////////////////////
// Test abolute URLs (return true if absolute)
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_is_absolute($url) {
	$pattern = "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

	return (bool) preg_match( $pattern, $url );
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Manage localized links
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_get_locale() {
	switch ( get_user_locale( get_current_user_id() ) ) {
		case "fr_FR":
		case "fr_BE":
		case "fr_CA":
		case "fr_LU":
		case "fr_MC":
		case "fr_CH":
			$locale_link = 'fr';
		break;
		default:
			$locale_link = '';
		break;
	}
	return $locale_link;
}

/**
 * Generate Tooltip
 * @since 3.8.2
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @return string tooltip title, tooltip description, tooltip url
 * @author Benjamin
 */
function seopress_tooltip($tooltip_title, $tooltip_desc, $tooltip_code) {
	$html = 
	'<button type="button" class="sp-tooltip"><span class="dashicons dashicons-editor-help"></span>
	<span class="sp-tooltiptext" role="tooltip" tabindex="0">
		<span class="sp-tooltip-headings">'.$tooltip_title.'</span>
		<span class="sp-tooltip-desc">'.$tooltip_desc.'</span>
		<span class="sp-tooltip-code">'.$tooltip_code.'</span>
	</span></button>';

	return $html;
}

/**
 * Generate Tooltip (alternative version)
 * @since 3.8.6
 * @param string $tooltip_title, $tooltip_desc, $tooltip_code
 * @return string tooltip title, tooltip description, tooltip url
 * @author Benjamin
 */
function seopress_tooltip_alt($tooltip_anchor, $tooltip_desc) {
	$html = 
	'<button type="button" class="sp-tooltip alt">'.$tooltip_anchor.'
	<span class="sp-tooltiptext" role="tooltip" tabindex="0">
		<span class="sp-tooltip-desc">'.$tooltip_desc.'</span>
	</span>
	</button>';

	return $html;
}

/**
 * Remove BOM
 * @since 3.8.2
 * @param mixed $text
 * @return mixed $text
 * @author Benjamin
 */
function seopress_remove_utf8_bom($text) {
	$bom = pack('H*','EFBBBF');
	$text = preg_replace("/^$bom/", '', $text);
	return $text;
}

/**
 * Generate notification (Notifications Center)
 * @since 3.8.2
 * @param array $args
 * @return string HTML notification
 * @author Benjamin
 */
function seopress_notification($args) {

	if (!empty($args)) {
		$id             = isset( $args['id'] ) ? $args['id'] : NULL;
		$title          = isset( $args['title'] ) ? $args['title'] : NULL;
		$desc           = isset( $args['desc'] ) ? $args['desc'] : NULL;
		$impact         = isset( $args['impact'] ) ? $args['impact'] : [];
		$link           = isset( $args['link'] ) ? $args['link'] : NULL;
		$deleteable     = isset( $args['deleteable'] ) ? $args['deleteable'] : NULL;
		$icon           = isset( $args['icon'] ) ? $args['icon'] : NULL;

		$class = '';
		if (!empty($impact)) {
			$class .= ' impact';
			$class .= ' '.key($impact);
		}

		if ($deleteable === true) {
			$class .= ' deleteable';
		}

		$html = '<div id="'.$id.'-alert" class="seopress-alert">';

			if (!empty($impact)) {
				$html .= '<span class="screen-reader-text">'.reset($impact).'</span>';
			}

			if (!empty($icon)) { 
				$html .= '<span class="dashicons '.$icon.'"></span>';
			} else {
				$html .= '<span class="dashicons dashicons-info"></span>';
			}

		$html .= '<div class="notice-left">
					<p>'.$title.'</p>
					<p>'.$desc.'</p>
				';
			
				$href = '';
				if (function_exists('seopress_get_locale') && seopress_get_locale() =='fr' &&  isset($link['fr'])) {
					$href = ' href="'.$link['fr'].'"';
				} elseif (isset($link['en'])) {
					$href = ' href="'.$link['en'].'"';
				}

				$target = '';
				if (isset($link['external']) && $link['external'] === true) {
					$target = ' target="_blank"';
				}
				
				if (!empty($link) || $deleteable === true) {
					$html .= '<div class="notice-right">';

					if (!empty($link)) {
						$html .= '<a class="button-primary"'.$href.$target.'>'.$link['title'].'</a>';
					}
					if ($deleteable === true) {
						$html .= '<span name="notice-title-tag" id="'.$id.'" class="dashicons dashicons-no-alt remove-notice" data-notice="'.$id.'"></span>';
					}

					$html .= '</div>';
				}
			$html .= '</div></div>';
	echo $html;
	}
}
/**
 * Filter the capability to allow other roles to use the plugin
 *
 * @since 3.8.2
 * @author Julio Potier
 *
 * @param (string) $cap
 * @param (string) $context
 * @return (string)
 **/
function seopress_capability( $cap, $context = '' ) {
	/**
	 * Filter the capability to allow other roles to use the plugin
	 *
	 * @since 3.8.2
	 * @author Julio Potier
	 *
	 * @param (string) $cap
	 * @param (string) $context
	 * @return (string)
	 **/
	$newcap = apply_filters( 'seopress_capability', $cap, $context );
	if ( ! current_user_can( $newcap ) ) {
		return $cap;
	}
	return $newcap;
}

/**
 * Check if the page is one of ours.
 *
 * @since 3.8.2
 * @author Julio Potier
 *
 * @return (bool)
 **/
function is_seopress_page() {
	if ( ! is_admin() && ( ! isset( $_REQUEST['page'] ) || ! isset( $_REQUEST['post_type'] ) ) ) {
		return false;
	}
	
	if ( isset( $_REQUEST['page'] ) ) {
		return 0 === strpos( $_REQUEST['page'], 'seopress' );
	} elseif ( isset( $_REQUEST['post_type'] ) ) {
		return 0 === strpos( $_REQUEST['post_type'], 'seopress' );
	}
}
/**
 * Only add our notices on our pages
 *
 * @since 3.8.2
 * @author Julio Potier
 *
 * @return (bool)
 **/
add_action( 'in_admin_header', 'seopress_remove_other_notices' );
function seopress_remove_other_notices() {
	if ( is_seopress_page() ) {
		remove_all_actions( 'network_admin_notices' );
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		add_action( 'admin_notices', 'seopress_admin_notices' );
		if ( is_plugin_active( 'wp-seopress-insights/seopress-insights.php' ) ) {
			add_action( 'admin_notices', 'seopress_insights_notice' );
		}
	}
}

/**
 * We replace the WP action by ours
 *
 * @since 3.8.2
 * @author Julio Potier
 *
 * @return (bool)
 **/
function seopress_admin_notices() {
	do_action( 'seopress_admin_notices' );
}

/**
 * Return the 7 days in correct order
 *
 * @since 3.8.2
 * @author Julio Potier
 *
 * @return (bool)
 **/
function seopress_get_days() {
	$start_of_week = (int) get_option( 'start_of_week' );
	return array_map(
		function() use ( $start_of_week ) {
			static $start_of_week;
			return ucfirst( date_i18n( 'l', strtotime( $start_of_week++ - date( 'w', 0 ) . ' day', 0 ) ) );
		},
		array_combine(
			array_merge(
				array_slice( range( 0, 6 ), $start_of_week, 7 ),
				array_slice( range( 0, 6 ), 0, $start_of_week )
			),
			range( 0, 6 )
		)
	);
}

/**
 * Check if a key exists in a multidimensional array
 *
 * @since 3.8.2
 * @author Benjamin Denis
 *
 * @return (bool)
 **/
function seopress_if_key_exists(array $arr, $key) {

	// is in base array?
	if (array_key_exists($key, $arr)) {
		return true;
	}

	// check arrays contained in this array
	foreach ($arr as $element) {
		if (is_array($element)) {
			if (seopress_if_key_exists($element, $key)) {
				return true;
			}
		}

	}

	return false;
}

/**
 * Get Oxygen Content
 *
 * @since 3.8.5
 * @author Benjamin Denis
 *
 * @return null
 **/
function seopress_get_oxygen_content() {
	if (is_plugin_active('oxygen/functions.php') && function_exists('ct_template_output')) {
		
		$seopress_get_the_content = ct_template_output();

		if ($seopress_get_the_content =='') {
			//Get post content
			$seopress_get_the_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
		}

		if ($seopress_get_the_content !='') {

			//Get Target Keywords
			if (get_post_meta(get_the_ID(),'_seopress_analysis_target_kw',true)) {
				$seopress_analysis_target_kw = array_filter(explode(',', strtolower(esc_attr(get_post_meta(get_the_ID(),'_seopress_analysis_target_kw',true)))));

				//Keywords density
				foreach ($seopress_analysis_target_kw as $kw) {
					if (preg_match_all('#\b('.$kw.')\b#iu', strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $m)) {
						$data['kws_density']['matches'][$kw][] = $m[0];
					}
				}
			}

			//Words Counter
			$data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses($seopress_get_the_content)), $matches);

			if (!empty($matches[0])) {
				$words_counter_unique = count(array_unique($matches[0]));
			} else {
				$words_counter_unique = '0';
			}
			$data['words_counter_unique'] = $words_counter_unique;

			//Update analysis
			update_post_meta(get_the_ID(), '_seopress_analysis_data', $data);
		}
	}
}

/**
 * Output follow us links to wizard
 *
 * @since 3.8.6
 * @author Benjamin Denis
 *
 **/
function seopress_wizard_follow_us() {
	?>
	<li class="seopress-wizard-additional-steps">
		<div class="seopress-wizard-next-step-description">
			<p class="next-step-heading"><?php esc_html_e( 'Follow us:', 'wp-seopress' ); ?></p>
		</div>
		<div class="seopress-wizard-next-step-action step">
			<ul class="recommended-step">
				<li class="recommended-item">
					<a href="https://www.facebook.com/seopresspro/" target="_blank">
						<span class="dashicons dashicons-facebook"></span>
						<?php _e('Like our Facebook page','wp-seopress'); ?>
					</a>
				</li>
				<li class="recommended-item">
					<a href="https://www.facebook.com/groups/seopress/" target="_blank">
						<span class="dashicons dashicons-facebook"></span>
						<?php _e('Join our Facebook Community group','wp-seopress'); ?>
					</a>
				</li>
				<li class="recommended-item">
					<a href="https://www.youtube.com/seopress" target="_blank">
						<span class="dashicons dashicons-video-alt3"></span>
						<?php _e('Watch our guided tour videos to learn more about SEOPress','wp-seopress'); ?>
					</a>
				</li>
				<li class="recommended-item">
					<?php
						if (function_exists('seopress_get_locale') && seopress_get_locale() =='fr') {
							$link = 'https://www.seopress.org/fr/blog/category/tutoriels/?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress';
						} else {
							$link = 'https://www.seopress.org/blog/how-to/?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress';
						} 
					?>
					<a href="<?php echo $link; ?>" target="_blank">
						<span class="dashicons dashicons-format-aside"></span>
						<?php _e('Read our blog posts about SEO concepts, tutorials and more','wp-seopress'); ?>
					</a>
				</li>
				<li class="recommended-item">
					<a href="https://twitter.com/wp_seopress" target="_blank">
						<span class="dashicons dashicons-twitter"></span>
						<?php _e('Follow us on Twitter','wp-seopress'); ?>
					</a>
				</li>
				<li class="recommended-item">
					<a href="https://www.instagram.com/wp_seopress/" target="_blank">
						<span class="dashicons dashicons-instagram"></span>
						<?php _e('The off side of SEOPress','wp-seopress'); ?>
					</a>
				</li>
			</ul>
		</div>
	</li>
	<?php
}
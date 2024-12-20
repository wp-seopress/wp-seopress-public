<?php
/*
Plugin Name: SEOPress
Plugin URI: https://www.seopress.org/
Description: One of the best SEO plugins for WordPress.
Author: The SEO Guys at SEOPress
Version: 8.4.1
Author URI: https://www.seopress.org/
License: GPLv2 or later
Text Domain: wp-seopress
Domain Path: /languages
Requires PHP: 7.4
Requires at least: 5.0
*/

/*  Copyright 2016 - 2024 - Benjamin Denis  (email : contact@seopress.org)

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
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

if ( ! function_exists('add_action')) {
	echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
	exit;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Hooks activation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_activation() {
	add_option('seopress_activated', 'yes');
	flush_rewrite_rules(false);

	do_action('seopress_activation');
}
register_activation_hook(__FILE__, 'seopress_activation');

function seopress_deactivation() {
	deactivate_plugins(['wp-seopress-pro/seopress-pro.php', 'wp-seopress-insights/seopress-insights.php']);

	delete_option('seopress_activated');
	flush_rewrite_rules(false);

	do_action('seopress_deactivation');
}
register_deactivation_hook(__FILE__, 'seopress_deactivation');

// Redirect User After Plugin Activation
function seopress_redirect_after_activation() {
    // Do not redirect if WP is doing AJAX requests OR multisite page OR incorrect user permissions
    if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Check if the plugin was activated
    if (get_option('seopress_activated') === 'yes') {
        // Delete the activation flag
        delete_option('seopress_activated');

        // If the wizard has already been completed, do not redirect the user
        $seopress_notices = get_option('seopress_notices', []);
        if (empty($seopress_notices) || !isset($seopress_notices['notice-wizard'])) {
            wp_safe_redirect( esc_url_raw(admin_url('admin.php?page=seopress-setup&step=welcome&parent=welcome')) );
            exit();
        }
    }
}
add_action('admin_init', 'seopress_redirect_after_activation');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Define
///////////////////////////////////////////////////////////////////////////////////////////////////
define('SEOPRESS_VERSION', '8.4.1');
define('SEOPRESS_AUTHOR', 'Benjamin Denis');
define('SEOPRESS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('SEOPRESS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SEOPRESS_ASSETS_DIR', SEOPRESS_PLUGIN_DIR_URL . 'assets');
define('SEOPRESS_TEMPLATE_DIR', SEOPRESS_PLUGIN_DIR_PATH . 'templates');
define('SEOPRESS_TEMPLATE_SITEMAP_DIR', SEOPRESS_TEMPLATE_DIR . '/sitemap');
define('SEOPRESS_TEMPLATE_JSON_SCHEMAS', SEOPRESS_TEMPLATE_DIR . '/json-schemas');
define('SEOPRESS_PATH_PUBLIC',  SEOPRESS_PLUGIN_DIR_PATH. 'public');
define('SEOPRESS_URL_PUBLIC', SEOPRESS_PLUGIN_DIR_URL . 'public');
define('SEOPRESS_URL_ASSETS', SEOPRESS_PLUGIN_DIR_URL . 'assets');

use SEOPress\Core\Kernel;

require_once __DIR__ . '/seopress-autoload.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/seopress-functions.php';

	Kernel::execute([
		'file'      => __FILE__,
		'slug'      => 'wp-seopress',
		'main_file' => 'seopress',
		'root'      => __DIR__,
	]);
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS INIT = Admin + Core + API
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_init($hook) {
	global $pagenow;
	global $typenow;
	global $wp_version;

    //Docs (must be loaded there)
    require_once dirname(__FILE__) . '/inc/admin/docs/DocsLinks.php';

	if (is_admin() || is_network_admin()) {
		require_once dirname(__FILE__) . '/inc/admin/plugin-upgrader.php';
		require_once dirname(__FILE__) . '/inc/admin/admin.php';
		require_once dirname(__FILE__) . '/inc/admin/migrate/MigrationTools.php';

		if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
			if ('seopress_schemas' != $typenow) {
				require_once dirname(__FILE__) . '/inc/admin/metaboxes/admin-metaboxes.php';
			}
		}
		if ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
			require_once dirname(__FILE__) . '/inc/admin/metaboxes/admin-term-metaboxes.php';
		}
		require_once dirname(__FILE__) . '/inc/admin/ajax.php';
		if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
			//do not load the SEOPress admin header
		} else {
			require_once dirname(__FILE__) . '/inc/admin/admin-bar/admin-header.php';
		}
	}

	require_once dirname(__FILE__) . '/inc/functions/options.php';

	require_once dirname(__FILE__) . '/inc/admin/admin-bar/admin-bar.php';

	remove_action('wp_head', 'rel_canonical'); //remove default WordPress Canonical

	// Elementor
	if (did_action('elementor/loaded') && apply_filters( 'seopress_elementor_integration_enabled', true ) === true) {
		include_once dirname(__FILE__) . '/inc/admin/page-builders/elementor/elementor-addon.php';
	}

	// Block Editor
	if (version_compare($wp_version, '5.0', '>=')) {
		include_once dirname(__FILE__) . '/inc/admin/page-builders/gutenberg/blocks.php';
	}

	// Classic Editor
	if ( is_admin() ) {
		include_once dirname(__FILE__) . '/inc/admin/page-builders/classic/classic-editor.php';
	}
}
add_action('plugins_loaded', 'seopress_init', 999);

function seopress_init_i18n() {
    //i18n
    load_plugin_textdomain('wp-seopress', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('init', 'seopress_init_i18n');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads dynamic variables for titles, metas, schemas...
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Render dynamic variables
 * @param array $variables
 * @param object $post
 * @param boolean $is_oembed
 * @return array $variables
 * @author Benjamin
 */
function seopress_dyn_variables_init($variables, $post = '', $is_oembed = false) {
	include_once dirname(__FILE__) . '/inc/functions/variables/dynamic-variables.php';
	return SEOPress\Helpers\CachedMemoizeFunctions::memoize('seopress_get_dynamic_variables')($variables, $post, $is_oembed);
}
add_filter('seopress_dyn_variables_fn', 'seopress_dyn_variables_init', 10, 3);

///////////////////////////////////////////////////////////////////////////////////////////////////
//Loads the JS/CSS in admin
///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Options page
function seopress_add_admin_options_scripts($hook) {
	$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style('seopress-admin', plugins_url('assets/css/seopress' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
	wp_enqueue_style('seopress-admin');

	if ( ! isset($_GET['page'])) {
		return;
	}

	if ('seopress-network-option' === $_GET['page']) {
		wp_enqueue_script('seopress-network-tabs', plugins_url('assets/js/seopress-network-tabs' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);
	}

	//Toggle / Notices JS
	$_pages = [
		'seopress-setup'                => true,
		'seopress-option'               => true,
		'seopress-network-option'       => true,
		'seopress-titles'               => true,
		'seopress-xml-sitemap'          => true,
		'seopress-social'               => true,
		'seopress-google-analytics'     => true,
		'seopress-pro-page'             => true,
		'seopress-instant-indexing'     => true,
		'seopress-advanced'             => true,
		'seopress-import-export'        => true,
		'seopress-bot-batch'            => true,
		'seopress-license'              => true,
		'seopress-insights'             => true,
		'seopress-insights-rankings'    => true,
		'seopress-insights-backlinks'   => true,
		'seopress-insights-competitors' => true,
		'seopress-insights-trends'      => true,
		'seopress-insights-settings'    => true,
		'seopress-insights-license'     => true,
	];
	if (isset($_pages[$_GET['page']])) {
		wp_enqueue_script('seopress-toggle-ajax', plugins_url('assets/js/seopress-dashboard' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);

		//Features
		$seopress_toggle_features = [
			'seopress_nonce'           => wp_create_nonce('seopress_toggle_features_nonce'),
			'seopress_toggle_features' => admin_url('admin-ajax.php'),
			'i18n'                     => __('has been successfully updated!', 'wp-seopress'),
		];
		wp_localize_script('seopress-toggle-ajax', 'seopressAjaxToggleFeatures', $seopress_toggle_features);

		//Notices
		$seopress_hide_notices = [
			'seopress_nonce'        => wp_create_nonce('seopress_hide_notices_nonce'),
			'seopress_hide_notices' => admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-toggle-ajax', 'seopressAjaxHideNotices', $seopress_hide_notices);

		//News panel
		$seopress_news = [
			'seopress_nonce'        => wp_create_nonce('seopress_news_nonce'),
			'seopress_news'         => admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-toggle-ajax', 'seopressAjaxNews', $seopress_news);

		//Display panel
		$seopress_display = [
			'seopress_nonce'        => wp_create_nonce('seopress_display_nonce'),
			'seopress_display'      => admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-toggle-ajax', 'seopressAjaxDisplay', $seopress_display);

	}

    // Wizard
    if ('seopress-setup' === $_GET['page']) {
		wp_enqueue_style('seopress-setup', plugins_url('assets/css/seopress-setup' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
		wp_enqueue_script('seopress-migrate-ajax', plugins_url('assets/js/seopress-migrate' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);
		wp_enqueue_media();
		wp_enqueue_script('seopress-media-uploader', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);

		$seopress_migrate = [
			'seopress_aio_migrate'				=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_aio_migrate_nonce'),
				'seopress_aio_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_yoast_migrate'			=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_yoast_migrate_nonce'),
				'seopress_yoast_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_framework_migrate'	=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_seo_framework_migrate_nonce'),
				'seopress_seo_framework_migration' 	=> admin_url('admin-ajax.php'),
			],
			'seopress_rk_migrate'				=> [
				'seopress_nonce'					=> wp_create_nonce('seopress_rk_migrate_nonce'),
				'seopress_rk_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_squirrly_migrate' 		=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_squirrly_migrate_nonce'),
				'seopress_squirrly_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_ultimate_migrate' 	=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
				'seopress_seo_ultimate_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wp_meta_seo_migrate'		=> [
				'seopress_nonce' 					=> wp_create_nonce('seopress_meta_seo_migrate_nonce'),
				'seopress_wp_meta_seo_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_premium_seo_pack_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
				'seopress_premium_seo_pack_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wpseo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_wpseo_migrate_nonce'),
				'seopress_wpseo_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_platinum_seo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_platinum_seo_migrate_nonce'),
				'seopress_platinum_seo_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_smart_crawl_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
				'seopress_smart_crawl_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seopressor_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_seopressor_migrate_nonce'),
				'seopress_seopressor_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_slim_seo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_slim_seo_migrate_nonce'),
				'seopress_slim_seo_migration'			=> admin_url('admin-ajax.php'),
			],
			'i18n'								=> [
				'migration'						=> __('Migration completed!', 'wp-seopress'),
				'export'						=> __('Export completed!', 'wp-seopress'),
			],
		];
		wp_localize_script('seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate);
    }

    // Dashboard
    if ('seopress-option' === $_GET['page']) {
        wp_register_style('seopress-admin-dashboard', plugins_url('assets/css/seopress-admin-dashboard' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
	    wp_enqueue_style('seopress-admin-dashboard');
    }

	//Migration
	if ('seopress-option' === $_GET['page'] || 'seopress-import-export' === $_GET['page']) {
		wp_enqueue_script('seopress-migrate-ajax', plugins_url('assets/js/seopress-migrate' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);

		$seopress_migrate = [
			'seopress_aio_migrate'				=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_aio_migrate_nonce'),
				'seopress_aio_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_yoast_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_yoast_migrate_nonce'),
				'seopress_yoast_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_framework_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_seo_framework_migrate_nonce'),
				'seopress_seo_framework_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_rk_migrate'				=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_rk_migrate_nonce'),
				'seopress_rk_migration'					=> admin_url('admin-ajax.php'),
			],
			'seopress_squirrly_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_squirrly_migrate_nonce'),
				'seopress_squirrly_migration'			=> admin_url('admin-ajax.php'),
			],
			'seopress_seo_ultimate_migrate'		=> [
				'seopress_nonce' 						=> wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
				'seopress_seo_ultimate_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_wp_meta_seo_migrate'		=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_meta_seo_migrate_nonce'),
				'seopress_wp_meta_seo_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_premium_seo_pack_migrate'	=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
				'seopress_premium_seo_pack_migration'	=> admin_url('admin-ajax.php'),
			],
			'seopress_wpseo_migrate'			=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_wpseo_migrate_nonce'),
				'seopress_wpseo_migration'				=> admin_url('admin-ajax.php'),
			],
			'seopress_platinum_seo_migrate'		=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_platinum_seo_migrate_nonce'),
				'seopress_platinum_seo_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_smart_crawl_migrate'		=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
				'seopress_smart_crawl_migration'		=> admin_url('admin-ajax.php'),
			],
			'seopress_seopressor_migrate'		=> [
				'seopress_nonce' 						=> wp_create_nonce('seopress_seopressor_migrate_nonce'),
				'seopress_seopressor_migration' 		=> admin_url('admin-ajax.php'),
			],
			'seopress_slim_seo_migrate'			=> [
				'seopress_nonce' 						=> wp_create_nonce('seopress_slim_seo_migrate_nonce'),
				'seopress_slim_seo_migration' 			=> admin_url('admin-ajax.php'),
			],
			'seopress_metadata_csv'				=> [
				'seopress_nonce' 						=> wp_create_nonce('seopress_export_csv_metadata_nonce'),
				'seopress_metadata_export' 				=> admin_url('admin-ajax.php'),
			],
			'i18n'								=> [
				'migration' 							=> __('Migration completed!', 'wp-seopress'),
				'export' 								=> __('Export completed!', 'wp-seopress'),
			],
		];
		wp_localize_script('seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate);
	}

	//Tabs
	if ('seopress-titles' === $_GET['page'] || 'seopress-xml-sitemap' === $_GET['page'] || 'seopress-social' === $_GET['page'] || 'seopress-google-analytics' === $_GET['page'] || 'seopress-advanced' === $_GET['page'] || 'seopress-import-export' === $_GET['page'] || 'seopress-instant-indexing' === $_GET['page'] || 'seopress-insights-settings' === $_GET['page']) {
		wp_enqueue_script('seopress-admin-tabs', plugins_url('assets/js/seopress-tabs' . $prefix . '.js', __FILE__), ['jquery-ui-tabs'], SEOPRESS_VERSION, true);
	}

	if ('seopress-google-analytics' === $_GET['page']) {
		wp_enqueue_style('wp-color-picker');

		wp_enqueue_script('wp-color-picker-alpha', plugins_url('assets/js/wp-color-picker-alpha' . $prefix . '.js', __FILE__), ['wp-color-picker'], SEOPRESS_VERSION, true);
		$color_picker_strings = [
			'clear'            => __('Clear', 'wp-seopress'),
			'clearAriaLabel'   => __('Clear color', 'wp-seopress'),
			'defaultString'    => __('Default', 'wp-seopress'),
			'defaultAriaLabel' => __('Select default color', 'wp-seopress'),
			'pick'             => __('Select Color', 'wp-seopress'),
			'defaultLabel'     => __('Color value', 'wp-seopress'),
		];
		wp_localize_script('wp-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings);

        $settings = wp_enqueue_code_editor( [ 'type' => 'text/html' ] );

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery(function($) {
					// Function to initialize a single Code Editor
					function initializeEditor(elementId, settings) {
						var $textarea = $("#" + elementId);
		
						// Check if the editor is already initialized
						if (!$textarea.data("codeMirrorInitialized")) {
							wp.codeEditor.initialize(elementId, settings);
							$textarea.data("codeMirrorInitialized", true); // Mark as initialized
						}
					}
		
					// Function to initialize all editors
					function initializeEditors() {
						initializeEditor("seopress_google_analytics_other_tracking", %s);
						initializeEditor("seopress_google_analytics_other_tracking_body", %s);
						initializeEditor("seopress_google_analytics_other_tracking_footer", %s);
					}
		
					// Run initialization when the page loads
					$(document).ready(function() {
						initializeEditors();
		
						// Recheck visibility after a short delay for hidden elements (e.g., inside tabs)
						setTimeout(initializeEditors, 100);
					});
				});',
				wp_json_encode($settings),
				wp_json_encode($settings),
				wp_json_encode($settings)
			)
		);
	}

	if ('seopress-social' === $_GET['page']) {
		wp_enqueue_script('seopress-media-uploader', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, false);
		wp_enqueue_media();
	}

	//Instant Indexing
	if ('seopress-instant-indexing' === $_GET['page']) {
		$seopress_instant_indexing_post = [
			'seopress_nonce'					=> wp_create_nonce('seopress_instant_indexing_post_nonce'),
			'seopress_instant_indexing_post'	=> admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-admin-tabs', 'seopressAjaxInstantIndexingPost', $seopress_instant_indexing_post);

		$seopress_instant_indexing_generate_api_key = [
			'seopress_nonce'								=> wp_create_nonce('seopress_instant_indexing_generate_api_key_nonce'),
			'seopress_instant_indexing_generate_api_key'	=> admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-admin-tabs', 'seopressAjaxInstantIndexingApiKey', $seopress_instant_indexing_generate_api_key);

        $settings = wp_enqueue_code_editor( [ 'type' => 'application/json' ] );

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery(function($) {
					// Function to initialize a single Code Editor
					function initializeEditor(elementId, settings) {
						var $textarea = $("#" + elementId);
		
						// Check if the editor is already initialized
						if (!$textarea.data("codeMirrorInitialized")) {
							wp.codeEditor.initialize(elementId, settings);
							$textarea.data("codeMirrorInitialized", true); // Mark as initialized
						}
					}
		
					// Function to initialize all editors
					function initializeEditors() {
						initializeEditor("seopress_instant_indexing_google_api_key", %s);
					}
		
					// Run initialization when the page loads
					$(document).ready(function() {
						initializeEditors();
		
						// Recheck visibility after a short delay for hidden elements (e.g., inside tabs)
						setTimeout(initializeEditors, 100);
					});
				});',
				wp_json_encode($settings)
			)
		);
	}

	//CSV Importer
	if ('seopress_csv_importer' === $_GET['page']) {
		wp_enqueue_style('seopress-setup', plugins_url('assets/css/seopress-setup' . $prefix . '.css', __FILE__), ['dashicons'], SEOPRESS_VERSION);
	}
}

add_action('admin_enqueue_scripts', 'seopress_add_admin_options_scripts', 10, 1);

//SEOPRESS Admin bar
function seopress_admin_bar_css() {
	$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	if (is_user_logged_in() && '1' !== seopress_get_service('AdvancedOption')->getAppearanceAdminBar()) {
		if (is_admin_bar_showing()) {
			wp_register_style('seopress-admin-bar', plugins_url('assets/css/seopress-admin-bar' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
			wp_enqueue_style('seopress-admin-bar');
		}
	}
}
add_action('init', 'seopress_admin_bar_css', 12, 1);

//Quick Edit
function seopress_add_admin_options_scripts_quick_edit() {
	$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script('seopress-quick-edit', plugins_url('assets/js/seopress-quick-edit' . $prefix . '.js', __FILE__), ['jquery', 'inline-edit-post'], SEOPRESS_VERSION, true);
}
add_action('admin_print_scripts-edit.php', 'seopress_add_admin_options_scripts_quick_edit');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Admin Body Class
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('admin_body_class', 'seopress_admin_body_class', 100);
function seopress_admin_body_class($classes) {
	if ( ! isset($_GET['page'])) {
		return $classes;
	}
	$_pages = [
		'seopress_csv_importer'             => true,
		'seopress-setup'                    => true,
		'seopress-option'                   => true,
		'seopress-network-option'           => true,
		'seopress-titles'                   => true,
		'seopress-xml-sitemap'              => true,
		'seopress-social'                   => true,
		'seopress-google-analytics'         => true,
		'seopress-advanced'                 => true,
		'seopress-import-export'            => true,
		'seopress-pro-page'                 => true,
		'seopress-instant-indexing'         => true,
		'seopress-bot-batch'                => true,
		'seopress-license'                  => true
	];
	if (isset($_pages[$_GET['page']])) {
		$classes .= ' seopress-styles ';
	}
    if (isset($_pages[$_GET['page']]) && 'seopress-option' === $_GET['page']) {
		$classes .= ' seopress-dashboard ';
	}
	if (isset($_pages[$_GET['page']]) && ('seopress_csv_importer' === $_GET['page'] || 'seopress-setup' === $_GET['page'] )) {
		$classes .= ' seopress-setup ';
	}
	if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
		$classes .= ' seopress-white-label ';
	}

	return $classes;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Plugin action links
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Shortcut settings page
 *
 * @since 3.5.9
 * @param string $links, $file
 * @return array $links
 * @author Benjamin
 */
add_filter('plugin_action_links', 'seopress_plugin_action_links', 10, 2);
function seopress_plugin_action_links($links, $file) {
	static $this_plugin;

	if ( ! $this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin) {
		$settings_link = '<a href="' . admin_url('admin.php?page=seopress-option') . '">' . __('Settings', 'wp-seopress') . '</a>';
		$website_link  = '<a href="https://www.seopress.org/support/" target="_blank">' . __('Docs', 'wp-seopress') . '</a>';
		$wizard_link   = '<a href="' . admin_url('admin.php?page=seopress-setup&step=welcome&parent=welcome') . '">' . __('Configuration Wizard', 'wp-seopress') . '</a>';
		if ( ! is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
			$pro_link = '<a href="https://www.seopress.org/seopress-pro/" style="color:red;font-weight:bold" target="_blank">' . __('GO PRO!', 'wp-seopress') . '</a>';
			array_unshift($links, $pro_link);
		}
		if (is_plugin_active('wp-seopress-pro/seopress-pro.php') || is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
			if (array_key_exists('deactivate', $links) && in_array($file, [
				'wp-seopress/seopress.php',
			]));
			unset($links['deactivate']);
		}


        if ( function_exists('seopress_pro_get_service') && '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel() && method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelHelpLinks') && '1' === seopress_pro_get_service('OptionPro')->getWhiteLabelHelpLinks()) {
            array_unshift($links, $settings_link, $wizard_link);
        } else {
            array_unshift($links, $settings_link, $wizard_link, $website_link);
        }
	}

	return $links;
}

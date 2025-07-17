<?php
/*
Plugin Name: SEOPress
Plugin URI: https://www.seopress.org/
Description: One of the best SEO plugins for WordPress.
Author: The SEO Guys at SEOPress
Version: 8.9.0.1
Author URI: https://www.seopress.org/
License: GPLv3 or later
Text Domain: wp-seopress
Domain Path: /languages
Requires PHP: 7.4
Requires at least: 5.0
*/

/*  Copyright 2016 - 2025 - Benjamin Denis  (email : contact@seopress.org)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as
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
defined('ABSPATH') or exit('Please don’t call the plugin directly. Thanks :)');

/**
 * Define constants
 */
define('SEOPRESS_VERSION', '8.9.0.1');
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

/**
 * Kernel
 */
use SEOPress\Core\Kernel;
require_once SEOPRESS_PLUGIN_DIR_PATH . 'seopress-autoload.php';

if (file_exists(SEOPRESS_PLUGIN_DIR_PATH . 'vendor/autoload.php')) {
    require_once SEOPRESS_PLUGIN_DIR_PATH . 'seopress-functions.php';
}

// Initialize the kernel if the vendor autoload exists
if (file_exists(SEOPRESS_PLUGIN_DIR_PATH . 'vendor/autoload.php')) {
    Kernel::execute([
        'file'      => __FILE__,
        'slug'      => 'wp-seopress',
        'main_file' => 'seopress',
        'root'      => __DIR__,
    ]);
}

/**
 * Activation hook
 * @return void
 */
function seopress_activation() {
	add_option('seopress_activated', "yes");
	flush_rewrite_rules(false);

	do_action('seopress_activation');
}
register_activation_hook(__FILE__, 'seopress_activation');

/**
 * Deactivation hook
 * @return void
 */
function seopress_deactivation() {
	deactivate_plugins(['wp-seopress-pro/seopress-pro.php']);
	delete_option('seopress_activated');
	flush_rewrite_rules(false);
	do_action('seopress_deactivation');
}
register_deactivation_hook(__FILE__, 'seopress_deactivation');

/**
 * Redirect User After Plugin Activation
 * @return void
 */
function seopress_redirect_after_activation() {
    // Do not redirect if WP is doing AJAX requests OR multisite page OR incorrect user permissions
    if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Check if the plugin was activated
    if (get_option('seopress_activated') === "yes") {
        
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

/**
 * Loads the SEOPress admin + core + API
 * @param string $hook
 * @return void
 */
function seopress_plugins_loaded($hook) {
	global $pagenow, $typenow, $wp_version;

    $plugin_dir = plugin_dir_path(__FILE__);

    // Load Docs
    require_once $plugin_dir . 'inc/admin/docs/DocsLinks.php';

	if (is_admin() || is_network_admin()) {
		require_once $plugin_dir . 'inc/admin/admin.php';

		// Load metaboxes only when editing posts or terms
		if (in_array($pagenow, ['post-new.php', 'post.php']) && 'seopress_schemas' !== $typenow) {
			require_once $plugin_dir . 'inc/admin/metaboxes/admin-metaboxes.php';
		} elseif (in_array($pagenow, ['term.php', 'edit-tags.php'])) {
			require_once $plugin_dir . 'inc/admin/metaboxes/admin-term-metaboxes.php';
		}

		// Load admin header unless explicitly disabled
		if (!defined('SEOPRESS_WL_ADMIN_HEADER') || SEOPRESS_WL_ADMIN_HEADER !== false) {
			require_once $plugin_dir . 'inc/admin/admin-bar/admin-header.php';
		}
	}

	// Load options and admin bar
	require_once $plugin_dir . 'inc/functions/options.php';
	require_once $plugin_dir . 'inc/admin/admin-bar/admin-bar.php';

	// Load integrations conditionally
	if (did_action('elementor/loaded') && apply_filters('seopress_elementor_integration_enabled', true)) {
		include_once $plugin_dir . 'inc/admin/page-builders/elementor/elementor-addon.php';
	}

	if (version_compare($wp_version, '5.0', '>=')) {
		include_once $plugin_dir . 'inc/admin/page-builders/gutenberg/blocks.php';
	}

	if (is_admin()) {
		include_once $plugin_dir . 'inc/admin/page-builders/classic/classic-editor.php';
	}
}
add_action('plugins_loaded', 'seopress_plugins_loaded', 999);

/**
 * Loads the SEOPress i18n + dynamic variables
 * @return void
 */
function seopress_init() {
    // i18n
    load_plugin_textdomain('wp-seopress', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    // Preload dynamic variables file
    include_once plugin_dir_path(__FILE__) . 'inc/functions/variables/dynamic-variables.php';
}
add_action('init', 'seopress_init');

/**
 * Render dynamic variables
 * @param array $variables
 * @param object $post
 * @param boolean $is_oembed
 * @return array $variables
 */
function seopress_dyn_variables_init($variables, $post = '', $is_oembed = false) {
    // Use memoized function for dynamic variable retrieval
    return SEOPress\Helpers\CachedMemoizeFunctions::memoize('seopress_get_dynamic_variables')($variables, $post, $is_oembed);
}
add_filter('seopress_dyn_variables_fn', 'seopress_dyn_variables_init', 10, 3);

/**
 * Loads the JS/CSS in admin
 * @param string $hook
 * @return void
 */
function seopress_add_admin_options_scripts($hook) {
    $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    
    // Register stylesheets
    wp_register_style('seopress-admin', plugins_url('assets/css/seopress' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
    wp_enqueue_style('seopress-admin');
    
    // Early return if no page query var
    if ( ! isset($_GET['page'])) {
        return;
    }

    // Preload scripts that are required for specific pages
    $page = $_GET['page'];
    $scripts = [];

    // Network options page
    if ($page === 'seopress-network-option') {
        $scripts[] = 'seopress-network-tabs';
    }

    // Pages needing Toggle / Notices JS
    $pages_with_toggle_js = array_map(function($page) {
        return 'seopress-' . $page;
    }, [
        'setup', 'option', 'network-option', 'titles', 'xml-sitemap', 'social', 'google-analytics', 'pro-page', 'instant-indexing', 'advanced', 'import-export', 'bot-batch', 'license'
    ]);

    if (in_array($page, $pages_with_toggle_js)) {
        $scripts[] = 'seopress-dashboard';
    }

    // Setup Wizard page
    if ($page === 'seopress-setup') {
        wp_enqueue_style('seopress-setup', plugins_url('assets/css/seopress-setup' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
        wp_enqueue_script('seopress-migrate', plugins_url('assets/js/seopress-migrate' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);
        wp_enqueue_media();
        wp_enqueue_script('seopress-media-uploader', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);
    }

    // Dashboard page styles
    if ($page === 'seopress-option') {
        wp_register_style('seopress-admin-dashboard', plugins_url('assets/css/seopress-admin-dashboard' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
        wp_enqueue_style('seopress-admin-dashboard');
    }

    // Load common migration scripts for multiple pages
    if (in_array($page, ['seopress-option', 'seopress-import-export'])) {
        $scripts[] = 'seopress-migrate';
    }

    // Tabs script
    $pages_with_tabs = array_map(function($page) {
        return 'seopress-' . $page;
    }, [
        'titles', 'xml-sitemap', 'social', 'google-analytics', 'advanced', 'import-export', 'instant-indexing'
    ]);

    if (in_array($page, $pages_with_tabs)) {
        $scripts[] = 'seopress-tabs';
    }

    // Load scripts conditionally
    foreach ($scripts as $script) {
        wp_enqueue_script($script, plugins_url('assets/js/' . $script . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);
    }

    if (in_array($page, $pages_with_toggle_js)) {
		//Features
		$seopress_toggle_features = [
			'seopress_nonce'           => wp_create_nonce('seopress_toggle_features_nonce'),
			'seopress_toggle_features' => admin_url('admin-ajax.php'),
			'i18n'                     => __('has been successfully updated!', 'wp-seopress'),
		];
		wp_localize_script('seopress-dashboard', 'seopressAjaxToggleFeatures', $seopress_toggle_features);

        //Notices
        $seopress_hide_notices = [
            'seopress_nonce'        => wp_create_nonce('seopress_hide_notices_nonce'),
            'seopress_hide_notices' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-dashboard', 'seopressAjaxHideNotices', $seopress_hide_notices);

        if ($page === 'seopress-option') {
            //Simple View
            $seopress_switch_view = [
                'seopress_nonce'        => wp_create_nonce('seopress_switch_view_nonce'),
                'seopress_switch_view' => admin_url('admin-ajax.php'),
            ];
            wp_localize_script('seopress-dashboard', 'seopressAjaxSwitchView', $seopress_switch_view);

            //News panel
            $seopress_news = [
                'seopress_nonce'        => wp_create_nonce('seopress_news_nonce'),
                'seopress_news'         => admin_url('admin-ajax.php'),
            ];
            wp_localize_script('seopress-dashboard', 'seopressAjaxNews', $seopress_news);

            //Display panel
            $seopress_display = [
                'seopress_nonce'        => wp_create_nonce('seopress_display_nonce'),
                'seopress_display'      => admin_url('admin-ajax.php'),
            ];
            wp_localize_script('seopress-dashboard', 'seopressAjaxDisplay', $seopress_display);
        }
    }

    // Google Analytics color picker
    if ($page === 'seopress-google-analytics') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker-alpha', plugins_url('assets/js/wp-color-picker-alpha' . $prefix . '.js', __FILE__), ['wp-color-picker'], SEOPRESS_VERSION, true);
        wp_localize_script('wp-color-picker-alpha', 'wpColorPickerL10n', [
            'clear' => __('Clear', 'wp-seopress'),
            'clearAriaLabel' => __('Clear color', 'wp-seopress'),
            'defaultString' => __('Default', 'wp-seopress'),
            'defaultAriaLabel' => __('Select default color', 'wp-seopress'),
            'pick' => __('Select Color', 'wp-seopress'),
            'defaultLabel' => __('Color value', 'wp-seopress')
        ]);
        
        $settings = wp_enqueue_code_editor(['type' => 'text/html']);
        wp_add_inline_script('code-editor', sprintf('jQuery(function($) { 
            function initializeEditor(elementId, settings) {
                var $textarea = $("#" + elementId);
                if (!$textarea.data("codeMirrorInitialized")) {
                    wp.codeEditor.initialize(elementId, settings);
                    $textarea.data("codeMirrorInitialized", true);
                }
            }
            function initializeEditors() {
                initializeEditor("seopress_google_analytics_other_tracking", %s);
                initializeEditor("seopress_google_analytics_other_tracking_body", %s);
                initializeEditor("seopress_google_analytics_other_tracking_footer", %s);
            }
            $(document).ready(function() {
                initializeEditors();
                setTimeout(initializeEditors, 100);
            });
        });', wp_json_encode($settings), wp_json_encode($settings), wp_json_encode($settings)));
    }

    // Localize migration data once for all migration pages
    if (in_array($page, ['seopress-option', 'seopress-import-export', 'seopress-setup'])) {
        $seopress_migrate = [
			'seopress_aio_migrate' => [
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
			'seopress_smart_crawl_migrate'		=> [
				'seopress_nonce'						=> wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
				'seopress_smart_crawl_migration'		=> admin_url('admin-ajax.php'),
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
        wp_localize_script('seopress-migrate', 'seopressAjaxMigrate', $seopress_migrate);
    }

    // Media uploader for social page
    if ($page === 'seopress-social') {
        wp_enqueue_script('seopress-media-uploader', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, false);
        wp_enqueue_media();
    }

    // Instant Indexing page
    if ($page === 'seopress-instant-indexing') {
        $seopress_instant_indexing_post = [
            'seopress_nonce' => wp_create_nonce('seopress_instant_indexing_post_nonce'),
            'seopress_instant_indexing_post' => admin_url('admin-ajax.php')
        ];
        wp_localize_script('seopress-dashboard', 'seopressAjaxInstantIndexingPost', $seopress_instant_indexing_post);

		$seopress_instant_indexing_generate_api_key = [
			'seopress_nonce'								=> wp_create_nonce('seopress_instant_indexing_generate_api_key_nonce'),
			'seopress_instant_indexing_generate_api_key'	=> admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-dashboard', 'seopressAjaxInstantIndexingApiKey', $seopress_instant_indexing_generate_api_key);

        $settings = wp_enqueue_code_editor( [ 'type' => 'application/json' ] );

		wp_add_inline_script('code-editor', sprintf('jQuery(function($) {
			function initializeEditor(elementId, settings) {
				var $textarea = $("#" + elementId);
				if (!$textarea.data("codeMirrorInitialized")) {
					wp.codeEditor.initialize(elementId, settings);
					$textarea.data("codeMirrorInitialized", true);
				}
			}
			function initializeEditors() {
				initializeEditor("seopress_instant_indexing_google_api_key", %s);
			}
			$(document).ready(function() {
				initializeEditors();
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

/**
 * Admin bar CSS
 * @return void
 */
function seopress_admin_bar_css() {
    // Only run when the admin bar is showing and the user is logged in
    if (is_user_logged_in() && is_admin_bar_showing()) {
        // Get the appearance setting only once
        $appearance_option = seopress_get_service('AdvancedOption')->getAppearanceAdminBar();

        // Enqueue the style only if the appearance option is not '1'
        if ('1' !== $appearance_option) {
            $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_style('seopress-admin-bar', plugins_url('assets/css/seopress-admin-bar' . $prefix . '.css', __FILE__), [], SEOPRESS_VERSION);
        }
    }
}
add_action('init', 'seopress_admin_bar_css', 12);

/**
 * Quick Edit - Enqueue Scripts for All Post Types
 * @return void
 */
function seopress_add_admin_options_scripts_quick_edit() {
	$screen = get_current_screen();
    if ('edit' !== $screen->base) {
        return;
    }

    if (is_plugin_active('admin-columns-pro/admin-columns-pro.php')) {
        return;
    }

    $prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    $script_url = plugins_url('assets/js/seopress-quick-edit' . $prefix . '.js', __FILE__);

    wp_enqueue_script('seopress-quick-edit', $script_url, ['jquery', 'inline-edit-post'], SEOPRESS_VERSION, true);
}
add_action('admin_print_scripts-edit.php', 'seopress_add_admin_options_scripts_quick_edit');

/**
 * Add custom body classes for specific SEOPress admin pages.
 * @param string $classes Existing body classes.
 * @return string Updated body classes.
 */
function seopress_admin_body_class($classes) {
    if (empty($_GET['page'])) {
        return $classes;
    }

    // List of pages to apply classes
    $seopress_pages = [
        'seopress_csv_importer',
        'seopress-setup',
        'seopress-option',
        'seopress-network-option',
        'seopress-titles',
        'seopress-xml-sitemap',
        'seopress-social',
        'seopress-google-analytics',
        'seopress-advanced',
        'seopress-import-export',
        'seopress-pro-page',
        'seopress-instant-indexing',
        'seopress-bot-batch',
        'seopress-license'
    ];

    $current_page = sanitize_text_field($_GET['page']);

    // Check if current page is in the defined pages
    if (in_array($current_page, $seopress_pages, true)) {
        $classes .= ' seopress-styles';

        // Additional class for specific pages
        if ('seopress-option' === $current_page) {
            $classes .= ' seopress-dashboard';
        } elseif (in_array($current_page, ['seopress_csv_importer', 'seopress-setup'], true)) {
            $classes .= ' seopress-setup';
        }
    }

    // Add white-label class if applicable
    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        $classes .= ' seopress-white-label';
    }

    // Add simple view class if applicable
    if (!empty(get_option('seopress_dashboard'))) {
        if ('simple' === get_option('seopress_dashboard')['view']) {
            $classes .= ' seopress-simple-view';
        }
    }

    return $classes;
}
add_filter('admin_body_class', 'seopress_admin_body_class', 100);

/**
 * Plugin action links
 * @param string $links, $file
 * @return array $links
 */
function seopress_plugin_action_links($links, $file) {
    static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file === $this_plugin) {
        // Define action links
        $settings_link = '<a href="' . admin_url('admin.php?page=seopress-option') . '">' . __('Settings', 'wp-seopress') . '</a>';
        $wizard_link = '<a href="' . admin_url('admin.php?page=seopress-setup&step=welcome&parent=welcome') . '">' . __('Configuration Wizard', 'wp-seopress') . '</a>';
        $website_link = '<a href="https://www.seopress.org/support/" target="_blank">' . __('Docs', 'wp-seopress') . '</a>';

        // Add "GO PRO!" link for non-PRO users
        if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
            $pro_link = '<a href="https://www.seopress.org/seopress-pro/" style="color:red;font-weight:bold" target="_blank">' . __('GO PRO!', 'wp-seopress') . '</a>';
            array_unshift($links, $pro_link);
        }

        // Remove "Deactivate" link if PRO plugins are active
        $is_pro_active = is_plugin_active('wp-seopress-pro/seopress-pro.php');
        if ($is_pro_active && isset($links['deactivate'])) {
            unset($links['deactivate']);
        }

        // Determine white-label behavior
        $use_white_label_links = function_exists('seopress_pro_get_service') &&
            '1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel() &&
            method_exists(seopress_pro_get_service('OptionPro'), 'getWhiteLabelHelpLinks') &&
            '1' === seopress_pro_get_service('OptionPro')->getWhiteLabelHelpLinks();

        // Add appropriate links
        if ($use_white_label_links) {
            array_unshift($links, $settings_link, $wizard_link);
        } else {
            array_unshift($links, $settings_link, $wizard_link, $website_link);
        }
    }

    return $links;
}
add_filter('plugin_action_links', 'seopress_plugin_action_links', 10, 2);
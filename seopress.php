<?php
/*
Plugin Name: SEOPress
Plugin URI: https://www.seopress.org/
Description: One of the best SEO plugins for WordPress.
Author: SEOPress
Version: 5.5.1
Author URI: https://www.seopress.org/
License: GPLv2
Text Domain: wp-seopress
Domain Path: /languages
*/

/*  Copyright 2016 - 2022 - Benjamin Denis  (email : contact@seopress.org)

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
if ( ! function_exists('add_action')) {
    echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
    exit;
}
///////////////////////////////////////////////////////////////////////////////////////////////////
//CRON
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_cron() {
    //CRON - Ping Google for XML Sitemaps
    if ( ! wp_next_scheduled('seopress_xml_sitemaps_ping_cron')) {
        wp_schedule_event(time(), 'daily', 'seopress_xml_sitemaps_ping_cron');
    }
}
///////////////////////////////////////////////////////////////////////////////////////////////////
//Hooks activation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_activation() {
    add_option('seopress_activated', 'yes');
    flush_rewrite_rules(false);

    seopress_cron();

    do_action('seopress_activation');
}
register_activation_hook(__FILE__, 'seopress_activation');

function seopress_deactivation() {
    deactivate_plugins(['wp-seopress-pro/seopress-pro.php', 'wp-seopress-insights/seopress-insights.php']);

    delete_option('seopress_activated');
    flush_rewrite_rules(false);

    //Remove our CRON
    wp_clear_scheduled_hook('seopress_xml_sitemaps_ping_cron');

    do_action('seopress_deactivation');
}
register_deactivation_hook(__FILE__, 'seopress_deactivation');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Define
///////////////////////////////////////////////////////////////////////////////////////////////////
define('SEOPRESS_VERSION', '5.5.1');
define('SEOPRESS_AUTHOR', 'Benjamin Denis');
define('SEOPRESS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('SEOPRESS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SEOPRESS_ASSETS_DIR', SEOPRESS_PLUGIN_DIR_URL . 'assets');
define('SEOPRESS_TEMPLATE_DIR', SEOPRESS_PLUGIN_DIR_PATH . 'templates');
define('SEOPRESS_TEMPLATE_SITEMAP_DIR', SEOPRESS_TEMPLATE_DIR . '/sitemap');
define('SEOPRESS_TEMPLATE_JSON_SCHEMAS', SEOPRESS_TEMPLATE_DIR . '/json-schemas');

define('SEOPRESS_DIRURL', plugin_dir_url(__FILE__));
define('SEOPRESS_URL_DIST', SEOPRESS_DIRURL . 'public');
define('SEOPRESS_URL_ASSETS', SEOPRESS_DIRURL . 'assets');
define('SEOPRESS_DIR_LANGUAGES', dirname(plugin_basename(__FILE__)) . '/languages/');

use SEOPress\Core\Kernel;

require_once __DIR__ . '/seopress-autoload.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/seopress-functions.php';
    require_once __DIR__ . '/inc/admin/cron.php';

    Kernel::execute([
        'file'      => __FILE__,
        'slug'      => 'wp-seopress',
        'main_file' => 'seopress',
        'root'      => __DIR__,
    ]);
}

function seopress_titles_single_cpt_enable_option($cpt) {
    $current_cpt                          = null;
    $seopress_titles_single_enable_option = get_option('seopress_titles_option_name');
    if ( ! empty($seopress_titles_single_enable_option)) {
        foreach ($seopress_titles_single_enable_option as $key => $seopress_titles_single_enable_value) {
            $options[$key] = $seopress_titles_single_enable_value;
            if (isset($seopress_titles_single_enable_option['seopress_titles_single_titles'][$cpt]['enable'])) {
                $current_cpt = $seopress_titles_single_enable_option['seopress_titles_single_titles'][$cpt]['enable'];
            }
        }
    }

    return $current_cpt;
}

//Archive CPT Titles
function seopress_titles_archive_titles_option() {
    global $post;
    $seopress_get_current_cpt = get_post_type($post);

    $seopress_titles_archive_titles_option = get_option('seopress_titles_option_name');
    if ( ! empty($seopress_titles_archive_titles_option)) {
        foreach ($seopress_titles_archive_titles_option as $key => $seopress_titles_archive_titles_value) {
            $options[$key] = $seopress_titles_archive_titles_value;
        }
        if (isset($seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'])) {
            return $seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'];
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS INIT = Admin + Core + API + Translation
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_init($hook) {
    //CRON
    seopress_cron();

    //i18n
    load_plugin_textdomain('wp-seopress', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    global $pagenow;
    global $typenow;
    global $wp_version;

    if (is_admin() || is_network_admin()) {
        require_once dirname(__FILE__) . '/inc/admin/plugin-upgrader.php';
        require_once dirname(__FILE__) . '/inc/admin/admin.php';
        require_once dirname(__FILE__) . '/inc/admin/migrate/MigrationTools.php';
        require_once dirname(__FILE__) . '/inc/admin/docs/DocsLinks.php';

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

    //Setup/welcome
    if ( ! empty($_GET['page'])) {
        switch ($_GET['page']) {
            case 'seopress-setup':
                include_once dirname(__FILE__) . '/inc/admin/wizard/admin-wizard.php';
                break;
            default:
                break;
        }
    }

    //Elementor
    if (did_action('elementor/loaded')) {
        include_once dirname(__FILE__) . '/inc/admin/page-builders/elementor/elementor-addon.php';
    }

    //Block Editor
    if (version_compare($wp_version, '5.0', '>=')) {
        include_once dirname(__FILE__) . '/inc/admin/page-builders/gutenberg/gutenberg-addon.php';
    }
}
add_action('plugins_loaded', 'seopress_init', 999);

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
    $variables = include dirname(__FILE__) . '/inc/functions/variables/dynamic-variables.php';

    return $variables;
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
        'seopress-insights-trends'      => true,
    ];
    if (isset($_pages[$_GET['page']])) {
        wp_enqueue_script('seopress-toggle-ajax', plugins_url('assets/js/seopress-dashboard' . $prefix . '.js', __FILE__), ['jquery', 'jquery-ui-sortable'], SEOPRESS_VERSION, true);

        //Features
        $seopress_toggle_features = [
            'seopress_nonce'           => wp_create_nonce('seopress_toggle_features_nonce'),
            'seopress_toggle_features' => admin_url('admin-ajax.php'),
            'i18n'                     => __('has been successfully updated!', 'wp-seopress'),
        ];
        wp_localize_script('seopress-toggle-ajax', 'seopressAjaxToggleFeatures', $seopress_toggle_features);

        //Drag and drop
        $seopress_dnd_features = [
            'seopress_nonce' => wp_create_nonce('seopress_dnd_features_nonce'),
            'seopress_dnd_features' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-toggle-ajax', 'seopressAjaxDndFeatures', $seopress_dnd_features);
    }
    unset($_pages);

    if ('seopress-option' === $_GET['page']) {
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

        //Admin Tabs
        wp_enqueue_script('seopress-reverse-ajax', plugins_url('assets/js/seopress-tabs' . $prefix . '.js', __FILE__), ['jquery-ui-tabs'], SEOPRESS_VERSION);

        //Reverse domains
        $seopress_request_reverse = [
            'seopress_nonce'           => wp_create_nonce('seopress_request_reverse_nonce'),
            'seopress_request_reverse' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-reverse-ajax', 'seopressAjaxReverse', $seopress_request_reverse);

        $seopress_clear_reverse_cache = [
            'seopress_nonce'               => wp_create_nonce('seopress_clear_reverse_cache_nonce'),
            'seopress_clear_reverse_cache' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-reverse-ajax', 'seopressAjaxClearReverseCache', $seopress_clear_reverse_cache);
    }

    //Migration
    if ('seopress-option' === $_GET['page'] || 'seopress-import-export' === $_GET['page']) {
        wp_enqueue_script('seopress-migrate-ajax', plugins_url('assets/js/seopress-migrate' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);

        $seopress_migrate = [
            'seopress_aio_migrate'				=> [
                'seopress_nonce'						      => wp_create_nonce('seopress_aio_migrate_nonce'),
                'seopress_aio_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_yoast_migrate'			=> [
                'seopress_nonce'						        => wp_create_nonce('seopress_yoast_migrate_nonce'),
                'seopress_yoast_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_seo_framework_migrate'	=> [
                'seopress_nonce'						              => wp_create_nonce('seopress_seo_framework_migrate_nonce'),
                'seopress_seo_framework_migration'		=> admin_url('admin-ajax.php'),
            ],
            'seopress_rk_migrate'				=> [
                'seopress_nonce'						      => wp_create_nonce('seopress_rk_migrate_nonce'),
                'seopress_rk_migration'					=> admin_url('admin-ajax.php'),
            ],
            'seopress_squirrly_migrate'			=> [
                'seopress_nonce'						          => wp_create_nonce('seopress_squirrly_migrate_nonce'),
                'seopress_squirrly_migration'			=> admin_url('admin-ajax.php'),
            ],
            'seopress_seo_ultimate_migrate'		=> [
                'seopress_nonce' 						            => wp_create_nonce('seopress_seo_ultimate_migrate_nonce'),
                'seopress_seo_ultimate_migration'		=> admin_url('admin-ajax.php'),
            ],
            'seopress_wp_meta_seo_migrate'		=> [
                'seopress_nonce'						            => wp_create_nonce('seopress_meta_seo_migrate_nonce'),
                'seopress_wp_meta_seo_migration'		=> admin_url('admin-ajax.php'),
            ],
            'seopress_premium_seo_pack_migrate'	=> [
                'seopress_nonce'						                => wp_create_nonce('seopress_premium_seo_pack_migrate_nonce'),
                'seopress_premium_seo_pack_migration'	=> admin_url('admin-ajax.php'),
            ],
            'seopress_wpseo_migrate'			=> [
                'seopress_nonce'						        => wp_create_nonce('seopress_wpseo_migrate_nonce'),
                'seopress_wpseo_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_platinum_seo_migrate'			=> [
                'seopress_nonce'						               => wp_create_nonce('seopress_platinum_seo_migrate_nonce'),
                'seopress_platinum_seo_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_smart_crawl_migrate'			=> [
                'seopress_nonce'						              => wp_create_nonce('seopress_smart_crawl_migrate_nonce'),
                'seopress_smart_crawl_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_seopressor_migrate'			=> [
                'seopress_nonce'						             => wp_create_nonce('seopress_seopressor_migrate_nonce'),
                'seopress_seopressor_migration'				=> admin_url('admin-ajax.php'),
            ],
            'seopress_metadata_csv'				=> [
                'seopress_nonce'						        => wp_create_nonce('seopress_export_csv_metadata_nonce'),
                'seopress_metadata_export'				=> admin_url('admin-ajax.php'),
            ],
            'i18n'								=> [
                'migration'								=> __('Migration completed!', 'wp-seopress'),
                'export'								   => __('Export completed!', 'wp-seopress'),
            ],
        ];
        wp_localize_script('seopress-migrate-ajax', 'seopressAjaxMigrate', $seopress_migrate);
    }

    //Tabs
    if ('seopress-titles' === $_GET['page'] || 'seopress-xml-sitemap' === $_GET['page'] || 'seopress-social' === $_GET['page'] || 'seopress-google-analytics' === $_GET['page'] || 'seopress-advanced' === $_GET['page'] || 'seopress-import-export' === $_GET['page'] || 'seopress-instant-indexing' === $_GET['page']) {
        wp_enqueue_script('seopress-admin-tabs-js', plugins_url('assets/js/seopress-tabs' . $prefix . '.js', __FILE__), ['jquery-ui-tabs'], SEOPRESS_VERSION);
    }

    if ('seopress-xml-sitemap' === $_GET['page'] || 'seopress-pro-page' === $_GET['page'] || 'seopress-network-option' === $_GET['page']) {
        wp_enqueue_script('seopress-xml-ajax', plugins_url('assets/js/seopress-sitemap-ajax' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, true);

        $seopress_ajax_permalinks = [
            'seopress_nonce'            => wp_create_nonce('seopress_flush_permalinks_nonce'),
            'seopress_ajax_permalinks' 	=> admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-xml-ajax', 'seopressAjaxResetPermalinks', $seopress_ajax_permalinks);
    }

    if ('seopress-google-analytics' === $_GET['page']) {
        wp_enqueue_style('wp-color-picker');

        wp_enqueue_script('wp-color-picker-alpha', plugins_url('assets/js/wp-color-picker-alpha.min.js', __FILE__), ['wp-color-picker'], SEOPRESS_VERSION, true);
        $color_picker_strings = [
            'clear'            => __('Clear', 'wp-seopress'),
            'clearAriaLabel'   => __('Clear color', 'wp-seopress'),
            'defaultString'    => __('Default', 'wp-seopress'),
            'defaultAriaLabel' => __('Select default color', 'wp-seopress'),
            'pick'             => __('Select Color', 'wp-seopress'),
            'defaultLabel'     => __('Color value', 'wp-seopress'),
        ];
        wp_localize_script('wp-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings);
    }

    if ('seopress-social' === $_GET['page']) {
        wp_enqueue_script('seopress-media-uploader-js', plugins_url('assets/js/seopress-media-uploader' . $prefix . '.js', __FILE__), ['jquery'], SEOPRESS_VERSION, false);
        wp_enqueue_media();
    }

    //Instant Indexing
    if ('seopress-instant-indexing' === $_GET['page']) {
        $seopress_instant_indexing_post = [
            'seopress_nonce'               => wp_create_nonce('seopress_instant_indexing_post_nonce'),
            'seopress_instant_indexing_post' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-admin-tabs-js', 'seopressAjaxInstantIndexingPost', $seopress_instant_indexing_post);

        $seopress_instant_indexing_generate_api_key = [
            'seopress_nonce'               => wp_create_nonce('seopress_instant_indexing_generate_api_key_nonce'),
            'seopress_instant_indexing_generate_api_key' => admin_url('admin-ajax.php'),
        ];
        wp_localize_script('seopress-admin-tabs-js', 'seopressAjaxInstantIndexingApiKey', $seopress_instant_indexing_generate_api_key);
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
    if (is_user_logged_in() && function_exists('seopress_advanced_appearance_adminbar_option') && '1' != seopress_advanced_appearance_adminbar_option()) {
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
        'seopress-license'                  => true,
        'seopress-insights'                 => true,
        'seopress-insights-rankings'        => true,
        'seopress-insights-backlinks'       => true,
        'seopress-insights-trends'          => true,
    ];
    if (isset($_pages[$_GET['page']])) {
        $classes .= ' seopress-styles ';
    }
    if (isset($_pages[$_GET['page']]) && 'seopress_csv_importer' === $_GET['page']) {
        $classes .= ' seopress-setup ';
    }

    return $classes;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//WP compatibility
///////////////////////////////////////////////////////////////////////////////////////////////////
/*
 * Remove WP default meta robots (added in WP 5.7)
 *
 * @since 4.4.0.7
 */
remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

/*
 * Remove WC default meta robots (added in WP 5.7)
 *
 * @since 4.6
 * @todo use wp_robots API
 */
function seopress_robots_wc_pages($robots) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        if (function_exists('wc_get_page_id')) {
            if (is_page(wc_get_page_id('cart')) || is_page(wc_get_page_id('checkout')) || is_page(wc_get_page_id('myaccount'))) {
                if ('0' === get_option('blog_public')) {
                    return $robots;
                } else {
                    unset($robots);
                    $robots = [];

                    return $robots;
                }
            }
        }
    }

    return $robots;
}
add_filter('wp_robots', 'seopress_robots_wc_pages', 20);

///////////////////////////////////////////////////////////////////////////////////////////////////
//3rd plugins compatibility
///////////////////////////////////////////////////////////////////////////////////////////////////
//Jetpack
function seopress_compatibility_jetpack() {
    if (function_exists('is_plugin_active')) {
        if (is_plugin_active('jetpack/jetpack.php') && ! is_admin()) {
            add_filter('jetpack_enable_open_graph', '__return_false');
            add_filter('jetpack_disable_seo_tools', '__return_true');
        }
    }
}
add_action('wp_head', 'seopress_compatibility_jetpack', 0);

/**
 * Remove default WC meta robots.
 *
 * @since 3.8.1
 */
function seopress_compatibility_woocommerce() {
    if (function_exists('is_plugin_active')) {
        if (is_plugin_active('woocommerce/woocommerce.php') && ! is_admin()) {
            remove_action('wp_head', 'wc_page_noindex');
        }
    }
}
add_action('wp_head', 'seopress_compatibility_woocommerce', 0);

/**
 * Remove WPML home url filter.
 *
 * @since 3.8.6
 *
 * @param mixed $home_url
 * @param mixed $url
 * @param mixed $path
 * @param mixed $orig_scheme
 * @param mixed $blog_id
 */
function seopress_remove_wpml_home_url_filter($home_url, $url, $path, $orig_scheme, $blog_id) {
    return $url;
}

/*
 * Remove third-parties metaboxes on our CPT
 * @author Benjamin Denis
 * @since 4.2
 */
add_action('do_meta_boxes', 'seopress_remove_metaboxes', 10);
function seopress_remove_metaboxes() {
    //Oxygen Builder
    remove_meta_box('ct_views_cpt', 'seopress_404', 'normal');
    remove_meta_box('ct_views_cpt', 'seopress_schemas', 'normal');
    remove_meta_box('ct_views_cpt', 'seopress_bot', 'normal');
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Shortcut settings page
///////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('plugin_action_links', 'seopress_plugin_action_links', 10, 2);

function seopress_plugin_action_links($links, $file) {
    static $this_plugin;

    if ( ! $this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . admin_url('admin.php?page=seopress-option') . '">' . __('Settings', 'wp-seopress') . '</a>';
        $website_link  = '<a href="https://www.seopress.org/support/" target="_blank">' . __('Docs', 'wp-seopress') . '</a>';
        $wizard_link   = '<a href="' . admin_url('admin.php?page=seopress-setup') . '">' . __('Configuration Wizard', 'wp-seopress') . '</a>';
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

        if (function_exists('seopress_get_toggle_white_label_option') && '1' == seopress_get_toggle_white_label_option() && function_exists('seopress_white_label_help_links_option') && '1' === seopress_white_label_help_links_option()) {
            array_unshift($links, $settings_link, $wizard_link);
        } else {
            array_unshift($links, $settings_link, $wizard_link, $website_link);
        }
    }

    return $links;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Test if a feature is ON
///////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Global check.
 *
 * @since 3.8
 *
 * @param string $feature
 *
 * @return string 1 if true
 *
 * @author Benjamin
 */
function seopress_get_toggle_option($feature) {
    $seopress_get_toggle_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_option)) {
        foreach ($seopress_get_toggle_option as $key => $seopress_get_toggle_value) {
            $options[$key] = $seopress_get_toggle_value;
            if (isset($seopress_get_toggle_option['toggle-' . $feature])) {
                return $seopress_get_toggle_option['toggle-' . $feature];
            }
        }
    }
}

// Is Titles enable?
/**
 * @since 3.8
 */
function seopress_get_toggle_titles_option() {
    $seopress_get_toggle_titles_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_titles_option)) {
        foreach ($seopress_get_toggle_titles_option as $key => $seopress_get_toggle_titles_value) {
            $options[$key] = $seopress_get_toggle_titles_value;
        }
        if (isset($seopress_get_toggle_titles_option['toggle-titles'])) {
            return $seopress_get_toggle_titles_option['toggle-titles'];
        }
    }
}
// Is Social enable?
/**
 * @since 3.8
 */
function seopress_get_toggle_social_option() {
    $seopress_get_toggle_social_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_social_option)) {
        foreach ($seopress_get_toggle_social_option as $key => $seopress_get_toggle_social_value) {
            $options[$key] = $seopress_get_toggle_social_value;
        }
        if (isset($seopress_get_toggle_social_option['toggle-social'])) {
            return $seopress_get_toggle_social_option['toggle-social'];
        }
    }
}
// Is XML Sitemap enable?
/**
 * @since 3.8
 */
function seopress_get_toggle_xml_sitemap_option() {
    $seopress_get_toggle_xml_sitemap_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_xml_sitemap_option)) {
        foreach ($seopress_get_toggle_xml_sitemap_option as $key => $seopress_get_toggle_xml_sitemap_value) {
            $options[$key] = $seopress_get_toggle_xml_sitemap_value;
        }
        if (isset($seopress_get_toggle_xml_sitemap_option['toggle-xml-sitemap'])) {
            return $seopress_get_toggle_xml_sitemap_option['toggle-xml-sitemap'];
        }
    }
}
// Is Google Analytics enable?
/**
 * @since 3.8
 */
function seopress_get_toggle_google_analytics_option() {
    $seopress_get_toggle_google_analytics_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_google_analytics_option)) {
        foreach ($seopress_get_toggle_google_analytics_option as $key => $seopress_get_toggle_google_analytics_value) {
            $options[$key] = $seopress_get_toggle_google_analytics_value;
        }
        if (isset($seopress_get_toggle_google_analytics_option['toggle-google-analytics'])) {
            return $seopress_get_toggle_google_analytics_option['toggle-google-analytics'];
        }
    }
}
// Is Advanced enable?
/**
 * @since 3.8
 */
function seopress_get_toggle_advanced_option() {
    $seopress_get_toggle_advanced_option = get_option('seopress_toggle');
    if ( ! empty($seopress_get_toggle_advanced_option)) {
        foreach ($seopress_get_toggle_advanced_option as $key => $seopress_get_toggle_advanced_value) {
            $options[$key] = $seopress_get_toggle_advanced_value;
        }
        if (isset($seopress_get_toggle_advanced_option['toggle-advanced'])) {
            return $seopress_get_toggle_advanced_option['toggle-advanced'];
        }
    }
}

/**
 * @deprecated 4.3.0
 */
function seopress_xml_sitemap_general_enable_option() {
    if ( ! function_exists('seopress_get_service')) {
        return '1';
    }

    return seopress_get_service('SitemapOption')->isEnabled();
}

/**
 * @deprecated 4.3.0
 */
function seopress_xml_sitemap_post_types_list_option() {
    if ( ! function_exists('seopress_get_service')) {
        return '';
    }

    return seopress_get_service('SitemapOption')->getPostTypesList();
}

/**
 * @deprecated 4.3.0
 */
function seopress_xml_sitemap_taxonomies_list_option() {
    if ( ! function_exists('seopress_get_service')) {
        return '';
    }

    return seopress_get_service('SitemapOption')->getTaxonomiesList();
}

/**
 * @deprecated 4.3.0
 */
function seopress_xml_sitemap_author_enable_option() {
    if ( ! function_exists('seopress_get_service')) {
        return '';
    }

    return seopress_get_service('SitemapOption')->authorIsEnable();
}

/**
 * @deprecated 4.3.0
 */
function seopress_xml_sitemap_img_enable_option() {
    if ( ! function_exists('seopress_get_service')) {
        return '';
    }

    return seopress_get_service('SitemapOption')->imageIsEnable();
}
/*
 * Rewrite Rules for XML Sitemaps
 */
if ('1' == seopress_xml_sitemap_general_enable_option() && '1' == seopress_get_toggle_option('xml-sitemap')) {
    function seopress_sitemaps_headers() {
        if ( ! function_exists('seopress_get_service')) {
            return;
        }

        seopress_get_service('SitemapHeaders')->printHeaders();
    }

    //WPML compatibility
    if (defined('ICL_SITEPRESS_VERSION')) {
        add_filter('request', 'seopress_wpml_block_secondary_languages');
    }

    function seopress_wpml_block_secondary_languages($q) {
        $current_language = apply_filters('wpml_current_language', false);
        $default_language = apply_filters('wpml_default_language', false);
        if ($current_language !== $default_language) {
            unset($q['seopress_sitemap']);
            unset($q['seopress_cpt']);
            unset($q['seopress_paged']);
            unset($q['seopress_author']);
            unset($q['seopress_sitemap_xsl']);
        }

        return $q;
    }
}

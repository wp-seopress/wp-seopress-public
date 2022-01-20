<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use SEOPress\Helpers\PagesAdmin;

class seopress_options
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private $options;

    /**
     * Start up.
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . '/admin-dyn-variables-helper.php'; //Dynamic variables

        add_action('admin_menu', [$this, 'add_plugin_page'], 10);
        add_action('admin_init', [$this, 'set_default_values'], 10);
        add_action('admin_init', [$this, 'page_init']);
        add_action('admin_init', [$this, 'seopress_feature_save'], 30);
        add_action('admin_init', [$this, 'seopress_feature_title'], 20);
        add_action('admin_init', [$this, 'load_sections'], 30);
        add_action('admin_init', [$this, 'load_callbacks'], 40);
    }

    public function seopress_feature_save()
    {
        $html = '';
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            $html .= '<div id="seopress-notice-save" class="sp-components-snackbar-list">';
        } else {
            $html .= '<div id="seopress-notice-save" class="sp-components-snackbar-list" style="display: none">';
        }
        $html .= '<div class="sp-components-snackbar">
                <div class="sp-components-snackbar__content">
                    <span class="dashicons dashicons-yes"></span>
                    ' . __('Your settings have been saved.', 'wp-seopress') . '
                </div>
            </div>
        </div>';

        return $html;
    }

    public function seopress_feature_title($feature)
    {
        global $title;

        $html = '<h1>' . $title;

        if (null !== $feature) {
            if ('1' == seopress_get_toggle_option($feature)) {
                $toggle = '"1"';
            } else {
                $toggle = '"0"';
            }

            $html .= '<input type="checkbox" name="toggle-' . $feature . '" id="toggle-' . $feature . '" class="toggle" data-toggle=' . $toggle . '>';
            $html .= '<label for="toggle-' . $feature . '"></label>';

            $html .= $this->seopress_feature_save();

            if ('1' == seopress_get_toggle_option($feature)) {
                $html .= '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
                $html .= '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
            } else {
                $html .= '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
                $html .= '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
            }
        }

        $html .= '<hr role="presentation"></h1>';

        return $html;
    }

    /**
     * Add options page.
     */
    public function add_plugin_page()
    {
        if (has_filter('seopress_seo_admin_menu')) {
            $sp_seo_admin_menu['icon'] = '';
            $sp_seo_admin_menu['icon'] = apply_filters('seopress_seo_admin_menu', $sp_seo_admin_menu['icon']);
        } else {
            $sp_seo_admin_menu['icon'] = 'dashicons-admin-seopress';
        }

        $sp_seo_admin_menu['title'] = __('SEO', 'wp-seopress');
        if (has_filter('seopress_seo_admin_menu_title')) {
            $sp_seo_admin_menu['title'] = apply_filters('seopress_seo_admin_menu_title', $sp_seo_admin_menu['title']);
        }

        add_menu_page(__('SEOPress Option Page', 'wp-seopress'), $sp_seo_admin_menu['title'], seopress_capability('manage_options', 'menu'), 'seopress-option', [$this, 'create_admin_page'], $sp_seo_admin_menu['icon'], 90);
        add_submenu_page('seopress-option', __('Dashboard', 'wp-seopress'), __('Dashboard', 'wp-seopress'), seopress_capability('manage_options', 'menu'), 'seopress-option', [$this, 'create_admin_page']);
        $seopress_titles_help_tab           = add_submenu_page('seopress-option', __('Titles & Metas', 'wp-seopress'), __('Titles & Metas', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::TITLE_METAS), 'seopress-titles', [$this, 'seopress_titles_page']);
        $seopress_xml_sitemaps_help_tab     = add_submenu_page('seopress-option', __('XML - HTML Sitemap', 'wp-seopress'), __('XML - HTML Sitemap', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::XML_HTML_SITEMAP), 'seopress-xml-sitemap', [$this, 'seopress_xml_sitemap_page']);
        $seopress_social_networks_help_tab  = add_submenu_page('seopress-option', __('Social Networks', 'wp-seopress'), __('Social Networks', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::SOCIAL_NETWORKS), 'seopress-social', [$this, 'seopress_social_page']);
        $seopress_google_analytics_help_tab = add_submenu_page('seopress-option', __('Analytics', 'wp-seopress'), __('Analytics', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::ANALYTICS), 'seopress-google-analytics', [$this, 'seopress_google_analytics_page']);
        add_submenu_page('seopress-option', __('Instant Indexing', 'wp-seopress'), __('Instant Indexing', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::INSTANT_INDEXING), 'seopress-instant-indexing', [$this, 'seopress_instant_indexing_page']);
        add_submenu_page('seopress-option', __('Advanced', 'wp-seopress'), __('Advanced', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::ADVANCED), 'seopress-advanced', [$this, 'seopress_advanced_page']);
        add_submenu_page('seopress-option', __('Tools', 'wp-seopress'), __('Tools', 'wp-seopress'), seopress_capability('manage_options', PagesAdmin::TOOLS), 'seopress-import-export', [$this, 'seopress_import_export_page']);

        if (function_exists('seopress_get_toggle_white_label_option')) {
            $white_label_toggle = seopress_get_toggle_white_label_option();
            if ('1' === $white_label_toggle) {
                if (function_exists('seopress_white_label_help_links_option') && '1' === seopress_white_label_help_links_option()) {
                    return;
                }
            }
        }

        //Help tabs
        require_once dirname(__FILE__) . '/help-tabs/Titles.php';
        require_once dirname(__FILE__) . '/help-tabs/Sitemaps.php';
        require_once dirname(__FILE__) . '/help-tabs/Social.php';
        require_once dirname(__FILE__) . '/help-tabs/Analytics.php';
    }

    //Admin Pages
    public function seopress_titles_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Titles.php';
    }

    public function seopress_xml_sitemap_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Sitemaps.php';
    }

    public function seopress_social_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Social.php';
    }

    public function seopress_google_analytics_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Analytics.php';
    }

    public function seopress_advanced_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Advanced.php';
    }

    public function seopress_import_export_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Tools.php';
    }

    public function seopress_instant_indexing_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/InstantIndexing.php';
    }

    public function create_admin_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/Main.php';
    }

    public function set_default_values()
    {
        $seopress_instant_indexing_option_name = get_option('seopress_instant_indexing_option_name');

        //Init if option doesn't exist
        if (false === $seopress_instant_indexing_option_name) {
            if ('1' == seopress_get_toggle_option('instant-indexing')) {
                seopress_instant_indexing_generate_api_key_fn();
            }
        }
    }

    public function page_init()
    {

        register_setting(
            'seopress_option_group', // Option group
            'seopress_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_titles_option_group', // Option group
            'seopress_titles_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_xml_sitemap_option_group', // Option group
            'seopress_xml_sitemap_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_social_option_group', // Option group
            'seopress_social_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_google_analytics_option_group', // Option group
            'seopress_google_analytics_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_advanced_option_group', // Option group
            'seopress_advanced_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_tools_option_group', // Option group
            'seopress_tools_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_import_export_option_group', // Option group
            'seopress_import_export_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        register_setting(
            'seopress_instant_indexing_option_group', // Option group
            'seopress_instant_indexing_option_name', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        require_once dirname(__FILE__) . '/settings/Titles.php';
        require_once dirname(__FILE__) . '/settings/Sitemaps.php';
        require_once dirname(__FILE__) . '/settings/Social.php';
        require_once dirname(__FILE__) . '/settings/Analytics.php';
        require_once dirname(__FILE__) . '/settings/ImageSEO.php';
        require_once dirname(__FILE__) . '/settings/Advanced.php';
        require_once dirname(__FILE__) . '/settings/InstantIndexing.php';
    }

    public function sanitize($input)
    {

        require_once dirname(__FILE__) . '/sanitize/Sanitize.php';

        if(isset($_POST['option_page']) && $_POST['option_page'] === 'seopress_advanced_option_group'){
            if(!isset($input['seopress_advanced_appearance_universal_metabox_disable'])){
                $input['seopress_advanced_appearance_universal_metabox_disable'] = '';
            }
        }

        return seopress_sanitize_options_fields($input);
    }

    public function load_sections()
    {
        require_once dirname(__FILE__) . '/sections/Titles.php';
        require_once dirname(__FILE__) . '/sections/Sitemaps.php';
        require_once dirname(__FILE__) . '/sections/Social.php';
        require_once dirname(__FILE__) . '/sections/Analytics.php';
        require_once dirname(__FILE__) . '/sections/ImageSEO.php';
        require_once dirname(__FILE__) . '/sections/Advanced.php';
        require_once dirname(__FILE__) . '/sections/InstantIndexing.php';
    }

    public function load_callbacks()
    {
        require_once dirname(__FILE__) . '/callbacks/Titles.php';
        require_once dirname(__FILE__) . '/callbacks/Sitemaps.php';
        require_once dirname(__FILE__) . '/callbacks/Social.php';
        require_once dirname(__FILE__) . '/callbacks/Analytics.php';
        require_once dirname(__FILE__) . '/callbacks/ImageSEO.php';
        require_once dirname(__FILE__) . '/callbacks/Advanced.php';
        require_once dirname(__FILE__) . '/callbacks/InstantIndexing.php';
    }
}

if (is_admin()) {
    $my_settings_page = new seopress_options();
}

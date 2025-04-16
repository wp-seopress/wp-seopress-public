<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use SEOPress\Helpers\PagesAdmin;

class seopress_options
{
	private $options;

	public function __construct()
	{
		$this->load_dependencies();
		$this->initialize_hooks();
	}

	private function load_dependencies()
	{
		global $pagenow, $typenow;
		
		require_once plugin_dir_path(__FILE__) . 'admin-dyn-variables-helper.php';
		require_once plugin_dir_path(__FILE__) . '/sanitize/Sanitize.php';

		if (wp_doing_ajax() || (isset($_GET['page']) && $_GET['page'] === 'seopress-option')) {
			require_once plugin_dir_path(__FILE__) . '/ajax/Dashboard.php';
		}

		if (wp_doing_ajax() || ('post-new.php' == $pagenow || 'post.php' == $pagenow) && ('seopress_schemas' != $typenow) || ('term.php' == $pagenow || 'edit-tags.php' == $pagenow)) {
			require_once plugin_dir_path(__FILE__) . '/ajax/ContentAnalysis.php';
		}

		if (wp_doing_ajax() || (isset($_GET['page']) && ($_GET['page'] === 'seopress-import-export' || $_GET['page'] === 'seopress-setup'))) {
			$ajax_migrate_files = [
				'/migrate/MigrationTools.php',
				'/ajax/migrate/smart-crawl.php',
				'/ajax/migrate/slim-seo.php',
				'/ajax/migrate/premium-seo-pack.php',
				'/ajax/migrate/wp-meta-seo.php',
				'/ajax/migrate/seo-ultimate.php',
				'/ajax/migrate/squirrly.php',
				'/ajax/migrate/seo-framework.php',
				'/ajax/migrate/yoast.php'
			];

			foreach ($ajax_migrate_files as $file) {
				require_once plugin_dir_path(__FILE__) . $file;
			}
		}	
	}

	private function initialize_hooks()
	{
		add_action('admin_menu', [$this, 'init_wizard'], 5);
		add_action('admin_menu', [$this, 'setup_admin_pages'], 10);
		add_action('admin_init', [$this, 'page_init'], 10);
		add_action('admin_init', [$this, 'feature_save'], 30);
		add_action('admin_init', [$this, 'feature_title'], 20);
		add_action('admin_init', [$this, 'load_sections'], 30);
		add_action('admin_init', [$this, 'load_callbacks'], 40);
		add_action('admin_init', [$this, 'pre_save_options'], 50);
	}

	public function init_wizard()
	{
		$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
    
        if ('seopress-setup' === $current_page) {
            ob_start();
            require_once plugin_dir_path(__FILE__) . 'wizard/admin-wizard.php';
        }
	}

	public function feature_save()
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

	public function feature_title($feature)
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

			$html .= $this->feature_save();

			if ('1' == seopress_get_toggle_option($feature)) {
				$html .= '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
				$html .= '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
			} else {
				$html .= '<span id="titles-state-default" class="feature-state"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to enable this feature', 'wp-seopress') . '</span>';
				$html .= '<span id="titles-state" class="feature-state feature-state-off"><span class="dashicons dashicons-arrow-left-alt"></span>' . __('Click to disable this feature', 'wp-seopress') . '</span>';
			}
		}

		$html .= '</h1>';

		return $html;
	}

	/**
	 * Add options page.
	 */
	public function setup_admin_pages() {
		$menu_icon = apply_filters(
			'seopress_seo_admin_menu',
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJ1dWlkLTRmNmE4YTQxLTE4ZTMtNGY3Ny1iNWE5LTRiMWIzOGFhMmRjOSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgODk5LjY1NSA0OTQuMzA5NCI+PHBhdGggaWQ9InV1aWQtYTE1NWMxY2EtZDg2OC00NjUzLTg0NzctOGRkODcyNDBhNzY1IiBkPSJNMzI3LjM4NDksNDM1LjEyOGwtMjk5Ljk5OTktLjI0OTdjLTE2LjI3MzUsMS4xOTM3LTI4LjQ5ODEsMTUuMzUzOC0yNy4zMDQ0LDMxLjYyNzMsMS4wNzE5LDE0LjYxMjgsMTIuNjkxNiwyNi4yMzI1LDI3LjMwNDQsMjcuMzA0NGwyOTkuOTk5OSwuMjQ5N2MxNi4yNzM1LTEuMTkzNywyOC40OTgxLTE1LjM1MzgsMjcuMzA0NC0zMS42MjczLTEuMDcxOC0xNC42MTI4LTEyLjY5MTYtMjYuMjMyNS0yNy4zMDQ0LTI3LjMwNDRaIiBzdHlsZT0iZmlsbDojZmZmOyIvPjxwYXRoIGlkPSJ1dWlkLWUzMGJhNGM2LTQ3NjktNDY2Yi1hMDNhLWU2NDRjNTE5OGU1NiIgZD0iTTI3LjM4NDksNTguOTMxN2wyOTkuOTk5OSwuMjQ5N2MxNi4yNzM1LTEuMTkzNywyOC40OTgxLTE1LjM1MzcsMjcuMzA0NC0zMS42MjczLTEuMDcxOC0xNC42MTI4LTEyLjY5MTYtMjYuMjMyNS0yNy4zMDQ0LTI3LjMwNDRMMjcuMzg0OSwwQzExLjExMTQsMS4xOTM3LTEuMTEzMiwxNS4zNTM3LC4wODA1LDMxLjYyNzNjMS4wNzE5LDE0LjYxMjgsMTIuNjkxNiwyNi4yMzI1LDI3LjMwNDQsMjcuMzA0NFoiIHN0eWxlPSJmaWxsOiNmZmY7Ii8+PHBhdGggaWQ9InV1aWQtMmJiZDUyZDYtYWVjMS00Njg5LTlkNGMtMjNjMzVkNGYyMmI4IiBkPSJNNjUyLjQ4NSwuMjg0OWMtMTI0LjkzODgsLjA2NC0yMzAuMTU1NCw5My40MTMyLTI0NS4xMDAxLDIxNy40NTVIMjcuMzg0OWMtMTYuMjczNSwxLjE5MzctMjguNDk4MSwxNS4zNTM3LTI3LjMwNDQsMzEuNjI3MiwxLjA3MTksMTQuNjEyOCwxMi42OTE2LDI2LjIzMjUsMjcuMzA0NCwyNy4zMDQ0SDQwNy4zODQ5YzE2LjIyOTgsMTM1LjQ0NTQsMTM5LjE4NywyMzIuMDg4OCwyNzQuNjMyMywyMTUuODU4OSwxMzUuNDQ1NS0xNi4yMjk4LDIzMi4wODg4LTEzOS4xODY5LDIxNS44NTg5LTI3NC42MzI0Qzg4Mi45OTIxLDkzLjY4MzQsNzc3LjU4ODQsLjIxMTIsNjUyLjQ4NSwuMjg0OVptMCw0MzMuNDIxN2MtMTAyLjk3NTQsMC0xODYuNDUzMy04My40NzgtMTg2LjQ1MzMtMTg2LjQ1MzMsMC0xMDIuOTc1Myw4My40NzgxLTE4Ni40NTMzLDE4Ni40NTMzLTE4Ni40NTMzLDEwMi45NzU0LDAsMTg2LjQ1MzMsODMuNDc4LDE4Ni40NTMzLDE4Ni40NTMzLC4wNTI0LDEwMi45NzUzLTgzLjM4MywxODYuNDk1OS0xODYuMzU4MywxODYuNTQ4My0uMDMxNiwwLS4wNjM0LDAtLjA5NTEsMHYtLjA5NVoiIHN0eWxlPSJmaWxsOiNmZmY7Ii8+PC9zdmc+'
		);
		
		$menu_title = apply_filters('seopress_seo_admin_menu_title', __('SEO', 'wp-seopress'));
		
		// SEO Dashboard page
		add_menu_page(
			__('SEOPress Option Page', 'wp-seopress'),
			$menu_title,
			seopress_capability('manage_options', 'menu'),
			'seopress-option',
			[$this, 'create_admin_page'],
			$menu_icon,
			90
		);
		
		// Add submenus
		$this->register_submenus();
		
		// Handle White Label Toggle
		$this->handle_white_label();
	}
	
	private function register_submenus() {
		$submenus = [
			[__('Dashboard', 'wp-seopress'), 'menu', 'seopress-option', 'create_admin_page'],
			[__('Titles & Metas', 'wp-seopress'), PagesAdmin::TITLE_METAS, 'seopress-titles', 'seopress_titles_page'],
			[__('XML - HTML Sitemap', 'wp-seopress'), PagesAdmin::XML_HTML_SITEMAP, 'seopress-xml-sitemap', 'seopress_xml_sitemap_page'],
			[__('Social Networks', 'wp-seopress'), PagesAdmin::SOCIAL_NETWORKS, 'seopress-social', 'seopress_social_page'],
			[__('Analytics', 'wp-seopress'), PagesAdmin::ANALYTICS, 'seopress-google-analytics', 'seopress_google_analytics_page'],
			[__('Instant Indexing', 'wp-seopress'), PagesAdmin::INSTANT_INDEXING, 'seopress-instant-indexing', 'seopress_instant_indexing_page'],
			[__('Advanced', 'wp-seopress'), PagesAdmin::ADVANCED, 'seopress-advanced', 'seopress_advanced_page'],
			[__('Tools', 'wp-seopress'), PagesAdmin::TOOLS, 'seopress-import-export', 'seopress_import_export_page'],
		];
	
		foreach ($submenus as $submenu) {
			add_submenu_page(
				'seopress-option',
				$submenu[0],
				$submenu[0],
				seopress_capability('manage_options', $submenu[1]),
				$submenu[2],
				[$this, $submenu[3]]
			);
		}
	}
	
	private function handle_white_label() {
		if (method_exists(seopress_get_service('ToggleOption'), 'getToggleWhiteLabel') &&
			'1' === seopress_get_service('ToggleOption')->getToggleWhiteLabel() &&
			function_exists('seopress_pro_get_service') &&
			method_exists('seopress_pro_get_service', 'getWhiteLabelHelpLinks') &&
			'1' === seopress_pro_get_service('OptionPro')->getWhiteLabelHelpLinks()) {
			return;
		}
	}

	public function create_admin_page()
	{
		require_once plugin_dir_path(__FILE__) . '/admin-pages/Main.php';
	}

	public function seopress_titles_page()
	{
		$this->load_admin_page('Titles.php');
	}

	public function seopress_xml_sitemap_page()
	{
		$this->load_admin_page('Sitemaps.php');
	}

	public function seopress_social_page()
	{
		$this->load_admin_page('Social.php');
	}

	public function seopress_google_analytics_page()
	{
		$this->load_admin_page('Analytics.php');
	}

	public function seopress_instant_indexing_page()
	{
		$this->load_admin_page('InstantIndexing.php');
	}

	public function seopress_advanced_page()
	{
		$this->load_admin_page('Advanced.php');
	}

	public function seopress_import_export_page()
	{
		$this->load_admin_page('Tools.php');
	}

	private function load_admin_page($file_name)
	{
		require_once plugin_dir_path(__FILE__) . "/admin-pages/{$file_name}";
	}

	public function page_init() {
		// Array of settings to register
		$settings = [
			['seopress_option_group', 'seopress_option_name'],
			['seopress_titles_option_group', 'seopress_titles_option_name'],
			['seopress_xml_sitemap_option_group', 'seopress_xml_sitemap_option_name'],
			['seopress_social_option_group', 'seopress_social_option_name'],
			['seopress_google_analytics_option_group', 'seopress_google_analytics_option_name'],
			['seopress_advanced_option_group', 'seopress_advanced_option_name'],
			['seopress_tools_option_group', 'seopress_tools_option_name'],
			['seopress_import_export_option_group', 'seopress_import_export_option_name'],
			['seopress_instant_indexing_option_group', 'seopress_instant_indexing_option_name'],
		];
	
		// Register settings dynamically
		foreach ($settings as [$group, $name]) {
			register_setting($group, $name, [$this, 'sanitize']);
		}
	
		// Array of files to include
		$setting_files = [
			'Titles.php',
			'Sitemaps.php',
			'Social.php',
			'Analytics.php',
			'ImageSEO.php',
			'Advanced.php',
			'InstantIndexing.php',
		];
	
		// Include files dynamically
		$settings_dir = plugin_dir_path(__FILE__) . 'settings/';
		foreach ($setting_files as $file) {
			require_once $settings_dir . $file;
		}
	}

	public function sanitize($input)
	{
		if(isset($_POST['option_page']) && $_POST['option_page'] === 'seopress_advanced_option_group'){
			if(!isset($input['seopress_advanced_appearance_universal_metabox_disable'])){
				$input['seopress_advanced_appearance_universal_metabox_disable'] = '';
			}
		}

		return seopress_sanitize_options_fields($input);
	}

	public function load_sections()
	{
		require_once plugin_dir_path(__FILE__) . '/sections/Titles.php';
		require_once plugin_dir_path(__FILE__) . '/sections/Sitemaps.php';
		require_once plugin_dir_path(__FILE__) . '/sections/Social.php';
		require_once plugin_dir_path(__FILE__) . '/sections/Analytics.php';
		require_once plugin_dir_path(__FILE__) . '/sections/ImageSEO.php';
		require_once plugin_dir_path(__FILE__) . '/sections/Advanced.php';
		require_once plugin_dir_path(__FILE__) . '/sections/InstantIndexing.php';
	}

	public function load_callbacks()
	{
		require_once plugin_dir_path(__FILE__) . '/callbacks/Titles.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/Sitemaps.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/Social.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/Analytics.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/ImageSEO.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/Advanced.php';
		require_once plugin_dir_path(__FILE__) . '/callbacks/InstantIndexing.php';
	}

	public function pre_save_options()
	{
		add_filter( 'pre_update_option_seopress_instant_indexing_option_name', [$this, 'pre_seopress_instant_indexing_option_name'], 10, 2 );
		add_filter( 'pre_update_option_seopress_xml_sitemap_option_name', [$this, 'pre_seopress_xml_sitemap_option_name'], 10, 2 );
	}

	public function pre_seopress_instant_indexing_option_name( $new_value, $old_value )
	{
		//If we are saving data from SEO, PRO, Google Search Console tab, we have to save all Indexing options!
		if (!array_key_exists('seopress_instant_indexing_bing_api_key', $new_value)) {
			$options = get_option('seopress_instant_indexing_option_name');
			$options['seopress_instant_indexing_google_api_key'] = $new_value['seopress_instant_indexing_google_api_key'];
			return $options;
		}
		return $new_value;
	}

	// Automatically flush permalinks after saving XML sitemaps global settings
	public function pre_seopress_xml_sitemap_option_name($new_value, $old_value) {
		flush_rewrite_rules(false);
		return $new_value;
	}
}

new seopress_options();

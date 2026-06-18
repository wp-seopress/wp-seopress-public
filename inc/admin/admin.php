<?php
/**
 * Admin
 *
 * @package SEOPress
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

use SEOPress\Helpers\PagesAdmin;

/**
 * SEOPress options
 */
class SEOPressOptions {

	/**
	 * Options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->initialize_hooks();
	}

	/**
	 * Load dependencies
	 */
	private function load_dependencies() {
		global $pagenow, $typenow;

		require_once plugin_dir_path( __FILE__ ) . 'admin-dyn-variables-helper.php';
		require_once plugin_dir_path( __FILE__ ) . '/sanitize/Sanitize.php';

		if ( wp_doing_ajax() || ( isset( $_GET['page'] ) && 'seopress-option' === $_GET['page'] ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/ajax/Dashboard.php';
		}

		if (
			wp_doing_ajax()
			|| ( ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) && ( 'seopress_schemas' !== $typenow ) )
			|| ( 'term.php' === $pagenow || 'edit-tags.php' === $pagenow )
		) {
			require_once plugin_dir_path( __FILE__ ) . '/ajax/ContentAnalysis.php';
		}

		if ( wp_doing_ajax() || ( isset( $_GET['page'] ) && ( 'seopress-import-export' === $_GET['page'] || 'seopress-setup' === $_GET['page'] ) ) ) {
			$ajax_migrate_files = array(
				'/migrate/MigrationTools.php',
				'/ajax/migrate/smart-crawl.php',
				'/ajax/migrate/slim-seo.php',
				'/ajax/migrate/premium-seo-pack.php',
				'/ajax/migrate/wp-meta-seo.php',
				'/ajax/migrate/seo-ultimate.php',
				'/ajax/migrate/squirrly.php',
				'/ajax/migrate/seo-framework.php',
				'/ajax/migrate/yoast.php',
			);

			foreach ( $ajax_migrate_files as $file ) {
				require_once plugin_dir_path( __FILE__ ) . $file;
			}
		}
	}

	/**
	 * Initialize hooks
	 */
	private function initialize_hooks() {
		add_action( 'admin_menu', array( $this, 'init_wizard' ), 5 );
		add_action( 'admin_menu', array( $this, 'setup_admin_pages' ), 10 );
		add_action( 'admin_init', array( $this, 'page_init' ), 10 );
		add_action( 'admin_init', array( $this, 'feature_save' ), 30 );
		add_action( 'admin_init', array( $this, 'feature_title' ), 20 );
		// Option save hooks (pre_update_option, update_option) are now in
		// SEOPress\Actions\Options\OptionSaveHooks to work for both admin and REST.
	}

	/**
	 * Initialize wizard
	 */
	public function init_wizard() {
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( 'seopress-setup' === $current_page ) {
			ob_start();
			require_once plugin_dir_path( __FILE__ ) . 'wizard/admin-wizard.php';
		}
	}

	/**
	 * Feature save
	 */
	public function feature_save() {
		$html = '';
		if ( isset( $_GET['settings-updated'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) {
			$html .= '<div id="seopress-notice-save" class="sp-components-snackbar-list">';
		} else {
			$html .= '<div id="seopress-notice-save" class="sp-components-snackbar-list" style="display: none">';
		}
		$html .= '<div class="sp-components-snackbar">
				<div class="sp-components-snackbar__content">
					<span class="dashicons dashicons-yes"></span>
					' . __( 'Your settings have been saved.', 'wp-seopress' ) . '
				</div>
			</div>
		</div>';

		return $html;
	}

	/**
	 * Feature title
	 *
	 * @param string $feature Feature.
	 * @return string HTML
	 */
	public function feature_title( $feature ) {
		global $title;

		$html = '<h1>' . $title;

		// Always render toggle elements so SPA navigation can show/hide them.
		$hidden = ( null === $feature ) ? ' style="display:none"' : '';

		if ( null !== $feature ) {
			$toggle = ( '1' == seopress_get_toggle_option( $feature ) ) ? '"1"' : '"0"'; // phpcs:ignore -- TODO: null comparison check.
			$html  .= '<input type="checkbox" name="toggle-' . $feature . '" id="toggle-' . $feature . '" class="toggle" data-toggle=' . $toggle . $hidden . '>';
			$html  .= '<label for="toggle-' . $feature . '"' . $hidden . '></label>';
		} else {
			$html .= '<input type="checkbox" name="toggle-placeholder" id="toggle-placeholder" class="toggle" data-toggle="0"' . $hidden . '>';
			$html .= '<label for="toggle-placeholder"' . $hidden . '></label>';
		}

		$html .= $this->feature_save();

		if ( null !== $feature && '1' == seopress_get_toggle_option( $feature ) ) { // phpcs:ignore -- TODO: null comparison check.
			$html .= '<span id="titles-state-default" class="feature-state"' . $hidden . '><span class="dashicons dashicons-arrow-left-alt"></span>' . __( 'Click to disable this feature', 'wp-seopress' ) . '</span>';
			$html .= '<span id="titles-state" class="feature-state feature-state-off"' . $hidden . '><span class="dashicons dashicons-arrow-left-alt"></span>' . __( 'Click to enable this feature', 'wp-seopress' ) . '</span>';
		} else {
			$html .= '<span id="titles-state-default" class="feature-state"' . $hidden . '><span class="dashicons dashicons-arrow-left-alt"></span>' . __( 'Click to enable this feature', 'wp-seopress' ) . '</span>';
			$html .= '<span id="titles-state" class="feature-state feature-state-off"' . $hidden . '><span class="dashicons dashicons-arrow-left-alt"></span>' . __( 'Click to disable this feature', 'wp-seopress' ) . '</span>';
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
			'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSItMjQgLTI0IDI0MiAyNDIiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTEyMC42MjYgMTI5LjY4NEMxMjIuODMzIDEyOS43NjMgMTI0LjkyOSAxMzAuNjc1IDEyNi40OTEgMTMyLjIzN0MxMjguMDUzIDEzMy43OTkgMTI4Ljk2NSAxMzUuODk1IDEyOS4wNDQgMTM4LjEwMkMxMjkuMTIyIDE0MC4zMSAxMjguMzYyIDE0Mi40NjUgMTI2LjkxNSAxNDQuMTM0TDEyNi44NTUgMTQ0LjIwMkwxMjYuNzkyIDE0NC4yNjVMODEuOTg1MSAxODkuMDA4TDgxLjkyMDcgMTg5LjA3NEw4MS44NTEzIDE4OS4xMzNDODAuMTgxMSAxOTAuNTczIDc4LjAyODIgMTkxLjMyNyA3NS44MjUgMTkxLjI0NkM3My42MjE1IDE5MS4xNjQgNzEuNTI5OCAxOTAuMjUzIDY5Ljk3MDUgMTg4LjY5NEM2OC40MTExIDE4Ny4xMzUgNjcuNDk4OCAxODUuMDQ0IDY3LjQxNjcgMTgyLjg0QzY3LjMzNDcgMTgwLjYzNyA2OC4wODkgMTc4LjQ4NCA2OS41MjgxIDE3Ni44MTNMNjkuNTg5NiAxNzYuNzQzTDY5LjY1NiAxNzYuNjc2TDExNC40NjUgMTMxLjkzM0wxMTQuNTI3IDEzMS44NzFMMTE0LjU5NCAxMzEuODEzQzExNi4yNjMgMTMwLjM2NiAxMTguNDE4IDEyOS42MDUgMTIwLjYyNiAxMjkuNjg0WiIgZmlsbD0id2hpdGUiLz48cGF0aCBkPSJNNTUuNzI2NiA2NC43ODU2QzU3LjkzNDEgNjQuODY0MyA2MC4wMjk4IDY1Ljc3NjQgNjEuNTkxOCA2Ny4zMzg0QzYzLjE1MzcgNjguOTAwMyA2NC4wNjU5IDcwLjk5NjEgNjQuMTQ0NSA3My4yMDM2QzY0LjIyMzEgNzUuNDExMSA2My40NjI1IDc3LjU2NjggNjIuMDE1NiA3OS4yMzU4TDYxLjk1NyA3OS4zMDMyTDYxLjg5MjYgNzkuMzY2N0wxNy44MTg0IDEyMy4zNjdWMTIzLjQ4M0wxNi45NTYxIDEyNC4yM0MxNS4yODcgMTI1LjY3NyAxMy4xMzEzIDEyNi40MzcgMTAuOTIzOCAxMjYuMzU5QzguNzE2MzEgMTI2LjI4IDYuNjIwNTYgMTI1LjM2OCA1LjA1ODU5IDEyMy44MDZDMy40OTY2MyAxMjIuMjQ0IDIuNTg0NDkgMTIwLjE0OCAyLjUwNTg2IDExNy45NDFDMi40MjcyNiAxMTUuNzMzIDMuMTg3OTEgMTEzLjU3OCA0LjYzNDc3IDExMS45MDlMNC42OTMzNiAxMTEuODQxTDQuNzU2ODQgMTExLjc3OEw0OS41NjU0IDY3LjAzNDdMNDkuNjI3OSA2Ni45NzIyTDQ5LjY5NDMgNjYuOTE0NkM1MS4zNjM1IDY1LjQ2NzcgNTMuNTE5IDY0LjcwNyA1NS43MjY2IDY0Ljc4NTZaIiBmaWxsPSJ3aGl0ZSIvPjxwYXRoIGQ9Ik0xMzMuNDY2IDIuNTk3NjZDMTQ2LjgxMSAxLjc5NzIyIDE1OS45ODYgNS45MTc5OSAxNzAuNDk3IDE0LjE3OTdDMTk0LjIxOCAzMi44MjQ2IDE5OC4zMzMgNjcuMTY4NyAxNzkuNjg4IDkwLjg4OTZDMTYxLjUwNCAxMTQuMDI1IDEyOC4zODUgMTE4LjUxMSAxMDQuNzU0IDEwMS40Mkw0OS41ODIgMTU2LjU5NEw0OS41MTY2IDE1Ni42NTlMNDkuNDQ2MyAxNTYuNzJDNDcuNzc2IDE1OC4xNTkgNDUuNjIyNCAxNTguOTE1IDQzLjQxODkgMTU4LjgzM0M0MS4yMTU2IDE1OC43NTEgMzkuMTI0NiAxNTcuODM5IDM3LjU2NTQgMTU2LjI4QzM2LjAwNjEgMTU0LjcyMSAzNS4wOTM4IDE1Mi42MyAzNS4wMTE3IDE1MC40MjdDMzQuOTI5NyAxNDguMjI0IDM1LjY4NDIgMTQ2LjA3MSAzNy4xMjMgMTQ0LjRMMzcuMTgzNiAxNDQuMzI5TDM3LjI0OSAxNDQuMjY0TDkyLjQyNzcgODkuMDg2OUM4NS4wNzc5IDc4Ljg5NjQgODEuNDQ5MiA2Ni40NjIzIDgyLjIwMTIgNTMuODc1QzgyLjk5NzQgNDAuNTQ2MiA4OC42NDczIDI3Ljk3MDEgOTguMDg0IDE4LjUyMzRDMTA3LjUzMiA5LjA2NDQ2IDEyMC4xMjEgMy4zOTgyIDEzMy40NjYgMi41OTc2NlpNMTYyLjc2OSAzMS4wODk4QzE0OC4zNzYgMTYuNjk5MiAxMjUuMDQzIDE2LjY5NzEgMTEwLjY1MiAzMS4wODg5Qzk2LjI2MTYgNDUuNDgxNiA5Ni4yNTk1IDY4LjgxNTkgMTEwLjY1MSA4My4yMDYxQzEyNS4wNDQgOTcuNTk2NiAxNDguMzc2IDk3LjU5NjUgMTYyLjc2OSA4My4yMDYxTDE2My41ODIgODIuMzkxNkMxNzcuMTcyIDY3LjkzOTcgMTc2Ljg5NCA0NS4yMTM1IDE2Mi43NjkgMzEuMDg5OFoiIGZpbGw9IndoaXRlIi8+PC9zdmc+'
		);

		$menu_title = apply_filters( 'seopress_seo_admin_menu_title', __( 'SEO', 'wp-seopress' ) );

		// SEO Dashboard page.
		add_menu_page(
			__( 'SEOPress Option Page', 'wp-seopress' ),
			$menu_title,
			seopress_capability( 'manage_options', 'menu' ),
			'seopress-option',
			array( $this, 'create_admin_page' ),
			$menu_icon,
			90
		);

		// Add submenus.
		$this->register_submenus();

		// Handle White Label Toggle.
		$this->handle_white_label();
	}

	/**
	 * Register submenus
	 */
	private function register_submenus() {
		$submenus = array(
			array( __( 'Dashboard', 'wp-seopress' ), 'menu', 'seopress-option', 'create_admin_page' ),
			array( __( 'Titles & Metas', 'wp-seopress' ), PagesAdmin::TITLE_METAS, 'seopress-titles', 'seopress_titles_page' ),
			array( __( 'XML - HTML Sitemap', 'wp-seopress' ), PagesAdmin::XML_HTML_SITEMAP, 'seopress-xml-sitemap', 'seopress_xml_sitemap_page' ),
			array( __( 'Social Networks', 'wp-seopress' ), PagesAdmin::SOCIAL_NETWORKS, 'seopress-social', 'seopress_social_page' ),
			array( __( 'Analytics', 'wp-seopress' ), PagesAdmin::ANALYTICS, 'seopress-google-analytics', 'seopress_google_analytics_page' ),
			array( __( 'Instant Indexing', 'wp-seopress' ), PagesAdmin::INSTANT_INDEXING, 'seopress-instant-indexing', 'seopress_instant_indexing_page' ),
			array( __( 'Advanced', 'wp-seopress' ), PagesAdmin::ADVANCED, 'seopress-advanced', 'seopress_advanced_page' ),
			array( __( 'Tools', 'wp-seopress' ), PagesAdmin::TOOLS, 'seopress-import-export', 'seopress_import_export_page' ),
		);

		foreach ( $submenus as $submenu ) {
			add_submenu_page(
				'seopress-option',
				$submenu[0],
				$submenu[0],
				seopress_capability( 'manage_options', $submenu[1] ),
				$submenu[2],
				array( $this, $submenu[3] )
			);
		}
	}

	/**
	 * Handle white label
	 */
	private function handle_white_label() {
		if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' ) &&
			'1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() &&
			function_exists( 'seopress_pro_get_service' ) &&
			method_exists( 'seopress_pro_get_service', 'getWhiteLabelHelpLinks' ) &&
			'1' === seopress_pro_get_service( 'OptionPro' )->getWhiteLabelHelpLinks() ) {
			return;
		}
	}

	/**
	 * Create admin page
	 */
	public function create_admin_page() {
		require_once plugin_dir_path( __FILE__ ) . '/admin-pages/Main.php';
	}

	/**
	 * SEOPress titles page
	 */
	public function seopress_titles_page() {
		$this->load_admin_page( 'Titles.php' );
	}

	/**
	 * SEOPress xml sitemap page
	 */
	public function seopress_xml_sitemap_page() {
		$this->load_admin_page( 'Sitemaps.php' );
	}

	/**
	 * SEOPress social page
	 */
	public function seopress_social_page() {
		$this->load_admin_page( 'Social.php' );
	}

	/**
	 * SEOPress google analytics page
	 */
	public function seopress_google_analytics_page() {
		$this->load_admin_page( 'Analytics.php' );
	}

	/**
	 * SEOPress instant indexing page
	 */
	public function seopress_instant_indexing_page() {
		$this->load_admin_page( 'InstantIndexing.php' );
	}

	/**
	 * SEOPress advanced page
	 */
	public function seopress_advanced_page() {
		$this->load_admin_page( 'Advanced.php' );
	}

	/**
	 * SEOPress import export page
	 */
	public function seopress_import_export_page() {
		$this->load_admin_page( 'Tools.php' );
	}

	/**
	 * Load SEOPress admin page
	 *
	 * @param string $file_name File name.
	 */
	private function load_admin_page( $file_name ) {
		require_once plugin_dir_path( __FILE__ ) . "/admin-pages/{$file_name}";
	}

	/**
	 * SEOPress page init
	 */
	public function page_init() {
		// Array of settings to register.
		$settings = array(
			array( 'seopress_option_group', 'seopress_option_name' ),
			array( 'seopress_titles_option_group', 'seopress_titles_option_name' ),
			array( 'seopress_xml_sitemap_option_group', 'seopress_xml_sitemap_option_name' ),
			array( 'seopress_social_option_group', 'seopress_social_option_name' ),
			array( 'seopress_google_analytics_option_group', 'seopress_google_analytics_option_name' ),
			array( 'seopress_advanced_option_group', 'seopress_advanced_option_name' ),
			array( 'seopress_tools_option_group', 'seopress_tools_option_name' ),
			array( 'seopress_import_export_option_group', 'seopress_import_export_option_name' ),
			array( 'seopress_instant_indexing_option_group', 'seopress_instant_indexing_option_name' ),
		);

		// Register settings dynamically.
		foreach ( $settings as [$group, $name] ) {
			register_setting( $group, $name, array( $this, 'sanitize' ) );
		}
	}

	/**
	 * Sanitize input
	 *
	 * @param array $input Input.
	 * @return array Sanitized input.
	 */
	public function sanitize( $input ) {
		return seopress_sanitize_options_fields( $input );
	}

}

/**
 * SEOPress options
 */
new SEOPressOptions();

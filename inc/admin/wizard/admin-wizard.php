<?php
/**
 * Setup Wizard Class.
 *
 * Renders the React-based setup wizard and handles each step's POST save.
 *
 * @package    Wizard
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * SEOPRESS_Admin_Setup_Wizard class.
 */
class SEOPRESS_Admin_Setup_Wizard {
	/**
	 * Current step.
	 *
	 * @var string
	 */
	private $step = '';

	/**
	 * Parent step.
	 *
	 * @var string
	 */
	private $parent = '';

	/**
	 * Steps for the setup wizard.
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * SEO title.
	 *
	 * @var string
	 */
	private $seo_title = '';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @var string
	 */
	public $plugin_slug = 'seopress-setup';

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'seopress_enable_setup_wizard', true ) && current_user_can( seopress_capability( 'manage_options', 'Admin_Setup_Wizard' ) ) ) {
			add_action( 'admin_menu', array( $this, 'load_wizard' ), 20 );

			add_action( 'admin_head', array( $this, 'hide_from_menus' ), 20 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_react_assets' ) );

			// Remove notices.
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );

			$this->seo_title = 'SEOPress';
			if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
				if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' ) && '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
					$this->seo_title = function_exists( 'seopress_pro_get_service' ) && method_exists( seopress_pro_get_service( 'OptionPro' ), 'getWhiteLabelListTitle' ) && seopress_pro_get_service( 'OptionPro' )->getWhiteLabelListTitle() ? seopress_pro_get_service( 'OptionPro' )->getWhiteLabelListTitle() : 'SEOPress';
				}
			}
		}
	}

	/**
	 * Add dashboard page.
	 */
	public function load_wizard() {
		add_submenu_page( 'seopress-option', __( 'Wizard', 'wp-seopress' ), __( 'Wizard', 'wp-seopress' ), seopress_capability( 'manage_options', 'menu' ), $this->plugin_slug, array( $this, 'setup_wizard' ), 10 );
	}

	/**
	 * Hide Wizard item from SEO menu.
	 */
	public function hide_from_menus() {
		global $submenu;

		if ( ! empty( $submenu ) ) {
			foreach ( $submenu as $key => $value ) {
				if ( 'seopress-option' === $key ) {
					foreach ( $value as $_key => $_value ) {
						if ( $this->plugin_slug === $_value[2] ) {
							unset( $submenu[ $key ][ $_key ] );
						}
					}
				}
			}
		}
	}

	/**
	 * Show the setup wizard. Renders the React mount point and dispatches
	 * the matching save handler when a step form is submitted.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'seopress-setup' !== $_GET['page'] ) {
			return;
		}

		$default_steps = array(
			'welcome'             => array(
				'handler' => array( $this, 'seopress_setup_import_settings_save' ),
				'parent'  => 'welcome',
			),
			'import_settings'     => array(
				'handler' => array( $this, 'seopress_setup_import_settings_save' ),
				'parent'  => 'welcome',
			),
			'site'                => array(
				'handler' => array( $this, 'seopress_setup_site_save' ),
				'parent'  => 'site',
			),
			'social_accounts'     => array(
				'handler' => array( $this, 'seopress_setup_social_accounts_save' ),
				'parent'  => 'site',
			),
			'indexing_post_types' => array(
				'handler' => array( $this, 'seopress_setup_indexing_post_types_save' ),
				'parent'  => 'indexing_post_types',
			),
			'indexing_archives'   => array(
				'handler' => array( $this, 'seopress_setup_indexing_archives_save' ),
				'parent'  => 'indexing_post_types',
			),
			'indexing_taxonomies' => array(
				'handler' => array( $this, 'seopress_setup_indexing_taxonomies_save' ),
				'parent'  => 'indexing_post_types',
			),
			'advanced'            => array(
				'handler' => array( $this, 'seopress_setup_advanced_save' ),
				'parent'  => 'advanced',
			),
			'metabox'             => array(
				'handler' => array( $this, 'seopress_setup_metabox_save' ),
				'parent'  => 'advanced',
			),
			'ready'               => array(
				'handler' => array( $this, 'seopress_final_subscribe' ),
			),
		);

		$this->steps  = apply_filters( 'seopress_setup_wizard_steps', $default_steps );
		$this->step   = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
		$this->parent = isset( $_GET['parent'] ) ? sanitize_key( $_GET['parent'] ) : current( array_keys( $this->steps ) );

		// Handle "Skip for now" dismissal from the welcome step: flag the wizard
		// as already seen so the plugin does not redirect to it on next activation.
		if ( isset( $_GET['seopress-action'] ) && 'dismiss-wizard' === $_GET['seopress-action'] ) {
			check_admin_referer( 'seopress-dismiss-wizard' );

			$seopress_notices = get_option( 'seopress_notices', array() );
			if ( ! is_array( $seopress_notices ) ) {
				$seopress_notices = array();
			}
			$seopress_notices['notice-wizard'] = '1';
			update_option( 'seopress_notices', $seopress_notices, false );

			wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=seopress-option' ) ) );
			exit;
		}

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		// Reaching Ready completes the wizard: stop nagging the user and flush
		// rewrites so any permalink-related changes made earlier in the flow
		// take effect.
		if ( 'ready' === $this->step ) {
			$seopress_notices = get_option( 'seopress_notices', array() );
			if ( ! is_array( $seopress_notices ) ) {
				$seopress_notices = array();
			}
			if ( empty( $seopress_notices['notice-wizard'] ) ) {
				$seopress_notices['notice-wizard'] = '1';
				update_option( 'seopress_notices', $seopress_notices, false );
			}
			flush_rewrite_rules( false );
		}

		echo '<div id="seopress-wizard-root" class="seopress-wizard-root"></div>';
	}

	/**
	 * Enqueue the React wizard bundle on the wizard page.
	 */
	public function enqueue_react_assets() {
		if ( ! isset( $_GET['page'] ) || 'seopress-setup' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$asset_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/wizard/index.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array(
			'dependencies' => array( 'react', 'react-dom', 'wp-components', 'wp-element', 'wp-i18n' ),
			'version'      => SEOPRESS_VERSION,
		);

		wp_enqueue_style( 'wp-components' );

		// Re-use the settings stylesheet so reused components (e.g. the
		// dynamic-variable token editor) inherit their styles.
		$settings_css = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/settings.css';
		if ( file_exists( $settings_css ) ) {
			wp_enqueue_style(
				'seopress-settings-shared',
				SEOPRESS_URL_PUBLIC . '/admin/settings.css',
				array( 'wp-components' ),
				$asset['version']
			);
		}

		$css_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/wizard/index.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'seopress-wizard',
				SEOPRESS_URL_PUBLIC . '/admin/wizard/index.css',
				array( 'wp-components', 'seopress-settings-shared' ),
				$asset['version']
			);
		}

		wp_enqueue_script(
			'seopress-wizard',
			SEOPRESS_URL_PUBLIC . '/admin/wizard/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( 'seopress-wizard', 'wp-seopress', WP_LANG_DIR . '/plugins' );

		$dismiss_url = wp_nonce_url(
			add_query_arg(
				array(
					'page'            => 'seopress-setup',
					'seopress-action' => 'dismiss-wizard',
				),
				admin_url( 'admin.php' )
			),
			'seopress-dismiss-wizard'
		);

		// admin_enqueue_scripts runs before setup_wizard() sets $this->step,
		// so resolve them from $_GET here.
		$step   = isset( $_GET['step'] ) ? sanitize_key( wp_unslash( $_GET['step'] ) ) : 'welcome';   // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$parent = isset( $_GET['parent'] ) ? sanitize_key( wp_unslash( $_GET['parent'] ) ) : '';     // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Form action mirrors the current admin URL so each step POSTs back to
		// itself; the seopress_setup_*_save handlers redirect onwards via
		// get_next_step_link().
		$form_action_args = array(
			'page' => 'seopress-setup',
			'step' => $step,
		);
		if ( '' !== $parent ) {
			$form_action_args['parent'] = $parent;
		}
		$form_action = add_query_arg( $form_action_args, admin_url( 'admin.php' ) );

		$initial_values    = $this->get_react_initial_values();
		$docs_links        = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();
		$indexing_data     = $this->get_react_indexing_data();
		$dynamic_variables = function_exists( 'seopress_get_dyn_variables' ) ? seopress_get_dyn_variables() : array();

		$wizard_data = array(
			'ADMIN_URL'         => admin_url(),
			'AJAX_URL'          => admin_url( 'admin-ajax.php' ),
			'STEP'              => $step,
			'PARENT'            => $parent,
			'DISMISS_URL'       => $dismiss_url,
			'PRO_URL'           => isset( $docs_links['wizard']['pro'] ) ? $docs_links['wizard']['pro'] : 'https://www.seopress.org/seopress-pro/',
			'ASSETS_URL'        => SEOPRESS_URL_PUBLIC . '/admin/wizard',
			'PLUGIN_URL'        => SEOPRESS_URL_ASSETS,
			'SITEMAP_URL'       => admin_url( 'admin.php?page=seopress-xml-sitemap' ),
			'PRIVACY_URL'       => isset( $docs_links['privacy'] ) ? $docs_links['privacy'] : 'https://www.seopress.org/privacy-policy/',
			'USER_EMAIL'        => function_exists( 'wp_get_current_user' ) && wp_get_current_user() ? (string) wp_get_current_user()->user_email : '',
			'SUB_ROUTINE'       => isset( $_GET['sub_routine'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['sub_routine'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'FORM_ACTION'       => $form_action,
			'NONCE'             => array(
				'setup' => wp_create_nonce( 'seopress-setup' ),
			),
			'INITIAL_VALUES'    => $initial_values,
			'INDEXING'          => $indexing_data,
			'DOCS_LINKS'        => array(
				'alt_title'    => isset( $docs_links['titles']['alt_title'] ) ? $docs_links['titles']['alt_title'] : '',
				'pro'          => isset( $docs_links['wizard']['pro'] ) ? $docs_links['wizard']['pro'] : 'https://www.seopress.org/seopress-pro/',
				'ebook'        => isset( $docs_links['wizard']['ebook'] ) ? $docs_links['wizard']['ebook'] : '',
				'video_id'     => isset( $docs_links['wizard']['video_id'] ) ? $docs_links['wizard']['video_id'] : '1nUkjCBpIts',
				'privacy'      => isset( $docs_links['privacy'] ) ? $docs_links['privacy'] : 'https://www.seopress.org/privacy-policy/',
			),
			'NEXT_STEP_URL'     => add_query_arg(
				array(
					'page'   => 'seopress-setup',
					'step'   => 'site',
					'parent' => 'site',
				),
				admin_url( 'admin.php' )
			),
			'IMPORT'            => $this->get_import_data(),
			'ADVANCED'          => $this->get_react_advanced_data(),
			'DYNAMIC_VARIABLES' => $dynamic_variables,
			'PREVIEW_VALUES'    => array(
				'%%sitetitle%%' => get_bloginfo( 'name' ),
				'%%tagline%%'   => get_bloginfo( 'description' ),
			),
		);

		/**
		 * Filter the data localized for the React wizard. Extensions (e.g. the
		 * Pro plugin) can add their own keys here without touching the free
		 * plugin (license CTA, extra promos, etc.).
		 *
		 * @param array $wizard_data Payload exposed as window.SEOPRESS_WIZARD_DATA.
		 */
		$wizard_data = apply_filters( 'seopress_wizard_data', $wizard_data );

		wp_localize_script( 'seopress-wizard', 'SEOPRESS_WIZARD_DATA', $wizard_data );
	}

	/**
	 * Build the Advanced step data (current values, contextual labels) for the
	 * React wizard. Mirrors what the legacy save handler reads from
	 * seopress_advanced_option_name.
	 *
	 * @return array
	 */
	private function get_react_advanced_data() {
		$adv = get_option( 'seopress_advanced_option_name' );

		$category_base = get_option( 'category_base' );
		$category_base = $category_base ? '/' . ltrim( $category_base, '/' ) : '/category/';
		$category_base = trailingslashit( $category_base );

		$wc_active             = function_exists( 'is_plugin_active' ) && is_plugin_active( 'woocommerce/woocommerce.php' );
		$product_category_base = '/product-category/';
		if ( $wc_active ) {
			$wc_permalinks = get_option( 'woocommerce_permalinks' );
			if ( is_array( $wc_permalinks ) && ! empty( $wc_permalinks['category_base'] ) ) {
				$product_category_base = '/' . trim( $wc_permalinks['category_base'], '/' ) . '/';
			}
		}

		// Universal SEO Metabox frontend visibility. Stored as
		// seopress_advanced_appearance_universal_metabox_disable_frontend where
		// '1' means the frontend beacon is hidden. The wizard checkbox semantics
		// are inverted (checked = visible on frontend).
		$metabox_frontend_visible = ! ( isset( $adv['seopress_advanced_appearance_universal_metabox_disable_frontend'] )
			&& '1' === (string) $adv['seopress_advanced_appearance_universal_metabox_disable_frontend'] );

		return array(
			'attachments_file'         => isset( $adv['seopress_advanced_advanced_attachments_file'] ) && '1' === (string) $adv['seopress_advanced_advanced_attachments_file'],
			'image_auto_alt_txt'       => isset( $adv['seopress_advanced_advanced_image_auto_alt_txt'] ) && '1' === (string) $adv['seopress_advanced_advanced_image_auto_alt_txt'],
			'category_url'             => isset( $adv['seopress_advanced_advanced_category_url'] ) && '1' === (string) $adv['seopress_advanced_advanced_category_url'],
			'product_category_url'     => isset( $adv['seopress_advanced_advanced_product_cat_url'] ) && '1' === (string) $adv['seopress_advanced_advanced_product_cat_url'],
			'category_base'            => $category_base,
			'product_category_base'    => $product_category_base,
			'woocommerce_active'       => $wc_active,
			'metabox_frontend_visible' => $metabox_frontend_visible,
		);
	}

	/**
	 * Build the initial form values for the React wizard from the existing
	 * SEOPress options.
	 *
	 * @return array
	 */
	private function get_react_initial_values() {
		$titles  = get_option( 'seopress_titles_option_name' );
		$social  = get_option( 'seopress_social_option_name' );
		$user    = wp_get_current_user();
		$user_em = isset( $user->user_email ) ? $user->user_email : '';

		$default_site_title = '%%sitetitle%% %%sep%% %%tagline%%';
		$saved_site_title   = isset( $titles['seopress_titles_home_site_title'] ) ? (string) $titles['seopress_titles_home_site_title'] : '';
		// Treat the built-in default template as "unset" so the input shows it as a
		// placeholder rather than a pre-filled value.
		if ( $saved_site_title === $default_site_title || $saved_site_title === (string) get_bloginfo( 'name' ) ) {
			$saved_site_title = '';
		}

		return array(
			'site'   => array(
				'site_title'             => $saved_site_title,
				'site_title_placeholder' => $default_site_title,
				'alt_site_title'         => isset( $titles['seopress_titles_home_site_title_alt'] ) ? (string) $titles['seopress_titles_home_site_title_alt'] : '',
				'knowledge_type'   => isset( $social['seopress_social_knowledge_type'] ) ? (string) $social['seopress_social_knowledge_type'] : 'none',
				// Legacy fields the new UI no longer surfaces. React renders them
				// as hidden inputs so the PHP save handler preserves them rather
				// than blanking the values for users who set them in the past.
				'knowledge_name'   => isset( $social['seopress_social_knowledge_name'] ) ? (string) $social['seopress_social_knowledge_name'] : '',
				'knowledge_img'    => isset( $social['seopress_social_knowledge_img'] ) ? (string) $social['seopress_social_knowledge_img'] : '',
				'knowledge_email'  => isset( $social['seopress_social_knowledge_email'] ) ? (string) $social['seopress_social_knowledge_email'] : $user_em,
				'knowledge_phone'  => isset( $social['seopress_social_knowledge_phone'] ) ? (string) $social['seopress_social_knowledge_phone'] : '',
				'knowledge_tax_id' => isset( $social['seopress_social_knowledge_tax_id'] ) ? (string) $social['seopress_social_knowledge_tax_id'] : '',
			),
			'social' => array(
				'facebook'  => isset( $social['seopress_social_accounts_facebook'] ) ? (string) $social['seopress_social_accounts_facebook'] : '',
				'twitter'   => isset( $social['seopress_social_accounts_twitter'] ) ? (string) $social['seopress_social_accounts_twitter'] : '',
				'pinterest' => isset( $social['seopress_social_accounts_pinterest'] ) ? (string) $social['seopress_social_accounts_pinterest'] : '',
				'instagram' => isset( $social['seopress_social_accounts_instagram'] ) ? (string) $social['seopress_social_accounts_instagram'] : '',
				'youtube'   => isset( $social['seopress_social_accounts_youtube'] ) ? (string) $social['seopress_social_accounts_youtube'] : '',
				'linkedin'  => isset( $social['seopress_social_accounts_linkedin'] ) ? (string) $social['seopress_social_accounts_linkedin'] : '',
				'extra'     => isset( $social['seopress_social_accounts_extra'] ) ? (string) $social['seopress_social_accounts_extra'] : '',
			),
		);
	}

	/**
	 * Build the indexing data (post types, archives, taxonomies, current values)
	 * the React wizard needs for the Indexing top-level step.
	 *
	 * @return array
	 */
	private function get_react_indexing_data() {
		$titles = get_option( 'seopress_titles_option_name' );

		$post_types = array();
		$archives   = array();
		$taxonomies = array();

		if ( function_exists( 'seopress_get_service' ) ) {
			$wpdata = seopress_get_service( 'WordPressData' );
			if ( is_object( $wpdata ) ) {
				if ( method_exists( $wpdata, 'getPostTypes' ) ) {
					foreach ( $wpdata->getPostTypes() as $cpt_key => $cpt_obj ) {
						$post_types[] = array(
							'key'   => $cpt_key,
							'label' => isset( $cpt_obj->labels->name ) ? $cpt_obj->labels->name : $cpt_key,
						);
					}
				}
				if ( method_exists( $wpdata, 'getTaxonomies' ) ) {
					foreach ( $wpdata->getTaxonomies() as $tax_key => $tax_obj ) {
						$taxonomies[] = array(
							'key'   => $tax_key,
							'label' => isset( $tax_obj->labels->name ) ? $tax_obj->labels->name : $tax_key,
						);
					}
				}
			}
		}

		// Archives mirror non-default post types (legacy behavior strips post/page).
		foreach ( $post_types as $pt ) {
			if ( 'post' === $pt['key'] || 'page' === $pt['key'] ) {
				continue;
			}
			$archives[] = $pt;
		}

		$single_noindex  = array();
		$archive_noindex = array();
		$tax_noindex     = array();
		foreach ( $post_types as $pt ) {
			$single_noindex[ $pt['key'] ] = isset( $titles['seopress_titles_single_titles'][ $pt['key'] ]['noindex'] )
				&& '1' === (string) $titles['seopress_titles_single_titles'][ $pt['key'] ]['noindex'];
		}
		foreach ( $archives as $pt ) {
			$archive_noindex[ $pt['key'] ] = isset( $titles['seopress_titles_archive_titles'][ $pt['key'] ]['noindex'] )
				&& '1' === (string) $titles['seopress_titles_archive_titles'][ $pt['key'] ]['noindex'];
		}
		foreach ( $taxonomies as $tx ) {
			$tax_noindex[ $tx['key'] ] = isset( $titles['seopress_titles_tax_titles'][ $tx['key'] ]['noindex'] )
				&& '1' === (string) $titles['seopress_titles_tax_titles'][ $tx['key'] ]['noindex'];
		}

		$special_archives = array(
			'date'   => isset( $titles['seopress_titles_archives_date_noindex'] )
				&& '1' === (string) $titles['seopress_titles_archives_date_noindex'],
			'search' => isset( $titles['seopress_titles_archives_search_title_noindex'] )
				&& '1' === (string) $titles['seopress_titles_archives_search_title_noindex'],
			'author' => isset( $titles['seopress_titles_archives_author_noindex'] )
				&& '1' === (string) $titles['seopress_titles_archives_author_noindex'],
		);

		return array(
			'post_types'       => $post_types,
			'archives'         => $archives,
			'taxonomies'       => $taxonomies,
			'single_noindex'   => $single_noindex,
			'archive_noindex'  => $archive_noindex,
			'tax_noindex'      => $tax_noindex,
			'special_archives' => $special_archives,
			'site_url'         => trailingslashit( home_url() ),
		);
	}

	/**
	 * Build the migration data (plugins, nonces, active plugins) for the React UI.
	 *
	 * @return array
	 */
	private function get_import_data() {
		$plugins = array(
			'yoast'            => array(
				'slug'   => array( 'wordpress-seo/wp-seo.php', 'wordpress-seo-premium/wp-seo-premium.php' ),
				'name'   => 'Yoast SEO',
				'img'    => 'yoast.png',
				'action' => 'seopress_yoast_migration',
				'nonce'  => 'seopress_yoast_migrate_nonce',
			),
			'aio'              => array(
				'slug'   => array( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ),
				'name'   => 'All In One SEO',
				'img'    => 'aio.svg',
				'action' => 'seopress_aio_migration',
				'nonce'  => 'seopress_aio_migrate_nonce',
			),
			'seo-framework'    => array(
				'slug'   => array( 'autodescription/autodescription.php' ),
				'name'   => 'The SEO Framework',
				'img'    => 'seo-framework.svg',
				'action' => 'seopress_seo_framework_migration',
				'nonce'  => 'seopress_seo_framework_migrate_nonce',
			),
			'rk'               => array(
				'slug'   => array( 'seo-by-rank-math/rank-math.php' ),
				'name'   => 'Rank Math',
				'img'    => 'rk.svg',
				'action' => 'seopress_rk_migration',
				'nonce'  => 'seopress_rk_migrate_nonce',
			),
			'squirrly'         => array(
				'slug'   => array( 'squirrly-seo/squirrly.php' ),
				'name'   => 'Squirrly SEO',
				'img'    => 'squirrly.png',
				'action' => 'seopress_squirrly_migration',
				'nonce'  => 'seopress_squirrly_migrate_nonce',
			),
			'seo-ultimate'     => array(
				'slug'   => array( 'seo-ultimate/seo-ultimate.php' ),
				'name'   => 'SEO Ultimate',
				'img'    => 'seo-ultimate.svg',
				'action' => 'seopress_seo_ultimate_migration',
				'nonce'  => 'seopress_seo_ultimate_migrate_nonce',
			),
			'wp-meta-seo'      => array(
				'slug'   => array( 'wp-meta-seo/wp-meta-seo.php' ),
				'name'   => 'WP Meta SEO',
				'img'    => 'wp-meta-seo.png',
				'action' => 'seopress_wp_meta_seo_migration',
				'nonce'  => 'seopress_meta_seo_migrate_nonce',
			),
			'premium-seo-pack' => array(
				'slug'   => array( 'premium-seo-pack/plugin.php' ),
				'name'   => 'Premium SEO Pack',
				'img'    => 'premium-seo-pack.png',
				'action' => 'seopress_premium_seo_pack_migration',
				'nonce'  => 'seopress_premium_seo_pack_migrate_nonce',
			),
			'siteseo'          => array(
				'slug'   => array( 'siteseo/siteseo.php' ),
				'name'   => 'SiteSEO',
				'img'    => 'siteseo.png',
				'action' => 'seopress_siteseo_migration',
				'nonce'  => 'seopress_siteseo_migrate_nonce',
			),
			'smart-crawl'      => array(
				'slug'   => array( 'smartcrawl-seo/wpmu-dev-seo.php' ),
				'name'   => 'SmartCrawl',
				'img'    => 'smart-crawl.png',
				'action' => 'seopress_smart_crawl_migration',
				'nonce'  => 'seopress_smart_crawl_migrate_nonce',
			),
			'slim-seo'         => array(
				'slug'   => array( 'slim-seo/slim-seo.php' ),
				'name'   => 'Slim SEO',
				'img'    => 'slim-seo.svg',
				'action' => 'seopress_slim_seo_migration',
				'nonce'  => 'seopress_slim_seo_migrate_nonce',
			),
			'surerank'         => array(
				'slug'   => array( 'surerank/surerank.php' ),
				'name'   => 'SureRank',
				'img'    => 'surerank.png',
				'action' => 'seopress_surerank_migration',
				'nonce'  => 'seopress_surerank_migrate_nonce',
			),
		);

		// Detect active competing SEO plugins so the React UI can warn the
		// user and pre-select the matching card.
		$active = array();
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$assets_url = defined( 'SEOPRESS_URL_ASSETS' ) ? SEOPRESS_URL_ASSETS : plugins_url( 'assets', __FILE__ . '/../../../' );

		$out = array();
		foreach ( $plugins as $key => $plugin ) {
			$is_active = false;
			foreach ( $plugin['slug'] as $slug ) {
				if ( is_plugin_active( $slug ) ) {
					$is_active = true;
					$active[]  = $plugin['name'];
					break;
				}
			}

			$out[ $key ] = array(
				'key'    => $key,
				'name'   => $plugin['name'],
				'img'    => $assets_url . '/img/import/' . $plugin['img'],
				'action' => $plugin['action'],
				'nonce'  => wp_create_nonce( $plugin['nonce'] ),
				'active' => $is_active,
			);
		}

		return array(
			'PLUGINS'        => array_values( $out ),
			'ACTIVE_PLUGINS' => array_values( array_unique( $active ) ),
		);
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step slug (default: current step).
	 *
	 * @return string URL for next step if a next step exists.
	 *                Admin URL if it's the last step.
	 *                Empty string on failure.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		$parent = '';
		$all    = $this->steps;
		if ( isset( $all[ $step ]['parent'] ) ) {
			$key = $keys[ $step_index + 1 ];
			if ( isset( $all[ $key ]['parent'] ) ) {
				$parent = $all[ $key ]['parent'];
			}
		}

		$args = array( 'step' => $keys[ $step_index + 1 ] );
		if ( '' !== $parent ) {
			$args['parent'] = $parent;
		}

		return add_query_arg( $args, remove_query_arg( 'parent' ) );
	}

	/**
	 * Save step 1.2 settings (no-op: the actual migration runs over AJAX).
	 */
	public function seopress_setup_import_settings_save() {
		check_admin_referer( 'seopress-setup' );
		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save step 2.0 settings.
	 */
	public function seopress_setup_site_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_titles_option = get_option( 'seopress_titles_option_name' );
		$seopress_social_option = get_option( 'seopress_social_option_name' );

		// Titles.
		$seopress_titles_option['seopress_titles_home_site_title']     = isset( $_POST['site_title'] ) ? sanitize_text_field( wp_unslash( $_POST['site_title'] ) ) : '';
		$seopress_titles_option['seopress_titles_home_site_title_alt'] = isset( $_POST['alt_site_title'] ) ? sanitize_text_field( wp_unslash( $_POST['alt_site_title'] ) ) : '';

		// Social.
		$seopress_social_option['seopress_social_knowledge_type']   = isset( $_POST['knowledge_type'] ) ? esc_attr( wp_unslash( $_POST['knowledge_type'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_name']   = isset( $_POST['knowledge_name'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_name'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_img']    = isset( $_POST['knowledge_img'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_img'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_email']  = isset( $_POST['knowledge_email'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_email'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_phone']  = isset( $_POST['knowledge_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_phone'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_tax_id'] = isset( $_POST['knowledge_tax_id'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_tax_id'] ) ) : '';
		$seopress_social_option['seopress_social_knowledge_nl']     = isset( $_POST['knowledge_nl'] ) ? esc_attr( wp_unslash( $_POST['knowledge_nl'] ) ) : null;

		// Save options.
		update_option( 'seopress_titles_option_name', $seopress_titles_option, false );
		update_option( 'seopress_social_option_name', $seopress_social_option, false );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save step 2.1 settings.
	 */
	public function seopress_setup_social_accounts_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_social_option = get_option( 'seopress_social_option_name' );

		// Social accounts.
		$seopress_social_option['seopress_social_accounts_facebook']  = isset( $_POST['knowledge_fb'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_fb'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_twitter']   = isset( $_POST['knowledge_tw'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_tw'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_pinterest'] = isset( $_POST['knowledge_pin'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_pin'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_instagram'] = isset( $_POST['knowledge_insta'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_insta'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_youtube']   = isset( $_POST['knowledge_yt'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_yt'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_linkedin']  = isset( $_POST['knowledge_li'] ) ? sanitize_text_field( wp_unslash( $_POST['knowledge_li'] ) ) : '';
		$seopress_social_option['seopress_social_accounts_extra']     = isset( $_POST['knowledge_extra'] ) ? sanitize_textarea_field( wp_unslash( $_POST['knowledge_extra'] ) ) : '';

		// Save options.
		update_option( 'seopress_social_option_name', $seopress_social_option, false );

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save Step 3.0 Post Types settings.
	 */
	public function seopress_setup_indexing_post_types_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_titles_option = get_option( 'seopress_titles_option_name' );
		$post_types             = seopress_get_service( 'WordPressData' )->getPostTypes();
		// Post Types noindex.
		foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
			if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] ) ) {
				$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] ) );
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] = $noindex;
		}

		// Save options.
		update_option( 'seopress_titles_option_name', $seopress_titles_option );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save Step 3.1 Archives settings.
	 */
	public function seopress_setup_indexing_archives_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_titles_option = get_option( 'seopress_titles_option_name' );
		$post_types             = seopress_get_service( 'WordPressData' )->getPostTypes();

		// Post Type archives noindex.
		foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
			if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][ $seopress_cpt_key ]['noindex'] ) ) {
				$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_archive_titles'][ $seopress_cpt_key ]['noindex'] ) );
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_archive_titles'][ $seopress_cpt_key ]['noindex'] = $noindex;
		}

		// Date archives noindex.
		if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_archives_date_noindex'] ) ) {
			$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_archives_date_noindex'] ) );
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_date_noindex'] = $noindex;

		// Search archives noindex.
		if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_archives_search_title_noindex'] ) ) {
			$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_archives_search_title_noindex'] ) );
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_search_title_noindex'] = $noindex;

		// Author indexing.
		if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_archives_author_noindex'] ) ) {
			$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_archives_author_noindex'] ) );
		} else {
			$noindex = null;
		}
		$seopress_titles_option['seopress_titles_archives_author_noindex'] = $noindex;

		// Save options.
		update_option( 'seopress_titles_option_name', $seopress_titles_option );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save Step 3.2 taxonomies settings.
	 */
	public function seopress_setup_indexing_taxonomies_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_titles_option = get_option( 'seopress_titles_option_name' );

		// Archives noindex.
		foreach ( seopress_get_service( 'WordPressData' )->getTaxonomies() as $seopress_tax_key => $seopress_tax_value ) {
			if ( isset( $_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] ) ) {
				$noindex = esc_attr( wp_unslash( $_POST['seopress_titles_option_name']['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] ) );
			} else {
				$noindex = null;
			}
			$seopress_titles_option['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] = $noindex;
		}

		// Save options.
		update_option( 'seopress_titles_option_name', $seopress_titles_option );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Save step 4.1 settings.
	 */
	public function seopress_setup_advanced_save() {
		check_admin_referer( 'seopress-setup' );

		// Get options.
		$seopress_advanced_option = get_option( 'seopress_advanced_option_name' );

		// Advanced.
		$seopress_advanced_option['seopress_advanced_advanced_attachments_file']   = isset( $_POST['attachments_file'] ) ? esc_attr( wp_unslash( $_POST['attachments_file'] ) ) : null;
		$seopress_advanced_option['seopress_advanced_advanced_image_auto_alt_txt'] = isset( $_POST['image_auto_alt_txt'] ) ? esc_attr( wp_unslash( $_POST['image_auto_alt_txt'] ) ) : null;
		$seopress_advanced_option['seopress_advanced_advanced_category_url']       = isset( $_POST['category_url'] ) ? esc_attr( wp_unslash( $_POST['category_url'] ) ) : null;

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$seopress_advanced_option['seopress_advanced_advanced_product_cat_url'] = isset( $_POST['product_category_url'] ) ? esc_attr( wp_unslash( $_POST['product_category_url'] ) ) : null;
		}

		// Save options.
		update_option( 'seopress_advanced_option_name', $seopress_advanced_option, false );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );

		exit;
	}

	/**
	 * Save the SEO Metabox step: toggles the Universal SEO Metabox frontend
	 * visibility. The on-screen checkbox semantics are inverted compared with
	 * the stored option (checked = visible on frontend).
	 */
	public function seopress_setup_metabox_save() {
		check_admin_referer( 'seopress-setup' );

		$seopress_advanced_option = get_option( 'seopress_advanced_option_name' );
		if ( ! is_array( $seopress_advanced_option ) ) {
			$seopress_advanced_option = array();
		}

		$enabled = ! empty( $_POST['universal_metabox_frontend'] );
		$seopress_advanced_option['seopress_advanced_appearance_universal_metabox_disable_frontend'] = $enabled ? '0' : '1';

		update_option( 'seopress_advanced_option_name', $seopress_advanced_option, false );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );

		exit;
	}

	/**
	 * Final subscribe handler for the Ready step's newsletter form.
	 */
	public function seopress_final_subscribe() {
		check_admin_referer( 'seopress-setup' );

		$email_routine = '';

		// Send email to SG if we have user consent.
		if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' ) && '1' !== seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
			$endpoint_url         = 'https://www.seopress.org/wizard-nl/';
			$endpoint_url_routine = 'https://www.seopress.org/wizard-nl-routine/';

			$email         = isset( $_POST['seopress_nl'] ) ? sanitize_text_field( wp_unslash( $_POST['seopress_nl'] ) ) : '';
			$email_routine = isset( $_POST['seopress_nl_routine'] ) ? sanitize_text_field( wp_unslash( $_POST['seopress_nl_routine'] ) ) : '';

			if ( ! empty( $email ) ) {
				$body = array(
					'email' => $email,
					'lang'  => seopress_get_locale(),
				);

				wp_remote_post(
					$endpoint_url,
					array(
						'method'   => 'POST',
						'body'     => $body,
						'timeout'  => 5,
						'blocking' => true,
					)
				);
			}

			if ( ! empty( $email_routine ) ) {
				$body = array(
					'email' => $email_routine,
					'lang'  => seopress_get_locale(),
				);

				wp_remote_post(
					$endpoint_url_routine,
					array(
						'method'   => 'POST',
						'body'     => $body,
						'timeout'  => 5,
						'blocking' => true,
					)
				);
			}
		}

		$status_key = ! empty( $email_routine ) ? 'sub_routine' : 'sub';
		$args       = array(
			'page'      => 'seopress-setup',
			'step'      => 'ready',
			$status_key => '1',
		);
		wp_safe_redirect( esc_url_raw( add_query_arg( $args, admin_url( 'admin.php' ) ) ) );
		exit;
	}
}
new SEOPRESS_Admin_Setup_Wizard();

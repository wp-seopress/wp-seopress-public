<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Enqueue the React-powered SEOPress dashboard on the seopress-option page.
 *
 * The dashboard is progressively migrated to React block by block; each
 * server-rendered block is replaced by a section in the React app. Until a
 * given block is migrated the PHP version keeps rendering, so the page is
 * always functional in either state.
 *
 * @since 9.9.0
 */
class ModuleDashboard implements ExecuteHooks {

	/**
	 * Page slug for the main SEOPress dashboard.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'seopress-option';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the dashboard React bundle when on the dashboard page.
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( ! $this->isDashboardPage() ) {
			return;
		}

		$capability = seopress_capability( 'manage_options', 'dashboard' );
		if ( ! current_user_can( $capability ) ) {
			return;
		}

		// WP core's wp-admin/css/forms.css ships globally-scoped rules on
		// `input[type=checkbox]`, `select`, etc. that clash with the React
		// dashboard's WP-components controls (the ToggleControl switch is
		// backed by a checkbox underneath). The dashboard renders no native
		// WP form controls of its own, so dropping the sheet is safe here.
		// Note: `forms` is a registered dependency of the `wp-admin` style,
		// so it would be re-printed via the dependency chain even after
		// wp_dequeue_style(). Strip it from wp-admin's deps too, then drop
		// it from the registry, on this page only.
		add_action(
			'admin_print_styles',
			static function () {
				global $wp_styles;
				if ( isset( $wp_styles->registered['wp-admin'] ) ) {
					$wp_styles->registered['wp-admin']->deps = array_values(
						array_diff( $wp_styles->registered['wp-admin']->deps, array( 'forms' ) )
					);
				}
				wp_dequeue_style( 'forms' );
				wp_deregister_style( 'forms' );
			},
			0
		);

		$asset_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/dashboard.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array(
			'dependencies' => array( 'react', 'react-dom', 'wp-components', 'wp-api-fetch', 'wp-i18n', 'wp-element' ),
			'version'      => SEOPRESS_VERSION,
		);

		wp_enqueue_style( 'wp-components' );

		$css_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/dashboard.css';
		if ( file_exists( $css_file ) ) {
			// In dev SEOPRESS_VERSION is the literal `{VERSION}` placeholder so
			// it never busts the browser cache between rebuilds. Hash the file
			// content so any rebuild produces a new URL — stronger than
			// filemtime because it survives filesystems that don't update mtime.
			$css_version = SEOPRESS_VERSION;
			if ( '{VERSION}' === $css_version || empty( $css_version ) ) {
				$css_version = substr( md5_file( $css_file ), 0, 12 );
			}

			wp_enqueue_style(
				'seopress-react-dashboard',
				SEOPRESS_URL_PUBLIC . '/admin/dashboard.css',
				array( 'wp-components' ),
				$css_version
			);
		}

		wp_enqueue_script(
			'seopress-react-dashboard',
			SEOPRESS_URL_PUBLIC . '/admin/dashboard.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( 'seopress-react-dashboard', 'wp-seopress', WP_LANG_DIR . '/plugins' );

		$features = $this->getFeatures();

		wp_localize_script(
			'seopress-react-dashboard',
			'SEOPRESS_DASHBOARD_DATA',
			array(
				'REST_URL'      => rest_url(),
				'NONCE'         => wp_create_nonce( 'wp_rest' ),
				'ADMIN_URL'     => admin_url(),
				'ASSETS_URL'    => SEOPRESS_URL_PUBLIC,
				'PLUGIN_URL'    => SEOPRESS_PLUGIN_DIR_URL,
				'FEATURES'      => $features,
				'GROUPS'        => $this->getFeatureGroups(),
				'INTRO'         => $this->getIntroPayload(),
				'GET_STARTED'   => $this->getGetStartedPayload(),
				'TASKS'         => $this->getTasksPayload(),
				'INTEGRATIONS'  => $this->getIntegrationsPayload(),
				'PROMOTIONS'    => $this->getPromotionsPayload(),
				'OVERVIEW'      => $this->getOverviewPayload(),
				'IMPROVEMENTS'  => $this->getImprovementsPayload(),
				'HIDDEN_BLOCKS' => $this->getHiddenBlocks(),
			)
		);
	}

	/**
	 * Intro section payload (header with plugin name + tagline).
	 *
	 * Mirrors inc/admin/blocks/intro.php including the white-label rules
	 * (constant SEOPRESS_WL_ADMIN_HEADER and the PRO ToggleWhiteLabel +
	 * WhiteLabelListTitle overrides).
	 *
	 * @return array
	 */
	private function getIntroPayload() {
		$visible = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );

		$plugin_name = 'SEOPress';
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$toggle_service = seopress_get_service( 'ToggleOption' );
			if ( method_exists( $toggle_service, 'getToggleWhiteLabel' ) && '1' === $toggle_service->getToggleWhiteLabel() ) {
				if ( function_exists( 'seopress_pro_get_service' )
					&& method_exists( seopress_pro_get_service( 'OptionPro' ), 'getWhiteLabelListTitle' )
					&& seopress_pro_get_service( 'OptionPro' )->getWhiteLabelListTitle() ) {
					$plugin_name = (string) seopress_pro_get_service( 'OptionPro' )->getWhiteLabelListTitle();
				}
			}
		}

		// SEOPRESS_VERSION is the literal "{VERSION}" placeholder until the
		// release build script substitutes it. Suppress it in that case so the
		// header doesn't ship a placeholder string to the user.
		$version = SEOPRESS_VERSION;
		if ( '{VERSION}' === $version ) {
			$version = '';
		}

		return array(
			'visible'    => $visible,
			'pluginName' => $plugin_name,
			'version'    => $version,
			'logoUrl'    => SEOPRESS_ASSETS_DIR . '/img/logo-seopress.svg',
			'tagline'    => __( 'Your control center for SEO.', 'wp-seopress' ),
		);
	}

	/**
	 * Get Started section payload.
	 *
	 * Mirrors the two display modes of inc/admin/blocks/get-started.php:
	 *  - "wizard"    : pitch the Setup Wizard to brand-new installs.
	 *  - "checklist" : show recurring SEO reminders once the wizard has
	 *                  been completed.
	 * Hidden entirely when SEOPRESS_WL_ADMIN_HEADER is false.
	 *
	 * @return array
	 */
	private function getGetStartedPayload() {
		$visible = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );

		$notice_wizard = (bool) seopress_get_service( 'NoticeOption' )->getNoticeWizard();
		$mode          = $notice_wizard ? 'checklist' : 'wizard';

		$wizard_url = admin_url( 'admin.php?page=seopress-setup' );

		if ( 'checklist' === $mode ) {
			$content = array(
				'badge'    => __( 'SEO Tips', 'wp-seopress' ),
				'title'    => __( 'SEO Checklist', 'wp-seopress' ),
				'subtitle' => __( 'Quick reminders to boost your rankings.', 'wp-seopress' ),
				'items'    => array(
					__( 'Add meta descriptions to your posts', 'wp-seopress' ),
					__( 'Optimize images with alt text', 'wp-seopress' ),
					__( 'Submit your sitemap to Google', 'wp-seopress' ),
					__( 'Check for broken links', 'wp-seopress' ),
				),
				'ctaLabel' => __( 'Run Setup Wizard', 'wp-seopress' ),
				'ctaUrl'   => $wizard_url,
			);
		} else {
			$content = array(
				'badge'    => __( 'Quick Setup', 'wp-seopress' ),
				'title'    => __( 'Rank Higher on Google', 'wp-seopress' ),
				'subtitle' => __( 'Configure your SEO in minutes with our guided wizard.', 'wp-seopress' ),
				'items'    => array(
					__( 'Optimize titles & meta', 'wp-seopress' ),
					__( 'Generate XML sitemaps', 'wp-seopress' ),
					__( 'Connect to Google', 'wp-seopress' ),
				),
				'ctaLabel' => __( 'Start Setup Wizard', 'wp-seopress' ),
				'ctaUrl'   => $wizard_url,
				'helper'   => __( 'Takes only 2 minutes', 'wp-seopress' ),
			);
		}

		return array(
			'visible'   => $visible,
			'mode'      => $mode,
			'content'   => $content,
			'hideNonce' => wp_create_nonce( 'seopress_hide_notices_nonce' ),
		);
	}

	/**
	 * Tasks (SEOPress Suite) section payload.
	 *
	 * Mirrors inc/admin/blocks/tasks.php — status badge + CTA per product,
	 * promo strip when PRO isn't active, white-label guards.
	 *
	 * @return array
	 */
	private function getTasksPayload() {
		$wl_admin = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );

		$wl_pro = false;
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$toggle_service = seopress_get_service( 'ToggleOption' );
			if ( method_exists( $toggle_service, 'getToggleWhiteLabel' ) && '1' === $toggle_service->getToggleWhiteLabel() ) {
				$wl_pro = true;
			}
		}

		if ( ! $wl_admin || $wl_pro ) {
			return array( 'visible' => false );
		}

		$docs = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();

		$status_pro      = 'valid' === get_option( 'seopress_pro_license_status' );
		$status_insights = 'valid' === get_option( 'seopress_insights_license_status' );

		$license_url = admin_url( 'admin.php?page=seopress-license' );

		$definitions = array(
			array(
				'key'        => 'wp-seopress/seopress.php',
				'title'      => 'SEOPress Free',
				'logo'       => SEOPRESS_URL_ASSETS . '/img/logo-seopress-free.svg',
				'url'        => isset( $docs['pricing'] ) ? $docs['pricing'] : '',
				'ctaText'    => __( 'Learn more', 'wp-seopress' ),
				'hasLicense' => false,
				'licenseOk'  => true,
			),
			array(
				'key'        => 'wp-seopress-pro/seopress-pro.php',
				'title'      => 'SEOPress PRO',
				'logo'       => SEOPRESS_URL_ASSETS . '/img/logo-seopress-pro.svg',
				'url'        => isset( $docs['addons']['pro'] ) ? $docs['addons']['pro'] : '',
				'ctaText'    => __( 'Get PRO', 'wp-seopress' ),
				'hasLicense' => true,
				'licenseOk'  => $status_pro,
			),
			array(
				'key'        => 'wp-seopress-insights/seopress-insights.php',
				'title'      => 'SEOPress Insights',
				'logo'       => SEOPRESS_URL_ASSETS . '/img/logo-seopress-insights.svg',
				'url'        => isset( $docs['addons']['insights'] ) ? $docs['addons']['insights'] : '',
				'ctaText'    => __( 'Get Insights', 'wp-seopress' ),
				'hasLicense' => true,
				'licenseOk'  => $status_insights,
			),
		);

		$products = array();
		foreach ( $definitions as $def ) {
			$is_active = is_plugin_active( $def['key'] );
			$status    = $is_active ? 'active' : 'inactive';
			$label     = $is_active ? __( 'Active', 'wp-seopress' ) : __( 'Inactive', 'wp-seopress' );

			if ( $is_active && $def['hasLicense'] ) {
				if ( $def['licenseOk'] ) {
					$label = __( 'License valid', 'wp-seopress' );
				} else {
					$status = 'expired';
					$label  = __( 'License invalid', 'wp-seopress' );
				}
			}

			$products[] = array(
				'key'          => $def['key'],
				'title'        => $def['title'],
				'logo'         => $def['logo'],
				'url'          => $def['url'],
				'ctaText'      => $def['ctaText'],
				'status'       => $status,
				'label'        => $label,
				'showUpsell'   => ! $is_active,
				'showActivate' => $is_active && $def['hasLicense'] && ! $def['licenseOk'],
				'activateUrl'  => $license_url,
			);
		}

		return array(
			'visible'   => true,
			'title'     => __( 'SEOPress Suite', 'wp-seopress' ),
			'subtitle'  => __( 'From on-site to off-site SEO, our SEO plugins cover all your needs to rank higher in search engines.', 'wp-seopress' ),
			'products'  => $products,
			'showPromo' => ! is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ),
			'promoText' => __( 'Unlock AI, redirections, schemas, and more.', 'wp-seopress' ),
			'promoCta'  => __( 'Get PRO', 'wp-seopress' ),
			'promoUrl'  => isset( $docs['addons']['pro'] ) ? $docs['addons']['pro'] : '',
		);
	}

	/**
	 * Integrations section payload.
	 *
	 * Detects every supported third-party plugin / theme that's currently
	 * active on the site, returns the matching titles. Mirrors
	 * inc/admin/blocks/integrations.php.
	 *
	 * @return array
	 */
	private function getIntegrationsPayload() {
		$visible = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );
		if ( ! $visible ) {
			return array( 'visible' => false );
		}

		$docs = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();

		$catalogue = array(
			array( 'astra', 'Astra' ),
			array( 'codepress-admin-columns/codepress-admin-columns.php', 'Admin Columns' ),
			array( 'admin-columns-pro/admin-columns-pro.php', 'Admin Columns PRO' ),
			array( 'advanced-custom-fields/acf.php', 'Advanced Custom Fields' ),
			array( 'advanced-custom-fields-pro/acf.php', 'Advanced Custom Fields PRO' ),
			array( 'amp/amp.php', 'AMP' ),
			array( 'bbpress/bbpress.php', 'bbPress' ),
			array( 'beaver-builder-lite-version/fl-builder.php', 'Beaver Builder Lite' ),
			array( 'bb-plugin/fl-builder.php', 'Beaver Builder Agency' ),
			array( 'bricks', 'Bricks' ),
			array( 'Divi', 'Divi' ),
			array( 'breakdance/plugin.php', 'Breakdance' ),
			array( 'buddypress/bp-loader.php', 'BuddyPress' ),
			array( 'easy-digital-downloads/easy-digital-downloads.php', 'Easy Digital Downloads' ),
			array( 'easy-digital-downloads-pro/easy-digital-downloads.php', 'Easy Digital Downloads PRO' ),
			array( 'elementor/elementor.php', 'Elementor' ),
			array( 'enfold', 'Enfold' ),
			array( 'the-events-calendar/the-events-calendar.php', 'The Events Calendar' ),
			array( 'events-calendar-pro/events-calendar-pro.php', 'The Events Calendar PRO' ),
			array( 'jetpack/jetpack.php', 'Jetpack' ),
			array( 'js_composer/js_composer.php', 'WPBakery Page Builder' ),
			array( 'multilingual-press/multilingual-press.php', 'MultilingualPress' ),
			array( 'oxygen/functions.php', 'Oxygen Builder' ),
			array( 'permalink-manager/permalink-manager.php', 'Permalink Manager' ),
			array( 'permalink-manager-pro/permalink-manager.php', 'Permalink Manager PRO' ),
			array( 'polylang/polylang.php', 'Polylang' ),
			array( 'polylang-pro/polylang.php', 'Polylang PRO' ),
			array( 'sitepress-multilingual-cms/sitepress.php', 'WPML' ),
			array( 'weglot/weglot.php', 'Weglot' ),
			array( 'wp-rocket/wp-rocket.php', 'WP Rocket' ),
			array( 'woocommerce/woocommerce.php', 'WooCommerce' ),
		);

		$theme  = wp_get_theme();
		$active = array();

		foreach ( $catalogue as $entry ) {
			list( $key, $title ) = $entry;

			$is_active = is_plugin_active( $key )
				|| $key === $theme->template
				|| $key === $theme->parent_theme;

			if ( $is_active ) {
				$active[] = array(
					'key'   => (string) $key,
					'title' => (string) $title,
				);
			}
		}

		return array(
			'visible'              => true,
			'title'                => __( 'Integrations', 'wp-seopress' ),
			'subtitle'             => __( 'You\'re using these plugins / themes on your site. We provide advanced integrations with them to improve your SEO.', 'wp-seopress' ),
			'items'                => $active,
			'allIntegrationsUrl'   => isset( $docs['integrations']['all'] ) ? $docs['integrations']['all'] : '',
			'allIntegrationsLabel' => __( 'See all', 'wp-seopress' ),
			'emptyText'            => __( 'Currently, no specific integration found for your site. Contact us if you have any doubts about the compatibility between your plugins/themes and our products.', 'wp-seopress' ),
			'contactUrl'           => isset( $docs['contact'] ) ? $docs['contact'] : '',
			'contactCta'           => __( 'Request an integration', 'wp-seopress' ),
		);
	}

	/**
	 * Promotions section payload (Recommended Partners — affiliates).
	 *
	 * Mirrors inc/admin/blocks/promotions.php. Affiliate brand colors come
	 * straight from the API and are passed through to React unchanged — the
	 * rest of the chrome stays on the WP admin theme color.
	 *
	 * @return array
	 */
	private function getPromotionsPayload() {
		$wl_admin = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );

		$wl_pro = false;
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$toggle_service = seopress_get_service( 'ToggleOption' );
			if ( method_exists( $toggle_service, 'getToggleWhiteLabel' ) && '1' === $toggle_service->getToggleWhiteLabel() ) {
				$wl_pro = true;
			}
		}

		if ( ! $wl_admin || $wl_pro ) {
			return array( 'visible' => false );
		}

		$affiliates = array();
		$service    = seopress_get_service( 'PromotionService' );
		if ( method_exists( $service, 'getAffiliatePartners' ) ) {
			$affiliates = $service->getAffiliatePartners();
		}

		if ( empty( $affiliates ) ) {
			return array( 'visible' => false );
		}

		$items = array();
		foreach ( $affiliates as $affiliate ) {
			$items[] = array(
				'id'              => isset( $affiliate['id'] ) ? (string) $affiliate['id'] : '',
				'name'            => isset( $affiliate['name'] ) ? (string) $affiliate['name'] : '',
				'description'     => isset( $affiliate['description'] ) ? (string) $affiliate['description'] : '',
				'url'             => isset( $affiliate['url'] ) ? (string) $affiliate['url'] : '',
				'icon'            => isset( $affiliate['icon'] ) ? (string) $affiliate['icon'] : '',
				'logoUrl'         => isset( $affiliate['logo_url'] ) ? (string) $affiliate['logo_url'] : '',
				'backgroundColor' => isset( $affiliate['styling']['background_color'] ) ? (string) $affiliate['styling']['background_color'] : '',
				'textColor'       => isset( $affiliate['styling']['text_color'] ) ? (string) $affiliate['styling']['text_color'] : '',
			);
		}

		return array(
			'visible'  => true,
			'title'    => __( 'Recommended Partners', 'wp-seopress' ),
			'subtitle' => __( 'Trusted tools to enhance your website performance.', 'wp-seopress' ),
			'ctaLabel' => __( 'Learn more', 'wp-seopress' ),
			'items'    => $items,
		);
	}

	/**
	 * Site overview payload.
	 *
	 * The free plugin only renders the chrome (title, period selector, tab
	 * strip, body). PRO injects tab renderers through the React extension
	 * registry (`registerOverviewTab`). The free section stays hidden
	 * until at least one tab is registered.
	 *
	 * @return array
	 */
	private function getOverviewPayload() {
		return array(
			'title' => __( 'Site overview', 'wp-seopress' ),
		);
	}

	/**
	 * Whether a dashboard block was hidden through the header "Display"
	 * panel.
	 *
	 * Reads the exact pre-existing SEOPress options DashboardDisplayPreference
	 * persists to — no new option is introduced: the notice toggles live in
	 * `seopress_notices`, the two appearance toggles in
	 * `seopress_advanced_option_name`. A stored '1' means "hide".
	 *
	 * @param string $store 'notice' for seopress_notices, anything else for
	 *                      seopress_advanced_option_name.
	 * @param string $key   The option array key.
	 * @return bool
	 */
	private function isBlockHidden( $store, $key ) {
		$option = 'notice' === $store ? 'seopress_notices' : 'seopress_advanced_option_name';
		$values = get_option( $option, array() );

		return is_array( $values ) && isset( $values[ $key ] ) && '1' === (string) $values[ $key ];
	}

	/**
	 * Per-section "hidden by the Display panel" state.
	 *
	 * Keyed by the React section id consumed by the useBlockHidden() hook.
	 * Values are read from the same pre-existing SEOPress options the
	 * DashboardDisplayPreference REST route writes to, so toggling a
	 * preference flips the matching block live without a page reload.
	 *
	 * @return array<string,bool>
	 */
	private function getHiddenBlocks() {
		return array(
			'get-started'   => $this->isBlockHidden( 'notice', 'notice-get-started' ),
			'tasks'         => $this->isBlockHidden( 'notice', 'notice-tasks' ),
			'integrations'  => $this->isBlockHidden( 'notice', 'notice-integrations' ),
			// Promotions (Recommended Partners) are always visible — users can no
			// longer hide them. The old `notice-promotions` option is ignored.
			'promotions'    => false,
			'site-overview' => $this->isBlockHidden( 'advanced', 'seopress_advanced_appearance_seo_tools' ),
			'notifications' => $this->isBlockHidden( 'advanced', 'seopress_advanced_appearance_notifications' ),
		);
	}

	/**
	 * SEO Improvements payload.
	 *
	 * Maps the existing Notifications service output to a flat list the
	 * React section can render as an accordion. We keep the impact key so
	 * the UI can color the badge dot, and pass through the action link so
	 * each tip ships a "Fix now" CTA when relevant.
	 *
	 * @return array
	 */
	private function getImprovementsPayload() {
		$visible = ! ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER );
		if ( ! $visible ) {
			return array( 'visible' => false );
		}

		$service = seopress_get_service( 'Notifications' );
		if ( ! method_exists( $service, 'generateAllNotifications' ) ) {
			return array( 'visible' => false );
		}

		$generated = $service->generateAllNotifications();
		if ( ! is_array( $generated ) ) {
			return array( 'visible' => false );
		}

		$raw = array();
		foreach ( $generated as $key => $entry ) {
			// `generateAllNotifications()` appends an `$args['impact']`
			// summary at the end of the array (counts per severity). It
			// is not a notification — skip it explicitly so it doesn't
			// leak into the React list as an empty card.
			if ( 'impact' === $key ) {
				continue;
			}
			if ( ! is_array( $entry ) ) {
				continue;
			}
			if ( isset( $entry['status'] ) && false === $entry['status'] ) {
				continue;
			}
			if ( empty( $entry['title'] ) ) {
				continue;
			}
			$raw[] = $entry;
		}

		if ( method_exists( $service, 'orderByImpact' ) ) {
			$raw = $service->orderByImpact( $raw );
		}

		$items = array();
		foreach ( $raw as $entry ) {
			$impact_key = '';
			if ( ! empty( $entry['impact'] ) && is_array( $entry['impact'] ) ) {
				$impact_key = (string) array_key_first( $entry['impact'] );
			}

			$link_url      = '';
			$link_label    = '';
			$link_external = false;
			if ( ! empty( $entry['link'] ) && is_array( $entry['link'] ) ) {
				$link_url      = isset( $entry['link']['en'] ) ? (string) $entry['link']['en'] : '';
				$link_label    = isset( $entry['link']['title'] ) ? (string) $entry['link']['title'] : '';
				$link_external = ! empty( $entry['link']['external'] );
			}

			$items[] = array(
				'id'           => isset( $entry['id'] ) ? sanitize_key( $entry['id'] ) : '',
				// React renders title/desc via dangerouslySetInnerHTML so they can
				// keep inline formatting (links, <strong>). wp_kses_post() at the
				// boundary closes the XSS path without changing the rendered look.
				'title'        => isset( $entry['title'] ) ? wp_kses_post( (string) $entry['title'] ) : '',
				'desc'         => isset( $entry['desc'] ) ? wp_kses_post( (string) $entry['desc'] ) : '',
				'impact'       => $impact_key,
				'linkUrl'      => esc_url_raw( $link_url ),
				'linkLabel'    => sanitize_text_field( $link_label ),
				'linkExternal' => (bool) $link_external,
				'deleteable'   => ! empty( $entry['deleteable'] ),
			);
		}

		return array(
			'visible'   => true,
			'title'     => __( 'SEO Improvements needed', 'wp-seopress' ),
			'subtitle'  => __( 'Quick reminders to boost your rankings.', 'wp-seopress' ),
			'items'     => $items,
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'hideNonce' => wp_create_nonce( 'seopress_hide_notices_nonce' ),
		);
	}

	/**
	 * Default group definitions for the dashboard features list.
	 *
	 * Extensions can add or rename groups via the `seopress_features_list_groups`
	 * filter. Each entry must declare an `id` and `label`.
	 *
	 * @return array
	 */
	private function getFeatureGroups() {
		$groups = array(
			array(
				'id'    => 'content',
				'label' => __( 'Content', 'wp-seopress' ),
			),
			array(
				'id'    => 'technical',
				'label' => __( 'Technical', 'wp-seopress' ),
			),
			array(
				'id'    => 'data-tracking',
				'label' => __( 'Data & Tracking', 'wp-seopress' ),
			),
		);

		return apply_filters( 'seopress_features_list_groups', $groups );
	}

	/**
	 * Default group per built-in feature.
	 *
	 * @return array
	 */
	private function getDefaultGroupMap() {
		return array(
			'titles'           => 'content',
			'social'           => 'content',
			'advanced'         => 'content',
			'xml-sitemap'      => 'technical',
			'instant-indexing' => 'technical',
			'tools'            => 'technical',
			'google-analytics' => 'data-tracking',
		);
	}

	/**
	 * Whether the current admin screen is the SEOPress dashboard.
	 *
	 * @return bool
	 */
	private function isDashboardPage() {
		if ( ! is_admin() ) {
			return false;
		}

		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		return self::PAGE_SLUG === sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Build the feature list payload consumed by the React FeaturesList section.
	 *
	 * Mirrors the data assembled by inc/admin/blocks/features-list.php so that
	 * extensions hooking into seopress_features_list_before_tools and
	 * seopress_features_list_after_tools (notably SEOPress PRO) keep working.
	 *
	 * @return array
	 */
	private function getFeatures() {
		$features = array(
			'titles'           => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-titles-metas.svg',
				'title'       => __( 'Titles & Metas', 'wp-seopress' ),
				'desc'        => __( 'Manage all your titles & metas for post types, taxonomies, archives...', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-titles' ),
				'filter'      => 'seopress_remove_feature_titles',
			),
			'xml-sitemap'      => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-sitemaps.svg',
				'title'       => __( 'XML & HTML Sitemaps', 'wp-seopress' ),
				'desc'        => __( 'Manage your XML - Image - Video - HTML Sitemap.', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-xml-sitemap' ),
				'filter'      => 'seopress_remove_feature_xml_sitemap',
			),
			'social'           => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-social-networks.svg',
				'title'       => __( 'Social Networks', 'wp-seopress' ),
				'desc'        => __( 'Open Graph, X Cards, Google Knowledge Graph and more...', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-social' ),
				'filter'      => 'seopress_remove_feature_social',
			),
			'google-analytics' => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-analytics.svg',
				'title'       => __( 'Analytics', 'wp-seopress' ),
				'desc'        => __( 'Track everything about your visitors with Google Analytics / Matomo / Microsoft Clarity.', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-google-analytics' ),
				'filter'      => 'seopress_remove_feature_google_analytics',
			),
			'instant-indexing' => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-instant-indexing.svg',
				'title'       => __( 'Instant Indexing', 'wp-seopress' ),
				'desc'        => __( 'Ping Google & Bing to quickly index your content.', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-instant-indexing' ),
				'filter'      => 'seopress_remove_feature_instant_indexing',
			),
			'advanced'         => array(
				'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-advanced.svg',
				'title'       => __( 'Image SEO & Advanced settings', 'wp-seopress' ),
				'desc'        => __( 'Optimize your images for SEO. Configure advanced settings.', 'wp-seopress' ),
				'btn_primary' => admin_url( 'admin.php?page=seopress-advanced' ),
				'filter'      => 'seopress_remove_feature_advanced',
			),
		);

		$features = apply_filters( 'seopress_features_list_before_tools', $features );

		$features['tools'] = array(
			'svg'         => SEOPRESS_URL_ASSETS . '/img/ico-tools.svg',
			'title'       => __( 'Tools', 'wp-seopress' ),
			'desc'        => __( 'Import/Export plugin settings from site to site.', 'wp-seopress' ),
			'btn_primary' => admin_url( 'admin.php?page=seopress-import-export' ),
			'filter'      => 'seopress_remove_feature_tools',
			'toggle'      => false,
		);

		$features = apply_filters( 'seopress_features_list_after_tools', $features );

		$group_map = $this->getDefaultGroupMap();
		$payload   = array();

		foreach ( $features as $key => $value ) {
			if ( isset( $value['filter'] ) ) {
				$keep = apply_filters( $value['filter'], true );
				if ( true !== $keep ) {
					continue;
				}
			}

			$toggle  = isset( $value['toggle'] ) ? (bool) $value['toggle'] : true;
			$enabled = $toggle ? '1' === (string) seopress_get_toggle_option( $key ) : true;

			// Extensions can override the group per feature by setting a `group`
			// key on the feature array; fall back to the built-in map, and to
			// "content" for anything still unassigned.
			$group = isset( $value['group'] ) ? (string) $value['group'] : '';
			if ( '' === $group && isset( $group_map[ $key ] ) ) {
				$group = $group_map[ $key ];
			}
			if ( '' === $group ) {
				$group = 'content';
			}

			$payload[] = array(
				'key'         => (string) $key,
				'title'       => isset( $value['title'] ) ? (string) $value['title'] : '',
				'description' => isset( $value['desc'] ) ? (string) $value['desc'] : '',
				'iconUrl'     => isset( $value['svg'] ) ? (string) $value['svg'] : '',
				'btnPrimary'  => isset( $value['btn_primary'] ) ? (string) $value['btn_primary'] : '',
				'hasToggle'   => $toggle,
				'enabled'     => $enabled,
				'group'       => $group,
			);
		}

		return $this->orderPayload( $payload );
	}

	/**
	 * Explicit display order of features inside each category.
	 *
	 * The free plugin only owns the order of its own keys; PRO replaces the
	 * whole sequence (free + PRO keys interleaved) through the
	 * `seopress_features_list_order` filter. Keys absent from a group's list
	 * fall to the end of that group, in their original insertion order.
	 *
	 * @return array<string,string[]> Ordered feature keys keyed by group id.
	 */
	private function getFeatureOrder() {
		$order = array(
			'content'       => array( 'titles', 'advanced', 'social' ),
			'technical'     => array( 'instant-indexing', 'xml-sitemap', 'tools' ),
			'data-tracking' => array( 'google-analytics' ),
		);

		return apply_filters( 'seopress_features_list_order', $order );
	}

	/**
	 * Sort the payload by category, then by the explicit per-group order,
	 * with a stable fallback on the original insertion index so unlisted
	 * keys keep a deterministic position at the end of their group.
	 *
	 * @param array $payload The unsorted feature payload.
	 * @return array The sorted feature payload.
	 */
	private function orderPayload( $payload ) {
		$order      = $this->getFeatureOrder();
		$group_rank = array();
		foreach ( array_values( $this->getFeatureGroups() ) as $gi => $g ) {
			if ( isset( $g['id'] ) ) {
				$group_rank[ $g['id'] ] = $gi;
			}
		}

		foreach ( $payload as $i => $unused ) {
			$payload[ $i ]['_idx'] = $i;
		}

		usort(
			$payload,
			function ( $a, $b ) use ( $order, $group_rank ) {
				$ga = isset( $group_rank[ $a['group'] ] ) ? $group_rank[ $a['group'] ] : PHP_INT_MAX;
				$gb = isset( $group_rank[ $b['group'] ] ) ? $group_rank[ $b['group'] ] : PHP_INT_MAX;
				if ( $ga !== $gb ) {
					return $ga <=> $gb;
				}

				$la = isset( $order[ $a['group'] ] ) ? $order[ $a['group'] ] : array();
				$lb = isset( $order[ $b['group'] ] ) ? $order[ $b['group'] ] : array();
				$pa = array_search( $a['key'], $la, true );
				$pb = array_search( $b['key'], $lb, true );
				$pa = ( false === $pa ) ? PHP_INT_MAX : $pa;
				$pb = ( false === $pb ) ? PHP_INT_MAX : $pb;
				if ( $pa !== $pb ) {
					return $pa <=> $pb;
				}

				return $a['_idx'] <=> $b['_idx'];
			}
		);

		foreach ( $payload as $i => $unused ) {
			unset( $payload[ $i ]['_idx'] );
		}

		return $payload;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Enqueue the React-powered SEOPress admin header on every SEOPress page.
 *
 * Replaces the markup previously echoed by seopress_admin_header(). The PHP
 * function now outputs only the mount point and a hidden payload; this
 * module ships the React bundle that renders the breadcrumb, activity panel
 * trigger, and slide-in drawer (Help / Display / Notifications).
 *
 * @since 9.9.0
 */
class ModuleAdminHeader implements ExecuteHooks {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the React admin header bundle on every SEOPress admin page.
	 *
	 * @return void
	 */
	public function enqueue() {
		$page = $this->getCurrentSeopressPage();
		if ( ! $page ) {
			return;
		}

		// White-label admin header hides the whole chrome.
		if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
			return;
		}

		$asset_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/header.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array(
			'dependencies' => array( 'react', 'react-dom', 'wp-components', 'wp-element', 'wp-i18n' ),
			'version'      => SEOPRESS_VERSION,
		);

		wp_enqueue_style( 'wp-components' );

		$css_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/header.css';
		if ( file_exists( $css_file ) ) {
			$css_version = SEOPRESS_VERSION;
			if ( '{VERSION}' === $css_version || empty( $css_version ) ) {
				$css_version = substr( md5_file( $css_file ), 0, 12 );
			}

			wp_enqueue_style(
				'seopress-admin-header',
				SEOPRESS_URL_PUBLIC . '/admin/header.css',
				array( 'wp-components' ),
				$css_version
			);
		}

		wp_enqueue_script(
			'seopress-admin-header',
			SEOPRESS_URL_PUBLIC . '/admin/header.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( 'seopress-admin-header', 'wp-seopress', WP_LANG_DIR . '/plugins' );

		wp_localize_script(
			'seopress-admin-header',
			'SEOPRESS_HEADER_DATA',
			array(
				'REST_URL'      => rest_url(),
				'NONCE'         => wp_create_nonce( 'wp_rest' ),
				'ADMIN_URL'     => admin_url(),
				'ASSETS_URL'    => SEOPRESS_URL_PUBLIC,
				'PLUGIN_URL'    => SEOPRESS_PLUGIN_DIR_URL,
				'BREADCRUMB'    => $this->getBreadcrumb( $page ),
				'IS_DASHBOARD'  => 'seopress-option' === $page,
				'HELP'          => $this->getHelpPayload(),
				'DISPLAY'       => 'seopress-option' === $page ? $this->getDisplayPayload() : null,
				'NOTIFICATIONS' => $this->getNotificationsPayload(),
				'TOP_BANNER'    => $this->getTopBannerPayload(),
			)
		);
	}

	/**
	 * Get the current SEOPress admin page slug, or false when not on one.
	 *
	 * @return string|false
	 */
	private function getCurrentSeopressPage() {
		if ( ! is_admin() ) {
			return false;
		}

		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		$page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' === $page || false === strpos( $page, 'seopress' ) ) {
			return false;
		}

		return $page;
	}

	/**
	 * Build the breadcrumb payload — "SEO / <page title>".
	 *
	 * @param string $page Current page slug.
	 * @return array
	 */
	private function getBreadcrumb( $page ) {
		$page_title = get_admin_page_title();

		return array(
			'rootLabel'   => __( 'SEO', 'wp-seopress' ),
			'rootUrl'     => admin_url( 'admin.php?page=seopress-option' ),
			'currentPage' => is_string( $page_title ) ? $page_title : '',
		);
	}

	/**
	 * Help panel payload — docs links, products and support.
	 *
	 * Mirrors the data the legacy admin-header used to render in PHP.
	 *
	 * @return array
	 */
	private function getHelpPayload() {
		// White-label "remove help links" (PRO): legacy hid .seopress-help /
		// .seopress-doc via CSS, which doesn't match the React header. Mirror
		// the option here so the Help button + panel are dropped entirely.
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )
			&& function_exists( 'seopress_pro_get_service' )
			&& method_exists( seopress_pro_get_service( 'OptionPro' ), 'getWhiteLabelHelpLinks' )
			&& '1' === seopress_pro_get_service( 'OptionPro' )->getWhiteLabelHelpLinks() ) {
			return null;
		}

		$docs = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();

		$guides = array();
		if ( ! empty( $docs['get_started'] ) && is_array( $docs['get_started'] ) ) {
			foreach ( $docs['get_started'] as $guide ) {
				if ( empty( $guide['link'] ) || empty( $guide['title'] ) || empty( $guide['desc'] ) || empty( $guide['ico'] ) ) {
					continue;
				}
				$guides[] = array(
					'iconUrl' => SEOPRESS_ASSETS_DIR . '/img/' . $guide['ico'] . '.svg',
					'title'   => (string) $guide['title'],
					'desc'    => (string) $guide['desc'],
					'link'    => (string) $guide['link'],
				);
			}
		}

		return array(
			'searchUrl'       => isset( $docs['website'] ) ? (string) $docs['website'] : '',
			'guides'          => $guides,
			'products'        => array(
				array(
					'label'   => 'SEOPress Free',
					'url'     => isset( $docs['support-free'] ) ? (string) $docs['support-free'] : '',
					'logoUrl' => SEOPRESS_ASSETS_DIR . '/img/support-seopress-free.svg',
				),
				array(
					'label'   => 'SEOPress PRO',
					'url'     => isset( $docs['support-pro'] ) ? (string) $docs['support-pro'] : '',
					'logoUrl' => SEOPRESS_ASSETS_DIR . '/img/support-seopress-pro.svg',
				),
				array(
					'label'   => 'SEOPress Insights',
					'url'     => isset( $docs['support-insights'] ) ? (string) $docs['support-insights'] : '',
					'logoUrl' => SEOPRESS_ASSETS_DIR . '/img/support-seopress-insights.svg',
				),
			),
			'translationsUrl' => isset( $docs['i18n'] ) ? (string) $docs['i18n'] : '',
			'updatesUrl'      => admin_url( 'update-core.php' ),
			'supportUrl'      => isset( $docs['support-tickets'] ) ? (string) $docs['support-tickets'] : '',
		);
	}

	/**
	 * Display panel payload — current state of the six dashboard
	 * "Hide ..." toggles plus a REST nonce.
	 *
	 * @return array
	 */
	private function getDisplayPayload() {
		$notice_option = seopress_get_service( 'NoticeOption' );
		$advanced      = get_option( 'seopress_advanced_option_name' );
		if ( ! is_array( $advanced ) ) {
			$advanced = array();
		}

		$bool = static function ( $value ) {
			return '1' === (string) $value;
		};

		return array(
			'toggles' => array(
				array(
					'key'     => 'notice-get-started',
					'label'   => __( 'Hide Get started?', 'wp-seopress' ),
					'checked' => $bool( $notice_option->getNoticeGetStarted() ),
				),
				array(
					'key'     => 'notice-tasks',
					'label'   => __( 'Hide SEO Suite?', 'wp-seopress' ),
					'checked' => $bool( $notice_option->getNoticeTasks() ),
				),
				array(
					'key'     => 'seopress-advanced-seo-tools',
					'label'   => __( 'Hide Site Overview?', 'wp-seopress' ),
					'checked' => $bool( isset( $advanced['seopress_advanced_appearance_seo_tools'] ) ? $advanced['seopress_advanced_appearance_seo_tools'] : '' ),
				),
				array(
					'key'     => 'seopress-advanced-notifications',
					'label'   => __( 'Hide Notifications Center?', 'wp-seopress' ),
					'checked' => $bool( isset( $advanced['seopress_advanced_appearance_notifications'] ) ? $advanced['seopress_advanced_appearance_notifications'] : '' ),
				),
				array(
					'key'     => 'notice-integrations',
					'label'   => __( 'Hide Integrations?', 'wp-seopress' ),
					'checked' => $bool( $notice_option->getNoticeIntegrations() ),
				),
			),
		);
	}

	/**
	 * Top banner promotion payload.
	 *
	 * Mirrors inc/admin/promotions/top-banner.php. The promotion comes from
	 * PromotionService->getPromotion('top_banner'). Returns null when there
	 * is nothing to render (white-label, no promo, or service missing).
	 *
	 * @return array|null
	 */
	private function getTopBannerPayload() {
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			$toggle_service = seopress_get_service( 'ToggleOption' );
			if ( method_exists( $toggle_service, 'getToggleWhiteLabel' ) && '1' === $toggle_service->getToggleWhiteLabel() ) {
				return null;
			}
		}

		$service = seopress_get_service( 'PromotionService' );
		if ( ! method_exists( $service, 'getPromotion' ) ) {
			return null;
		}

		$promotion = $service->getPromotion( 'top_banner' );
		if ( ! $promotion ) {
			return null;
		}

		$content = isset( $promotion['content'] ) && is_array( $promotion['content'] ) ? $promotion['content'] : array();

		return array(
			'id'              => isset( $promotion['id'] ) ? (string) $promotion['id'] : '',
			'icon'            => isset( $content['icon'] ) ? (string) $content['icon'] : '',
			'title'           => isset( $content['title'] ) ? (string) $content['title'] : '',
			'body'            => isset( $content['body'] ) ? (string) $content['body'] : '',
			'ctaUrl'          => isset( $content['cta_url'] ) ? (string) $content['cta_url'] : '',
			'ctaText'         => isset( $content['cta_text'] ) ? (string) $content['cta_text'] : '',
			'buttonStyle'     => isset( $promotion['styling']['button_style'] ) ? (string) $promotion['styling']['button_style'] : 'primary',
			'dismissible'     => ! empty( $promotion['dismissible'] ),
			'dismissDuration' => isset( $promotion['dismiss_duration_days'] ) ? (int) $promotion['dismiss_duration_days'] : 30,
			'dismissNonce'    => wp_create_nonce( 'seopress_dismiss_promotion_nonce' ),
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		);
	}

	/**
	 * Notifications panel payload.
	 *
	 * Renders each notification server-side via the existing
	 * Notifications->renderNotification() so the rich HTML (icons, CTAs,
	 * severity badges) is preserved without re-implementing the renderer in
	 * React. The React panel injects the strings through
	 * dangerouslySetInnerHTML.
	 *
	 * @return array
	 */
	private function getNotificationsPayload() {
		$service = seopress_get_service( 'Notifications' );

		$severity = $service->getSeverityNotification( 'all' );
		$total    = isset( $severity['total'] ) ? (int) $severity['total'] : 0;

		$items = method_exists( $service, 'generateAllNotifications' ) ? $service->generateAllNotifications() : array();
		$items = method_exists( $service, 'orderByImpact' ) ? $service->orderByImpact( $items ) : $items;

		$active = array();
		$hidden = array();
		if ( is_array( $items ) ) {
			foreach ( $items as $key => $item ) {
				// generateAllNotifications() appends an `impact` summary
				// (counts per severity) at the end of the array. It is not
				// a notification — skip it so it doesn't get rendered as an
				// empty card and inflate the hidden count by one.
				if ( 'impact' === $key ) {
					continue;
				}
				if ( ! is_array( $item ) ) {
					continue;
				}
				if ( empty( $item['title'] ) ) {
					continue;
				}
				$html = method_exists( $service, 'renderNotification' ) ? (string) $service->renderNotification( $item ) : '';
				if ( '' === $html ) {
					continue;
				}
				if ( ! empty( $item['status'] ) ) {
					$active[] = $html;
				} else {
					$hidden[] = $html;
				}
			}
		}

		return array(
			'total'         => $total,
			'activeItems'   => $active,
			'hiddenItems'   => $hidden,
			'emptyIconUrl'  => SEOPRESS_ASSETS_DIR . '/img/ico-notifications.svg',
			'hiddenIconUrl' => SEOPRESS_ASSETS_DIR . '/img/ico-notifications-hidden.svg',
			'hideNonce'     => wp_create_nonce( 'seopress_hide_notices_nonce' ),
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		);
	}
}

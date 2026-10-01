<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\PagesAdmin;

/**
 * Custom capabilities
 *
 * Implements ExecuteHooks (not ExecuteHooksBackend) so that the
 * `seopress_capability` filter is also registered on REST API and CLI
 * requests, where `is_admin()` is false. Without this, REST permission
 * callbacks calling `seopress_capability( 'manage_options', $context )`
 * would fall through to the unfiltered `manage_options` cap and reject
 * non-admin roles that legitimately have the custom seopress_manage_*
 * caps. Admin-only side effects (option_page_capability_* save filters
 * and the role-cap sync on init) stay gated behind is_admin() below.
 */
class CustomCapabilities implements ExecuteHooks {

	/**
	 * The CustomCapabilities hooks.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'pre_update_option_seopress_advanced_option_name', array( $this, 'preserve_explicit_revocations' ), 10, 2 );
		add_action( 'update_option_seopress_advanced_option_name', array( $this, 'addCapabilities' ) );
		add_action( 'add_option_seopress_advanced_option_name', array( $this, 'addCapabilities' ) );
		add_action( 'delete_option', array( $this, 'revoke_managed_capabilities_on_reset' ) );

		if ( '1' !== seopress_get_toggle_option( 'advanced' ) ) {
			return;
		}

		// Capability translation filter must be available on every request
		// type (admin pages, REST API, frontend caps checks). The cost is
		// negligible: a single add_filter() call per request.
		add_filter( 'seopress_capability', array( $this, 'custom' ), 9999, 2 );

		if ( ! is_admin() ) {
			return;
		}

		// Admin-only side effects: settings-API save capability remapping
		// and one-shot role/capability synchronisation on init.
		add_filter( 'option_page_capability_seopress_titles_option_group', array( $this, 'capabilitySaveTitlesMetas' ) );
		add_filter( 'option_page_capability_seopress_xml_sitemap_option_group', array( $this, 'capabilitySaveXmlSitemap' ) );
		add_filter( 'option_page_capability_seopress_social_option_group', array( $this, 'capabilitySaveSocial' ) );
		add_filter( 'option_page_capability_seopress_google_analytics_option_group', array( $this, 'capabilitySaveAnalytics' ) );
		add_filter( 'option_page_capability_seopress_instant_indexing_option_group', array( $this, 'capabilitySaveInstantIndexing' ) );
		add_filter( 'option_page_capability_seopress_advanced_option_group', array( $this, 'capabilitySaveAdvanced' ) );
		add_filter( 'option_page_capability_seopress_tools_option_group', array( $this, 'capabilitySaveTools' ) );
		add_filter( 'option_page_capability_seopress_import_export_option_group', array( $this, 'capabilitySaveImportExport' ) );

		add_filter( 'option_page_capability_seopress_pro_mu_option_group', array( $this, 'capabilitySavePro' ) );
		add_filter( 'option_page_capability_seopress_pro_option_group', array( $this, 'capabilitySavePro' ) );
		add_filter( 'option_page_capability_seopress_bot_option_group', array( $this, 'capabilitySaveBot' ) );

		add_action( 'init', array( $this, 'addCapabilities' ) );
	}

	/**
	 * Add capabilities.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function addCapabilities() {
		$roles = wp_roles();
		$pages = PagesAdmin::getPages();

		if ( isset( $roles->role_objects['administrator'] ) ) {
			$role = $roles->role_objects['administrator'];
			foreach ( $pages as $value ) {
				$role->add_cap( \sprintf( 'seopress_manage_%s', $value ), true );
			}
		}

		$this->sync_configured_roles( seopress_get_service( 'AdvancedOption' )->getOption() );
	}

	/** Apply only areas explicitly managed through the settings. */
	private function sync_configured_roles( $options ) {
		if ( ! is_array( $options ) ) {
			return;
		}
		foreach ( PagesAdmin::getPages() as $area ) {
			$page = PagesAdmin::getPageByCapability( $area );
			$capability = PagesAdmin::getCapabilityByPage( $page );
			$key = 'seopress_advanced_security_metaboxe_' . $page;
			if ( null === $capability || ! array_key_exists( $key, $options ) ) {
				continue;
			}
			$selected = is_array( $options[ $key ] ) ? $options[ $key ] : array();
			foreach ( wp_roles()->role_objects as $name => $role ) {
				if ( 'administrator' === $name ) {
					continue;
				}
				if ( isset( $selected[ $name ] ) && in_array( $selected[ $name ], array( '1', 1, true ), true ) ) {
					$role->add_cap( 'seopress_manage_' . $capability, true );
				} else {
					$role->remove_cap( 'seopress_manage_' . $capability );
				}
			}
		}
	}

	/**
	 * A cleared legacy checkbox group disappears from the submitted array.
	 * Keep that explicit revocation distinct from an area never configured here.
	 *
	 * @param mixed $options New settings.
	 * @param mixed $previous Previously saved settings.
	 * @return mixed
	 */
	public function preserve_explicit_revocations( $options, $previous ) {
		if ( ! is_array( $options ) || ! is_array( $previous ) ) {
			return $options;
		}
		foreach ( PagesAdmin::getPages() as $area ) {
			$page = PagesAdmin::getPageByCapability( $area );
			$key = 'seopress_advanced_security_metaboxe_' . $page;
			if ( null !== PagesAdmin::getCapabilityByPage( $page ) && array_key_exists( $key, $previous ) && ! array_key_exists( $key, $options ) ) {
				$options[ $key ] = array();
			}
		}
		return $options;
	}

	/**
	 * Explicit settings resets revoke only the areas this UI managed.
	 *
	 * @param string $option Option about to be deleted.
	 */
	public function revoke_managed_capabilities_on_reset( $option ) {
		if ( 'seopress_advanced_option_name' !== $option ) {
			return;
		}
		$options = get_option( $option );
		if ( is_array( $options ) ) {
			$this->sync_configured_roles( array_fill_keys( array_keys( $options ), array() ) );
		}
	}

	/**
	 * Custom capabilities.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap     The capability.
	 * @param string $context The context.
	 *
	 * @return string
	 */
	public function custom( $cap, $context ) {
		switch ( $context ) {
			case 'xml_html_sitemap':
			case 'social_networks':
			case 'analytics':
			case 'tools':
			case 'instant_indexing':
			case 'titles_metas':
			case 'advanced':
			case 'pro':
			case 'bot':
				return PagesAdmin::getCustomCapability( $context );
			case 'dashboard':
				$capabilities = array(
					'xml_html_sitemap',
					'social_networks',
					'analytics',
					'tools',
					'instant_indexing',
					'titles_metas',
					'advanced',
					'pro',
					'bot',
				);
				foreach ( $capabilities as $key => $value ) {
					if ( current_user_can( PagesAdmin::getCustomCapability( $value ) ) ) { // phpcs:ignore
						return PagesAdmin::getCustomCapability( $value );
					}
				}

				return $cap;
			default:
				return $cap;
		}
	}

	/**
	 * Capability save titles metas.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveTitlesMetas( $cap ) {
		return PagesAdmin::getCustomCapability( 'titles_metas' );
	}

	/**
	 * Capability save xml sitemap.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveXmlSitemap( $cap ) {
		return PagesAdmin::getCustomCapability( 'xml_html_sitemap' );
	}

	/**
	 * Capability save social.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveSocial( $cap ) {
		return PagesAdmin::getCustomCapability( 'social_networks' );
	}

	/**
	 * Capability save analytics.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveAnalytics( $cap ) {
		return PagesAdmin::getCustomCapability( 'analytics' );
	}

	/**
	 * Capability save advanced.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveAdvanced( $cap ) {
		return PagesAdmin::getCustomCapability( 'advanced' );
	}

	/**
	 * Capability save tools.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveTools( $cap ) {
		return PagesAdmin::getCustomCapability( 'tools' );
	}

	/**
	 * Capability save instant indexing.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveInstantIndexing( $cap ) {
		return PagesAdmin::getCustomCapability( 'instant_indexing' );
	}

	/**
	 * Capability save import export.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveImportExport( $cap ) {
		return PagesAdmin::getCustomCapability( 'tools' );
	}

	/**
	 * Capability save pro.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySavePro( $cap ) {
		return PagesAdmin::getCustomCapability( 'pro' );
	}

	/**
	 * Capability save bot.
	 *
	 * @since 4.6.0
	 *
	 * @param string $cap The capability.
	 *
	 * @return string
	 */
	public function capabilitySaveBot( $cap ) {
		return PagesAdmin::getCustomCapability( 'bot' );
	}
}

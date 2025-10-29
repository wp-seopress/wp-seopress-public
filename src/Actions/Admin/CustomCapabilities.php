<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Helpers\PagesAdmin;

/**
 * Custom capabilities
 */
class CustomCapabilities implements ExecuteHooksBackend {

	/**
	 * The CustomCapabilities hooks.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( '1' === seopress_get_toggle_option( 'advanced' ) ) {
			add_filter( 'seopress_capability', array( $this, 'custom' ), 9999, 2 );
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

		$options = seopress_get_service( 'AdvancedOption' )->getOption();
		if ( ! $options ) {
			return;
		}
		$needle = 'seopress_advanced_security_metaboxe';

		foreach ( $pages as $key => $page_value ) {
			$page_for_capability = PagesAdmin::getPageByCapability( $page_value );
			$capability          = PagesAdmin::getCapabilityByPage( $page_for_capability );

			$option_key = sprintf( '%s_%s', $needle, $page_for_capability );
			if ( ! \array_key_exists( $option_key, $options ) ) {
				// Remove all cap for a specific role if option not set.
				foreach ( $roles->role_objects as $key_role => $role ) {
					if ( 'administrator' === $key_role ) {
						continue;
					}

					if ( null === $capability ) {
						continue;
					}

					$role->remove_cap( \sprintf( 'seopress_manage_%s', $capability ) );
				}
			} else {
				foreach ( $roles->role_objects as $key_role => $role ) {
					if ( ! \array_key_exists( $role->name, $options[ $option_key ] ) && 'administrator' !== $key_role ) {
						$role->remove_cap( \sprintf( 'seopress_manage_%s', $capability ) );
					} else {
						$role->add_cap( \sprintf( 'seopress_manage_%s', $capability ), true );
					}
				}
			}
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

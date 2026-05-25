<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * REST endpoint for toggling SEOPress feature modules from the React dashboard.
 *
 * Behaviour-compatible replacement for the legacy
 * `wp_ajax_seopress_toggle_features` AJAX action. The AJAX action stays
 * registered for any third-party code that may still call it directly.
 *
 * @since 9.9.0
 */
class DashboardFeatureToggle implements ExecuteHooks {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Permission check — must hold the dashboard capability.
	 *
	 * @return bool
	 */
	public function permissionCheck() {
		return current_user_can( seopress_capability( 'manage_options', 'dashboard' ) );
	}

	/**
	 * Register the REST route.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'seopress/v1',
			'/dashboard/feature-toggle',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'process' ),
				'permission_callback' => array( $this, 'permissionCheck' ),
				'args'                => array(
					'feature' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'enabled' => array(
						'required' => true,
						'type'     => 'boolean',
					),
				),
			)
		);
	}

	/**
	 * Persist the new toggle state and flush rewrite rules when relevant.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 *
	 * @return \WP_REST_Response
	 */
	public function process( \WP_REST_Request $request ) {
		$feature = (string) $request->get_param( 'feature' );
		$enabled = (bool) $request->get_param( 'enabled' );

		// The React payload uses bare feature slugs ("titles", "xml-sitemap").
		// The seopress_toggle option is keyed with a "toggle-" prefix to match
		// what the PHP-rendered features-list block has always written, so we
		// re-add the prefix here. Accept both forms defensively to keep any
		// extension that may already send the prefixed key working.
		$option_key = 0 === strpos( $feature, 'toggle-' ) ? $feature : 'toggle-' . $feature;

		if ( 'toggle-universal-metabox' === $option_key ) {
			// Since 9.8.0 the universal metabox is always-on; the only related
			// surface that can still be toggled is the frontend SEO beacon.
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name' );
			if ( ! is_array( $seopress_advanced_option_name ) ) {
				$seopress_advanced_option_name = array();
			}

			$seopress_advanced_option_name['seopress_advanced_appearance_universal_metabox_disable_frontend'] = $enabled ? '0' : '1';
			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );

			return new \WP_REST_Response(
				array(
					'feature' => $feature,
					'enabled' => $enabled,
				)
			);
		}

		$seopress_toggle_options = get_option( 'seopress_toggle', array() );
		if ( ! is_array( $seopress_toggle_options ) ) {
			$seopress_toggle_options = array();
		}

		$seopress_toggle_options[ $option_key ] = $enabled ? '1' : '0';
		update_option( 'seopress_toggle', $seopress_toggle_options, false );

		// Mirror the AJAX handler: when a feature with rewrite rules is toggled
		// we must purge stale rules registered at init() with the OLD value
		// and re-register them with the NEW value before flushing.
		if ( 'toggle-xml-sitemap' === $option_key || 'toggle-news' === $option_key ) {
			global $wp_rewrite;

			if ( ! empty( $wp_rewrite->extra_rules_top ) ) {
				foreach ( $wp_rewrite->extra_rules_top as $pattern => $query ) {
					if ( false !== strpos( $query, 'seopress_' ) ) {
						unset( $wp_rewrite->extra_rules_top[ $pattern ] );
					}
				}
			}

			$sitemap_options = get_option( 'seopress_xml_sitemap_option_name' );
			\SEOPress\Actions\Sitemap\Router::registerRewriteRules( $sitemap_options, $seopress_toggle_options );

			do_action( 'seopress_re_register_sitemap_rules', $sitemap_options, $seopress_toggle_options );

			delete_option( 'rewrite_rules' );
			flush_rewrite_rules( false );
		}

		return new \WP_REST_Response(
			array(
				'feature' => $feature,
				'enabled' => $enabled,
			)
		);
	}
}

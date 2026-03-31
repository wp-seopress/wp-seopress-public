<?php // phpcs:ignore

namespace SEOPress\Actions\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Registers option save hooks that must fire for both admin and REST API contexts.
 *
 * Previously these were registered on admin_init (via admin.php) and therefore
 * did not fire during REST API requests. Moving them to an ExecuteHooks class
 * ensures they are loaded unconditionally by the Kernel.
 */
class OptionSaveHooks implements ExecuteHooks {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'pre_update_option_seopress_instant_indexing_option_name', array( $this, 'preInstantIndexingOption' ), 10, 2 );
		add_action( 'update_option_seopress_xml_sitemap_option_name', array( $this, 'afterSitemapOptionSave' ), 10, 3 );
	}

	/**
	 * Merge-protect Instant Indexing options.
	 *
	 * When saving from SEO PRO Google Search Console tab, we only get the
	 * google_api_key field. We must merge it with the existing option to
	 * avoid losing other indexing settings.
	 *
	 * @param array $new_value New value.
	 * @param array $old_value Old value.
	 * @return array
	 */
	public function preInstantIndexingOption( $new_value, $old_value ) {
		if ( ! is_array( $new_value ) ) {
			return $new_value;
		}

		if ( ! array_key_exists( 'seopress_instant_indexing_bing_api_key', $new_value ) ) {
			$options = get_option( 'seopress_instant_indexing_option_name' );
			if ( is_array( $options ) && isset( $new_value['seopress_instant_indexing_google_api_key'] ) ) {
				$options['seopress_instant_indexing_google_api_key'] = $new_value['seopress_instant_indexing_google_api_key'];
				return $options;
			}
		}

		return $new_value;
	}

	/**
	 * Flush rewrite rules after saving XML sitemaps settings.
	 *
	 * Runs AFTER the option is saved to the database. We manually re-register
	 * rewrite rules because flush_rewrite_rules() uses the rules registered
	 * during init (with the OLD values).
	 *
	 * @param mixed  $old_value Old value.
	 * @param mixed  $new_value New value.
	 * @param string $option    Option name.
	 * @return void
	 */
	public function afterSitemapOptionSave( $old_value, $new_value, $option ) {
		// The new value is already saved to the database at this point.
		// We need to force WordPress to regenerate rewrite rules with the new settings.

		// Clear only SEOPress's own rewrite rules from the in-memory cache.
		// We must NOT clear all rules as that would break other plugins' rewrite rules
		// (REST API, custom post types, etc.).
		global $wp_rewrite;
		if ( ! empty( $wp_rewrite->extra_rules_top ) ) {
			foreach ( $wp_rewrite->extra_rules_top as $pattern => $query ) {
				if ( false !== strpos( $query, 'seopress_' ) ) {
					unset( $wp_rewrite->extra_rules_top[ $pattern ] );
				}
			}
		}

		// Re-register SEOPress sitemap rewrite rules with the NEW settings.
		// flush_rewrite_rules() does not re-fire the init action, so we must
		// manually re-register the rules before flushing.
		$toggle_options = get_option( 'seopress_toggle' );
		\SEOPress\Actions\Sitemap\Router::registerRewriteRules( $new_value, $toggle_options );

		// Allow PRO and extensions to re-register their sitemap rewrite rules
		// (e.g., news.xml, video*.xml) before the flush persists everything.
		do_action( 'seopress_re_register_sitemap_rules', $new_value, $toggle_options );

		// Clear the rewrite rules from the database so they get regenerated.
		delete_option( 'rewrite_rules' );

		// Flush to regenerate and save all rewrite rules.
		flush_rewrite_rules( false );
	}
}

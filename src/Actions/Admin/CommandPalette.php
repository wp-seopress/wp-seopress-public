<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Data\CommandPaletteIndex;

/**
 * Registers SEOPress commands in the native WP command palette (Cmd/Ctrl+K).
 *
 * The bundle is enqueued on every admin page so the palette works from
 * anywhere inside wp-admin, not just the SEOPress screens.
 *
 * @since 9.8.0
 */
class CommandPalette implements ExecuteHooks {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the command palette bundle on every admin page.
	 *
	 * @return void
	 */
	public function enqueue() {
		// Only load for users who can access SEOPress settings.
		if ( ! current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) ) {
			return;
		}

		// Bail if wp-commands handle isn't registered (WP < 6.3).
		if ( ! wp_script_is( 'wp-commands', 'registered' ) ) {
			return;
		}

		$asset_file = SEOPRESS_PLUGIN_DIR_PATH . 'public/admin/command-palette.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		// Ensure wp-commands is among the dependencies.
		$dependencies = isset( $asset['dependencies'] ) ? $asset['dependencies'] : array();
		if ( ! in_array( 'wp-commands', $dependencies, true ) ) {
			$dependencies[] = 'wp-commands';
		}

		wp_enqueue_script(
			'seopress-command-palette',
			SEOPRESS_URL_PUBLIC . '/admin/command-palette.js',
			$dependencies,
			$asset['version'],
			true
		);

		$index = CommandPaletteIndex::all();

		wp_localize_script(
			'seopress-command-palette',
			'SEOPRESS_COMMAND_PALETTE',
			array(
				'ADMIN_URL'  => admin_url(),
				'HOME_URL'   => home_url( '/' ),
				'HAS_PRO'    => defined( 'SEOPRESS_PRO_VERSION' ),
				'REST_NONCE' => wp_create_nonce( 'wp_rest' ),
				'INDEX'      => $index,
				'PREVIEW'    => $this->build_preview_values( $index ),
			)
		);

		wp_set_script_translations( 'seopress-command-palette', 'wp-seopress', WP_LANG_DIR . '/plugins' );
	}

	/**
	 * Collect the current values of the toggle/text settings referenced by the
	 * palette index, so each command can show the live state inline.
	 *
	 * Only reads SEOPress option groups already in memory — no extra queries.
	 *
	 * @param array<int,array<string,mixed>> $index The full command index.
	 * @return array<string,mixed> field_id => current value
	 */
	private function build_preview_values( array $index ) {
		// Collect unique field ids referenced by the index.
		$field_ids = array();
		foreach ( $index as $cmd ) {
			if ( ! empty( $cmd['field'] ) ) {
				$field_ids[ $cmd['field'] ] = true;
			}
		}

		if ( empty( $field_ids ) ) {
			return array();
		}

		$groups = array(
			'seopress_titles_option_name',
			'seopress_xml_sitemap_option_name',
			'seopress_social_option_name',
			'seopress_google_analytics_option_name',
			'seopress_advanced_option_name',
			'seopress_instant_indexing_option_name',
		);

		$preview = array();
		foreach ( $groups as $group ) {
			$options = get_option( $group, array() );
			if ( ! is_array( $options ) ) {
				continue;
			}
			foreach ( $field_ids as $key => $_ ) {
				if ( array_key_exists( $key, $options ) ) {
					$preview[ $key ] = $options[ $key ];
				}
			}
		}

		/**
		 * Filters the preview values shown inline in the command palette.
		 *
		 * Use this to inject preview values for custom settings registered by
		 * Pro or third-party add-ons (stored outside the core option groups).
		 *
		 * @since 9.8.0
		 *
		 * @param array<string,mixed>            $preview Field id => current value.
		 * @param array<int,array<string,mixed>> $index   The command index.
		 */
		return apply_filters( 'seopress_command_palette_preview_values', $preview, $index );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;


/**
 * Advanced Settings
 */
class AdvancedSettings implements ExecuteHooks {
	/**
	 * The Advanced Settings hooks.
	 *
	 * @since 5.0.0
	 */

	/**
	 * The Advanced Settings register.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		register_post_meta(
			'',
			'_seopress_robots_primary_cat',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => array( $this, 'meta_auth' ),
			)
		);
		register_post_meta(
			'',
			'_seopress_titles_title',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => array( $this, 'meta_auth' ),
			)
		);
		register_post_meta(
			'',
			'_seopress_titles_desc',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => array( $this, 'meta_auth' ),
			)
		);
		register_post_meta(
			'',
			'_seopress_robots_index',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => array( $this, 'meta_auth' ),
			)
		);
		// Exposed so Gutenberg's post save (REST POST/PUT to /wp/v2/<type>/<id>)
		// persists the Content analysis tokens written into core/editor by the
		// React metabox. Sanitizer mirrors TargetKeywords::processPut() so the
		// value stored via this path matches the dedicated PUT endpoint and
		// the Classic Editor save_post fallback.
		register_post_meta(
			'',
			'_seopress_analysis_target_kw',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => array( $this, 'sanitize_target_keywords' ),
			)
		);
	}

	/**
	 * Normalize a comma-separated target keywords string: trim each token,
	 * drop empties, rejoin without surrounding spaces, then sanitize as text.
	 *
	 * @param mixed $value Raw value submitted via REST or update_post_meta.
	 *
	 * @return string
	 */
	public function sanitize_target_keywords( $value ) { // phpcs:ignore
		if ( ! is_string( $value ) ) {
			return '';
		}
		$parts = array_filter(
			array_map( 'trim', explode( ',', $value ) ),
			static function ( $token ) {
				return '' !== $token;
			}
		);
		return sanitize_text_field( implode( ',', $parts ) );
	}

	/**
	 * Auth callback is required for protected meta keys.
	 *
	 * @param   bool   $allowed  Is allowed.
	 * @param   string $meta_key The meta key.
	 * @param   int    $id The id.
	 *
	 * @return  bool   $allowed The allowed.
	 */
	public function meta_auth( $allowed, $meta_key, $id ) {
		return current_user_can( 'edit_posts', $id );
	}
}

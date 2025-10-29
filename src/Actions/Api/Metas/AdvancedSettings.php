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

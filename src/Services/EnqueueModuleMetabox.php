<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EnqueueModuleMetabox
 */
class EnqueueModuleMetabox {

	/**
	 * The canEnqueue function.
	 *
	 * @return bool
	 */
	public function canEnqueue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$response = true;

		// WordPress 6.5+ is required for the universal metabox (stable ProgressBar in @wordpress/components).
		global $wp_version;
		if ( version_compare( $wp_version, '6.5', '<' ) ) {
			return false;
		}

		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			$response = false;
		}

		if ( isset( $_GET['seopress_preview'] ) || isset( $_GET['preview'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['post_type'] ) && 'elementor_library' === $_GET['post_type'] ) { // Elementor library page
			$response = false;
		}

		if ( isset( $_GET['oxygen_iframe'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['brickspreview'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['et_bfb'] ) ) { // phpcs:ignore
			$response = false;
		}

		// Page builder preview iframes load editor scripts (Backbone /
		// vendor globals) that conflict with the React beacon when both
		// are enqueued in the same document. These query parameters are
		// only set inside the preview frame, so suppressing here keeps
		// the builder usable while leaving outer builder shells and
		// regular frontend pages governed by the appearance toggle.
		if (
			isset( $_GET['builder_id'] )                                   // phpcs:ignore -- preview iframe
			|| isset( $_GET['fbpreview'] )                                 // phpcs:ignore -- preview iframe (alt)
			|| ( isset( $_GET['builder'] ) && 'true' === $_GET['builder'] ) // phpcs:ignore -- preview iframe
		) {
			$response = false;
		}

		// Avada layout-builder CPT (`fusion_element`). The legacy classic
		// metaboxes are already removed for this post type by
		// seopress_remove_metaboxes() in seopress-functions.php. Apply the
		// same exclusion to the React beacon so editing an Avada layout
		// element doesn't surface an SEO panel that has nowhere to render.
		if ( is_admin() ) {
			$current_post_type = '';
			if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore
				$current_post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore
			} elseif ( isset( $_GET['post'] ) ) { // phpcs:ignore
				$current_post_type = get_post_type( (int) $_GET['post'] ); // phpcs:ignore
			}
			if ( 'fusion_element' === $current_post_type ) {
				$response = false;
			}
		}

		if ( ! is_admin() && ! is_singular() ) {
			$response = false;
		}

		if ( get_the_ID() === (int) get_option( 'page_on_front' ) ) {
			$response = true;
		}

		if ( get_the_ID() === (int) get_option( 'page_for_posts' ) ) {
			$response = true;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$response = false;
		}

		// Compatibility with WooCommerce beta product page.
		if ( isset( $_GET['page'] ) && 'wc-admin' === $_GET['page'] ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['path'] ) && false !== strpos( wp_unslash( $_GET['path'] ), 'product' ) ) {
			$response = true;
		}

		$settings_advanced = seopress_get_service( 'AdvancedOption' );
		$roles_tabs        = array(
			'GLOBAL'           => $settings_advanced->getSecurityMetaboxRole(),
			'CONTENT_ANALYSIS' => $settings_advanced->getSecurityMetaboxRoleContentAnalysis(),
		);

		$user             = wp_get_current_user();
		$roles            = (array) $user->roles;
		$counter_can_edit = 0;

		foreach ( $roles_tabs as $key => $role_tab ) {
			if ( null === $role_tab ) {
				continue;
			}

			$diff = array_diff( $roles, array_keys( $role_tab ) );
			if ( count( $diff ) !== count( $roles ) ) {
				++$counter_can_edit;
			}
		}

		if ( $counter_can_edit >= 2 ) {
			$response = false;
		}

		if ( isset( $_POST['can_enqueue_seopress_metabox'] ) && '1' !== $_POST['can_enqueue_seopress_metabox'] ) { // phpcs:ignore
			$response = false;
		}
		if ( isset( $_POST['can_enqueue_seopress_metabox'] ) && '1' === $_POST['can_enqueue_seopress_metabox'] ) { // phpcs:ignore
			$response = true;
		}

		// Honor the "Hide SEO beacon on frontend" appearance option.
		// Placed last so it overrides the home/blog page_on_front overrides above.
		if ( ! is_admin() && '1' === $settings_advanced->getAppearanceUniversalMetaboxFrontendDisable() ) {
			$response = false;
		}

		return apply_filters( 'seopress_can_enqueue_universal_metabox', $response );
	}
}

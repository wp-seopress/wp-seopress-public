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

		if ( ! current_user_can( $this->getCurrentEditCapability() ) ) {
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

		// The Advanced ▸ Security role restrictions ("SEO metaboxes" and
		// "Content Analysis") are enforced inside the React universal
		// metabox itself: ModuleMetabox localizes USER_ROLES and
		// ROLES_BLOCKED (GLOBAL / CONTENT_ANALYSIS) so the blocked tabs are
		// hidden client-side. We therefore never fall back to the legacy
		// PHP metabox just because a role is checked here — the universal
		// metabox stays loaded regardless of those checkboxes.

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

	/**
	 * Resolve the capability required to edit the post type of the current
	 * edit screen.
	 *
	 * The universal metabox was gated behind the generic `edit_posts`
	 * capability, but post types declaring their own `capability_type`
	 * (WooCommerce `product` -> `edit_products`, and any custom CPT) are
	 * editable by roles that lack `edit_posts`. Those roles wrongly fell
	 * back to the legacy classic metabox. We resolve the post type's
	 * mapped `edit_posts` meta-cap instead, defaulting to the generic
	 * `edit_posts` on term screens, the frontend or when the post type
	 * can't be determined.
	 *
	 * @since 9.9.0
	 *
	 * @return string Capability slug.
	 */
	protected function getCurrentEditCapability() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$post_type = '';

		if ( is_admin() ) {
			if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} elseif ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$post_type = (string) get_post_type( (int) $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} else {
				global $typenow;
				$post_type = is_string( $typenow ) ? $typenow : '';
			}
		}

		if ( '' === $post_type ) {
			return 'edit_posts';
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( null === $post_type_object || ! isset( $post_type_object->cap->edit_posts ) ) {
			return 'edit_posts';
		}

		return $post_type_object->cap->edit_posts;
	}
}

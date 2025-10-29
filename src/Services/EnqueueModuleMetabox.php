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

		global $pagenow;

		if ( 'widgets.php' === $pagenow ) {
			$response = false;
		}

		if ( isset( $_GET['seopress_preview'] ) || isset( $_GET['preview'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['oxygen_iframe'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['fb-edit'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['brickspreview'] ) ) { // phpcs:ignore
			$response = false;
		}

		if ( isset( $_GET['et_bfb'] ) ) { // phpcs:ignore
			$response = false;
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

		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = \get_current_screen();

			if ( $current_screen && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() === false ) {
				$response = false;
			}

			if ( $current_screen && ! seopress_get_service( 'AdvancedOption' )->getAccessUniversalMetaboxGutenberg() && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() !== false ) {
				$response = false;
			}
		}

		if ( seopress_get_service( 'AdvancedOption' )->getDisableUniversalMetaboxGutenberg() ) {
			$response = false;
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

		return apply_filters( 'seopress_can_enqueue_universal_metabox', $response );
	}
}

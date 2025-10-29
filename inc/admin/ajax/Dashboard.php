<?php // phpcs:ignore
/**
 * Dashboard AJAX functions.
 *
 * @package SEOPress
 * @subpackage Ajax
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Dashboard toggle features
 */
function seopress_toggle_features() {
	check_ajax_referer( 'seopress_toggle_features_nonce', '_ajax_nonce', true );

	if ( current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) && is_admin() ) {
		if ( isset( $_POST['feature'] ) && isset( $_POST['feature_value'] ) ) {
			$feature       = sanitize_text_field( wp_unslash( $_POST['feature'] ) );
			$feature_value = sanitize_text_field( wp_unslash( $_POST['feature_value'] ) );

			if ( 'toggle-universal-metabox' === $feature ) {
				$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name' );
				if ( '1' === $_POST['feature_value'] ) {
					$seopress_advanced_option_name['seopress_advanced_appearance_universal_metabox_disable'] = '0';
				} else {
					$seopress_advanced_option_name['seopress_advanced_appearance_universal_metabox_disable'] = '1';
				}
				update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
			} else {
				$seopress_toggle_options             = get_option( 'seopress_toggle' );
				$seopress_toggle_options[ $feature ] = $feature_value;

				// Flush permalinks for XML sitemaps.
				if ( 'toggle-xml-sitemap' === $feature_value ) {
					flush_rewrite_rules( false );
				}

				update_option( 'seopress_toggle', $seopress_toggle_options, false );
			}
		}
		exit();
	}
}
add_action( 'wp_ajax_seopress_toggle_features', 'seopress_toggle_features' );

/**
 * Dashboard Display Panel
 */
function seopress_display() {
	check_ajax_referer( 'seopress_display_nonce', '_ajax_nonce', true );
	if ( current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) && is_admin() ) {
		// Notifications Center.
		if ( isset( $_POST['notifications_center'] ) ) {
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name' );

			if ( '1' === $_POST['notifications_center'] ) {
				$seopress_advanced_option_name['seopress_advanced_appearance_notifications'] = sanitize_text_field( wp_unslash( $_POST['notifications_center'] ) );
			} else {
				unset( $seopress_advanced_option_name['seopress_advanced_appearance_notifications'] );
			}

			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
		}

		// News Panel.
		if ( isset( $_POST['news_center'] ) ) {
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name' );

			if ( '1' === $_POST['news_center'] ) {
				$seopress_advanced_option_name['seopress_advanced_appearance_news'] = sanitize_text_field( wp_unslash( $_POST['news_center'] ) );
			} else {
				unset( $seopress_advanced_option_name['seopress_advanced_appearance_news'] );
			}

			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
		}
		// Tools Panel.
		if ( isset( $_POST['tools_center'] ) ) {
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name' );

			if ( '1' === $_POST['tools_center'] ) {
				$seopress_advanced_option_name['seopress_advanced_appearance_seo_tools'] = sanitize_text_field( wp_unslash( $_POST['tools_center'] ) );
			} else {
				unset( $seopress_advanced_option_name['seopress_advanced_appearance_seo_tools'] );
			}

			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
		}
		exit();
	}
}
add_action( 'wp_ajax_seopress_display', 'seopress_display' );

/**
 * Dashboard hide notices
 */
function seopress_hide_notices() {
	check_ajax_referer( 'seopress_hide_notices_nonce', '_ajax_nonce', true );

	if ( current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) && is_admin() ) {
		if ( isset( $_POST['notice'] ) && isset( $_POST['notice_value'] ) ) {
			$seopress_notices_options = get_option( 'seopress_notices', array() );

			$notice       = sanitize_text_field( wp_unslash( $_POST['notice'] ) );
			$notice_value = sanitize_text_field( wp_unslash( $_POST['notice_value'] ) );

			if ( false !== $notice && false !== $notice_value ) {
				$seopress_notices_options[ $notice ] = $notice_value;
			}
			update_option( 'seopress_notices', $seopress_notices_options, false );
		}
		exit();
	}
}
add_action( 'wp_ajax_seopress_hide_notices', 'seopress_hide_notices' );

/**
 * Dashboard switch view
 */
function seopress_switch_view() {
	check_ajax_referer( 'seopress_switch_view_nonce', '_ajax_nonce', true );

	if ( current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) && is_admin() ) {
		if ( isset( $_POST['view'] ) ) {
			$seopress_dashboard_options = get_option( 'seopress_dashboard', array() );

			$view = sanitize_text_field( wp_unslash( $_POST['view'] ) );

			if ( false !== $view ) {
				$seopress_dashboard_options['view'] = $view;
			}
			update_option( 'seopress_dashboard', $seopress_dashboard_options, false );
		}
		exit();
	}
}
add_action( 'wp_ajax_seopress_switch_view', 'seopress_switch_view' );

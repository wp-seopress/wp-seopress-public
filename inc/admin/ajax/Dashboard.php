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
				$seopress_toggle_options = get_option( 'seopress_toggle', array() );
				if ( ! is_array( $seopress_toggle_options ) ) {
					$seopress_toggle_options = array();
				}
				$seopress_toggle_options[ $feature ] = $feature_value;

				update_option( 'seopress_toggle', $seopress_toggle_options, false );

				// Flush permalinks when toggling features that register rewrite rules.
				// flush_rewrite_rules() rebuilds the rules from $wp_rewrite->extra_rules_top,
				// which was populated at init() based on the OLD toggle value. We must purge
				// the stale rules and re-register them with the NEW value before flushing,
				// otherwise the toggle has no effect until the next request triggers another flush.
				if ( 'toggle-xml-sitemap' === $feature || 'toggle-news' === $feature ) {
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

					// Let PRO and extensions re-register their sitemap rewrite rules (news, video, ...).
					do_action( 'seopress_re_register_sitemap_rules', $sitemap_options, $seopress_toggle_options );

					delete_option( 'rewrite_rules' );
					flush_rewrite_rules( false );
				}
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
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name', array() );
			if ( ! is_array( $seopress_advanced_option_name ) ) {
				$seopress_advanced_option_name = array();
			}

			if ( '1' === $_POST['notifications_center'] ) {
				$seopress_advanced_option_name['seopress_advanced_appearance_notifications'] = sanitize_text_field( wp_unslash( $_POST['notifications_center'] ) );
			} else {
				unset( $seopress_advanced_option_name['seopress_advanced_appearance_notifications'] );
			}

			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
		}

		// News Panel.
		if ( isset( $_POST['news_center'] ) ) {
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name', array() );
			if ( ! is_array( $seopress_advanced_option_name ) ) {
				$seopress_advanced_option_name = array();
			}

			if ( '1' === $_POST['news_center'] ) {
				$seopress_advanced_option_name['seopress_advanced_appearance_news'] = sanitize_text_field( wp_unslash( $_POST['news_center'] ) );
			} else {
				unset( $seopress_advanced_option_name['seopress_advanced_appearance_news'] );
			}

			update_option( 'seopress_advanced_option_name', $seopress_advanced_option_name, false );
		}
		// Tools Panel.
		if ( isset( $_POST['tools_center'] ) ) {
			$seopress_advanced_option_name = get_option( 'seopress_advanced_option_name', array() );
			if ( ! is_array( $seopress_advanced_option_name ) ) {
				$seopress_advanced_option_name = array();
			}

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

/**
 * Dashboard dismiss promotion.
 *
 * @since 9.6.0
 */
function seopress_dismiss_promotion() {
	check_ajax_referer( 'seopress_dismiss_promotion_nonce', '_ajax_nonce', true );

	if ( ! current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) || ! is_admin() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-seopress' ) ) );
	}

	$promo_id = isset( $_POST['promo_id'] ) ? sanitize_text_field( wp_unslash( $_POST['promo_id'] ) ) : '';
	$duration = isset( $_POST['duration'] ) ? absint( $_POST['duration'] ) : 30;

	if ( empty( $promo_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid promotion ID.', 'wp-seopress' ) ) );
	}

	// Use the PromotionService to dismiss the promotion.
	$result = seopress_get_service( 'PromotionService' )->dismissPromotion( $promo_id, $duration );

	if ( $result ) {
		wp_send_json_success( array( 'message' => __( 'Promotion dismissed.', 'wp-seopress' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Failed to dismiss promotion.', 'wp-seopress' ) ) );
	}
}
add_action( 'wp_ajax_seopress_dismiss_promotion', 'seopress_dismiss_promotion' );

/**
 * Dashboard toggle promotions visibility.
 *
 * @since 9.6.0
 */
function seopress_toggle_promotions() {
	check_ajax_referer( 'seopress_toggle_promotions_nonce', '_ajax_nonce', true );

	if ( ! current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) || ! is_admin() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-seopress' ) ) );
	}

	$disable_all = isset( $_POST['disable_all'] ) && '1' === $_POST['disable_all'];

	// Use the PromotionService to set the preference.
	$result = seopress_get_service( 'PromotionService' )->setPreference( 'disable_all', $disable_all );

	if ( $result ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}
add_action( 'wp_ajax_seopress_toggle_promotions', 'seopress_toggle_promotions' );

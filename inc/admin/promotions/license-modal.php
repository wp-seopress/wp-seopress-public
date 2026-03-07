<?php
/**
 * License Renewal Modal.
 *
 * Netflix-style modal for expired PRO licenses.
 *
 * @package SEOPress
 * @subpackage Promotions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Render the license renewal modal.
 *
 * @since 9.6.0
 *
 * @return void
 */
function seopress_render_license_modal() {
	// Only show on admin pages.
	if ( ! is_admin() ) {
		return;
	}

	// Only show if PRO is installed.
	if ( ! is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		return;
	}

	// White-label checks.
	if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
		&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
		return;
	}

	if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
		return;
	}

	// Check license status.
	$license_status = get_option( 'seopress_pro_license_status' );
	$license_expiry = get_option( 'seopress_pro_license_expiry' );

	$is_expired = false;

	// Check if status is explicitly expired.
	if ( 'expired' === $license_status ) {
		$is_expired = true;
	}

	// Check if expiry date has passed.
	if ( ! $is_expired && is_numeric( $license_expiry ) && $license_expiry < time() ) {
		$is_expired = true;
	}

	// Exit if license is not expired.
	if ( ! $is_expired ) {
		return;
	}

	// Check if user has capability.
	if ( ! current_user_can( seopress_capability( 'manage_options', 'dashboard' ) ) ) {
		return;
	}

	// Try to get custom modal content from API.
	$modal_promo = seopress_get_service( 'PromotionService' )->getPromotion( 'modal' );

	// Default content.
	$title       = __( 'Your SEOPress PRO License Has Expired', 'wp-seopress' );
	$body        = __( 'Renew now to continue receiving updates, support, and access to all PRO features.', 'wp-seopress' );
	$cta_text    = __( 'Renew License', 'wp-seopress' );
	$cta_url     = 'https://www.seopress.org/account/?utm_source=plugin&utm_medium=license-modal&utm_campaign=renewal';
	$remind_text = __( 'Remind Me Later', 'wp-seopress' );

	// Override with API content if available.
	if ( $modal_promo && isset( $modal_promo['content'] ) ) {
		if ( ! empty( $modal_promo['content']['title'] ) ) {
			$title = $modal_promo['content']['title'];
		}
		if ( ! empty( $modal_promo['content']['body'] ) ) {
			$body = $modal_promo['content']['body'];
		}
		if ( ! empty( $modal_promo['content']['cta_text'] ) ) {
			$cta_text = $modal_promo['content']['cta_text'];
		}
		if ( ! empty( $modal_promo['content']['cta_url'] ) ) {
			$cta_url = $modal_promo['content']['cta_url'];
		}
	}
	?>
	<div id="seopress-license-modal" class="seopress-modal-overlay hidden">
		<div class="seopress-modal" role="dialog" aria-modal="true" aria-labelledby="seopress-modal-title">
			<div class="modal-icon">
				<span class="dashicons dashicons-warning" aria-hidden="true"></span>
			</div>
			<h2 id="seopress-modal-title"><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $body ); ?></p>
			<div class="modal-features">
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Automatic updates', 'wp-seopress' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Premium support', 'wp-seopress' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'All PRO features', 'wp-seopress' ); ?></li>
				</ul>
			</div>
			<div class="modal-actions">
				<a href="<?php echo esc_url( $cta_url ); ?>"
				   class="btn btnPrimary"
				   target="_blank"
				   rel="noopener noreferrer">
					<?php echo esc_html( $cta_text ); ?>
				</a>
				<button type="button" class="btn btnSecondary" data-dismiss="license-modal">
					<?php echo esc_html( $remind_text ); ?>
				</button>
			</div>
		</div>
	</div>
	<style>
		.seopress-modal .modal-features {
			margin: 0 0 24px;
			text-align: left;
		}
		.seopress-modal .modal-features ul {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		.seopress-modal .modal-features li {
			display: flex;
			align-items: center;
			gap: 8px;
			padding: 8px 0;
			font-size: 14px;
			color: #333;
		}
		.seopress-modal .modal-features .dashicons {
			color: #16a34a;
			font-size: 18px;
			width: 18px;
			height: 18px;
		}
	</style>
	<?php
}

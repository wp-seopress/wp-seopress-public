<?php
/**
 * Contextual Ads Renderer.
 *
 * Inline promotions for specific settings pages (e.g., Redirections -> PRO feature).
 *
 * @package SEOPress
 * @subpackage Promotions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Render a contextual promotion by location.
 *
 * @since 9.6.0
 *
 * @param string $location The promotion location (e.g., 'settings_redirections', 'settings_schemas').
 *
 * @return void
 */
function seopress_render_contextual_promo( $location ) {
	// White-label checks.
	if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
			&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
			return;
		}
	}

	if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
		return;
	}

	// Get promotion for this location.
	$promotion = seopress_get_service( 'PromotionService' )->getPromotion( $location );

	if ( ! $promotion ) {
		return;
	}

	// Get styling.
	$bg_color   = isset( $promotion['styling']['background_color'] ) ? $promotion['styling']['background_color'] : '';
	$text_color = isset( $promotion['styling']['text_color'] ) ? $promotion['styling']['text_color'] : '';

	$style = '';
	if ( $bg_color ) {
		$style .= 'background-color: ' . esc_attr( $bg_color ) . ';';
	}
	if ( $text_color ) {
		$style .= 'color: ' . esc_attr( $text_color ) . ';';
	}
	?>
	<div class="seopress-contextual-promo"
		 data-promo-id="<?php echo esc_attr( $promotion['id'] ); ?>"
		 <?php if ( $style ) : ?>style="<?php echo esc_attr( $style ); ?>"<?php endif; ?>>
		<div class="promo-content">
			<?php if ( ! empty( $promotion['content']['icon'] ) ) : ?>
				<div class="promo-icon">
					<span class="dashicons dashicons-<?php echo esc_attr( $promotion['content']['icon'] ); ?>"></span>
				</div>
			<?php endif; ?>

			<div class="promo-text">
				<?php if ( ! empty( $promotion['content']['title'] ) ) : ?>
					<h4 class="promo-title"><?php echo esc_html( $promotion['content']['title'] ); ?></h4>
				<?php endif; ?>

				<?php if ( ! empty( $promotion['content']['body'] ) ) : ?>
					<p class="promo-body"><?php echo esc_html( $promotion['content']['body'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $promotion['content']['cta_url'] ) && ! empty( $promotion['content']['cta_text'] ) ) : ?>
				<a href="<?php echo esc_url( $promotion['content']['cta_url'] ); ?>"
				   class="btn btnPrimary promo-cta"
				   target="_blank"
				   rel="noopener noreferrer">
					<?php echo esc_html( $promotion['content']['cta_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $promotion['dismissible'] ) ) : ?>
			<button type="button"
					class="promo-dismiss"
					data-promo-id="<?php echo esc_attr( $promotion['id'] ); ?>"
					data-dismiss-duration="<?php echo esc_attr( $promotion['dismiss_duration_days'] ?? 30 ); ?>"
					aria-label="<?php esc_attr_e( 'Dismiss', 'wp-seopress' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a PRO feature upsell banner.
 *
 * @since 9.6.0
 *
 * @param string $feature_name The feature name for display.
 * @param string $feature_slug The feature slug for tracking.
 *
 * @return void
 */
function seopress_render_pro_upsell( $feature_name, $feature_slug = '' ) {
	// Only show if PRO is not installed.
	if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		return;
	}

	// White-label checks.
	if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
		return;
	}

	// Try to get custom content from API.
	$promo_location = $feature_slug ? 'settings_' . $feature_slug : '';
	$promotion      = null;

	if ( $promo_location ) {
		$promotion = seopress_get_service( 'PromotionService' )->getPromotion( $promo_location );
	}

	// Use custom content or default.
	$title    = $promotion ? ( $promotion['content']['title'] ?? '' ) : '';
	$body     = $promotion ? ( $promotion['content']['body'] ?? '' ) : '';
	$cta_text = $promotion ? ( $promotion['content']['cta_text'] ?? '' ) : '';
	$cta_url  = $promotion ? ( $promotion['content']['cta_url'] ?? '' ) : '';

	// Defaults.
	if ( ! $title ) {
		/* translators: %s: Feature name */
		$title = sprintf( __( '%s is a PRO Feature', 'wp-seopress' ), $feature_name );
	}
	if ( ! $body ) {
		$body = __( 'Upgrade to SEOPress PRO to unlock this feature and many more advanced SEO tools.', 'wp-seopress' );
	}
	if ( ! $cta_text ) {
		$cta_text = __( 'Upgrade to PRO', 'wp-seopress' );
	}
	if ( ! $cta_url ) {
		$utm_campaign = $feature_slug ? 'upsell-' . $feature_slug : 'upsell';
		$cta_url      = 'https://www.seopress.org/pricing/?utm_source=plugin&utm_medium=contextual&utm_campaign=' . $utm_campaign;
	}
	?>
	<div class="seopress-pro-upsell">
		<div class="upsell-content">
			<div class="upsell-icon">
				<span class="dashicons dashicons-star-filled"></span>
			</div>
			<div class="upsell-text">
				<h4 class="upsell-title"><?php echo esc_html( $title ); ?></h4>
				<p class="upsell-body"><?php echo esc_html( $body ); ?></p>
			</div>
			<a href="<?php echo esc_url( $cta_url ); ?>"
			   class="btn btnPrimary upsell-cta"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php echo esc_html( $cta_text ); ?>
			</a>
		</div>
	</div>
	<style>
		.seopress-pro-upsell {
			background: linear-gradient(135deg, #4E21E7 0%, #6B3CE7 100%);
			border-radius: 8px;
			padding: 20px;
			margin: 20px 0;
			color: #fff;
		}
		.seopress-pro-upsell .upsell-content {
			display: flex;
			align-items: center;
			gap: 16px;
			flex-wrap: wrap;
		}
		.seopress-pro-upsell .upsell-icon {
			flex-shrink: 0;
			width: 48px;
			height: 48px;
			background: rgba(255, 255, 255, 0.2);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.seopress-pro-upsell .upsell-icon .dashicons {
			font-size: 24px;
			width: 24px;
			height: 24px;
			color: #fbbf24;
		}
		.seopress-pro-upsell .upsell-text {
			flex: 1;
			min-width: 200px;
		}
		.seopress-pro-upsell .upsell-title {
			margin: 0 0 4px;
			font-size: 16px;
			font-weight: 600;
			color: #fff;
		}
		.seopress-pro-upsell .upsell-body {
			margin: 0;
			font-size: 14px;
			opacity: 0.9;
			line-height: 1.4;
		}
		.seopress-pro-upsell .upsell-cta {
			flex-shrink: 0;
			background: #fff;
			color: #4E21E7;
			border: none;
			padding: 10px 20px;
			font-weight: 600;
		}
		.seopress-pro-upsell .upsell-cta:hover {
			background: #f5f5f5;
		}
		@media (max-width: 600px) {
			.seopress-pro-upsell .upsell-content {
				flex-direction: column;
				text-align: center;
			}
			.seopress-pro-upsell .upsell-cta {
				width: 100%;
			}
		}
	</style>
	<?php
}

/**
 * Check if there are any contextual promotions for the current admin page.
 *
 * @since 9.6.0
 *
 * @return bool True if promotions are available.
 */
function seopress_has_contextual_promos() {
	// Get current page from URL.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore

	if ( empty( $page ) || strpos( $page, 'seopress' ) === false ) {
		return false;
	}

	// Map pages to locations.
	$page_locations = array(
		'seopress-redirections' => 'settings_redirections',
		'seopress-schemas'      => 'settings_schemas',
		'seopress-analytics'    => 'settings_analytics',
		'seopress-sitemap'      => 'settings_sitemap',
	);

	if ( ! isset( $page_locations[ $page ] ) ) {
		return false;
	}

	$promotion = seopress_get_service( 'PromotionService' )->getPromotion( $page_locations[ $page ] );

	return ! empty( $promotion );
}

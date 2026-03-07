<?php
/**
 * Top Banner Promotion.
 *
 * Displays a dismissible banner at the top of SEOPress admin pages.
 *
 * @package SEOPress
 * @subpackage Promotions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Render the top banner promotion.
 *
 * @since 9.6.0
 *
 * @return void
 */
function seopress_render_top_banner() {
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

	// Get the promotion for top banner.
	$promotion = seopress_get_service( 'PromotionService' )->getPromotion( 'top_banner' );

	if ( ! $promotion ) {
		return;
	}

	// Default colors and button style.
	$bg_color     = isset( $promotion['styling']['background_color'] ) ? $promotion['styling']['background_color'] : '#4E21E7';
	$text_color   = isset( $promotion['styling']['text_color'] ) ? $promotion['styling']['text_color'] : '#FFFFFF';
	$button_style = isset( $promotion['styling']['button_style'] ) ? $promotion['styling']['button_style'] : 'primary';

	// Build button inline styles based on button style.
	switch ( $button_style ) {
		case 'secondary':
			$btn_inline_style = "background: transparent; border: 2px solid {$text_color}; color: {$text_color};";
			break;
		case 'link':
			$btn_inline_style = "background: transparent; border: none; color: {$text_color}; text-decoration: underline; padding: 6px 0;";
			break;
		case 'primary':
		default:
			$btn_inline_style = "background: {$text_color}; border: none; color: {$bg_color};";
			break;
	}

	?>
	<div id="seopress-promo-banner"
		 class="seopress-promo-banner deleteable"
		 data-promo-id="<?php echo esc_attr( $promotion['id'] ); ?>"
		 style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>;">
		<div class="promo-content">
			<?php if ( ! empty( $promotion['content']['icon'] ) ) : ?>
				<span class="promo-icon dashicons dashicons-<?php echo esc_attr( $promotion['content']['icon'] ); ?>"></span>
			<?php endif; ?>

			<?php if ( ! empty( $promotion['content']['title'] ) ) : ?>
				<strong class="promo-title"><?php echo esc_html( $promotion['content']['title'] ); ?></strong>
			<?php endif; ?>

			<?php if ( ! empty( $promotion['content']['body'] ) ) : ?>
				<span class="promo-text"><?php echo esc_html( $promotion['content']['body'] ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $promotion['content']['cta_url'] ) && ! empty( $promotion['content']['cta_text'] ) ) : ?>
				<a href="<?php echo esc_url( $promotion['content']['cta_url'] ); ?>"
				   class="btn promo-cta"
				   style="<?php echo esc_attr( $btn_inline_style ); ?>"
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
	<script>document.body.classList.add('has-promo-banner');</script>
	<?php
}

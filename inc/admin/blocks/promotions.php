<?php
/**
 * Promotions block - Recommended Partners card.
 *
 * @package SEOPress
 * @subpackage Blocks
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

// White-label checks.
if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
	if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' ) && '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
		return;
	}
}

if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
	return;
}

// Get affiliate partners.
$affiliates = seopress_get_service( 'PromotionService' )->getAffiliatePartners();

// Exit if no affiliates to display.
if ( empty( $affiliates ) ) {
	return;
}

$class = '1' !== seopress_get_service( 'NoticeOption' )->getNoticePromotions() ? 'is-active' : '';
?>

<div id="seopress-promotions-panel" class="seopress-card <?php echo esc_attr( $class ); ?>" style="display: none">
	<div class="seopress-card-title">
		<h2><?php esc_html_e( 'Recommended Partners', 'wp-seopress' ); ?></h2>
		<p><?php esc_html_e( 'Trusted tools to enhance your website performance.', 'wp-seopress' ); ?></p>
	</div>
	<div class="seopress-card-content">
		<div class="seopress-affiliates-list">
			<?php foreach ( $affiliates as $affiliate ) :
				$bg_color   = isset( $affiliate['styling']['background_color'] ) ? $affiliate['styling']['background_color'] : '#4E21E7';
				$text_color = isset( $affiliate['styling']['text_color'] ) ? $affiliate['styling']['text_color'] : '#FFFFFF';
			?>
				<div class="affiliate-card" data-affiliate-id="<?php echo esc_attr( $affiliate['id'] ); ?>">
					<?php if ( ! empty( $affiliate['icon'] ) ) : ?>
						<div class="affiliate-icon" style="background: <?php echo esc_attr( $bg_color ); ?>;">
							<span class="dashicons dashicons-<?php echo esc_attr( $affiliate['icon'] ); ?>" style="color: <?php echo esc_attr( $text_color ); ?>;"></span>
						</div>
					<?php elseif ( ! empty( $affiliate['logo_url'] ) ) : ?>
						<img src="<?php echo esc_url( $affiliate['logo_url'] ); ?>"
							 alt="<?php echo esc_attr( $affiliate['name'] ); ?>"
							 class="affiliate-logo"
							 loading="lazy" />
					<?php endif; ?>

					<div class="affiliate-info">
						<strong class="affiliate-name"><?php echo esc_html( $affiliate['name'] ); ?></strong>
						<?php if ( ! empty( $affiliate['description'] ) ) : ?>
							<p class="affiliate-desc"><?php echo esc_html( $affiliate['description'] ); ?></p>
						<?php endif; ?>
					</div>

					<a href="<?php echo esc_url( $affiliate['url'] ); ?>"
					   class="btn btnSecondary"
					   target="_blank"
					   rel="noopener noreferrer">
						<?php esc_html_e( 'Learn more', 'wp-seopress' ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php
/**
 * Affiliate Partners Display.
 *
 * Contextual partner recommendations based on user's plugin setup.
 *
 * @package SEOPress
 * @subpackage Promotions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Render affiliate partners section.
 *
 * @since 9.6.0
 *
 * @param string $context Optional context for contextual display (e.g., 'international', 'performance').
 *
 * @return void
 */
function seopress_render_affiliates( $context = '' ) {
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

	// Get affiliate partners.
	$affiliates = seopress_get_service( 'PromotionService' )->getAffiliatePartners();

	if ( empty( $affiliates ) ) {
		return;
	}

	// Filter by context if provided.
	if ( ! empty( $context ) ) {
		$affiliates = array_filter(
			$affiliates,
			function ( $affiliate ) use ( $context ) {
				return isset( $affiliate['context'] ) && $affiliate['context'] === $context;
			}
		);
	}

	if ( empty( $affiliates ) ) {
		return;
	}
	?>
	<div class="seopress-affiliates-standalone">
		<h4 class="affiliates-header">
			<span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
			<?php esc_html_e( 'Recommended Partners', 'wp-seopress' ); ?>
		</h4>
		<div class="affiliates-grid">
			<?php foreach ( $affiliates as $affiliate ) : ?>
				<div class="affiliate-item" data-affiliate-id="<?php echo esc_attr( $affiliate['id'] ); ?>">
					<?php if ( ! empty( $affiliate['logo_url'] ) ) : ?>
						<div class="affiliate-logo-wrap">
							<img src="<?php echo esc_url( $affiliate['logo_url'] ); ?>"
								 alt="<?php echo esc_attr( $affiliate['name'] ); ?>"
								 class="affiliate-logo"
								 loading="lazy" />
						</div>
					<?php endif; ?>

					<div class="affiliate-details">
						<strong class="affiliate-name"><?php echo esc_html( $affiliate['name'] ); ?></strong>

						<?php if ( ! empty( $affiliate['description'] ) ) : ?>
							<p class="affiliate-desc"><?php echo esc_html( $affiliate['description'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $affiliate['features'] ) && is_array( $affiliate['features'] ) ) : ?>
							<ul class="affiliate-features">
								<?php foreach ( $affiliate['features'] as $feature ) : ?>
									<li><span class="dashicons dashicons-yes"></span> <?php echo esc_html( $feature ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>

					<a href="<?php echo esc_url( $affiliate['url'] ); ?>"
					   class="btn btnSecondary affiliate-cta"
					   target="_blank"
					   rel="noopener noreferrer">
						<?php esc_html_e( 'Learn more', 'wp-seopress' ); ?>
						<span class="dashicons dashicons-external"></span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<style>
		.seopress-affiliates-standalone {
			background: var(--backgroundSecondary, #f8f9fa);
			border-radius: 8px;
			padding: 20px;
			margin: 20px 0;
		}
		.seopress-affiliates-standalone .affiliates-header {
			display: flex;
			align-items: center;
			gap: 8px;
			margin: 0 0 16px;
			font-size: 14px;
			font-weight: 600;
			color: var(--textPrimary, #333);
		}
		.seopress-affiliates-standalone .affiliates-header .dashicons {
			color: #f59e0b;
		}
		.seopress-affiliates-standalone .affiliates-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
			gap: 16px;
		}
		.seopress-affiliates-standalone .affiliate-item {
			background: #fff;
			border: 1px solid var(--borderColor, #e0e0e0);
			border-radius: 8px;
			padding: 16px;
			display: flex;
			flex-direction: column;
			gap: 12px;
		}
		.seopress-affiliates-standalone .affiliate-logo-wrap {
			height: 40px;
			display: flex;
			align-items: center;
		}
		.seopress-affiliates-standalone .affiliate-logo {
			max-height: 40px;
			max-width: 120px;
			width: auto;
			object-fit: contain;
		}
		.seopress-affiliates-standalone .affiliate-details {
			flex: 1;
		}
		.seopress-affiliates-standalone .affiliate-name {
			display: block;
			font-size: 15px;
			font-weight: 600;
			color: var(--textPrimary, #333);
			margin-bottom: 4px;
		}
		.seopress-affiliates-standalone .affiliate-desc {
			margin: 0 0 8px;
			font-size: 13px;
			color: var(--textSecondary, #666);
			line-height: 1.4;
		}
		.seopress-affiliates-standalone .affiliate-features {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		.seopress-affiliates-standalone .affiliate-features li {
			display: flex;
			align-items: center;
			gap: 4px;
			font-size: 12px;
			color: var(--textSecondary, #666);
			padding: 2px 0;
		}
		.seopress-affiliates-standalone .affiliate-features .dashicons {
			color: #16a34a;
			font-size: 14px;
			width: 14px;
			height: 14px;
		}
		.seopress-affiliates-standalone .affiliate-cta {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 4px;
			width: 100%;
			text-align: center;
		}
		.seopress-affiliates-standalone .affiliate-cta .dashicons {
			font-size: 14px;
			width: 14px;
			height: 14px;
		}
	</style>
	<?php
}

/**
 * Get a specific affiliate partner by ID.
 *
 * @since 9.6.0
 *
 * @param string $affiliate_id The affiliate ID.
 *
 * @return array|null The affiliate data or null if not found.
 */
function seopress_get_affiliate( $affiliate_id ) {
	$affiliates = seopress_get_service( 'PromotionService' )->getAffiliatePartners();

	foreach ( $affiliates as $affiliate ) {
		if ( isset( $affiliate['id'] ) && $affiliate['id'] === $affiliate_id ) {
			return $affiliate;
		}
	}

	return null;
}

/**
 * Render a single affiliate partner inline.
 *
 * @since 9.6.0
 *
 * @param string $affiliate_id The affiliate ID.
 *
 * @return void
 */
function seopress_render_affiliate_inline( $affiliate_id ) {
	$affiliate = seopress_get_affiliate( $affiliate_id );

	if ( ! $affiliate ) {
		return;
	}

	// White-label checks.
	if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
			&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
			return;
		}
	}
	?>
	<div class="seopress-affiliate-inline">
		<span class="affiliate-text">
			<?php
			/* translators: %s: Partner name */
			printf( esc_html__( 'Recommended: %s', 'wp-seopress' ), esc_html( $affiliate['name'] ) );
			?>
		</span>
		<?php if ( ! empty( $affiliate['description'] ) ) : ?>
			<span class="affiliate-desc"> - <?php echo esc_html( $affiliate['description'] ); ?></span>
		<?php endif; ?>
		<a href="<?php echo esc_url( $affiliate['url'] ); ?>"
		   class="affiliate-link"
		   target="_blank"
		   rel="noopener noreferrer">
			<?php esc_html_e( 'Learn more', 'wp-seopress' ); ?>
			<span class="dashicons dashicons-external"></span>
		</a>
	</div>
	<style>
		.seopress-affiliate-inline {
			display: inline-flex;
			align-items: center;
			flex-wrap: wrap;
			gap: 8px;
			padding: 8px 12px;
			background: #f0f9ff;
			border: 1px solid #bae6fd;
			border-radius: 4px;
			font-size: 13px;
			margin: 8px 0;
		}
		.seopress-affiliate-inline .affiliate-text {
			font-weight: 500;
			color: #0369a1;
		}
		.seopress-affiliate-inline .affiliate-desc {
			color: #64748b;
		}
		.seopress-affiliate-inline .affiliate-link {
			display: inline-flex;
			align-items: center;
			gap: 2px;
			color: #0369a1;
			text-decoration: none;
			font-weight: 500;
		}
		.seopress-affiliate-inline .affiliate-link:hover {
			text-decoration: underline;
		}
		.seopress-affiliate-inline .affiliate-link .dashicons {
			font-size: 14px;
			width: 14px;
			height: 14px;
		}
	</style>
	<?php
}

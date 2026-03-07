<?php
/**
 * Contextual Ads for Settings Pages.
 *
 * Displays relevant promotional content on specific settings pages.
 *
 * @package SEOPress
 * @subpackage Promotions
 * @since 9.6.0
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Get contextual promotion based on page context.
 *
 * @since 9.6.0
 *
 * @param string $context The page context (e.g., 'redirections', 'schemas', 'local_seo').
 *
 * @return array|null Promotion data or null if none applicable.
 */
function seopress_get_contextual_promotion( $context ) {
	// Don't show if PRO is active.
	if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		return null;
	}

	// White-label check.
	if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
		&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
		return null;
	}

	if ( defined( 'SEOPRESS_WL_ADMIN_HEADER' ) && false === SEOPRESS_WL_ADMIN_HEADER ) {
		return null;
	}

	// Try to get promotion from API first.
	$promotion_service = seopress_get_service( 'PromotionService' );
	if ( $promotion_service ) {
		$api_promotion = $promotion_service->getPromotion( $context );
		if ( $api_promotion ) {
			// Map API response format to expected format.
			return array(
				'id'       => $api_promotion['id'] ?? '',
				'icon'     => $api_promotion['content']['icon'] ?? 'star-filled',
				'title'    => $api_promotion['content']['title'] ?? '',
				'body'     => $api_promotion['content']['body'] ?? '',
				'cta_text' => $api_promotion['content']['cta_text'] ?? __( 'Learn more', 'wp-seopress' ),
				'cta_url'  => $api_promotion['content']['cta_url'] ?? '',
			);
		}
	}

	// Fallback to hardcoded promotions.
	$docs = seopress_get_docs_links();

	$promotions = array(
		'titles'    => array(
			'id'          => 'ctx-titles',
			'icon'        => 'edit',
			'title'       => __( 'Need more control over your titles?', 'wp-seopress' ),
			'body'        => __( 'PRO adds AI-powered title generation, advanced breadcrumbs, and more.', 'wp-seopress' ),
			'cta_text'    => __( 'Discover PRO', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
		'sitemaps'  => array(
			'id'          => 'ctx-sitemaps',
			'icon'        => 'networking',
			'title'       => __( 'Video & News Sitemaps', 'wp-seopress' ),
			'body'        => __( 'Get your videos and news articles indexed faster with dedicated sitemaps.', 'wp-seopress' ),
			'cta_text'    => __( 'Learn more', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
		'analytics' => array(
			'id'          => 'ctx-analytics',
			'icon'        => 'chart-area',
			'title'       => __( 'Advanced Analytics & Events', 'wp-seopress' ),
			'body'        => __( 'Track custom events, download tracking, affiliate links, and more with PRO.', 'wp-seopress' ),
			'cta_text'    => __( 'Explore features', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
		'advanced'  => array(
			'id'          => 'ctx-advanced',
			'icon'        => 'admin-settings',
			'title'       => __( 'Redirections, Broken Links & 404 Monitoring', 'wp-seopress' ),
			'body'        => __( 'Manage redirections, monitor 404 errors, and fix broken links automatically.', 'wp-seopress' ),
			'cta_text'    => __( 'Get PRO', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
		'schemas'   => array(
			'id'          => 'ctx-schemas',
			'icon'        => 'editor-code',
			'title'       => __( 'Automatic Schemas & Rich Snippets', 'wp-seopress' ),
			'body'        => __( 'Add structured data automatically for products, recipes, FAQs, and more.', 'wp-seopress' ),
			'cta_text'    => __( 'Unlock schemas', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
		'woocommerce' => array(
			'id'          => 'ctx-woocommerce',
			'icon'        => 'cart',
			'title'       => __( 'WooCommerce SEO', 'wp-seopress' ),
			'body'        => __( 'Optimize product pages, add schema markup, and improve store visibility.', 'wp-seopress' ),
			'cta_text'    => __( 'Boost your store', 'wp-seopress' ),
			'cta_url'     => $docs['addons']['pro'],
		),
	);

	return isset( $promotions[ $context ] ) ? $promotions[ $context ] : null;
}

/**
 * Render a contextual promotion.
 *
 * @since 9.6.0
 *
 * @param string $context The page context.
 *
 * @return void
 */
function seopress_render_contextual_promotion( $context ) {
	$promotion = seopress_get_contextual_promotion( $context );

	if ( ! $promotion ) {
		return;
	}

	// Check if dismissed.
	if ( seopress_get_service( 'NoticeOption' )->getPromotionDismissed( $promotion['id'] ) ) {
		return;
	}
	?>
	<div class="seopress-contextual-promo" data-promo-id="<?php echo esc_attr( $promotion['id'] ); ?>">
		<div class="promo-content">
			<div class="promo-icon">
				<span class="dashicons dashicons-<?php echo esc_attr( $promotion['icon'] ); ?>"></span>
			</div>
			<div class="promo-text">
				<h4 class="promo-title"><?php echo esc_html( $promotion['title'] ); ?></h4>
				<p class="promo-body"><?php echo esc_html( $promotion['body'] ); ?></p>
			</div>
			<a href="<?php echo esc_url( $promotion['cta_url'] ); ?>"
			   class="btn btnSecondary promo-cta"
			   target="_blank"
			   rel="noopener noreferrer">
				<?php echo esc_html( $promotion['cta_text'] ); ?>
			</a>
			<button type="button"
					class="promo-dismiss"
					data-promo-id="<?php echo esc_attr( $promotion['id'] ); ?>"
					data-dismiss-duration="30"
					aria-label="<?php esc_attr_e( 'Dismiss', 'wp-seopress' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
	</div>
	<?php
}

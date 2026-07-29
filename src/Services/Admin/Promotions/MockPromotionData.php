<?php // phpcs:ignore

namespace SEOPress\Services\Admin\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MockPromotionData
 *
 * Provides default promotion data when the remote API returns no promotions.
 * These serve as fallback promotions to always show something to users.
 *
 * @since 9.6.0
 */
class MockPromotionData {

	/**
	 * Get mock promotion data.
	 *
	 * @since 9.6.0
	 *
	 * @return array
	 */
	public static function getData(): array {
		return array(
			'version'   => '1.0',
			'cache_ttl' => 21600,
			'promotions' => self::getPromotions(),
			'affiliate_partners' => self::getAffiliatePartners(),
		);
	}

	/**
	 * Build the PRO pricing URL with UTM parameters from the centralized docs links.
	 *
	 * @since 10.1
	 *
	 * @param string $utm_medium UTM medium value.
	 *
	 * @return string
	 */
	protected static function getPricingUrl( string $utm_medium ): string {
		$docs_links = function_exists( 'seopress_get_docs_links' ) ? seopress_get_docs_links() : array();

		return add_query_arg(
			array(
				'utm_source'   => 'plugin',
				'utm_medium'   => $utm_medium,
				'utm_campaign' => 'pro',
			),
			isset( $docs_links['pricing'] ) ? $docs_links['pricing'] : ''
		);
	}

	/**
	 * Get mock promotions.
	 *
	 * @since 9.6.0
	 *
	 * @return array
	 */
	protected static function getPromotions(): array {
		return array(
			// Dashboard Block - PRO promotion.
			array(
				'id'       => 'promo-dashboard-001',
				'type'     => 'block',
				'location' => 'dashboard',
				'priority' => 90,
				'content'  => array(
					'title'     => 'Unlock the Full Power of SEO',
					'body'      => 'Get redirections, schemas, WooCommerce SEO, local SEO, and more with SEOPress PRO.',
					'cta_text'  => 'Discover PRO',
					'cta_url'   => self::getPricingUrl( 'dashboard' ),
					'image_url' => '',
					'icon'      => 'awards',
				),
				'styling'  => array(
					'background_color' => '#1e1b4b',
					'text_color'       => '#FFFFFF',
				),
				'conditions' => array(
					'pro_license_active' => false,
				),
				'dismissible' => true,
				'dismiss_duration_days' => 30,
			),

			// Metabox Banner - PRO promotion in post editor.
			array(
				'id'       => 'promo-metabox-001',
				'type'     => 'banner',
				'location' => 'metabox',
				'priority' => 80,
				'content'  => array(
					'title'    => 'Boost Your SEO',
					'body'     => 'Get advanced schemas, AI content analysis, and more with PRO.',
					'cta_text' => 'Upgrade Now',
					'cta_url'  => self::getPricingUrl( 'metabox' ),
					'icon'     => 'star-filled',
				),
				'styling'  => array(
					'background_color' => 'var(--wp-admin-theme-color)',
					'text_color'       => '#FFFFFF',
				),
				'conditions' => array(
					'pro_license_active' => false,
				),
				'dismissible' => true,
				'dismiss_duration_days' => 14,
			),

		);
	}

	/**
	 * Get mock affiliate partners.
	 *
	 * Fallback partners shown when the API returns no data.
	 * No restrictive conditions here: this is a safety net that must always
	 * produce at least one visible partner regardless of the site context.
	 *
	 * @since 9.6.0
	 *
	 * @return array
	 */
	protected static function getAffiliatePartners(): array {
		return array(
			array(
				'id'          => 'mailerpress',
				'name'        => 'MailerPress',
				'url'         => 'https://mailerpress.com/?ref=seopress',
				'logo_url'    => SEOPRESS_ASSETS_DIR . '/img/logo-mailerpress.svg',
				'description' => 'Create beautiful email campaigns directly in WordPress.',
				'features'    => array(
					'Drag & drop builder',
					'Newsletter automation',
					'Subscriber management',
				),
				'context'     => 'marketing',
				'conditions'  => array(),
			),
		);
	}
}

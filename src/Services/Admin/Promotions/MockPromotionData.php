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
					'cta_url'   => 'https://www.seopress.org/pricing/?utm_source=plugin&utm_medium=dashboard&utm_campaign=pro',
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
					'cta_url'  => 'https://www.seopress.org/pricing/?utm_source=plugin&utm_medium=metabox&utm_campaign=pro',
					'icon'     => 'star-filled',
				),
				'styling'  => array(
					'background_color' => '#4E21E7',
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
				'conditions'  => array(
					'plugins_inactive' => array( 'mailerpress/mailerpress.php' ),
				),
			),
		);
	}
}

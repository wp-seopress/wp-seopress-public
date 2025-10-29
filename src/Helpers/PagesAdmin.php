<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PagesAdmin
 */
abstract class PagesAdmin {
	/**
	 * The dashboard constant.
	 *
	 * @var string
	 */
	const DASHBOARD = 'dashboard';

	/**
	 * The xml_html_sitemap constant.
	 *
	 * @var string
	 */
	const XML_HTML_SITEMAP = 'xml_html_sitemap';

	/**
	 * The social_networks constant.
	 *
	 * @var string
	 */
	const SOCIAL_NETWORKS = 'social_networks';

	/**
	 * The titles_metas constant.
	 *
	 * @var string
	 */
	const TITLE_METAS = 'titles_metas';

	/**
	 * The analytics constant.
	 *
	 * @var string
	 */
	const ANALYTICS = 'analytics';

	/**
	 * The advanced constant.
	 *
	 * @var string
	 */
	const ADVANCED = 'advanced';

	/**
	 * The tools constant.
	 *
	 * @var string
	 */
	const TOOLS = 'tools';

	/**
	 * The instant_indexing constant.
	 *
	 * @var string
	 */
	const INSTANT_INDEXING = 'instant_indexing';

	/**
	 * The pro constant.
	 *
	 * @var string
	 */
	const PRO = 'pro';

	/**
	 * The schemas constant.
	 *
	 * @var string
	 */
	const SCHEMAS = 'schemas';

	/**
	 * The bot constant.
	 *
	 * @var string
	 */
	const BOT = 'bot';

	/**
	 * The license constant.
	 *
	 * @var string
	 */
	const LICENSE = 'license';

	/**
	 * The get_pages function.
	 *
	 * @return array
	 */
	public static function getPages() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return apply_filters(
			'seopress_pages_admin',
			array(
				self::DASHBOARD,
				self::TITLE_METAS,
				self::XML_HTML_SITEMAP,
				self::SOCIAL_NETWORKS,
				self::ANALYTICS,
				self::ADVANCED,
				self::TOOLS,
				self::INSTANT_INDEXING,
				self::PRO,
				self::SCHEMAS,
				self::BOT,
				self::LICENSE,
			)
		);
	}

	/**
	 * The get_capability_by_page function.
	 *
	 * @param string $page The page.
	 *
	 * @since 4.6.0
	 *
	 * @return string
	 */
	public static function getCapabilityByPage( $page ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $page ) {
			case 'seopress-titles':
				return self::TITLE_METAS;
			case 'seopress-xml-sitemap':
				return self::XML_HTML_SITEMAP;
			case 'seopress-social':
				return self::SOCIAL_NETWORKS;
			case 'seopress-google-analytics':
				return self::ANALYTICS;
			case 'seopress-import-export':
				return self::TOOLS;
			case 'seopress-instant-indexing':
				return self::INSTANT_INDEXING;
			case 'seopress-pro-page':
				return self::PRO;
			case 'seopress-advanced':
				return self::ADVANCED;
			case 'seopress-bot-batch':
				return self::BOT;
			default:
				return apply_filters( 'seopress_get_capability_by_page', null );
		}
	}

	/**
	 * The get_page_by_capability function.
	 *
	 * @since 4.6.0
	 *
	 * @param string $capability The capability.
	 *
	 * @return string
	 */
	public static function getPageByCapability( $capability ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		switch ( $capability ) {
			case self::TITLE_METAS:
				return 'seopress-titles';
			case self::XML_HTML_SITEMAP:
				return 'seopress-xml-sitemap';
			case self::SOCIAL_NETWORKS:
				return 'seopress-social';
			case self::ANALYTICS:
				return 'seopress-google-analytics';
			case self::TOOLS:
				return 'seopress-import-export';
			case self::INSTANT_INDEXING:
				return 'seopress-instant-indexing';
			case self::PRO:
				return 'seopress-pro-page';
			case self::ADVANCED:
				return 'seopress-advanced';
			case self::BOT:
				return 'seopress-bot-batch';
			default:
				return apply_filters( 'seopress_get_page_by_capability', null );
		}
	}

	/**
	 * The get_custom_capability function.
	 *
	 * @since 4.6.0
	 *
	 * @param string $capability The capability.
	 *
	 * @return string
	 */
	public static function getCustomCapability( $capability ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf( 'seopress_manage_%s', $capability );
	}
}

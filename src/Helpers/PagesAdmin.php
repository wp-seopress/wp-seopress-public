<?php

namespace SEOPress\Helpers;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

abstract class PagesAdmin {
    const DASHBOARD        = 'dashboard';

    const XML_HTML_SITEMAP = 'xml_html_sitemap';

    const SOCIAL_NETWORKS  = 'social_networks';

    const TITLE_METAS      = 'titles_metas';

    const ANALYTICS        = 'analytics';

    const ADVANCED         = 'advanced';

    const TOOLS            = 'tools';

    const INSTANT_INDEXING = 'instant_indexing';

    const PRO              = 'pro';

    const SCHEMAS          = 'schemas';

    const BOT              = 'bot';

    const LICENSE          = 'license';

    public static function getPages() {
        return apply_filters('seopress_pages_admin', [
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
        ]);
    }

    /**
     * @since 4.6.0
     *
     * @param string $page
     *
     * @return string
     */
    public static function getCapabilityByPage($page) {
        switch ($page) {
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
                return apply_filters('seopress_get_capability_by_page', null);
        }
    }

    /**
     * @since 4.6.0
     *
     * @param string $capability
     *
     * @return string
     */
    public static function getPageByCapability($capability) {
        switch ($capability) {
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
                return apply_filters('seopress_get_page_by_capability', null);
        }
    }

    /**
     * @since 4.6.0
     *
     * @param string $capability
     *
     * @return string
     */
    public static function getCustomCapability($capability) {
        return sprintf('seopress_manage_%s', $capability);
    }
}

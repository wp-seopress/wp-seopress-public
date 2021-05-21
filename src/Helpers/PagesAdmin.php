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
            case 'titles-metas':
                return self::TITLE_METAS;
            case 'xml-html-sitemap':
                return self::XML_HTML_SITEMAP;
            case 'social-networks':
                return self::SOCIAL_NETWORKS;
            case 'analytics':
                return self::ANALYTICS;
            case 'tools':
                return self::TOOLS;
            case 'pro':
                return self::PRO;
            case 'advanced':
                return self::ADVANCED;
            case 'schemas':
                return self::SCHEMAS;
            case 'license':
                return self::LICENSE;
            case 'bot':
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
                return 'titles-metas';
            case self::XML_HTML_SITEMAP:
                return 'xml-html-sitemap';
            case self::SOCIAL_NETWORKS:
                return 'social-networks';
            case self::ANALYTICS:
                return 'analytics';
            case self::TOOLS:
                return 'tools';
            case self::PRO:
                return 'pro';
            case self::ADVANCED:
                return 'advanced';
            case self::SCHEMAS:
                return 'schemas';
            case self::LICENSE:
                return 'license';
            case self::BOT:
                return 'bot';
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

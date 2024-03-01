<?php

namespace SEOPress\Constants;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class Options {
    /**
     * @since 4.5.0
     *
     * @var string
     */
    const KEY_TOGGLE_OPTION = 'seopress_toggle';

    /**
     * @since 6.0.0
     *
     * @var string
     */
    const KEY_OPTION_NOTICE = 'seopress_notices';

    /**
     * @since 6.6.0
     *
     * @var string
     */
    const KEY_OPTION_DASHBOARD = 'seopress_dashboard_option_name';

    /**
     * @since 4.3.0
     *
     * @var string
     */
    const KEY_OPTION_TITLE = 'seopress_titles_option_name';

    /**
     * @since 4.3.0
     *
     * @var string
     */
    const KEY_OPTION_SITEMAP = 'seopress_xml_sitemap_option_name';

    /**
     * @since 4.5.0
     *
     * @var string
     */
    const KEY_OPTION_SOCIAL = 'seopress_social_option_name';

    /**
     * @since 5.8.0
     *
     * @var string
     */
    const KEY_OPTION_GOOGLE_ANALYTICS = 'seopress_google_analytics_option_name';

    /**
     * @since 4.6.0
     *
     * @var string
     */
    const KEY_OPTION_ADVANCED = 'seopress_advanced_option_name';
}

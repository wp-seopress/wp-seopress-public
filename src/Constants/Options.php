<?php

namespace SEOPress\Constants;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class Options {
    /**
     * @since 4.3.0
     *
     * @var string
     */
    const KEY_OPTION_SITEMAP = 'seopress_xml_sitemap_option_name';

    /**
     * @since 4.3.0
     *
     * @var string
     */
    const KEY_OPTION_TITLE = 'seopress_titles_option_name';
}

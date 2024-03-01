<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SiteAlternateName implements GetTagValue {
    const NAME = 'site_alternate_name';

    const ALIAS = ['alternate_site_title'];

    /**
     * 7.4.0.
     *
     * @return string
     */
    public static function getDescription() {
        return __('Alternative site title', 'wp-seopress');
    }

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = !empty(seopress_get_service('TitleOption')->getHomeSiteTitleAlt()) ? seopress_get_service('TitleOption')->getHomeSiteTitleAlt() : get_bloginfo('name');

        return apply_filters('seopress_get_tag_schema_site_alternate_name', $value, $context);
    }
}

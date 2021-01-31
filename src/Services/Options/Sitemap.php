<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class Sitemap {
    const NAME_SERVICE = 'SitemapOption';

    /**
     * @since 4.3.0
     *
     * @return array
     */
    public function getOption() {
        return get_option(Options::KEY_OPTION_SITEMAP);
    }

    /**
     * @since 4.3.0
     *
     * @return string|nul
     *
     * @param string $key
     */
    protected function searchOptionByKey($key) {
        $data = $this->getOption();

        if (empty($data)) {
            return null;
        }

        if ( ! isset($data[$key])) {
            return null;
        }

        return $data[$key];
    }

    /**
     * @since 4.3.0
     *
     * @return string|null
     */
    public function isEnabled() {
        return $this->searchOptionByKey('seopress_xml_sitemap_general_enable');
    }

    /**
     * @since 4.3.0
     *
     * @return string|null
     */
    public function getPostTypesList() {
        return $this->searchOptionByKey('seopress_xml_sitemap_post_types_list');
    }

    /**
     * @since 4.3.0
     *
     * @return string|null
     */
    public function getTaxonomiesList() {
        return $this->searchOptionByKey('seopress_xml_sitemap_taxonomies_list');
    }

    /**
     * @since 4.3.0
     *
     * @return string|null
     */
    public function authorIsEnable() {
        return $this->searchOptionByKey('seopress_xml_sitemap_author_enable');
    }

    /**
     * @since 4.3.0
     *
     * @return string|null
     */
    public function imageIsEnable() {
        return $this->searchOptionByKey('seopress_xml_sitemap_img_enable');
    }
}

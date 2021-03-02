<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class TitleOption {
    /**
     * @since 4.3.0
     *
     * @return array
     */
    public function getOption() {
        return get_option(Options::KEY_OPTION_TITLE);
    }

    /**
     * @since 4.3.0
     *
     * @param string $key
     *
     * @return mixed
     */
    public function searchOptionByKey($key) {
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
     * @param string $path
     *
     * @return string|null
     */
    public function getTitlesCptNoIndexByPath($path) {
        $data = $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($data[$path]['noindex'])) {
            return null;
        }

        return $data[$path]['noindex'];
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getSeparator() {
        return $this->searchOptionByKey('seopress_titles_sep');
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getHomeSiteTitle() {
        return $this->searchOptionByKey('seopress_titles_home_site_title');
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getHomeDescriptionTitle() {
        return $this->searchOptionByKey('seopress_titles_home_site_desc');
    }
}

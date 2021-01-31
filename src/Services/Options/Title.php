<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class Title {
    const NAME_SERVICE = 'TitleOption';

    public function getOption() {
        return get_option(Options::KEY_OPTION_TITLE);
    }

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
}

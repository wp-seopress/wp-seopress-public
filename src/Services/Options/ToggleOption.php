<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class ToggleOption {
    /**
     * @since 4.3.0
     *
     * @return array
     */
    public function getOption($is_multisite) {
        if ($is_multisite === true && function_exists('get_network')) {
            $network = get_network();
            $main_network_id = $network->site_id;
            return get_blog_option($main_network_id, Options::KEY_TOGGLE_OPTION);
        } else {
            return get_option(Options::KEY_TOGGLE_OPTION);
        }
    }

    /**
     * @since 4.3.0
     *
     * @param string $key
     *
     * @return mixed
     */
    public function searchOptionByKey($key, $is_multisite = false) {
        $data = $this->getOption($is_multisite);

        if (empty($data)) {
            return null;
        }

        $keyComposed = sprintf('toggle-%s', $key);
        if ( ! isset($data[$keyComposed])) {
            return null;
        }

        return $data[$keyComposed];
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getToggleLocalBusiness() {
        return $this->searchOptionByKey('local-business');
    }

    public function getToggleGoogleNews(){
        return $this->searchOptionByKey('news');
    }

    public function getToggleInspectUrl(){
        return $this->searchOptionByKey('inspect-url');
    }

    /**
     * @since 6.4.0
     *
     * @return string
     */
    public function getToggleAi(){
        return $this->searchOptionByKey('ai');
    }

    /**
     * @since 6.6.0
     *
     * @return string
     */
    public function getToggleWhiteLabel(){
        if (is_network_admin() || is_multisite()) {
            return $this->searchOptionByKey('white-label', true);
        }
        return $this->searchOptionByKey('white-label');
    }
}

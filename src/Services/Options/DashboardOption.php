<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class DashboardOption
{
    /**
     * @since 6.6.0
     *
     * @return array
     */
    public function getOption()
    {
        return get_option(Options::KEY_OPTION_DASHBOARD);
    }

    /**
     * @since 6.6.0
     *
     * @param string $key
     *
     * @return mixed
     */
    public function searchOptionByKey($key)
    {
        $data = $this->getOption();

        if (empty($data)) {
            return null;
        }

        if (! isset($data[$key])) {
            return null;
        }

        return $data[$key];
    }
}

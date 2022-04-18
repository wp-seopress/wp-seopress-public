<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class AdvancedOption
{
    /**
     * @since 4.6.0
     *
     * @return array
     */
    public function getOption()
    {
        return get_option(Options::KEY_OPTION_ADVANCED);
    }

    /**
     * @since 4.6.0
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

    /**
     * @since 5.0.0
     *
     * @return string
     */
    public function getAccessUniversalMetaboxGutenberg(){
        return $this->searchOptionByKey('seopress_advanced_appearance_universal_metabox');
    }

    /**
     * @since 5.0.0
     *
     * @return string
     */
    public function getDisableUniversalMetaboxGutenberg(){
        $data = $this->getOption();

        if(!isset($data['seopress_advanced_appearance_universal_metabox_disable'])){
            return true;
        }

        return $data['seopress_advanced_appearance_universal_metabox_disable'] === '1';
    }

    /**
     * @since 5.0.3
     */
    public function getSecurityMetaboxRole(){
        return $this->searchOptionByKey('seopress_advanced_security_metaboxe_role');
    }

    /**
     * @since 5.0.3
     */
    public function getSecurityMetaboxRoleContentAnalysis(){
        return $this->searchOptionByKey('seopress_advanced_security_metaboxe_ca_role');
    }
}

<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class GoogleAnalyticsOption
{
    /**
     * @since 5.8.0
     *
     * @return array
     */
    public function getOption() {
        return get_option(Options::KEY_OPTION_GOOGLE_ANALYTICS);
    }

    /**
     * @since 5.8.0
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
     * @since 5.8.0
     *
     * @return string
     */
    public function getHook() {
        return $this->searchOptionByKey('seopress_google_analytics_hook');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getOptOutMessageOk() {
        return $this->searchOptionByKey('seopress_google_analytics_opt_out_msg_ok');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getOptOutMessageClose() {
        return $this->searchOptionByKey('seopress_google_analytics_opt_out_msg_close');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBg() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_bg');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbTxtCol() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_txt_col');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbLkCol() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_lk_col');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnBg() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_bg');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnBgHov() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_bg_hov');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnCol() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_col');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnColHov() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_col_hov');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnSecBg() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_sec_bg');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnSecCol() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_sec_col');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnSecBgHov() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_sec_bg_hov');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBtnSecColHov() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_btn_sec_col_hov');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbPos() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_pos');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbWidth() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_width');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBackdrop() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_backdrop');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbBackdropBg() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_backdrop_bg');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbTxtAlign() {
        return $this->searchOptionByKey('seopress_google_analytics_cb_txt_align');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getOptOutEditChoice() {
        return $this->searchOptionByKey('seopress_google_analytics_opt_out_edit_choice');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getOptOutMessageEdit() {
        return $this->searchOptionByKey('seopress_google_analytics_opt_out_msg_edit');
    }

}

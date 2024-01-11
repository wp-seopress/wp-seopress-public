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

    /**
     * Ads
     * @since 5.8.0
     *
     * @return string
     */
    public function getAds() {
        return $this->searchOptionByKey('seopress_google_analytics_ads');
    }

    /**
     * Additional tracking code - head
     * @since 5.8.0
     *
     * @return string
     */
    public function getOtherTracking() {
        return $this->searchOptionByKey('seopress_google_analytics_other_tracking');
    }

    /**
     * Additional tracking code - body
     * @since 5.8.0
     *
     * @return string
     */
    public function getOtherTrackingBody() {
        return $this->searchOptionByKey('seopress_google_analytics_other_tracking_body');
    }

    /**
     * Additional tracking code - footer
     * @since 5.8.0
     *
     * @return string
     */
    public function getOtherTrackingFooter() {
        return $this->searchOptionByKey('seopress_google_analytics_other_tracking_footer');
    }

    /**
     * Events external links tracking Enable
     * @since 5.8.0
     *
     * @return string
     */
    public function getLinkTrackingEnable() {
        return $this->searchOptionByKey('seopress_google_analytics_link_tracking_enable');
    }

    /**
     * Events downloads tracking Enable
     * @since 5.8.0
     *
     * @return string
     */
    public function getDownloadTrackingEnable() {
        return $this->searchOptionByKey('seopress_google_analytics_download_tracking_enable');
    }

    /**
     * Events tracking file types
     * @since 5.8.0
     *
     * @return string
     */
    public function getDownloadTracking() {
        return $this->searchOptionByKey('seopress_google_analytics_download_tracking');
    }

    /**
     * Events affiliate links tracking Enable
     * @since 5.8.0
     *
     * @return string
     */
    public function getAffiliateTrackingEnable() {
        return $this->searchOptionByKey('seopress_google_analytics_affiliate_tracking_enable');
    }

    /**
     * Events tracking affiliate match
     * @since 5.8.0
     *
     * @return string
     */
    public function getAffiliateTracking() {
        return $this->searchOptionByKey('seopress_google_analytics_affiliate_tracking');
    }

    /**
     * Events phone tracking
     * @since 6.3.0
     *
     * @return string
     */
    public function getPhoneTracking() {
        return $this->searchOptionByKey('seopress_google_analytics_phone_tracking');
    }

    /**
     * Custom Dimension Author
     * @since 5.8.0
     *
     * @return string
     */
    public function getCdAuthor() {
        return $this->searchOptionByKey('seopress_google_analytics_cd_author');
    }

    /**
     * Custom Dimension Category
     * @since 5.8.0
     *
     * @return string
     */
    public function getCdCategory() {
        return $this->searchOptionByKey('seopress_google_analytics_cd_category');
    }

    /**
     * Custom Dimension Tag
     * @since 5.8.0
     *
     * @return string
     */
    public function getCdTag() {
        return $this->searchOptionByKey('seopress_google_analytics_cd_tag');
    }

    /**
     * Custom Dimension Post Type
     * @since 5.8.0
     *
     * @return string
     */
    public function getCdPostType() {
        return $this->searchOptionByKey('seopress_google_analytics_cd_post_type');
    }

    /**
     * Custom Dimension Logged In
     * @since 5.8.0
     *
     * @return string
     */
    public function getCdLoggedInUser() {
        return $this->searchOptionByKey('seopress_google_analytics_cd_logged_in_user');
    }

    /**
     * Get option for "Measure purchases"
     * @since 5.8.0
     *
     * @return string
     */
    public function getPurchases() {
        return $this->searchOptionByKey('seopress_google_analytics_purchases');
    }

    /**

     * Get option for "View item details"
     * @since 7.0.0
     *
     * @return string
     */
    public function getViewItemsDetails() {
        return $this->searchOptionByKey('seopress_google_analytics_view_product');
    }

    /**
     * Get option for "Add to cart event"
     * @since 5.8.0
     *
     * @return string
     */
    public function getAddToCart() {
        return $this->searchOptionByKey('seopress_google_analytics_add_to_cart');
    }

    /**

     * Get option for "Remove from cart event"
     * @since 5.8.0
     *
     * @return string
     */
    public function getRemoveFromCart() {
        return $this->searchOptionByKey('seopress_google_analytics_remove_from_cart');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getEnableOption(){
        return $this->searchOptionByKey('seopress_google_analytics_enable');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getGA4(){
        return $this->searchOptionByKey('seopress_google_analytics_ga4');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getGA4PropertId(){
        return $this->searchOptionByKey('seopress_google_analytics_ga4_property_id');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getRoles(){
        return $this->searchOptionByKey('seopress_google_analytics_roles');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getDisable(){
        return $this->searchOptionByKey('seopress_google_analytics_disable');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getHalfDisable(){
        return $this->searchOptionByKey('seopress_google_analytics_half_disable');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getOptOutMsg(){
        return $this->searchOptionByKey('seopress_google_analytics_opt_out_msg');
    }

    /**
     * @since 5.8.0
     *
     * @return string
     */
    public function getCbExpDate(){
        return $this->searchOptionByKey('seopress_google_analytics_cb_exp_date');
    }

    /**
     *
     * @return string
     */
    public function getMatomoEnable() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_enable');
    }

    /**
     *
     * @return string
     */
    public function getMatomoSelfHosted() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_self_hosted');
    }

    /**
     *
     * @return string
     */
    public function getMatomoId() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_id');
    }

    /**
     *
     * @return string
     */
    public function getMatomoSiteId() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_site_id');
    }

    /**
     *
     * @return string
     */
    public function getMatomoSubdomains() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_subdomains');
    }


    /**
     *
     * @return string
     */
    public function getMatomoSiteDomain() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_site_domain');
    }

    /**
     *
     * @return string
     */
    public function getMatomoNoJS() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_no_js');
    }

    /**
     *
     * @return string
     */
    public function getMatomoCrossDomain() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_cross_domain');
    }

    /**
     *
     * @return string
     */
    public function getMatomoCrossDomainSites() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_cross_domain_sites');
    }

    /**
     *
     * @return string
     */
    public function getMatomoDnt() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_dnt');
    }

    /**
     *
     * @return string
     */
    public function getMatomoNoCookies() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_no_cookies');
    }

    /**
     *
     * @return string
     */
    public function getMatomoLinkTracking() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_link_tracking');
    }

    /**
     *
     * @return string
     */
    public function getMatomoNoHeatmaps() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_no_heatmaps');
    }

    /**
     * @since 6.0.0
     *
     * @return string
     */
    public function getMatomoAuthToken() {
        return $this->searchOptionByKey('seopress_google_analytics_matomo_widget_auth_token');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getRemoveToCart() {
        return $this->searchOptionByKey('seopress_google_analytics_remove_from_cart');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getAuth() {
        return $this->searchOptionByKey('seopress_google_analytics_auth');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getAuthClientId() {
        return $this->searchOptionByKey('seopress_google_analytics_auth_client_id');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getAuthSecretId() {
        return $this->searchOptionByKey('seopress_google_analytics_auth_secret_id');
    }

    /**
     * @since 6.6.0
     *
     * @return boolean
     */
    public function getClarityEnable() {
        return $this->searchOptionByKey('seopress_google_analytics_clarity_enable');
    }

    /**
     * @since 6.6.0
     *
     * @return boolean
     */
    public function getClarityProjectId() {
        return $this->searchOptionByKey('seopress_google_analytics_clarity_project_id');
    }
}

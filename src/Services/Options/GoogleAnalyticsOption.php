<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * GoogleAnalyticsOption
 */
class GoogleAnalyticsOption {

	/**
	 * The getOption function.
	 *
	 * @since 5.8.0
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_GOOGLE_ANALYTICS );
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 5.8.0
	 *
	 * @param string $key The key.
	 *
	 * @return mixed
	 */
	public function searchOptionByKey( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->getOption();

		if ( empty( $data ) ) {
			return null;
		}

		if ( ! isset( $data[ $key ] ) ) {
			return null;
		}

		return $data[ $key ];
	}

	/**
	 * The getHook function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getHook() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_hook' );
	}

	/**
	 * The getOptOutMessageOk function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOptOutMessageOk() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_opt_out_msg_ok' );
	}

	/**
	 * The getOptOutMessageClose function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOptOutMessageClose() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_opt_out_msg_close' );
	}

	/**
	 * The getCbBg function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_bg' );
	}

	/**
	 * The getCbTxtCol function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbTxtCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_txt_col' );
	}

	/**
	 * The getCbLkCol function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbLkCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_lk_col' );
	}

	/**
	 * The getCbBtnBg function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnBg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_bg' );
	}

	/**
	 * The getCbBtnBgHov function.
	 *
	 * @since 5.8
	 *
	 * @return string
	 */
	public function getCbBtnBgHov() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_bg_hov' );
	}

	/**
	 * The getCbBtnCol function.
	 *
	 * @since 5.8
	 *
	 * @return string
	 */
	public function getCbBtnCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_col' );
	}

	/**
	 * The getCbBtnColHov function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnColHov() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_col_hov' );
	}

	/**
	 * The getCbBtnSecBg function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnSecBg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_sec_bg' );
	}

	/**
	 * The getCbBtnSecCol function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnSecCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_sec_col' );
	}

	/**
	 * The getCbBtnSecBgHov function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnSecBgHov() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_sec_bg_hov' );
	}

	/**
	 * The getCbBtnSecColHov function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBtnSecColHov() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_btn_sec_col_hov' );
	}

	/**
	 * The getCbPos function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbPos() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_pos' );
	}

	/**
	 * The getCbWidth function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbWidth() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_width' );
	}

	/**
	 * The getCbAlign function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbAlign() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_align' );
	}

	/**
	 * The getCbBackdrop function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBackdrop() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_backdrop' );
	}

	/**
	 * The getCbBackdropBg function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbBackdropBg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_backdrop_bg' );
	}

	/**
	 * The getCbTxtAlign function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbTxtAlign() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_txt_align' );
	}

	/**
	 * The getOptOutEditChoice function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOptOutEditChoice() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_opt_out_edit_choice' );
	}

	/**
	 * The getOptOutMessageEdit function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOptOutMessageEdit() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_opt_out_msg_edit' );
	}

	/**
	 * Ads
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getAds() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_ads' );
	}

	/**
	 * Additional tracking code - head
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOtherTracking() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_other_tracking' );
	}

	/**
	 * Additional tracking code - body
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOtherTrackingBody() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_other_tracking_body' );
	}

	/**
	 * Additional tracking code - footer
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOtherTrackingFooter() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_other_tracking_footer' );
	}

	/**
	 * Events external links tracking Enable
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getLinkTrackingEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_link_tracking_enable' );
	}

	/**
	 * Events downloads tracking Enable
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getDownloadTrackingEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_download_tracking_enable' );
	}

	/**
	 * Events tracking file types
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getDownloadTracking() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_download_tracking' );
	}

	/**
	 * Events affiliate links tracking Enable
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getAffiliateTrackingEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_affiliate_tracking_enable' );
	}

	/**
	 * Events tracking affiliate match
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getAffiliateTracking() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_affiliate_tracking' );
	}

	/**
	 * Events phone tracking
	 *
	 * @since 6.3.0
	 *
	 * @return string
	 */
	public function getPhoneTracking() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_phone_tracking' );
	}

	/**
	 * Custom Dimension Author
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCdAuthor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cd_author' );
	}

	/**
	 * Custom Dimension Category
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCdCategory() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cd_category' );
	}

	/**
	 * Custom Dimension Tag
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCdTag() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cd_tag' );
	}

	/**
	 * Custom Dimension Post Type
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCdPostType() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cd_post_type' );
	}

	/**
	 * Custom Dimension Logged In
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCdLoggedInUser() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cd_logged_in_user' );
	}

	/**
	 * Get option for "Measure purchases"
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getPurchases() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_purchases' );
	}

	/**

	 * Get option for "View item details"
	 *
	 * @since 7.0.0
	 *
	 * @return string
	 */
	public function getViewItemsDetails() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_view_product' );
	}

	/**
	 * Get option for "Add to cart event"
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getAddToCart() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_add_to_cart' );
	}

	/**

	 * Get option for "Remove from cart event"
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getRemoveFromCart() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_remove_from_cart' );
	}

	/**
	 * The getEnableOption function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getEnableOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_enable' );
	}

	/**
	 * The getGA4 function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getGA4() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_ga4' );
	}

	/**
	 * The getGA4PropertId function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getGA4PropertId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_ga4_property_id' );
	}

	/**
	 * The getRoles function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getRoles() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_roles' );
	}

	/**
	 * The getDisable function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_disable' );
	}

	/**
	 * The getHalfDisable function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getHalfDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_half_disable' );
	}

	/**
	 * The getOptOutMsg function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getOptOutMsg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_opt_out_msg' );
	}

	/**
	 * The getCbExpDate function.
	 *
	 * @since 5.8.0
	 *
	 * @return string
	 */
	public function getCbExpDate() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_cb_exp_date' );
	}

	/**
	 * The getMatomoEnable function.
	 *
	 * @return string
	 */
	public function getMatomoEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_enable' );
	}

	/**
	 * The getMatomoSelfHosted function.
	 *
	 * @return string
	 */
	public function getMatomoSelfHosted() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_self_hosted' );
	}

	/**
	 * The getMatomoId function.
	 *
	 * @return string
	 */
	public function getMatomoId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_id' );
	}

	/**
	 * The getMatomoSiteId function.
	 *
	 * @return string
	 */
	public function getMatomoSiteId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_site_id' );
	}

	/**
	 * The getMatomoSubdomains function.
	 *
	 * @return string
	 */
	public function getMatomoSubdomains() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_subdomains' );
	}


	/**
	 * The getMatomoSiteDomain function.
	 *
	 * @return string
	 */
	public function getMatomoSiteDomain() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_site_domain' );
	}

	/**
	 * The getMatomoNoJS function.
	 *
	 * @return string
	 */
	public function getMatomoNoJS() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_no_js' );
	}

	/**
	 * The getMatomoCrossDomain function.
	 *
	 * @return string
	 */
	public function getMatomoCrossDomain() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_cross_domain' );
	}

	/**
	 * The getMatomoCrossDomainSites function.
	 *
	 * @return string
	 */
	public function getMatomoCrossDomainSites() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_cross_domain_sites' );
	}

	/**
	 * The getMatomoDnt function.
	 *
	 * @return string
	 */
	public function getMatomoDnt() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_dnt' );
	}

	/**
	 * The getMatomoNoCookies function.
	 *
	 * @return string
	 */
	public function getMatomoNoCookies() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_no_cookies' );
	}

	/**
	 * The getMatomoLinkTracking function.
	 *
	 * @return string
	 */
	public function getMatomoLinkTracking() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_link_tracking' );
	}

	/**
	 * The getMatomoNoHeatmaps function.
	 *
	 * @return string
	 */
	public function getMatomoNoHeatmaps() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_no_heatmaps' );
	}

	/**
	 * The getMatomoAuthToken function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getMatomoAuthToken() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_matomo_widget_auth_token' );
	}

	/**
	 * The getRemoveToCart function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getRemoveToCart() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_remove_from_cart' );
	}

	/**
	 * The getAuth function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getAuth() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_auth' );
	}

	/**
	 * The getAuthClientId function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getAuthClientId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_auth_client_id' );
	}

	/**
	 * The getAuthSecretId function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getAuthSecretId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_auth_secret_id' );
	}

	/**
	 * The getClarityEnable function.
	 *
	 * @since 6.6.0
	 *
	 * @return boolean
	 */
	public function getClarityEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_clarity_enable' );
	}

	/**
	 * The getClarityProjectId function.
	 *
	 * @since 6.6.0
	 *
	 * @return boolean
	 */
	public function getClarityProjectId() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_google_analytics_clarity_project_id' );
	}
}

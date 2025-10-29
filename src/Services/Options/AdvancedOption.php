<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * AdvancedOption
 */
class AdvancedOption {

	/**
	 * The getOption function.
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_ADVANCED );
	}

	/**
	 * The searchOptionByKey function.
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
	 * The getAccessUniversalMetaboxGutenberg function.
	 *
	 * @return string
	 */
	public function getAccessUniversalMetaboxGutenberg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_universal_metabox' );
	}

	/**
	 * The getAppearanceNotification function.
	 *
	 * @return string
	 */
	public function getAppearanceNotification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_notifications' );
	}

	/**
	 * The getDisableUniversalMetaboxGutenberg function.
	 *
	 * @return string
	 */
	public function getDisableUniversalMetaboxGutenberg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->getOption();

		if ( ! isset( $data['seopress_advanced_appearance_universal_metabox_disable'] ) ) {
			return true;
		}

		return '1' === $data['seopress_advanced_appearance_universal_metabox_disable'];
	}

	/**
	 * The getSecurityMetaboxRole function.
	 *
	 * @since 5.0.3
	 */
	public function getSecurityMetaboxRole() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_security_metaboxe_role' );
	}

	/**
	 * The getSecurityMetaboxRoleContentAnalysis function.
	 *
	 * @since 5.0.3
	 */
	public function getSecurityMetaboxRoleContentAnalysis() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_security_metaboxe_ca_role' );
	}

	/**
	 * The getAdvancedAttachments function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedAttachments() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_attachments' );
	}

	/**
	 * The getAdvancedAttachmentsFile function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedAttachmentsFile() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_attachments_file' );
	}

	/**
	 * The getAdvancedReplytocom function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedReplytocom() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_replytocom' );
	}

	/**
	 * The getAdvancedNoReferrer function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedNoReferrer() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_noreferrer' );
	}

	/**
	 * The getAdvancedWPGenerator function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedWPGenerator() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_generator' );
	}

	/**
	 * The getAdvancedHentry function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedHentry() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_hentry' );
	}

	/**
	 * The getAdvancedWPShortlink function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedWPShortlink() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_shortlink' );
	}

	/**
	 * The getAdvancedWPManifest function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedWPManifest() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_wlw' );
	}

	/**
	 * The getAdvancedWPRSD function.
	 *
	 * @since 5.4.0
	 */
	public function getAdvancedWPRSD() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_rsd' );
	}

	/**
	 * The getAdvancedOEmbed function.
	 *
	 * @since 6.7.0
	 */
	public function getAdvancedOEmbed() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_oembed' );
	}

	/**
	 * The getAdvancedXPingback function.
	 *
	 * @since 6.7.0
	 */
	public function getAdvancedXPingback() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_x_pingback' );
	}

	/**
	 * The getAdvancedXPoweredBy function.
	 *
	 * @since 6.7
	 */
	public function getAdvancedXPoweredBy() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_wp_x_powered_by' );
	}

	/**
	 * The getAdvancedEmoji function.
	 *
	 * @since 7.6
	 */
	public function getAdvancedEmoji() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_emoji' );
	}

	/**
	 * The getAdvancedGoogleVerification function.
	 *
	 * @since 5.4
	 */
	public function getAdvancedGoogleVerification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_google' );
	}

	/**
	 * The getAdvancedBingVerification function.
	 *
	 * @since 5.4
	 */
	public function getAdvancedBingVerification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_bing' );
	}

	/**
	 * The getAdvancedPinterestVerification function.
	 *
	 * @since 5.4
	 */
	public function getAdvancedPinterestVerification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_pinterest' );
	}

	/**
	 * The getAdvancedYandexVerification function.
	 *
	 * @since 5.4
	 */
	public function getAdvancedYandexVerification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_yandex' );
	}

	/**
	 * The getAdvancedBaiduVerification function.
	 *
	 * @since 7.8
	 */
	public function getAdvancedBaiduVerification() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_baidu' );
	}

	/**
	 * The getAdvancedTaxDescEditor function.
	 *
	 * @since 6.5
	 */
	public function getAdvancedTaxDescEditor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_tax_desc_editor' );
	}

	/**
	 * The getImageAutoTitleEditor function.
	 *
	 * @since 5.4
	 */
	public function getImageAutoTitleEditor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_title_editor' );
	}

	/**
	 * The getImageAutoAltEditor function.
	 *
	 * @since 5.4
	 */
	public function getImageAutoAltEditor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_alt_editor' );
	}

	/**
	 * The getImageAutoCaptionEditor function.
	 *
	 * @since 5.4
	 */
	public function getImageAutoCaptionEditor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_caption_editor' );
	}

	/**
	 * The getImageAutoDescriptionEditor function.
	 *
	 * @since 5.4
	 */
	public function getImageAutoDescriptionEditor() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_desc_editor' );
	}

	/**
	 * The getAppearanceMetaboxePosition function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceMetaboxePosition() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_metaboxe_position' );
	}

	/**
	 * The getAppearanceTitleCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceTitleCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_title_col' );
	}

	/**
	 * The getAppearanceMetaDescriptionCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceMetaDescriptionCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_meta_desc_col' );
	}

	/**
	 * The getAppearanceRedirectUrlCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceRedirectUrlCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_redirect_url_col' );
	}

	/**
	 * The getAppearanceRedirectEnableCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceRedirectEnableCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_redirect_enable_col' );
	}

	/**
	 * The getAppearanceCanonical function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceCanonical() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_canonical' );
	}

	/**
	 * The getAppearanceTargetKwCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceTargetKwCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_target_kw_col' );
	}

	/**
	 * The getAppearanceNoIndexCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceNoIndexCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_noindex_col' );
	}

	/**
	 * The getAppearanceNoFollowCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceNoFollowCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_nofollow_col' );
	}

	/**
	 * The getAppearanceInboundCol function.
	 *
	 * @since 7.1
	 */
	public function getAppearanceInboundCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_inbound_col' );
	}

	/**
	 * The getAppearanceOutboundCol function.
	 *
	 * @since 7.1
	 */
	public function getAppearanceOutboundCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_outbound_col' );
	}

	/**
	 * The getAppearancePsCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearancePsCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_ps_col' );
	}

	/**
	 * The getAppearanceScoreCol function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceScoreCol() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_score_col' );
	}

	/**
	 * The getAppearanceCaMetaboxe function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceCaMetaboxe() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_ca_metaboxe' );
	}

	/**
	 * The getAppearanceAdminBar function.
	 *
	 * @since 6.6
	 */
	public function getAppearanceAdminBar() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_adminbar' );
	}

	/**
	 * The getAppearanceAdminBarCounter function.
	 *
	 * @since 8.7
	 */
	public function getAppearanceAdminBarCounter() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_adminbar_counter' );
	}

	/**
	 * The getAppearanceHideSiteOverview function.
	 *
	 * @since 6.6
	 */
	public function getAppearanceHideSiteOverview() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_seo_tools' );
	}

	/**
	 * The getAppearanceSearchConsole function.
	 *
	 * @since 5.4
	 */
	public function getAppearanceSearchConsole() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_search_console' );
	}

	/**
	 * The getAppearanceAdminBarNoIndex function.
	 *
	 * @since 6.6
	 */
	public function getAppearanceAdminBarNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_adminbar_noindex' );
	}

	/**
	 * The getAppearanceNews function.
	 *
	 * @since 6.6
	 */
	public function getAppearanceNews() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_appearance_news' );
	}

	/**
	 * The getAdvancedCleaningFileName function.
	 *
	 * @since 5.8
	 */
	public function getAdvancedCleaningFileName() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_clean_filename' );
	}

	/**
	 * The getAdvancedRemoveCategoryURL function.
	 *
	 * @since 6.6
	 */
	public function getAdvancedRemoveCategoryURL() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_category_url' );
	}

	/**
	 * The getAdvancedRemoveProductCategoryURL function.
	 *
	 * @since 6.6
	 */
	public function getAdvancedRemoveProductCategoryURL() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_product_cat_url' );
	}

	/**
	 * The getAdvancedImageAutoAltTargetKw function.
	 *
	 * @since 5.8
	 */
	public function getAdvancedImageAutoAltTargetKw() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_alt_target_kw' );
	}

	/**
	 * The getAdvancedImageAutoAltTxt function.
	 *
	 * @since 8.3
	 */
	public function getAdvancedImageAutoAltTxt() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_image_auto_alt_txt' );
	}

	/**
	 * The getSecurityGaWidgetRole function.
	 *
	 * @since 5.8
	 */
	public function getSecurityGaWidgetRole() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_security_ga_widget_role' );
	}

	/**
	 * The getSecurityMatomoWidgetRole function.
	 *
	 * @since 6.1
	 */
	public function getSecurityMatomoWidgetRole() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_security_matomo_widget_role' );
	}

	/**
	 * The getAdvancedCommentsAuthorURLDisable function.
	 *
	 * @since 6.6.0
	 */
	public function getAdvancedCommentsAuthorURLDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_comments_author_url' );
	}

	/**
	 * The getAdvancedCommentsWebsiteDisable function.
	 *
	 * @since 6.6.0
	 */
	public function getAdvancedCommentsWebsiteDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_comments_website' );
	}

	/**
	 * The getAdvancedCommentsFormLinkDisable function.
	 *
	 * @since 6.6.0
	 */
	public function getAdvancedCommentsFormLinkDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_advanced_advanced_comments_form_link' );
	}
}

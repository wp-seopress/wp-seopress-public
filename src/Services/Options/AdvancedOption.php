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

    /**
     * @since 5.4.0
     */
    public function getAdvancedAttachments(){
        return $this->searchOptionByKey('seopress_advanced_advanced_attachments');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedAttachmentsFile(){
        return $this->searchOptionByKey('seopress_advanced_advanced_attachments_file');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedReplytocom(){
        return $this->searchOptionByKey('seopress_advanced_advanced_replytocom');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedWPGenerator(){
        return $this->searchOptionByKey('seopress_advanced_advanced_wp_generator');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedHentry(){
        return $this->searchOptionByKey('seopress_advanced_advanced_hentry');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedWPShortlink(){
        return $this->searchOptionByKey('seopress_advanced_advanced_wp_shortlink');
    }

    /**
     * @since 5.4.0
     */
    public function getAdvancedWPManifest(){
        return $this->searchOptionByKey('seopress_advanced_advanced_wp_wlw');
    }
    /**
     * @since 5.4.0
     */
    public function getAdvancedWPRSD(){
        return $this->searchOptionByKey('seopress_advanced_advanced_wp_rsd');
    }
    /**
     * @since 5.4.0
     */
    public function getAdvancedGoogleVerification(){
        return $this->searchOptionByKey('seopress_advanced_advanced_google');
    }
    /**
     * @since 5.4.0
     */
    public function getAdvancedBingVerification(){
        return $this->searchOptionByKey('seopress_advanced_advanced_bing');
    }
    /**
     * @since 5.4.0
     */
    public function getAdvancedPinterestVerification(){
        return $this->searchOptionByKey('seopress_advanced_advanced_pinterest');
    }
    /**
     * @since 5.4.0
     */
    public function getAdvancedYandexVerification(){
        return $this->searchOptionByKey('seopress_advanced_advanced_yandex');
    }
    /**
     * @since 5.4.0
     */
    public function getImageAutoTitleEditor(){
        return $this->searchOptionByKey('seopress_advanced_advanced_image_auto_title_editor');
    }
    /**
     * @since 5.4.0
     */
    public function getImageAutoAltEditor(){
        return $this->searchOptionByKey('seopress_advanced_advanced_image_auto_alt_editor');
    }
    /**
     * @since 5.4.0
     */
    public function getImageAutoCaptionEditor(){
        return $this->searchOptionByKey('seopress_advanced_advanced_image_auto_caption_editor');
    }
    /**
     * @since 5.4.0
     */
    public function getImageAutoDescriptionEditor(){
        return $this->searchOptionByKey('seopress_advanced_advanced_image_auto_desc_editor');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceMetaboxePosition(){
        return $this->searchOptionByKey('seopress_advanced_appearance_metaboxe_position');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceSchemaDefaultTab(){
        return $this->searchOptionByKey('seopress_advanced_appearance_schema_default_tab');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceTitleCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_title_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceMetaDescriptionCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_meta_desc_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceRedirectUrlCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_redirect_url_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceRedirectEnableCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_redirect_enable_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceCanonical(){
        return $this->searchOptionByKey('seopress_advanced_appearance_canonical');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceTargetKwCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_target_kw_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceNoIndexCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_noindex_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceNoFollowCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_nofollow_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceWordsCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_words_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearancePsCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_ps_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceInsightsCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_insights_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceScoreCol(){
        return $this->searchOptionByKey('seopress_advanced_appearance_score_col');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceCaMetaboxe(){
        return $this->searchOptionByKey('seopress_advanced_appearance_ca_metaboxe');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceGenesisSeoMetaboxe(){
        return $this->searchOptionByKey('seopress_advanced_appearance_genesis_seo_metaboxe');
    }
    /**
     * @since 5.4.0
     */
    public function getAppearanceGenesisSeoMenu(){
        return $this->searchOptionByKey('seopress_advanced_appearance_genesis_seo_menu');
    }
}

<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class SocialOption
{
    /**
     * @since 4.5.0
     *
     * @return array
     */
    public function getOption() {
        return get_option(Options::KEY_OPTION_SOCIAL);
    }

    /**
     * @since 4.5.0
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
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgeType() {
        return $this->searchOptionByKey('seopress_social_knowledge_type');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgeName() {
        return $this->searchOptionByKey('seopress_social_knowledge_name');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsFacebook() {
        return $this->searchOptionByKey('seopress_social_accounts_facebook');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsTwitter() {
        return $this->searchOptionByKey('seopress_social_accounts_twitter');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsPinterest() {
        return $this->searchOptionByKey('seopress_social_accounts_pinterest');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsInstagram() {
        return $this->searchOptionByKey('seopress_social_accounts_instagram');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsYoutube() {
        return $this->searchOptionByKey('seopress_social_accounts_youtube');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialAccountsLinkedin() {
        return $this->searchOptionByKey('seopress_social_accounts_linkedin');
    }

    /**
     * @since 6.5.0
     *
     * @return string
     */
    public function getSocialAccountsExtra() {
        return $this->searchOptionByKey('seopress_social_accounts_extra');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgeImage() {
        return $this->searchOptionByKey('seopress_social_knowledge_img');
    }

    /**
     * @since 7.4.0
     *
     * @return string
     */
    public function getSocialKnowledgeDesc() {
        return $this->searchOptionByKey('seopress_social_knowledge_desc');
    }

    /**
     * @since 7.4.0
     *
     * @return string
     */
    public function getSocialKnowledgeEmail() {
        return $this->searchOptionByKey('seopress_social_knowledge_email');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgePhone() {
        return $this->searchOptionByKey('seopress_social_knowledge_phone');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgeContactType() {
        return $this->searchOptionByKey('seopress_social_knowledge_contact_type');
    }

    /**
     * @since 4.5.0
     *
     * @return string
     */
    public function getSocialKnowledgeContactOption() {
        return $this->searchOptionByKey('seopress_social_knowledge_contact_option');
    }

    /**
     * @since 7.4.0
     *
     * @return string
     */
    public function getSocialKnowledgeTaxID() {
        return $this->searchOptionByKey('seopress_social_knowledge_tax_id');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getSocialTwitterCard() {
        return $this->searchOptionByKey('seopress_social_twitter_card');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getSocialTwitterCardOg() {
        return $this->searchOptionByKey('seopress_social_twitter_card_og');
    }

    /**
     * @since 6.2
     *
     * @return string
     */
    public function getSocialTwitterImg() {
        return $this->searchOptionByKey('seopress_social_twitter_card_img');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getSocialTwitterImgSize() {
        return $this->searchOptionByKey('seopress_social_twitter_card_img_size');
    }


    /**
     * @since 6.5.0
     *
     * @return string
     */
    public function getSocialFacebookOGEnable() {
        return $this->searchOptionByKey('seopress_social_facebook_og');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getSocialFacebookImgDefault() {
        return $this->searchOptionByKey('seopress_social_facebook_img_default');
    }

    /**
     * @since 5.9.0
     *
     * @return string
     */
    public function getSocialFacebookImg() {
        return $this->searchOptionByKey('seopress_social_facebook_img');
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getSocialFacebookImgCpt($id = null) {
        $arg = $id;

        if (null === $id) {
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $arg = $post;
        }

        $currentCpt = get_post_type($arg);

        $option =  $this->searchOptionByKey('seopress_social_facebook_img_cpt');

        if ( ! isset($option[$currentCpt]['url'])) {
            return;
        }

        return $option[$currentCpt]['url'];
    }

    /**
     * @since 6.5.0
     *
     * @return string
     */
    public function getSocialFacebookLinkOwnership() {
        return $this->searchOptionByKey('seopress_social_facebook_link_ownership_id');
    }

    /**
     * @since 6.5.0
     *
     * @return string
     */
    public function getSocialFacebookAdminID() {
        return $this->searchOptionByKey('seopress_social_facebook_admin_id');
    }

    /**
     * @since 6.5.0
     *
     * @return string
     */
    public function getSocialFacebookAppID() {
        return $this->searchOptionByKey('seopress_social_facebook_app_id');
    }

    public function getFacebookTitlePostOption($id) {

        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_fb_title', true);
        }

        return get_post_meta($id, '_seopress_social_fb_title', true);
    }

    public function getFacebookDescriptionPostOption($id) {

        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_fb_desc', true);
        }

        return get_post_meta($id, '_seopress_social_fb_desc', true);
    }

    public function getFacebookImagePostOption($id){

        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_fb_img', true);
        }

        return get_post_meta($id, '_seopress_social_fb_img', true);

    }

    public function getFacebookImageHomeOption(){
        $pageId                 = get_option('page_for_posts');

        $value = get_post_meta($pageId, '_seopress_social_fb_img', true);
        if ( ! empty($value)) {
            return $value;
        } elseif (has_post_thumbnail($pageId)) {
            return get_the_post_thumbnail_url($pageId);
        }

    }

    public function getTwitterTitlePostOption($id){
        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_twitter_title', true);
        }

        return get_post_meta($id, '_seopress_social_twitter_title', true);
    }

    public function getTwitterDescriptionPostOption($id) {

        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_twitter_desc', true);
        }

        return get_post_meta($id, '_seopress_social_twitter_desc', true);
    }

    public function getTwitterImagePostOption($id){

        if (function_exists('is_shop') && is_shop()) {
            return get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_twitter_img', true);
        }

        return get_post_meta($id, '_seopress_social_twitter_img', true);

    }

    public function getTwitterImageHome(){
        $id = get_option('page_for_posts');
        if ( ! empty($_seopress_social_twitter_img)) {
            $value = get_post_meta($id, '_seopress_social_twitter_img', true);
            return $value;
        } elseif (has_post_thumbnail($id)) {
            return get_the_post_thumbnail_url($id);
        }
    }

    /**
     * @since 7.4.0
     *
     * @return string
     */
    public function getSocialTwitterImgDefault() {
        return $this->searchOptionByKey('seopress_social_twitter_card_img');
    }

    /**
     * @since 7.8.0
     *
     * @return string
     */
    public function getSocialLIImgSize() {
        return $this->searchOptionByKey('seopress_social_li_img_size');
    }

    /**
     * @since 8.0.0
     *
     * @return string
     */
    public function getSocialFvCreator() {
        return $this->searchOptionByKey('seopress_social_fv_creator');
    }
}

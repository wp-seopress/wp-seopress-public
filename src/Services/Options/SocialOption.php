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
}

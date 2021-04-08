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
}

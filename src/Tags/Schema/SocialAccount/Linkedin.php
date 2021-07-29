<?php

namespace SEOPress\Tags\Schema\SocialAccount;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class Linkedin implements GetTagValue {
    const NAME = 'social_account_linkedin';

    public static function getDescription() {
        return __('LinkedIn URL', 'wp-seopress');
    }

    /**
     * @since 4.5.0
     *
     * @param array $args
     *
     * @return string
     */
    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = seopress_get_service('SocialOption')->getSocialAccountsLinkedin();

        return apply_filters('seopress_get_tag_schema_social_account_linkedin', $value, $context);
    }
}

<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SocialKnowledgeContactOption implements GetTagValue
{
    const NAME = 'social_knowledge_contact_option';

    /**
     * @since 4.5.0
     *
     * @param array $args
     *
     * @return string
     */
    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = seopress_get_service('SocialOption')->getSocialKnowledgeContactOption();
        if ('None' === $value) {
            $value = '';
        }

        return apply_filters('seopress_get_tag_schema_social_knowledge_contact_option', $value, $context);
    }
}

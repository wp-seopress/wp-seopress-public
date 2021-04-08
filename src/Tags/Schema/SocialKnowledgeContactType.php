<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SocialKnowledgeContactType implements GetTagValue
{
    const NAME = 'social_knowledge_contact_type';

    /**
     * @since 4.5.0
     *
     * @param array $args
     *
     * @return string
     */
    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = seopress_get_service('SocialOption')->getSocialKnowledgeContactType();

        return apply_filters('seopress_get_tag_schema_social_knowledge_contact_type', $value, $context);
    }
}

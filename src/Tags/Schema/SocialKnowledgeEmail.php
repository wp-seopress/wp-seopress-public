<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SocialKnowledgeEmail implements GetTagValue
{
    const NAME = 'social_knowledge_email';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = seopress_get_service('SocialOption')->getSocialKnowledgeEmail();

        return apply_filters('seopress_get_tag_schema_organization_email', $value, $context);
    }
}

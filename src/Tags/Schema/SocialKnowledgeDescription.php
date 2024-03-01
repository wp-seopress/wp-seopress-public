<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SocialKnowledgeDescription implements GetTagValue
{
    const NAME = 'social_knowledge_description';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = !empty(seopress_get_service('SocialOption')->getSocialKnowledgeDesc()) ? seopress_get_service('SocialOption')->getSocialKnowledgeDesc() : get_bloginfo('tagline');

        return apply_filters('seopress_get_tag_schema_organization_description', $value, $context);
    }
}

<?php

namespace SEOPress\Tags\Schema;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Models\GetTagValue;

class SocialKnowledgeTaxId implements GetTagValue
{
    const NAME = 'social_knowledge_tax_id';

    public function getValue($args = null) {
        $context = isset($args[0]) ? $args[0] : null;

        $value   = seopress_get_service('SocialOption')->getSocialKnowledgeTaxID();

        return apply_filters('seopress_get_tag_schema_organization_tax_id', $value, $context);
    }
}

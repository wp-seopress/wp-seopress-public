<?php

namespace SEOPress\Services\Metas;

if (! defined('ABSPATH')) {
    exit;
}

class DescriptionMeta
{
    /**
     *
     * @param array $context
     * @return string|null
     */
    public function getValue($context)
    {
        if(!isset($context['post'])){
            return null;
        }

        $id = $context['post']->ID;

        $value = get_post_meta($id, '_seopress_titles_desc', true);

        return seopress_get_service('TagsToString')->replace($value, $context);
    }
}



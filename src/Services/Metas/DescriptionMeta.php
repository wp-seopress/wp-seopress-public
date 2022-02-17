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

	    $fn = $context['is_category'] ? 'get_term_meta' : 'get_post_meta';
	    $value = $fn($id, '_seopress_titles_desc', true);

        return seopress_get_service('TagsToString')->replace($value, $context);
    }
}



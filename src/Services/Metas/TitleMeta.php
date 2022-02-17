<?php

namespace SEOPress\Services\Metas;

if (! defined('ABSPATH')) {
    exit;
}

class TitleMeta
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
	    $value = $fn($id, '_seopress_titles_title', true);

        return seopress_get_service('TagsToString')->replace($value, $context);
    }
}



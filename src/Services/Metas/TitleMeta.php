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

        $value = null;
        if(isset($context['post'])){
            $id = $context['post']->ID;
            $value = get_post_meta($id, '_seopress_titles_title', true);
        }

        if(isset($context['term_id'])){
            $id = $context['term_id'];
            $value = get_term_meta($id, '_seopress_titles_title', true);
        }

        if($value === null){
            return $value;
        }

        return seopress_get_service('TagsToString')->replace($value, $context);

    }
}



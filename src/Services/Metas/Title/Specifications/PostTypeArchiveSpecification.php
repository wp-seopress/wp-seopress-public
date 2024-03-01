<?php

namespace SEOPress\Services\Metas\Title\Specifications;

class PostTypeArchiveSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $context = $params['context'];

        $postType = isset($context['post']) ? $context['post']->post_type : null;
        $value   = seopress_get_service('TitleOption')->getArchiveCptTitle($postType);

        if(empty($value) || !$value){
            return "";
        }

        return seopress_get_service('TagsToString')->replace($value, $context);
    }



    /**
     *
     * @param array $params [
     *     'post' => \WP_Post
     *     'title' => string
     *     'context' => array
     *
     * ]
     * @return boolean
     */
    public function isSatisfyBy($params)
    {
        $context = $params['context'];

        if ($context['is_post_type_archive'] && !$context['is_tax'])    {
            $postType = isset($context['post']) ? $context['post']->post_type : null;
            $value   = seopress_get_service('TitleOption')->getArchiveCptTitle($postType);

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



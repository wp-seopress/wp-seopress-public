<?php

namespace SEOPress\Services\Metas\Description\Specifications;

class PostTypeArchiveSpecification
{

    const NAME_SERVICE = 'PostTypeArchiveDescriptionSpecification';

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
        $value   = seopress_get_service('TitleOption')->getArchiveCptDescription($postType);

        if(empty($value) || !$value){
            return "";
        }

        return seopress_get_service('TagsToString')->replace($value, $context);
    }



    /**
     *
     * @param array $params [
     *     'post' => \WP_Post
     *     'description' => string
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
            $value   = seopress_get_service('TitleOption')->getArchiveCptDescription($postType);

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



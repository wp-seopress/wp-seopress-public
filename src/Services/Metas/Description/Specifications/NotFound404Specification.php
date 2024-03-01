<?php

namespace SEOPress\Services\Metas\Description\Specifications;

class NotFound404Specification
{

    const NAME_SERVICE = 'NotFound404DescriptionSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $title   = seopress_get_service('TitleOption')->getArchives404Desc();
        if(empty($title) || !$title){
            return "";
        }

        $context = $params['context'];

        return seopress_get_service('TagsToString')->replace($title, $context);
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

        if ($context['is_404']) {
            $value   = seopress_get_service('TitleOption')->getArchives404Desc();

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



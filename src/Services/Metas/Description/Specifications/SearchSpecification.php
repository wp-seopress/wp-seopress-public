<?php

namespace SEOPress\Services\Metas\Description\Specifications;

class SearchSpecification
{

    const NAME_SERVICE = 'SearchDescriptionSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $title   = seopress_get_service('TitleOption')->getArchivesSearchDesc();
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

        if ($context['is_search']) {
            $value   = seopress_get_service('TitleOption')->getArchivesSearchDesc();

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



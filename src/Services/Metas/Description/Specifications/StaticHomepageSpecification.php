<?php

namespace SEOPress\Services\Metas\Description\Specifications;

class StaticHomepageSpecification
{

    const NAME_SERVICE = 'StaticHomepageDescriptionSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $title   = seopress_get_service('TitleOption')->getHomeDescriptionTitle();
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
        $descriptionValue = $params['description'];
        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_front_page'] && $post && empty($descriptionValue)) {

            $value   = seopress_get_service('TitleOption')->getHomeDescriptionTitle();

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



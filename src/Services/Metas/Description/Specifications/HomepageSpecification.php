<?php

namespace SEOPress\Services\Metas\Description\Specifications;


class HomepageSpecification
{

    const NAME_SERVICE = 'HomepageDescriptionSpecification';
    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value   = seopress_get_service('TitleOption')->getHomeDescriptionTitle();

        if(empty($value) || !$value){
            return "";
        }

        $context = $params['context'];

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
        $descriptionValue = $params['description'];
        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_front_page'] && $context['is_home'] && isset($post) && empty($descriptionValue)) { //HOMEPAGE

            $value   = seopress_get_service('TitleOption')->getHomeDescriptionTitle();
            if(!empty($value)){
                return true;
            }
        }

        return false;

    }
}



<?php

namespace SEOPress\Services\Metas\Title\Specifications;

class StaticHomepageSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $title   = seopress_get_service('TitleOption')->getHomeSiteTitle();
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
     *     'title' => string
     *     'context' => array
     *
     * ]
     * @return boolean
     */
    public function isSatisfyBy($params)
    {
        $titleValue = $params['title'];
        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_front_page'] && $post && empty($titleValue)) {

            $homeTitle   = seopress_get_service('TitleOption')->getHomeSiteTitle();

            if(!empty($homeTitle)){
                return true;
            }

        }

        return false;

    }
}



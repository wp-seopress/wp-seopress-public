<?php

namespace SEOPress\Services\Metas\Title\Specifications;

class LatestPostsSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value   = seopress_get_service('TitleOption')->getHomeSiteTitle();
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
     *     'title' => string
     *     'context' => array
     *
     * ]
     * @return boolean
     */
    public function isSatisfyBy($params)
    {

        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_home'] && 'posts' == get_option('show_on_front')) {

            $value   = seopress_get_service('TitleOption')->getHomeSiteTitle();

            if(!empty($value)){
                return true;
            }

        }

        return false;

    }
}



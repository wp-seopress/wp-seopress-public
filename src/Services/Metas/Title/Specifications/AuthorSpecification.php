<?php

namespace SEOPress\Services\Metas\Title\Specifications;


class AuthorSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value   = seopress_get_service('TitleOption')->getArchivesAuthorTitle();

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

        if ($context['is_author']) {

            $value   = seopress_get_service('TitleOption')->getArchivesAuthorTitle();
            if(!empty($value)){
                return true;
            }
        }

        return false;

    }
}



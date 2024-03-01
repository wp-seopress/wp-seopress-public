<?php

namespace SEOPress\Services\Metas\Description\Specifications;


class AuthorSpecification
{

    const NAME_SERVICE = 'AuthorDescriptionSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value   = seopress_get_service('TitleOption')->getArchivesAuthorDescription();

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
        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_author']) {

            $value   = seopress_get_service('TitleOption')->getArchivesAuthorDescription();
            if(!empty($value)){
                return true;
            }
        }

        return false;

    }
}



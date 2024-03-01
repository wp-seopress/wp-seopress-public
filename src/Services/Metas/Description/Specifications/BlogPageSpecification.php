<?php

namespace SEOPress\Services\Metas\Description\Specifications;

class BlogPageSpecification
{

    const NAME_SERVICE = 'BlogPageDescriptionSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value = isset($params['description']) ? $params['description'] : '';

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

        if ($context['is_home'] && !empty($descriptionValue)) {
            return true;

        }

        return false;

    }
}



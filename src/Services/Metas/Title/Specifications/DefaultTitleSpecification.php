<?php

namespace SEOPress\Services\Metas\Title\Specifications;


class DefaultTitleSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $title   = sprintf('%s - %s', get_bloginfo('name'), get_bloginfo('description'));

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
       return true;

    }
}



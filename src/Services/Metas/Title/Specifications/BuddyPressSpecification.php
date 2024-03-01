<?php

namespace SEOPress\Services\Metas\Title\Specifications;


class BuddyPressSpecification
{

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {
        $value   = seopress_get_service('TitleOption')->getTitleBpGroups();

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
        if(!function_exists('bp_is_group')){
            return false;
        }

        if(!bp_is_group()){
            return false;
        }

        $value = seopress_get_service('TitleOption')->getTitleBpGroups();
        if(empty($value)){
            return false;
        }


        return true;

    }
}



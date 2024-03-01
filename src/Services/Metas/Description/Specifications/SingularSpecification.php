<?php

namespace SEOPress\Services\Metas\Description\Specifications;

use SEOPress\Constants\MetasDefaultValues;

class SingularSpecification
{

    const NAME_SERVICE = 'SingularDescriptionSpecification';


    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        $post = $params['post'];
        $value = $params['description'];
        $context = $params['context'];

        if($post){
            $context['user_id'] = $post->post_author;
        }

        if(empty($value) || !$value){
            // Global
            $globalCpt = seopress_get_service('TitleOption')->getSingleCptDesc($post->ID);
            if(!empty($globalCpt)){
                $value = $globalCpt;
            }
        }

        if(empty($value) || !$value){
           // Default excerpt or content
            $value = MetasDefaultValues::getPostTypeDescriptionValue();
        }

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

        if ($context['is_singular'] ) {
           return true;
        }

        return false;

    }
}



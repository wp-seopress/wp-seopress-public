<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Description;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Description\AbstractDescriptionSpecification;

class SingularSpecification extends AbstractDescriptionSpecification
{
    const NAME_SERVICE = 'SingularDescriptionSocialTwitterSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        $context = $params['context'];

        $post = $params['post'];

        $value = seopress_get_service('SocialOption')->getTwitterDescriptionPostOption($post->ID);

        return $this->applyFilter(seopress_get_service('TagsToString')->replace($value, $context));
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

        if ($context['is_singular'] ) {
            $post = $params['post'];
            if(!empty(seopress_get_service('SocialOption')->getTwitterDescriptionPostOption($post->ID))){
                return true;
            }
        }

        return false;

    }
}



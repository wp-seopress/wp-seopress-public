<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Title\AbstractTitleSpecification;

class SingularSpecification extends AbstractTitleSpecification
{
    const NAME_SERVICE = 'SingularSocialTwitterSpecification';

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

        $value = seopress_get_service('SocialOption')->getTwitterTitlePostOption($post->ID);

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
            if(!empty(seopress_get_service('SocialOption')->getTwitterTitlePostOption($post->ID))){
                return true;
            }
        }

        return false;

    }
}



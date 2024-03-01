<?php

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Description;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Description\AbstractDescriptionSpecification;
use SEOPress\Services\Metas\Description\DescriptionMeta;

class HomeSpecification extends AbstractDescriptionSpecification
{
    const NAME_SERVICE = 'HomeDescriptionSocialFacebookSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        $context = $params['context'];
        $value   = seopress_get_service('SocialMeta')->getFacebookHomeDescription();

        if(empty($value)){
            $descriptionMeta = new DescriptionMeta();
            $result = $descriptionMeta->getValue($params);
            if(!empty($result)){
                $value = $result;
            }
        }

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
        if(!parent::isSatisfyBy($params)){
            return false;
        }

        $context = $params['context'];
        $post = $params['post'];

        if ($context['is_home']) {
            return true;
        }

        return false;
    }
}



<?php

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Image\AbstractImageSpecification;

class SingularApplyAllSpecification extends AbstractImageSpecification
{
    const NAME_SERVICE = 'SingularImageApplyAllSocialFacebookSpecification';

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
        $GLOBALS['post'] = $post;

        return $this->applyFilter([
            'url' => seopress_get_service('FacebookImageOptionMeta')->getOnlyImageUrlFromGlobals()
        ], $params);
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
            if(
                seopress_get_service('SocialOption')->getSocialFacebookImgDefault() === "1" &&
                !empty(seopress_get_service('SocialOption')->getSocialFacebookImg())){
                return true;
            }
        }

        return false;

    }
}



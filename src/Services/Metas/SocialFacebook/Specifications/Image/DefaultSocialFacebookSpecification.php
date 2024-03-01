<?php

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Image;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialFacebook\Specifications\Image\AbstractImageSpecification;

class DefaultSocialFacebookSpecification extends AbstractImageSpecification
{
    const NAME_SERVICE = 'DefaultImageSocialFacebookSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        $site_icon = wp_get_attachment_url(get_option('site_icon'));


        return $this->applyFilter([
            'url' => $site_icon,
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
    public function isSatisfyBy($params){

        return !empty(get_option('site_icon'));
    }
}



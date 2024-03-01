<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;

class DefaultSocialTwitterSpecification extends AbstractImageSpecification
{
    const NAME_SERVICE = 'DefaultImageSocialTwitterSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        if('1' === seopress_get_service('SocialOption')->getSocialTwitterCardOg()){
            return $this->applyFilter([
                'url' => seopress_get_service('FacebookImageOptionMeta')->getOnlyImageUrlFromGlobals(),
            ], $params);
        }

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



<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;

class FeaturedImageSpecification extends AbstractImageSpecification
{
    const NAME_SERVICE = 'FeaturedImageSocialTwitterSpecification';

    /**
     * @param array $params [
     *     'context' => array
     *
     * ]
     * @return string
     */
    public function getValue($params) {

        $post = $params['post'];
        $GLOBALS['post'] = $post;

        return $this->applyFilter([
            'url' =>  get_the_post_thumbnail_url($post->ID, 'large'),
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

            if (has_post_thumbnail($post->ID)) {
                return true;
            }
        }

        return false;
    }
}



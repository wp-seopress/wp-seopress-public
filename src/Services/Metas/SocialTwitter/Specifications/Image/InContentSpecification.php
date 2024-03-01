<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;

use SEOPress\Services\Metas\SocialTwitter\Specifications\Image\AbstractImageSpecification;
use SEOPress\Services\Metas\GetImageInContent;

class InContentSpecification extends AbstractImageSpecification
{
    const NAME_SERVICE = 'InContentSocialTwitterSpecification';

    protected function  getThumbnailInContent($postId) {
        $manager = new GetImageInContent();
        return $manager->getThumbnailInContentByPostId($postId);
    }

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
            'url' =>  $this->getThumbnailInContent($post->ID),
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

            if (!empty($this->getThumbnailInContent($post->ID))) {

                return true;
            }
        }

        return false;
    }
}



<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\Title\TitleMeta;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Title\AbstractTitleSpecification;

class WithTitleSpecification extends AbstractTitleSpecification
{
    const NAME_SERVICE = 'WithTitleSocialTwitterSpecification';

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

        $titleMeta = new TitleMeta();
        return $this->applyFilter($titleMeta->getValue($params['context']));

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
        $titleMeta = new TitleMeta();
        $value = $titleMeta->getValue($params['context']);
        return !empty($value);
    }
}



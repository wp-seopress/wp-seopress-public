<?php

namespace SEOPress\Services\Metas;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\Metas\SocialSettings;

class SocialMeta
{
    protected function getTypeSocial($meta){
        switch ($meta) {
            case '_seopress_social_fb_title':
            case '_seopress_social_fb_desc':
            case '_seopress_social_fb_img':
                return 'og';

            case '_seopress_social_twitter_title':
            case '_seopress_social_twitter_desc':
            case '_seopress_social_twitter_img':

                return "twitter";
        }
    }
    protected function getKeySocial($meta){
        switch ($meta) {
            case '_seopress_social_fb_title':
            case '_seopress_social_twitter_title':
                return 'title';
            case '_seopress_social_fb_desc':
            case '_seopress_social_twitter_desc':
                    return 'description';

            case '_seopress_social_fb_img':
            case '_seopress_social_twitter_img':
                return "image";
        }
    }

    /**
     *
     * @param array $context
     * @return string|null
     */
    public function getValue($context)
    {
        $data = ["og" => [], "twitter" => []];
        if(!isset($context['post'])){
            return $data;
        }

        $id = $context['post']->ID;

        $metas = SocialSettings::getMetaKeys($id);

        foreach ($metas as $key => $value) {
            $type = $this->getTypeSocial($value['key']);
            $result = get_post_meta($id, $value['key'], true);
            $keySocial = $this->getKeySocial($value['key']);

            $data[$type][$keySocial] = $result;
        }

        return $data;
    }
}




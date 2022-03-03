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

        $callback = 'get_post_meta';
        $id = null;
        if(isset($context['post'])){
            $id = $context['post']->ID;
        }
        else if(isset($context['term_id'])){
            $id = $context['term_id'];
            $callback = 'get_term_meta';
        }

        if($id === null){
            return $data;
        }

        $metas = SocialSettings::getMetaKeys($id);

        foreach ($metas as $key => $value) {
            $type = $this->getTypeSocial($value['key']);
            $result = $callback($id, $value['key'], true);
            $keySocial = $this->getKeySocial($value['key']);

            $data[$type][$keySocial] = $result;
        }

        return $data;
    }
}




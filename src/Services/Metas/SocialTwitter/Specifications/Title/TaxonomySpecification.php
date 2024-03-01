<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Title;

use SEOPress\Helpers\Metas\SocialSettings;
use SEOPress\Services\Metas\Title\TitleMeta;
use SEOPress\Services\Metas\SocialTwitter\Specifications\Title\AbstractTitleSpecification;

class TaxonomySpecification extends AbstractTitleSpecification
{

    const NAME_SERVICE = "TaxonomySocialTwitterSpecification";

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

        $term = isset($context['term']) ? $context['term'] : null;

        $titleMeta = new TitleMeta();

        $value = "";
        # Try to get the value from the term meta for twitter
        if($term && !empty(get_term_meta($term->term_id, '_seopress_social_twitter_title', true))){
            $value = get_term_meta($term->term_id, '_seopress_social_twitter_title', true);
        }
        # Try to get the value from the term meta for facebook
        else if($term && !empty(get_term_meta($term->term_id, '_seopress_social_fb_title', true))){
            $value = get_term_meta($term->term_id, '_seopress_social_fb_title', true);
        }
        else if(!empty($titleMeta->getValue($params['context']))){
            $value = $titleMeta->getValue($params['context']);
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
        $context = $params['context'];

        if (($context['is_tax'] || $context['is_category'] || $context['is_tag']) && !$context['is_search']) {
            return true;
        }

        return false;

    }
}



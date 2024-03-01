<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Title;


abstract class AbstractTitleSpecification
{

    public function applyFilter($value){
        if (has_filter('seopress_social_twitter_card_title')) {
            return apply_filters('seopress_social_twitter_card_title', $value);
        }

        return $value;
    }

}



<?php

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Title;


abstract class AbstractTitleSpecification
{

    public function applyFilter($value){
        if (has_filter('seopress_social_og_title')) {
            return apply_filters('seopress_social_og_title', $value);
        }

        return $value;
    }

}



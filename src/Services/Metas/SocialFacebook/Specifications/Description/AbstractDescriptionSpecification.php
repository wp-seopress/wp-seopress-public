<?php

namespace SEOPress\Services\Metas\SocialFacebook\Specifications\Description;


abstract class AbstractDescriptionSpecification
{

    public function applyFilter($value){
        if (has_filter('seopress_social_og_desc')) {
            return apply_filters('seopress_social_og_desc', $value);
        }

        return $value;
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
        if($context['is_search']){
            return false;
        }

        if (function_exists('wc_memberships_is_post_content_restricted') && wc_memberships_is_post_content_restricted()) {
            return false;
        }

        return true;
    }
}



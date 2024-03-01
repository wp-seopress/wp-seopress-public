<?php

namespace SEOPress\Services\Metas\Title\Specifications;

use SEOPress\Constants\MetasDefaultValues;

class SingularSpecification
{

    protected function checkBuddypressPostId($id){
        //IS BUDDYPRESS ACTIVITY PAGE
        if (function_exists('bp_is_current_component') && bp_is_current_component('activity')) {
            return buddypress()->pages->activity->id;
        }
        //IS BUDDYPRESS MEMBERS PAGE
        if (function_exists('bp_is_current_component') && bp_is_current_component('members')) {
            return buddypress()->pages->members->id;
        }

        //IS BUDDYPRESS GROUPS PAGE
        if (function_exists('bp_is_current_component') && bp_is_current_component('groups')) {
            return buddypress()->pages->groups->id;
        }

        return $id;
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
        $title = $params['title'];
        $context = $params['context'];
        $post->ID = $this->checkBuddypressPostId($post->ID);
        // Re-request the title from the post meta for check buddypress
        $value = get_post_meta($post->ID, '_seopress_titles_title', true);

        if(!$value || empty($value)){ // Default Global
            $postType = isset($context['post']) ? $context['post']->post_type : null;
            $value = seopress_get_service('TitleOption')->getTitleFromSingle($postType);
        }

        if(empty($value)){
            $value = MetasDefaultValues::getPostTypeTitleValue();
        }

        $context['user_id'] = $post->post_author;

        return seopress_get_service('TagsToString')->replace($value, $context);
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
           return true;
        }

        return false;

    }
}



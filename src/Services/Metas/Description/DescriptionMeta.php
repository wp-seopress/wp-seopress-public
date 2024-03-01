<?php

namespace SEOPress\Services\Metas\Description;

if (! defined('ABSPATH')) {
    exit;
}

class DescriptionMeta
{

    protected $specifications = [];

    public function __construct(){
        $this->createDefaultSpecifications();
    }

    protected function createDefaultSpecifications(){
        $this->specifications = [
            seopress_get_service('StaticHomepageDescriptionSpecification'),
            seopress_get_service('HomepageDescriptionSpecification'),
            seopress_get_service('BlogPageDescriptionSpecification'),
            seopress_get_service('LatestPostsDescriptionSpecification'),
            seopress_get_service('BuddyPressDescriptionSpecification'),
            seopress_get_service('SingularDescriptionSpecification'),
            seopress_get_service('PostTypeArchiveDescriptionSpecification'),
            seopress_get_service('TaxonomyDescriptionSpecification'),
            seopress_get_service('AuthorDescriptionSpecification'),
            seopress_get_service('DateDescriptionSpecification'),
            seopress_get_service('SearchDescriptionSpecification'),
            seopress_get_service('NotFound404DescriptionSpecification'),
            seopress_get_service('DefaultDescriptionSpecification'),

        ];
    }

    protected function getDescriptionForPost($context){
        $variables = apply_filters('seopress_dyn_variables_fn', []);

        $post       = $variables['post'] ??  $context['post'];

        $descriptionValue = isset($variables['seopress_titles_description_template']) ? $variables['seopress_titles_description_template'] : '';

        if(empty($descriptionValue)){
            $descriptionValue = get_post_meta($post->ID, '_seopress_titles_desc', true);
        }

        foreach($this->specifications as $specification){
            if($specification->isSatisfyBy([
                'post' => $post,
                'description' => $descriptionValue,
                'context' => $context
            ])){
                $descriptionValue = $specification->getValue([
                    'post' => $post,
                    'description' => $descriptionValue,
                    'context' => $context,
                ]);
                break;
            }
        }

        if (has_filter('seopress_titles_desc')) {
            $descriptionValue = apply_filters('seopress_titles_desc', $descriptionValue, $context);
        }

        return $descriptionValue;

    }

    protected function getDescriptionForTaxonomy($context){

        $variables = apply_filters('seopress_dyn_variables_fn', []);

        $term       = $context['term'] ?? get_term($context['term_id']);

        $descriptionValue = isset($variables['seopress_titles_description_template']) ? $variables['seopress_titles_description_template'] : "";

        if(empty($descriptionValue)){
            $descriptionValue = get_term_meta($term->term_id, '_seopress_titles_desc', true);
        }

        foreach($this->specifications as $specification){
            if($specification->isSatisfyBy([
                'post' => $term,
                'description' => $descriptionValue,
                'context' => $context
            ])){

                $descriptionValue = $specification->getValue([
                    'post' => $term,
                    'description' => $descriptionValue,
                    'context' => $context,
                ]);
                break;
            }
        }

        if (has_filter('seopress_titles_desc')) {
            $descriptionValue = apply_filters('seopress_titles_desc', $descriptionValue, $context);
        }


        return $descriptionValue;
    }

    /**
     *
     * @param array $context
     * @return string|null
     */
    public function getValue($context)
    {

        $value = null;
        if(isset($context['post'])){
            return $this->getDescriptionForPost($context);
        }

        if(isset($context['term_id'])){
            return $this->getDescriptionForTaxonomy($context);
        }

        if($value === null){
            return $value;
        }

        return seopress_get_service('TagsToString')->replace($value, $context);
    }

}



<?php

namespace SEOPress\Services\Metas\Title;


class TitleMeta
{

    protected $specifications = [];

    public function __construct(){
        $this->createDefaultSpecifications();
    }

    protected function createDefaultSpecifications(){
        $this->specifications = [
            seopress_get_service('HomepageSpecification'),
            seopress_get_service('StaticHomepageSpecification'),
            seopress_get_service('SingularSpecification'),
            seopress_get_service('PostTypeArchiveSpecification'),
            seopress_get_service('TaxonomySpecification'),
            seopress_get_service('AuthorSpecification'),
            seopress_get_service('SearchSpecification'),
            seopress_get_service('NotFound404Specification'),
            seopress_get_service('DefaultTitleSpecification'), // Don't move, it's the last one, always "yes" for isSatisfyBy
        ];
    }

    protected function getTitleForPost($context){
        $variables = apply_filters('seopress_dyn_variables_fn', []);

        $post       = $variables['post'] ??  $context['post'];

        $titleValue = isset($variables['seopress_titles_title_template']) ? $variables['seopress_titles_title_template'] : "";

        if(empty($titleValue)){
            $titleValue = get_post_meta($post->ID, '_seopress_titles_title', true);
        }


        foreach($this->specifications as $specification){
            if($specification->isSatisfyBy([
                'post' => $post,
                'title' => $titleValue,
                'context' => $context
            ])){

                $titleValue = $specification->getValue([
                    'post' => $post,
                    'title' => $titleValue,
                    'context' => $context,
                ]);
                break;
            }
        }

        if (has_filter('seopress_titles_title')) {
            $titleValue = apply_filters('seopress_titles_title', $titleValue, $context);
        }

        return $titleValue;

    }

    protected function getTitleForTaxonomy($context){

        $variables = apply_filters('seopress_dyn_variables_fn', []);

        $term       = $context['term'] ?? get_term($context['term_id']);

        $titleValue = isset($variables['seopress_titles_title_template']) ? $variables['seopress_titles_title_template'] : "";

        if(empty($titleValue)){
            $titleValue = get_term_meta($term->term_id, '_seopress_titles_title', true);
        }

        foreach($this->specifications as $specification){
            if($specification->isSatisfyBy([
                'post' => $term,
                'title' => $titleValue,
                'context' => $context
            ])){

                $titleValue = $specification->getValue([
                    'post' => $term,
                    'title' => $titleValue,
                    'context' => $context,
                ]);
                break;
            }
        }

        if (has_filter('seopress_titles_title')) {
            $titleValue = apply_filters('seopress_titles_title', $titleValue, $context);
        }

        return $titleValue;
    }

    /**
     *
     * @param array $context
     * @return string|null
     */
    public function getValue($context)
    {
        $value = null;

        if(isset($context['post']) && $context['post'] !== null){
            $value = $this->getTitleForPost($context);
        }
        if(isset($context['term_id'])){
            $value = $this->getTitleForTaxonomy($context);
        }

        if($value === null){
            return $value;
        }

        return seopress_get_service('TagsToString')->replace($value, $context);

    }
}



<?php

namespace SEOPress\Services\Metas\SocialTwitter;

use SEOPress\Helpers\Metas\SocialSettings;

class SocialTwitterMeta
{

    protected $specifications = [];

    public function __construct(){
        $this->createDefaultSpecifications();
    }

    protected function createDefaultSpecifications(){
        $this->specifications = [
            "title" => [
                seopress_get_service('TaxonomySocialTwitterSpecification'),
                seopress_get_service('SingularSocialTwitterSpecification'),
                seopress_get_service('WithTitleSocialTwitterSpecification'),
                seopress_get_service('DefaultSocialTwitterSpecification'), // Don't move, it's the last one, always "yes" for isSatisfyBy
            ],
            "description" => [
                seopress_get_service('HomeDescriptionSocialTwitterSpecification'),
                seopress_get_service('TaxonomyDescriptionSocialTwitterSpecification'),
                seopress_get_service('SingularDescriptionSocialTwitterSpecification'),
                seopress_get_service('DefaultDescriptionSocialTwitterSpecification'),
            ],
            "image" => [
                seopress_get_service('HomeImageSocialTwitterSpecification'),
                seopress_get_service('FeaturedImageSocialTwitterSpecification'),
                seopress_get_service('InContentSocialTwitterSpecification'),
                seopress_get_service('SingularImageSocialTwitterSpecification'),
                seopress_get_service('SingularImageApplyAllSocialTwitterSpecification'),
                seopress_get_service('DefaultImageSocialTwitterSpecification'),
            ]
        ];
    }

    protected function getMetasForPost($context){

        $variables = apply_filters('seopress_dyn_variables_fn', []);

        $post       = $variables['post'] ??  $context['post'];

        $data = [
            'title' =>  "",
            'description' =>  "",
            'image' =>  "",
            'attachment_id' =>  "",
            'image_width' =>  "",
            'image_height' =>  ""
        ];

        if(seopress_get_service('SocialOption')->getSocialTwitterCard() !== '1'){
            return $data;
        }

        $metas = SocialSettings::getMetaKeysTwitter();

        foreach ($metas as $key => $value) {
            $keySocial = seopress_get_service('SocialMeta')->getKeySocial($value['key']);

            if(!isset($this->specifications[$keySocial])){
                continue;
            }

            foreach($this->specifications[$keySocial] as $keySpecification => $specification){
                if(!$specification->isSatisfyBy([
                    'post' => $post,
                    'context' => $context
                ])){
                    continue;
                }

                $item = $specification->getValue([
                    'post' => $post,
                    'context' => $context,
                ]);

                if($keySocial === 'image'){
                    $data[$keySocial] = isset($item['url']) ? $item['url'] : '';
                    $data['attachment_id'] = isset($item['attachment_id']) ? $item['attachment_id'] : '';
                    $data['image_width'] = isset($item['image_width']) ? $item['image_width'] : '';
                    $data['image_height'] = isset($item['image_height']) ? $item['image_height'] : '';
                }
                else{
                    $data[$keySocial] = $item;
                }
                break;
            }

        }

        return $data;

    }

    protected function getTitleForTaxonomy($context){
        return "";
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
            $value = $this->getMetasForPost($context);
        }
        if(isset($context['term_id'])){
            $value = $this->getTitleForTaxonomy($context);
        }

        if($value === null){
            return $value;
        }

        return $value;

    }
}



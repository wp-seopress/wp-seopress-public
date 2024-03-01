<?php

namespace SEOPress\Services\Metas\SocialFacebook;

use SEOPress\Helpers\Metas\SocialSettings;

class SocialFacebookMeta
{

    protected $specifications = [];

    public function __construct(){
        $this->createDefaultSpecifications();
    }

    protected function createDefaultSpecifications(){
        $this->specifications = [
            "title" => [
                seopress_get_service('TaxonomySocialFacebookSpecification'),
                seopress_get_service('SingularSocialFacebookSpecification'),
                seopress_get_service('WithTitleSocialFacebookSpecification'),
                seopress_get_service('DefaultSocialFacebookSpecification'), // Don't move, it's the last one, always "yes" for isSatisfyBy
            ],
            "description" => [
                seopress_get_service('HomeDescriptionSocialFacebookSpecification'),
                seopress_get_service('TaxonomyDescriptionSocialFacebookSpecification'),
                seopress_get_service('SingularDescriptionSocialFacebookSpecification'),
                seopress_get_service('DefaultDescriptionSocialFacebookSpecification'),
            ],
            "image" => [
                seopress_get_service('HomeImageSocialFacebookSpecification'),
                seopress_get_service('FeaturedImageSocialFacebookSpecification'),
                seopress_get_service('InContentSocialFacebookSpecification'),
                seopress_get_service('SingularImageSocialFacebookSpecification'),
                seopress_get_service('SingularImageApplyAllSocialFacebookSpecification'),
                seopress_get_service('DefaultImageSocialFacebookSpecification'),
            ]
        ];
    }

    protected function getMetasForPost($context){

        header('Content-Type: text/html; charset=utf-8');
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

        if(seopress_get_service('SocialOption')->getSocialFacebookOGEnable() !== '1'){
            return $data;
        }

        $metas = SocialSettings::getMetaKeysFacebook();

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

                    $data[$keySocial] = $item['url'];
                    $data['attachment_id'] = isset($item['attachment_id']) ? $item['attachment_id'] : "";
                    $data['image_width'] = isset($item['image_width']) ? $item['image_width'] : "";
                    $data['image_height'] = isset($item['image_height']) ? $item['image_height'] : "";
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



<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class ContentAnalysisDatabase
{

    public function saveData($postId, $data, $keywords)
    {
        if(!$postId || empty($data)){
            return;
        }

        $items = [
            "title" => isset($data['title']) ? $data['title']['value'] : "",
            "description" => isset($data['description']) ? $data['description']['value'] : "",
            "og_title" => isset($data['og:title']) ? $data['og:title']['value'] : "",
            "og_description" => isset($data['og:description']) ? $data['og:description']['value'] : "",
            "og_image" => isset($data['og:image']) ? $data['og:image']['value'] : "",
            'og_url' => isset($data['og:url']) ? $data['og:url']['value'] : "",
            'og_site_name' => isset($data['og:site_name']) ? $data['og:site_name']['value'] : "",
            'twitter_title' => isset($data['twitter:title']) ? $data['twitter:title']['value'] : "",
            'twitter_description' => isset($data['twitter:description']) ? $data['twitter:description']['value'] : "",
            'twitter_image' => isset($data['twitter:image']) ? $data['twitter:image']['value'] : "",
            'twitter_image_src' => isset($data['twitter:image:src']) ? $data['twitter:image:src']['value'] : "",
            'canonical' => isset($data['canonical']) ? $data['canonical']['value'] : "",
            'h1' => isset($data['h1']['value']) ? $data['h1']['value'] : "",
            'h2' => isset($data['h2']['value']) ? $data['h2']['value'] : "",
            'h3' => isset($data['h3']['value']) ? $data['h3']['value'] : "",
            'images' => isset($data['images']) ? $data['images']['value'] : "",
            'meta_robots' => isset($data['meta_robots']) ? $data['meta_robots']['value'] : "",
            'meta_google' => isset($data['meta_google']) ? $data['meta_google']['value'] : "",
            'links_no_follow' => isset($data['links_no_follow']) ? $data['links_no_follow']['value'] : "",
            'outbound_links' => isset($data['outbound_links']) ? $data['outbound_links']['value'] : "",
            'internal_links' => isset($data['internal_links']) ? $data['internal_links']['value'] : "",
            'json_schemas' => isset($data['schemas']) ? $data['schemas']['value'] : "",
            'keywords' => $keywords,
            "post_id" => $postId,
            "score" => isset($data['score']) ? $data['score'] : null,
            "permalink" => isset($data['permalink']) ? $data['permalink'] : null,
            "analysis_date" => new \DateTime()
        ];

        $alreadyExist = seopress_get_service('ContentAnalysisRepository')->analysisAlreadyExistForPostId($postId);

        if($alreadyExist){
            seopress_get_service('ContentAnalysisRepository')->updateContentAnalysis($postId, $items);
            return;
        }

        seopress_get_service('ContentAnalysisRepository')->insertContentAnalysis($items);
    }

    public function getData($postId, $columns = ["*"])
    {
        $data = seopress_get_service('ContentAnalysisRepository')->getContentAnalysis($postId, $columns);
        return $data;
    }
}

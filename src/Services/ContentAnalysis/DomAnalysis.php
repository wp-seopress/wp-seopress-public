<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class DomAnalysis
{
    protected function getMatches($content, $targetKeywords)
    {
        $data = [];
        foreach ($targetKeywords as $kw) {
            if (preg_match_all('#\b(' . $kw . ')\b#iu', $content, $m)) {
                $data[$kw][] = $m[0];
            }
        }

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function getDataAnalyze($data, $options)
    {
        if (!isset($options['id'])) {
            return $data;
        }


        $post = get_post($options['id']);

        $targetKeywords = isset($options['target_keywords']) && !empty($options['target_keywords']) ? $options['target_keywords'] : get_post_meta($options['id'], '_seopress_analysis_target_kw', true);

        $targetKeywords = array_filter(explode(',', strtolower($targetKeywords)));

        $targetKeywords = apply_filters( 'seopress_content_analysis_target_keywords', $targetKeywords, $options['id'] );

        //Manage keywords with special characters
        foreach ($targetKeywords as $key => $kw) {
            $kw               = str_replace('-', ' ', $kw); //remove dashes
            $targetKeywords[$key] = trim(htmlspecialchars_decode($kw, ENT_QUOTES));
        }

        //Remove duplicates
        $targetKeywords = array_unique($targetKeywords);


        $keysAnalyze = [
            "title",
            "description",
            "h1",
            "h2",
            "h3",
        ];

        foreach ($keysAnalyze as $value) {
            if (!isset($data[$value]) || !isset($data[$value]['value'])) {
                continue;
            }
            $data[$value]['matches'] = [];

            $items = $data[$value]['value'];
            if (is_string($items)) {
                $matches = $this->getMatches($items, $targetKeywords);
                if ($matches !== null) {
                    $keys = array_keys($matches);

                    foreach ($keys as $keyMatch => $valueMatch) {
                        $data[$value]['matches'][]= [
                            "key" => $valueMatch,
                            "count" => count($matches[$valueMatch][0])
                        ];
                    }
                }
            } elseif (is_array($items)) {
                foreach ($items as $key => $item) {
                    $matches = $this->getMatches($item, $targetKeywords);
                    if ($matches !== null) {
                        $keys = array_keys($matches);
                        foreach ($keys as $keyMatch => $valueMatch) {
                            $data[$value]['matches'][]= [
                                "key" => $valueMatch,
                                "count" => count($matches[$valueMatch][0])
                            ];
                        }
                    }
                }
            }
        }

        //Keywords in permalink
        $slug = urldecode($post->post_name);

        if (is_plugin_active('permalink-manager-pro/permalink-manager.php')) {
            global $permalink_manager_uris;
            $slug = urldecode($permalink_manager_uris[$options['id']]);
        }

        $slug = str_replace('-', ' ', $slug);

        $data['kws_permalink'] = [
            "matches" => []
        ];

        if (!empty($targetKeywords)) {
            $matches = $this->getMatches($slug, $targetKeywords);
            if ($matches !== null) {
                $keys = array_keys($matches);
                foreach ($keys as $key => $value) {
                    $data['kws_permalink']['matches'][]= [
                        "key" => $value,
                        "count" => count($matches[$value][0])
                    ];
                }
            }
        }

        //Old post
        $data['old_post'] = [
            'value' => strtotime($post->post_modified) < strtotime('-365 days')
        ];

        return $data;
    }
}

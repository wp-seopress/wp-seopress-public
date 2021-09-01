<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class DomFilterContent
{
    /**
     * @param string $str
     * @param mixed  $id
     *
     * @return array
     */
    public function getData($str, $id)
    {
        if (empty($str)) {
            return [
                'code' => 'no_data',
            ];
        }

        $dom                     = new \DOMDocument();
        $internalErrors          = libxml_use_internal_errors(true);
        $dom->preserveWhiteSpace = false;

        $dom->loadHTML($str);

        //Disable wptexturize
        add_filter('run_wptexturize', '__return_false');

        $xpath = new \DOMXPath($dom);

        $data = [
            'title' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Title',
                'value' => '',
            ],
            'description' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Description',
                'value' => '',
            ],
            'og:title' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Title',
                'value' => '',
            ],
            'og:description' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Description',
                'value' => '',
            ],
            'og:image' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Image',
                'value' => '',
            ],
            'og:url' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Url',
                'value' => '',
            ],
            'og:site_name' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\OG\Sitename',
                'value' => '',
            ],
            'twitter:title' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Title',
                'value' => '',
            ],
            'twitter:description' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Description',
                'value' => '',
            ],
            'twitter:image' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\Image',
                'value' => '',
            ],
            'twitter:image:src' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Twitter\ImageSrc',
                'value' => '',
            ],
            'canonical' => [
                'class' => '\SEOPress\Services\ContentAnalysis\GetContent\Canonical',
                'value' => '',
            ],
            'h1' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
                'value'   => '',
                'options' => [
                    'hn' => 'h1',
                ],
            ],
            'h2' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
                'value'   => '',
                'options' => [
                    'hn' => 'h2',
                ],
            ],
            'h3' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Hn',
                'value'   => '',
                'options' => [
                    'hn' => 'h3',
                ],
            ],
            'images' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Image',
                'value'   => '',
            ],
            'meta_robots' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Metas\Robot',
                'value'   => '',
            ],
            'meta_google' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Metas\Google',
                'value'   => '',
            ],
            'links_no_follow' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\LinkNoFollow',
                'value'   => '',
            ],
            'outbound_links' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\OutboundLinks',
                'value'   => '',
            ],
            'internal_links' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\InternalLinks',
                'value'   => '',
                'options' => [
                    'id' => $id,
                ],
            ],
            'schemas' => [
                'class'   => '\SEOPress\Services\ContentAnalysis\GetContent\Schema',
                'value'   => '',
            ],
        ];

        $data = apply_filters('seopress_get_data_dom_filter_content', $data);

        foreach ($data as $key => $item) {
            $class = new $item['class']();

            $options = isset($item['options']) ? $item['options'] : [];

            if (method_exists($class, 'getDataByXPath')) {
                $data[$key]['value'] = $class->getDataByXPath($xpath, $options);
            } elseif (method_exists($class, 'getDataByDom')) {
                $data[$key]['value'] = $class->getDataByDom($dom, $options);
            }
        }

        $data["permalink"] = [
            "value" => get_permalink($id)
        ];

        $data['id_homepage'] = [
            "value" => get_option('page_on_front')
        ];

        return $data;
    }
}

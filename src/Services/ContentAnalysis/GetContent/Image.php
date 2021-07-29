<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Image
{
    public function getDataByXPath($xpath, $options)
    {
        $data = [];

        $items = $xpath->query('//img');

        foreach ($items as $key => $img) {
            if (! $img->hasAttribute('src')) {
                continue;
            }

            $result = preg_match_all('#\b(avatar)\b#iu', $img->getAttribute('class'), $matches);

            //Exclude avatars from analysis
            if ($result) {
                continue;
            }

            $data[$key]['src'] = $img->getAttribute('src');
            $data[$key]['alt'] =  $img->getAttribute('alt');
        }

        return array_values($data);
    }
}

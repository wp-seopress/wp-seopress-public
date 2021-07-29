<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Schema
{
    public function getDataByXPath($xpath, $options)
    {
        $data           = [];

        $items = $xpath->query('//script[@type="application/ld+json"]');
        foreach ($items as $key => $node) {
            $json = json_decode($node->nodeValue, true);
            if (isset($json['@type'])) {
                $data[] = $json['@type'];
            }
        }

        return $data;
    }
}

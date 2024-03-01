<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Canonical {
    public function getDataByXPath($xpath, $options = []) {
        $items = $xpath->query('//link[@rel="canonical"]/@href');

        $data  = [];
        foreach ($items as $key => $item) {
            $data[] = $item->nodeValue;
        }

        return $data;
    }
}

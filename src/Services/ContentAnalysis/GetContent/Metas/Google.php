<?php

namespace SEOPress\Services\ContentAnalysis\GetContent\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Google {
    public function getDataByXPath($xpath, $options = []) {
        $items = $xpath->query('//meta[@name="google"]/@content');

        $data  = [];
        foreach ($items as $key => $item) {
            $data[] = $item->nodeValue;
        }
        
        return $data;
    }
}

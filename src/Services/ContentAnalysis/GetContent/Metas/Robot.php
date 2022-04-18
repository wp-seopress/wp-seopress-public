<?php

namespace SEOPress\Services\ContentAnalysis\GetContent\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Robot {
    public function getDataByXPath($xpath, $options = []) {
        $items = $xpath->query('//meta[@name="robots"]/@content');

        $data  = [];
        foreach ($items as $key => $item) {
            $data[] = $item->nodeValue;
        }

        return $data;
    }
}

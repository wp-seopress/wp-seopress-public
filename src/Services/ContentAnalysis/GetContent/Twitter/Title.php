<?php

namespace SEOPress\Services\ContentAnalysis\GetContent\Twitter;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Title {
    public function getDataByXPath($xpath, $options = []) {
        $values = $xpath->query('//meta[@name="twitter:title"]/@content');

        $data = [];
        if (empty($values)) {
            return $data;
        }
        foreach ($values as $key => $item) {
            $data[] = $item->nodeValue;
        }

        return $data;
    }
}

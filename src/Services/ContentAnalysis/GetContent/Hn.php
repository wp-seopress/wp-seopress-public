<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Hn {
    public function getDataByXPath($xpath, $options) {
        $data = [];
        if ( ! isset($options['hn'])) {
            return $data;
        }

        $items = $xpath->query(sprintf('//%s', $options['hn']));

        foreach ($items as $key => $item) {
            $data[] = $item->nodeValue;
        }

        return $data;
    }
}

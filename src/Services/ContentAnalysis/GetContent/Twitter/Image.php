<?php

namespace SEOPress\Services\ContentAnalysis\GetContent\Twitter;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Image {
    public function getDataByXPath($xpath, $options = []) {
        $values = $xpath->query('//meta[@name="twitter:image"]/@content');

        $data = [];

        if ($values->length === 0) {
            $values = $xpath->query('//meta[@name="twitter:image:src"]/@content');
        }

        if ($values->length === 0) {
            return $data;
        }

        foreach ($values as $key => $item) {
            $data[] = $item->nodeValue;
        }

        return $data;
    }
}

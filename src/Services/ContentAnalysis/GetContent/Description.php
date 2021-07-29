<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Description {
    public function getDataByXPath($xpath, $options = []) {
        $metas = $xpath->query('//meta[@name="description"]/@content');

        $value = '';
        foreach ($metas as $key => $item) {
            $value = $item->nodeValue;
        }

        return $value;
    }
}

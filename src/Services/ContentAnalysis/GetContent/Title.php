<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Title {
    public function getDataByDom($dom, $options = []) {
        $list = $dom->getElementsByTagName('title');

        if (0 === $list->length) {
            return '';
        }

        return $list->item(0)->textContent;
    }
}

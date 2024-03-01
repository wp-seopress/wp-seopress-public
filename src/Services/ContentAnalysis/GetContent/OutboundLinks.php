<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class OutboundLinks
{
    public function getDataByXPath($xpath, $options)
    {
        $data           = [];
        $siteUrl        = wp_parse_url(get_home_url(), PHP_URL_HOST);
        $items          =    $xpath->query("//a[not(contains(@href, '" . $siteUrl . "'))]");
        foreach ($items as $key => $link) {
            if (! empty(wp_parse_url($link->getAttribute('href'), PHP_URL_HOST))) {
                $data[] = [
                    "value"=> $link->nodeValue,
                    "url" => $link->getAttribute('href')
                ];
            }
        }

        return $data;
    }
}

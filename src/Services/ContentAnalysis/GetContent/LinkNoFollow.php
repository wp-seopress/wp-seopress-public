<?php

namespace SEOPress\Services\ContentAnalysis\GetContent;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class LinkNoFollow
{
    public function getDataByXPath($xpath, $options)
    {
        $data = [];

        $items = $xpath->query("//a[contains(@rel, 'nofollow') and not(contains(@rel, 'ugc'))]");

        foreach ($items as $link) {
            if (! preg_match_all('#\b(cancel-comment-reply-link)\b#iu', $link->getAttribute('id'), $m) && ! preg_match_all('#\b(comment-reply-link)\b#iu', $link->getAttribute('class'), $m)) {
                $data[] = [
                    "value"=> $link->nodeValue,
                    "url" => $link->getAttribute('href')
                ];
            }
        }

        return $data;
    }
}

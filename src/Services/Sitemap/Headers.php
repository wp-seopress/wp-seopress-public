<?php

namespace SEOPress\Services\Sitemap;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class Headers {
    const NAME_SERVICE = 'SitemapHeaders';

    /**
     * @since 4.3.0
     *
     * @return void
     */
    public function printHeaders() {
        $headers = ['Content-type' => 'text/xml', 'x-robots-tag' => 'noindex, follow'];
        $headers = apply_filters('seopress_sitemaps_headers', $headers);
        if (empty($headers)) {
            return;
        }

        foreach ($headers as $key => $header) {
            header($key . ':' . $header);
        }
    }
}

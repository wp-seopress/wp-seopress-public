<?php

namespace SEOPress\Services\Metas;

class GetImageInContent
{

    public function  getThumbnailInContentByPostId($postId) {
        //Get post content
        $content = get_post_field('post_content', $postId);

        if(empty($content)){
            return;
        }

        //DomDocument
        $dom            = new \DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);

        $dom->preserveWhiteSpace = false;
        if ('' != $dom->getElementsByTagName('img')) {
            $images = $dom->getElementsByTagName('img');
        }

        if (isset($images) && ! empty($images)) {
            if ($images->length >= 1) {
                foreach ($images as $img) {
                    $url = $img->getAttribute('src');
                    //Exclude Base64 img
                    if (false === strpos($url, 'data:image/')) {
                        if (true === seopress_is_absolute($url)) {
                            //do nothing
                        } else {
                            $url = get_home_url() . $url;
                        }
                        //cleaning url
                        $url = htmlspecialchars(esc_attr(wp_filter_nohtml_kses($url)));

                        //remove query strings
                        $parse_url = wp_parse_url($url);

                        if ( ! empty($parse_url['scheme']) && ! empty($parse_url['host']) && ! empty($parse_url['path'])) {
                            return $parse_url['scheme'] . '://' . $parse_url['host'] . $parse_url['path'];
                        } else {
                            return $url;
                        }
                    }
                }
            }
        }
        libxml_use_internal_errors($internalErrors);
    }


}



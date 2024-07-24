<?php

namespace SEOPress\Services\Metas\SocialTwitter\Specifications\Image;


abstract class AbstractImageSpecification
{

    public function applyFilter($value, $params){
        if (has_filter('seopress_social_twitter_card_thumb')) {
            $value['url'] = apply_filters('seopress_social_twitter_card_thumb', $value['url']);
            if (preg_match('/content="([^"]+)"/', $value['url'], $matches)) {
                $value['url'] = $matches[1];
            }
        }

        $stop_attachment_url_to_postid = apply_filters( 'seopress_stop_attachment_url_to_postid', false );

        $context = $params['context'];
        $postId = null;

        if (isset($context['post']) && !empty($context['post'])) {
            $postId = get_post_thumbnail_id($context['post']->ID);
        }

        if ($postId === 0 && $stop_attachment_url_to_postid === false) {
            $postId = attachment_url_to_postid($value['url']);

            //If cropped image
            if (0 != $postId) {
                $dir  = wp_upload_dir();
                $path = $value['url'];
                if (0 === strpos($path, $dir['baseurl'] . '/')) {
                    $path = substr($path, strlen($dir['baseurl'] . '/'));
                }

                if (preg_match('/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches)) {
                    $value['url']     = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
                    $postId = attachment_url_to_postid($value['url']);
                }
            }
        }

        if($postId !== 0){
            $imageSrc = wp_get_attachment_image_src($postId, 'full');

            $value['attachment_id'] = $postId;

            if ( ! empty($imageSrc)) {
                $value['image_width']  = $imageSrc[1];
                $value['image_height'] = $imageSrc[2];
            }

            if (!empty(get_post_meta($postId, '_wp_attachment_image_alt', true))) {
                $value['alt'] = get_post_meta($postId, '_wp_attachment_image_alt', true);
            }
        }

        return $value;
    }
}



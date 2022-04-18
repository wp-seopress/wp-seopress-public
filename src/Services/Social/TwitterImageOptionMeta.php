<?php

namespace SEOPress\Services\Social;

if ( ! defined('ABSPATH')) {
    exit;
}

class TwitterImageOptionMeta {

    public function getUrl(){
        if (function_exists('is_shop') && is_shop()) {
            $value = get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_twitter_img', true);
        } else {
            $value = get_post_meta(get_the_ID(), '_seopress_social_twitter_img', true);
        }

        return $value;
    }

    public function getAttachmentId(){
        if (function_exists('is_shop') && is_shop()) {
            $value = get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_twitter_img_attachment_id', true);
        } else {
            $value = get_post_meta(get_the_ID(), '_seopress_social_twitter_img_attachment_id', true);
        }

        return $value;

    }

}

<?php

namespace SEOPress\Services\Social;

if ( ! defined('ABSPATH')) {
    exit;
}

class FacebookImageOptionMeta {

    public function getUrl(){
        if (function_exists('is_shop') && is_shop()) {
            $value = get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_fb_img', true);
        } else {
            $value = get_post_meta(get_the_ID(), '_seopress_social_fb_img', true);
        }

        if(empty($value) &&  '1' === seopress_get_service('SocialOption')->getSocialFacebookImgDefault() ){
            $options = get_option('seopress_social_option_name');
            $value = isset($options['seopress_social_facebook_img']) ? $options['seopress_social_facebook_img'] : null;
        }

        return $value;
    }

    public function getAttachmentId(){
        if (function_exists('is_shop') && is_shop()) {
            $value = get_post_meta(get_option('woocommerce_shop_page_id'), '_seopress_social_fb_img_attachment_id', true);
        } else {
            $value = get_post_meta(get_the_ID(), '_seopress_social_fb_img_attachment_id', true);
        }

        if(empty($value) &&  '1' === seopress_get_service('SocialOption')->getSocialFacebookImgDefault() && empty(get_post_meta(get_the_ID(), '_seopress_social_fb_img', true)) ){
            $options = get_option('seopress_social_option_name');
            $value = isset($options['seopress_social_facebook_img_attachment_id']) ? $options['seopress_social_facebook_img_attachment_id'] : null;
        }

        return $value;

    }


    public function getMetasBy($strategy = 'url'){

        if($strategy === 'url'){
            $url = $this->getUrl();

            if(empty($url)){
                return '';
            }

            return $this->getMetasByUrl($url);
        }

        else if($strategy === 'id'){
            $id = $this->getAttachmentId();

            $stop_attachment_url_to_postid = apply_filters( 'seopress_stop_attachment_url_to_postid', false );

            if((empty($id) || $id === null) && !$stop_attachment_url_to_postid){
                return $this->getMetasBy('url');
            }

            return $this->getMetasStringByAttachmentId($id);
        }

        return '';
    }

    public function getMetasByUrl($url){
        $str = '';
        if (!function_exists('attachment_url_to_postid')) {
            return $str;
        }

        $postId = attachment_url_to_postid($url);

        if(empty($postId) && !empty($url)){
            return $this->getMetasStringByUrl($url);
        }

        return $this->getMetasStringByAttachmentId($postId);
    }


    public function getMetasStringByUrl($url){
        $str = '';

        //OG:IMAGE
        $str = '';
        $str .= '<meta property="og:image" content="' . esc_attr($url) . '">';
        $str .= "\n";

        //OG:IMAGE:SECURE_URL IF SSL
        if (is_ssl()) {
            $str .= '<meta property="og:image:secure_url" content="' . esc_attr($url) . '">';
            $str .= "\n";
        }

        return $str;

    }

    public function getMetasStringByAttachmentId($postId){
        $str = '';

        $imageSrc = wp_get_attachment_image_src($postId, 'full');

        if(empty($imageSrc)){
            return $str;
        }

        $url = $imageSrc[0];

        //If cropped image
        if (0 != $postId) {
            $dir  = wp_upload_dir();
            $path = $url;
            if (0 === strpos($path, $dir['baseurl'] . '/')) {
                $path = substr($path, strlen($dir['baseurl'] . '/'));
            }

            if (preg_match('/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches) && function_exists('attachment_url_to_postid')) {
                $url     = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
                $postId = attachment_url_to_postid($url);
            }
        }


        //OG:IMAGE
        $str = '';
        $str .= '<meta property="og:image" content="' . $url . '">';
        $str .= "\n";

        //OG:IMAGE:SECURE_URL IF SSL
        if (is_ssl()) {
            $str .= '<meta property="og:image:secure_url" content="' . $url . '">';
            $str .= "\n";
        }

        //OG:IMAGE:WIDTH + OG:IMAGE:HEIGHT
        if ( ! empty($imageSrc)) {
            $str .= '<meta property="og:image:width" content="' . $imageSrc[1] . '">';
            $str .= "\n";
            $str .= '<meta property="og:image:height" content="' . $imageSrc[2] . '">';
            $str .= "\n";
        }

        //OG:IMAGE:ALT
        $alt = get_post_meta($postId, '_wp_attachment_image_alt', true);
        if (!empty($alt)) {
            $str .= '<meta property="og:image:alt" content="' . esc_attr(get_post_meta($postId, '_wp_attachment_image_alt', true)) . '">';
            $str .= "\n";
        }

        return $str;

    }
}

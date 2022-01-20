<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

//Advanced
//=================================================================================================
//?replytocom
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_replytocom_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedReplytocom();
}

if ('1' == seopress_advanced_advanced_replytocom_option()) {
    add_filter('comment_reply_link', 'seopress_remove_reply_to_com');
}
function seopress_remove_reply_to_com($link) {
    return preg_replace('/href=\'(.*(\?|&)replytocom=(\d+)#respond)/', 'href=\'#comment-$3', $link);
}

//WordPress Meta generator
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_wp_generator_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedWPGenerator();
}

if ('1' == seopress_advanced_advanced_wp_generator_option()) {
    remove_action('wp_head', 'wp_generator');
}

//Remove hentry post class
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_hentry_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedHentry();
}

if ('1' == seopress_advanced_advanced_hentry_option()) {
    function seopress_advanced_advanced_hentry_hook($classes) {
        $classes = array_diff($classes, ['hentry']);

        return $classes;
    }
    add_filter('post_class', 'seopress_advanced_advanced_hentry_hook');
}

//WordPress
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_wp_shortlink_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedWPShortlink();
}

if ('1' == seopress_advanced_advanced_wp_shortlink_option()) {
    remove_action('wp_head', 'wp_shortlink_wp_head');
}

//WordPress WLWManifest
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_wp_wlw_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedWPManifest();
}

if ('1' == seopress_advanced_advanced_wp_wlw_option()) {
    remove_action('wp_head', 'wlwmanifest_link');
}

//WordPress RSD
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_wp_rsd_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedWPRSD();
}

if ('1' == seopress_advanced_advanced_wp_rsd_option()) {
    remove_action('wp_head', 'rsd_link');
}

//Google site verification
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_google_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedGoogleVerification();
}

function seopress_advanced_advanced_google_hook() {
    if ('' != seopress_advanced_advanced_google_option()) {
        $seopress_advanced_advanced_google = '<meta name="google-site-verification" content="' . seopress_advanced_advanced_google_option() . '" />';
        $seopress_advanced_advanced_google .= "\n";
        echo $seopress_advanced_advanced_google;
    }
}
if (is_home() || is_front_page()) {
    add_action('wp_head', 'seopress_advanced_advanced_google_hook', 2);
}

//Bing site verification
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_bing_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedBingVerification();
}

function seopress_advanced_advanced_bing_hook() {
    if ('' != seopress_advanced_advanced_bing_option()) {
        $seopress_advanced_advanced_bing = '<meta name="msvalidate.01" content="' . seopress_advanced_advanced_bing_option() . '" />';
        $seopress_advanced_advanced_bing .= "\n";
        echo $seopress_advanced_advanced_bing;
    }
}
if (is_home() || is_front_page()) {
    add_action('wp_head', 'seopress_advanced_advanced_bing_hook', 2);
}

//Pinterest site verification
/**
 * @deprecated 5.4.0
 */
function seopress_advanced_advanced_pinterest_option() {
    return seopress_get_service('AdvancedOption')->getAdvancedPinterestVerification();
}

function seopress_advanced_advanced_pinterest_hook() {
    if ('' != seopress_advanced_advanced_pinterest_option()) {
        $seopress_advanced_advanced_pinterest = '<meta name="p:domain_verify" content="' . seopress_advanced_advanced_pinterest_option() . '" />';
        $seopress_advanced_advanced_pinterest .= "\n";
        echo $seopress_advanced_advanced_pinterest;
    }
}

if (is_home() || is_front_page()) {
    add_action('wp_head', 'seopress_advanced_advanced_pinterest_hook', 2);
}

//Yandex site verification
function seopress_advanced_advanced_yandex_hook() {
    $contentYandex = seopress_get_service('AdvancedOption')->getAdvancedYandexVerification();

    if(empty($contentYandex)){
        return;
    }

    $meta = '<meta name="yandex-verification" content="' . $contentYandex . '" />';
    $meta .= "\n";
    echo $meta;
}

if (is_home() || is_front_page()) {
    add_action('wp_head', 'seopress_advanced_advanced_yandex_hook', 2);
}

//Automatic alt text based on target kw
function seopress_advanced_advanced_image_auto_alt_target_kw_option() {
    $seopress_advanced_advanced_image_auto_alt_target_kw_option = get_option('seopress_advanced_option_name');
    if ( ! empty($seopress_advanced_advanced_image_auto_alt_target_kw_option)) {
        foreach ($seopress_advanced_advanced_image_auto_alt_target_kw_option as $key => $seopress_advanced_advanced_image_auto_alt_target_kw_value) {
            $options[$key] = $seopress_advanced_advanced_image_auto_alt_target_kw_value;
        }
        if (isset($seopress_advanced_advanced_image_auto_alt_target_kw_option['seopress_advanced_advanced_image_auto_alt_target_kw'])) {
            return $seopress_advanced_advanced_image_auto_alt_target_kw_option['seopress_advanced_advanced_image_auto_alt_target_kw'];
        }
    }
}

if ('' != seopress_advanced_advanced_image_auto_alt_target_kw_option()) {
    function seopress_auto_img_alt_thumb_target_kw($atts, $attachment) {
        if ( ! is_admin()) {
            if (empty($atts['alt'])) {
                if ('' != get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true)) {
                    $atts['alt'] = esc_html(get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true));

                    $atts['alt'] = apply_filters('seopress_auto_image_alt_target_kw', $atts['alt']);
                }
            }
        }

        return $atts;
    }
    add_filter('wp_get_attachment_image_attributes', 'seopress_auto_img_alt_thumb_target_kw', 10, 2);

    /**
     * Replace alt for content no use gutenberg.
     *
     * @since 4.4.0.5
     *
     * @param string $content
     *
     * @return void
     */
    function seopress_auto_img_alt_target_kw($content) {
        if (empty($content)) {
            return $content;
        }

        $target_keyword = get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true);

        $target_keyword = apply_filters('seopress_auto_image_alt_target_kw', $target_keyword);

        if (empty($target_keyword)) {
            return $content;
        }

        $regex = '#<img[^>]* alt=(?:\"|\')(?<alt>([^"]*))(?:\"|\')[^>]*>#mU';

        preg_match_all($regex, $content, $matches);

        $matchesTag = $matches[0];
        $matchesAlt = $matches['alt'];

        if (empty($matchesAlt)) {
            return $content;
        }

        $regexSrc = '#<img[^>]* src=(?:\"|\')(?<src>([^"]*))(?:\"|\')[^>]*>#mU';

        foreach ($matchesAlt as $key => $alt) {
            if ( ! empty($alt)) {
                continue;
            }
            $contentMatch = $matchesTag[$key];
            preg_match($regexSrc, $contentMatch, $matchSrc);

            $contentToReplace  = str_replace('alt=""', 'alt="' . htmlspecialchars(esc_html($target_keyword)) . '"', $contentMatch);

            if ($contentMatch !== $contentToReplace) {
                $content = str_replace($contentMatch, $contentToReplace, $content);
            }
        }

        return $content;
    }
    add_filter('the_content', 'seopress_auto_img_alt_target_kw', 20);
}

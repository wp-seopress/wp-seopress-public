<?php
defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Robots.txt
///////////////////////////////////////////////////////////////////////////////////////////////////
//Add our xml sitemaps to default WP robots.txt file
function seopress_xml_sitemaps_robots_txt($output, $public) {
    if (
        (function_exists('seopress_get_toggle_option') && '1' === seopress_get_toggle_option('robots')) &&
        (function_exists('seopress_pro_get_service') && method_exists(seopress_pro_get_service('OptionPro'), 'getRobotsTxtEnable') && '1' === seopress_pro_get_service('OptionPro')->getRobotsTxtEnable())
    ){
        return $output;
    }

    if ($public === false) {
        return $output;
    }

    $output .= "\n";
    $output .= 'Sitemap: ' . get_home_url() . '/sitemaps.xml';

    return $output;
};
add_filter('robots_txt', 'seopress_xml_sitemaps_robots_txt', 10, 2);

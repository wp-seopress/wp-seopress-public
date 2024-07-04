<?php
defined('ABSPATH') or die('Please don&rsquo;t call the plugin directly. Thanks :)');

//oEmbed
//=================================================================================================
/**
 * Get Oembed Title (custom OG:title or Post title)
 * @since 3.8.9
 * @param string $post
 * @return string seopress_oembed_title
 * @author Benjamin
 */
function seopress_oembed_title_hook($post)
{
    global $wp_query;
    //Init
    $seopress_oembed_title ='';

    $variables = null;
    $variables = apply_filters('seopress_dyn_variables_fn', $variables, $post, true);

    $seopress_titles_template_variables_array 	= $variables['seopress_titles_template_variables_array'];
    $seopress_titles_template_replace_array 	= $variables['seopress_titles_template_replace_array'];

    //If OG title set
    if (get_post_meta($post->ID, '_seopress_social_fb_title', true) !='') {
        $seopress_oembed_title = get_post_meta($post->ID, '_seopress_social_fb_title', true);
    } elseif (get_post_meta($post->ID, '_seopress_titles_title', true) !='') {
        $seopress_oembed_title = get_post_meta($post->ID, '_seopress_titles_title', true);
    } elseif (get_the_title($post) !='') {
        if ((int)get_option('page_on_front') === $post->ID && null !== seopress_get_service('TitleOption')->getHomeSiteTitle()) {
            $seopress_oembed_title = esc_attr(seopress_get_service('TitleOption')->getHomeSiteTitle());
        } else {
            $seopress_oembed_title = the_title_attribute(['before'=>'','after'=>'','echo'=>false,'post'=>$post]);
        }
    }

    //Apply dynamic variables
    preg_match_all('/%%_cf_(.*?)%%/', $seopress_oembed_title, $matches); //custom fields

    if ( ! empty($matches)) {
        $seopress_titles_cf_template_variables_array = [];
        $seopress_titles_cf_template_replace_array   = [];

        foreach ($matches['0'] as $key => $value) {
            $seopress_titles_cf_template_variables_array[] = $value;
        }

        foreach ($matches['1'] as $key => $value) {
            $seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID, $value, true));
        }
    }

    preg_match_all('/%%_ct_(.*?)%%/', $seopress_oembed_title, $matches2); //custom terms taxonomy

    if ( ! empty($matches2)) {
        $seopress_titles_ct_template_variables_array = [];
        $seopress_titles_ct_template_replace_array   = [];

        foreach ($matches2['0'] as $key => $value) {
            $seopress_titles_ct_template_variables_array[] = $value;
        }

        foreach ($matches2['1'] as $key => $value) {
            $term = wp_get_post_terms($post->ID, $value);
            if ( ! is_wp_error($term)) {
                $terms                                       = esc_attr($term[0]->name);
                $seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
            }
        }
    }

    //Default
    $seopress_oembed_title = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_oembed_title);

    //Custom fields
    if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
        $seopress_oembed_title = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $seopress_oembed_title);
    }

    //Custom terms taxonomy
    if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
        $seopress_oembed_title = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $seopress_oembed_title);
    }

    $seopress_oembed_title = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_oembed_title);

    //Hook on post oEmbed title - 'seopress_oembed_title'
    $seopress_oembed_title = apply_filters('seopress_oembed_title', $seopress_oembed_title);

    return $seopress_oembed_title;
}

/**
 * Get Oembed Thumbnail (custom OG:IMAGE or Post thumbnail)
 * @since 3.8.9
 * @param string $post
 * @return array seopress_oembed_thumbnail
 * @author Benjamin
 */
function seopress_oembed_thumbnail_hook($post)
{
    //Init
    $seopress_oembed_thumbnail = [];

    //If OG title set
    if (get_post_meta($post->ID, '_seopress_social_fb_img', true) !='') {
        $seopress_oembed_thumbnail['url'] = get_post_meta($post->ID, '_seopress_social_fb_img', true);
    } elseif (get_post_thumbnail_id($post) !='') {
        $post_thumbnail_id 	=  get_post_thumbnail_id($post);

        $img_size 			= seopress_get_service('SocialOption')->getSocialLIImgSize() ? esc_attr(seopress_get_service('SocialOption')->getSocialLIImgSize()) : 'full';

        $img_size 			= apply_filters('seopress_oembed_thumbnail_size', $img_size);

        $attachment 		= wp_get_attachment_image_src($post_thumbnail_id, $img_size);

        if (is_array($attachment)) {
            $seopress_oembed_thumbnail['url'] 		= $attachment[0];
            $seopress_oembed_thumbnail['width']		= $attachment[1];
            $seopress_oembed_thumbnail['height'] 	= $attachment[2];
        }
    }

    //Hook on post oEmbed thumbnail - 'seopress_oembed_thumbnail'
    $seopress_oembed_thumbnail = apply_filters('seopress_oembed_thumbnail', $seopress_oembed_thumbnail);

    return $seopress_oembed_thumbnail;
}

add_filter('oembed_response_data', 'seopress_oembed_response_data', 10, 4);
function seopress_oembed_response_data($data, $post, $width, $height)
{
    if (function_exists('seopress_oembed_title_hook') && seopress_oembed_title_hook($post) !='') {
        $data['title'] = seopress_oembed_title_hook($post);
    }
    if (function_exists('seopress_oembed_thumbnail_hook') && seopress_oembed_thumbnail_hook($post) !='') {
        $thumbnail = seopress_oembed_thumbnail_hook($post);

        if (!empty($thumbnail['url'])) {
            $data['thumbnail_url']		= $thumbnail['url'];
        }
        if (!empty($thumbnail['width'])) {
            $data['thumbnail_width']	= $thumbnail['width'];
        } else {
            $data['thumbnail_width']	= '';
        }
        if (!empty($thumbnail['height'])) {
            $data['thumbnail_height']	= $thumbnail['height'];
        } else {
            $data['thumbnail_height']	= '';
        }
    }
    return $data;
}

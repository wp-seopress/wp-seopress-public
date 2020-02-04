<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//oEmbed
//=================================================================================================
//oEmbed Title
function seopress_oembed_title_post_option() {
	if (function_exists("is_shop") && is_shop()) {
		$_seopress_oembed_title = get_post_meta(get_option( 'woocommerce_shop_page_id' ),'_seopress_social_fb_title',true);
	} else {
		$_seopress_oembed_title = get_post_meta(get_the_ID(),'_seopress_social_fb_title',true);
	}
	if ($_seopress_oembed_title != '') {
		return $_seopress_oembed_title;
	}
}

function seopress_oembed_title_term_option() {
	$_seopress_oembed_title = get_term_meta(get_queried_object()->{'term_id'},'_seopress_social_fb_title',true);
	if ($_seopress_oembed_title != '') {
		return $_seopress_oembed_title;
	}
}

function seopress_oembed_title_home_option() {
    $page_id                = get_option( 'page_for_posts' );
	$_seopress_oembed_title = get_post_meta( $page_id, '_seopress_social_fb_title', true );
	if ( ! empty( $_seopress_oembed_title ) ) {
		return $_seopress_oembed_title;
	}
}

function seopress_oembed_title_hook() {
    //Init
    $seopress_oembed_title ='';

    if (is_home()) {
        if (seopress_oembed_title_home_option() !='') {
            $seopress_oembed_title = seopress_oembed_title_home_option();
        } elseif (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
            $seopress_oembed_title = esc_attr(seopress_titles_the_title());
        }
    } elseif (is_tax() || is_category() || is_tag()) {
        if (seopress_oembed_title_term_option() !='') {
            $seopress_oembed_title = seopress_oembed_title_term_option();
        } else {
            $seopress_oembed_title = single_term_title('', false).' - '.get_bloginfo('name');
        }
    } elseif (is_singular() && seopress_oembed_title_post_option() !='') {
        $seopress_oembed_title = seopress_oembed_title_post_option();
    } elseif(function_exists("is_shop") && is_shop() && seopress_oembed_title_post_option() !='') {
        $seopress_oembed_title = seopress_oembed_title_post_option();
    } elseif (function_exists('seopress_titles_the_title') && seopress_titles_the_title() !='') {
        $seopress_oembed_title = esc_attr(seopress_titles_the_title());
    } elseif (get_the_title() !='') {
        $seopress_oembed_title = the_title_attribute('echo=0');
    }

    //Hook on post oEmbed title - 'seopress_oembed_title'
    if (has_filter('seopress_oembed_title')) {
        $seopress_oembed_title = apply_filters('seopress_oembed_title', $seopress_oembed_title);
    }
    if (isset($seopress_oembed_title) && $seopress_oembed_title !='') {
        return $seopress_oembed_title;
    }
}

add_action('oembed_response_data', 'seopress_oembed_response_data', 10, 4);
function seopress_oembed_response_data($data, $post, $width, $height) {
    if (function_exists('seopress_oembed_title_hook') && seopress_oembed_title_hook() !='') {
        $data['title'] = seopress_oembed_title_hook();
    }
    return $data;
}

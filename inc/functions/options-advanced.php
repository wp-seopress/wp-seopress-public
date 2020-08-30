<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Advanced
//=================================================================================================
//?replytocom
function seopress_advanced_advanced_replytocom_option() {
	$seopress_advanced_advanced_replytocom_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_replytocom_option ) ) {
		foreach ($seopress_advanced_advanced_replytocom_option as $key => $seopress_advanced_advanced_replytocom_value)
			$options[$key] = $seopress_advanced_advanced_replytocom_value;
		 if (isset($seopress_advanced_advanced_replytocom_option['seopress_advanced_advanced_replytocom'])) { 
		 	return $seopress_advanced_advanced_replytocom_option['seopress_advanced_advanced_replytocom'];
		 }
	}
}

if (seopress_advanced_advanced_replytocom_option() =='1') {
	add_filter( 'comment_reply_link', 'seopress_remove_reply_to_com');
}
function seopress_remove_reply_to_com( $link ) {
	return preg_replace( '/href=\'(.*(\?|&)replytocom=(\d+)#respond)/', 'href=\'#comment-$3', $link );
}

//WordPress Meta generator
function seopress_advanced_advanced_wp_generator_option() {
	$seopress_advanced_advanced_wp_generator_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_wp_generator_option ) ) {
		foreach ($seopress_advanced_advanced_wp_generator_option as $key => $seopress_advanced_advanced_wp_generator_value)
			$options[$key] = $seopress_advanced_advanced_wp_generator_value;
		 if (isset($seopress_advanced_advanced_wp_generator_option['seopress_advanced_advanced_wp_generator'])) { 
		 	return $seopress_advanced_advanced_wp_generator_option['seopress_advanced_advanced_wp_generator'];
		 }
	}
}

if (seopress_advanced_advanced_wp_generator_option() =='1') {
	remove_action('wp_head', 'wp_generator');
}

//Remove hentry post class
function seopress_advanced_advanced_hentry_option() {
	$seopress_advanced_advanced_hentry_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_hentry_option ) ) {
		foreach ($seopress_advanced_advanced_hentry_option as $key => $seopress_advanced_advanced_hentry_value)
			$options[$key] = $seopress_advanced_advanced_hentry_value;
		 if (isset($seopress_advanced_advanced_hentry_option['seopress_advanced_advanced_hentry'])) {
		 	return $seopress_advanced_advanced_hentry_option['seopress_advanced_advanced_hentry'];
		 }
	}
}
if (seopress_advanced_advanced_hentry_option() =='1') {
	function seopress_advanced_advanced_hentry_hook( $classes ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
		return $classes;
	}
	add_filter( 'post_class', 'seopress_advanced_advanced_hentry_hook' );
}

//WordPress Shortlink
function seopress_advanced_advanced_wp_shortlink_option() {
	$seopress_advanced_advanced_wp_shortlink_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_wp_shortlink_option ) ) {
		foreach ($seopress_advanced_advanced_wp_shortlink_option as $key => $seopress_advanced_advanced_wp_shortlink_value)
			$options[$key] = $seopress_advanced_advanced_wp_shortlink_value;
		 if (isset($seopress_advanced_advanced_wp_shortlink_option['seopress_advanced_advanced_wp_shortlink'])) { 
		 	return $seopress_advanced_advanced_wp_shortlink_option['seopress_advanced_advanced_wp_shortlink'];
		 }
	}
}

if (seopress_advanced_advanced_wp_shortlink_option() =='1') {
	remove_action('wp_head', 'wp_shortlink_wp_head');
}

//WordPress WLWManifest
function seopress_advanced_advanced_wp_wlw_option() {
	$seopress_advanced_advanced_wp_wlw_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_wp_wlw_option ) ) {
		foreach ($seopress_advanced_advanced_wp_wlw_option as $key => $seopress_advanced_advanced_wp_wlw_value)
			$options[$key] = $seopress_advanced_advanced_wp_wlw_value;
		 if (isset($seopress_advanced_advanced_wp_wlw_option['seopress_advanced_advanced_wp_wlw'])) { 
		 	return $seopress_advanced_advanced_wp_wlw_option['seopress_advanced_advanced_wp_wlw'];
		 }
	}
}

if (seopress_advanced_advanced_wp_wlw_option() =='1') {
	remove_action('wp_head', 'wlwmanifest_link');
}

//WordPress RSD
function seopress_advanced_advanced_wp_rsd_option() {
	$seopress_advanced_advanced_wp_rsd_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_wp_rsd_option ) ) {
		foreach ($seopress_advanced_advanced_wp_rsd_option as $key => $seopress_advanced_advanced_wp_rsd_value)
			$options[$key] = $seopress_advanced_advanced_wp_rsd_value;
		 if (isset($seopress_advanced_advanced_wp_rsd_option['seopress_advanced_advanced_wp_rsd'])) { 
		 	return $seopress_advanced_advanced_wp_rsd_option['seopress_advanced_advanced_wp_rsd'];
		 }
	}
}

if (seopress_advanced_advanced_wp_rsd_option() =='1') {
	remove_action('wp_head', 'rsd_link');
}

//Google site verification
function seopress_advanced_advanced_google_option() {
	$seopress_advanced_advanced_google_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_google_option ) ) {
		foreach ($seopress_advanced_advanced_google_option as $key => $seopress_advanced_advanced_google_value)
			$options[$key] = $seopress_advanced_advanced_google_value;
		 if (isset($seopress_advanced_advanced_google_option['seopress_advanced_advanced_google'])) { 
		 	return $seopress_advanced_advanced_google_option['seopress_advanced_advanced_google'];
		 }
	}
}

function seopress_advanced_advanced_google_hook() {
	if (seopress_advanced_advanced_google_option() !='') {
		$seopress_advanced_advanced_google = '<meta name="google-site-verification" content="'.seopress_advanced_advanced_google_option().'" />';
		$seopress_advanced_advanced_google .= "\n";
		echo $seopress_advanced_advanced_google;
	}
}
if (is_home() || is_front_page()) {
	add_action( 'wp_head', 'seopress_advanced_advanced_google_hook', 2 );
}

//Bing site verification
function seopress_advanced_advanced_bing_option() {
	$seopress_advanced_advanced_bing_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_bing_option ) ) {
		foreach ($seopress_advanced_advanced_bing_option as $key => $seopress_advanced_advanced_bing_value)
			$options[$key] = $seopress_advanced_advanced_bing_value;
		 if (isset($seopress_advanced_advanced_bing_option['seopress_advanced_advanced_bing'])) { 
		 	return $seopress_advanced_advanced_bing_option['seopress_advanced_advanced_bing'];
		 }
	}
};

function seopress_advanced_advanced_bing_hook() {
	if (seopress_advanced_advanced_bing_option() !='') {
		$seopress_advanced_advanced_bing = '<meta name="msvalidate.01" content="'.seopress_advanced_advanced_bing_option().'" />';
		$seopress_advanced_advanced_bing .= "\n";
		echo $seopress_advanced_advanced_bing;
	}
}
if (is_home() || is_front_page()) {
	add_action( 'wp_head', 'seopress_advanced_advanced_bing_hook', 2 );
}

//Pinterest site verification
function seopress_advanced_advanced_pinterest_option() {
	$seopress_advanced_advanced_pinterest_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_pinterest_option ) ) {
		foreach ($seopress_advanced_advanced_pinterest_option as $key => $seopress_advanced_advanced_pinterest_value)
			$options[$key] = $seopress_advanced_advanced_pinterest_value;
		 if (isset($seopress_advanced_advanced_pinterest_option['seopress_advanced_advanced_pinterest'])) { 
		 	return $seopress_advanced_advanced_pinterest_option['seopress_advanced_advanced_pinterest'];
		 }
	}
};

function seopress_advanced_advanced_pinterest_hook() {
	if (seopress_advanced_advanced_pinterest_option() !='') {
		$seopress_advanced_advanced_pinterest = '<meta name="p:domain_verify" content="'.seopress_advanced_advanced_pinterest_option().'" />';
		$seopress_advanced_advanced_pinterest .= "\n";
		echo $seopress_advanced_advanced_pinterest;
	}
}

if (is_home() || is_front_page()) {
	add_action( 'wp_head', 'seopress_advanced_advanced_pinterest_hook', 2 );
}

//Yandex site verification
function seopress_advanced_advanced_yandex_option() {
	$seopress_advanced_advanced_yandex_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_yandex_option ) ) {
		foreach ($seopress_advanced_advanced_yandex_option as $key => $seopress_advanced_advanced_yandex_value)
			$options[$key] = $seopress_advanced_advanced_yandex_value;
		 if (isset($seopress_advanced_advanced_yandex_option['seopress_advanced_advanced_yandex'])) { 
		 	return $seopress_advanced_advanced_yandex_option['seopress_advanced_advanced_yandex'];
		 }
	}
};

function seopress_advanced_advanced_yandex_hook() {
	if (seopress_advanced_advanced_yandex_option() !='') {
		$seopress_advanced_advanced_yandex = '<meta name="yandex-verification" content="'.seopress_advanced_advanced_yandex_option().'" />';
		$seopress_advanced_advanced_yandex .= "\n";
		echo $seopress_advanced_advanced_yandex;
	}
}

if (is_home() || is_front_page()) {
	add_action( 'wp_head', 'seopress_advanced_advanced_yandex_hook', 2 );
}

//Automatic alt text based on target kw
function seopress_advanced_advanced_image_auto_alt_target_kw_option() {
	$seopress_advanced_advanced_image_auto_alt_target_kw_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_image_auto_alt_target_kw_option ) ) {
		foreach ($seopress_advanced_advanced_image_auto_alt_target_kw_option as $key => $seopress_advanced_advanced_image_auto_alt_target_kw_value)
			$options[$key] = $seopress_advanced_advanced_image_auto_alt_target_kw_value;
		if (isset($seopress_advanced_advanced_image_auto_alt_target_kw_option['seopress_advanced_advanced_image_auto_alt_target_kw'])) {
			return $seopress_advanced_advanced_image_auto_alt_target_kw_option['seopress_advanced_advanced_image_auto_alt_target_kw'];
		}
	}
}

if (seopress_advanced_advanced_image_auto_alt_target_kw_option() !='') {
	function seopress_auto_img_alt_thumb_target_kw( $atts, $attachment ) {
		if (!is_admin()) {
			if (empty($atts['alt'])) {
				if (get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true) !='') {
					$atts['alt'] = esc_html(get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true));
				}
			}
		}
		return $atts;
	}
	add_filter( 'wp_get_attachment_image_attributes', 'seopress_auto_img_alt_thumb_target_kw', 10, 2 );

	function seopress_auto_img_alt_target_kw($content) {
			if ($content =='') {
				return $content;
			}

			$dom = new domDocument;
			$internalErrors = libxml_use_internal_errors(true);
			
			if (function_exists('mb_convert_encoding')) {
				$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
			} else {
				$dom->loadHTML('<?xml encoding="utf-8" ?>'.$content);
			}

			$dom->preserveWhiteSpace = false;

			if ($dom->getElementsByTagName('img') !='') {
				$images = $dom->getElementsByTagName('img');
			}
			
			libxml_use_internal_errors($internalErrors);

			if ((isset($images) && !empty ($images) && $images->length>=1)) {
				foreach($images as $img) {
					if ($img->getAttribute('alt') =='') {
						if (get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true) !='') {
							$img = $img->setAttribute('alt', htmlspecialchars(esc_html(get_post_meta(get_the_ID(), '_seopress_analysis_target_kw', true))));
						}
					}
				}
			}
			$content = $dom->saveHTML();

			return $content;
	}
	add_filter('the_content', 'seopress_auto_img_alt_target_kw', 20, 1);
}
<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Advanced
//=================================================================================================
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
};

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



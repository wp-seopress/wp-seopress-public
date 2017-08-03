<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

///////////////////////////////////////////////////////////////////////////////////////////////////
//SEOPRESS Core
///////////////////////////////////////////////////////////////////////////////////////////////////

//Import / Export tool
add_action('init', 'seopress_enable', 999);
function seopress_enable() {
	if (is_admin()){
		require_once ( dirname( __FILE__ ) . '/options-import-export.php'); //Import Export
	}
}

//Front END
if (seopress_get_toggle_titles_option() =='1') {
	add_action('wp_head', 'seopress_load_titles_options', 0);
	function seopress_load_titles_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-titles-metas.php'); //Titles & metas
		}
	}
}
if (seopress_get_toggle_social_option() =='1') {
	add_action('wp_head', 'seopress_load_social_options', 0);
	function seopress_load_social_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-social.php'); //Social
		}
	}
}
if (seopress_get_toggle_google_analytics_option() =='1') {
	add_action('wp_head', 'seopress_load_google_analytics_options', 0);
	function seopress_load_google_analytics_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-google-analytics.php'); //Google Analytics
		}
	}
}
add_action('init', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options() {
	if (!is_admin()){	
	    require_once ( dirname( __FILE__ ) . '/options-redirections.php'); //Redirections
	}
}
if (seopress_get_toggle_xml_sitemap_option() =='1') {
	add_action('init', 'seopress_load_sitemap', 999);
	function seopress_load_sitemap() {
		if (!is_admin()) {
			require_once ( dirname( __FILE__ ) . '/options-sitemap.php'); //XML / HTML Sitemap
		}
	}	
}
if (seopress_get_toggle_advanced_option() =='1') {
	add_action('wp_head', 'seopress_load_advanced_options', 0);
	function seopress_load_advanced_options() {
		if (!is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-advanced.php'); //Advanced
		}
	}
	add_action('init', 'seopress_load_advanced_admin_options', 0);
	function seopress_load_advanced_admin_options() {
		if (is_admin()){	
		    require_once ( dirname( __FILE__ ) . '/options-advanced-admin.php'); //Advanced (admin)
		}
	}
}
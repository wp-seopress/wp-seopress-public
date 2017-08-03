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

//Front END - Titles & metas - Redirections
add_action('wp_head', 'seopress_load_titles_options', 0);
function seopress_load_titles_options() {
	if (!is_admin()){	
	    require_once ( dirname( __FILE__ ) . '/options-titles-metas.php'); //Titles & metas
	    require_once ( dirname( __FILE__ ) . '/options-social.php'); //Social
	}
}

add_action('init', 'seopress_load_redirections_options', 0);
function seopress_load_redirections_options() {
	if (!is_admin()){	
	    require_once ( dirname( __FILE__ ) . '/options-redirections.php'); //Redirections
	}
}

add_action('init', 'seopress_load_sitemap', 999);
function seopress_load_sitemap() {
	require_once ( dirname( __FILE__ ) . '/options-sitemap.php'); //XML Sitemap
}	

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
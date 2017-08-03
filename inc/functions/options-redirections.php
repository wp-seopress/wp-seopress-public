<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Redirections
//=================================================================================================
//Enabled
function seopress_redirections_enabled() {
	global $post;
	if ($post) {
		if (get_post_meta($post->ID,'_seopress_redirections_enabled',true)) { 
			$seopress_redirections_enabled = get_post_meta($post->ID,'_seopress_redirections_enabled',true);
			return $seopress_redirections_enabled;
		}
	}
}

//Type
function seopress_redirections_type() {
	global $post;
	if (get_post_meta($post->ID,'_seopress_redirections_type',true)) { 
		$seopress_redirections_type = get_post_meta($post->ID,'_seopress_redirections_type',true);
		return $seopress_redirections_type;
	}
}

//URL to redirect
function seopress_redirections_value() {
	global $post;
	if (get_post_meta($post->ID,'_seopress_redirections_value',true)) {
		$seopress_redirections_value = get_post_meta($post->ID,'_seopress_redirections_value',true);
		return $seopress_redirections_value;
	}
}

function seopress_redirections_hook() {
	if (seopress_redirections_enabled() =='yes') {
		if (seopress_redirections_type() && seopress_redirections_value() !='') {
			wp_redirect(seopress_redirections_value(), seopress_redirections_type());
			exit();	
		}
	}
}
add_action('template_redirect', 'seopress_redirections_hook', 1);


//Attachments redirects
function seopress_advanced_advanced_attachments_option() {
	$seopress_advanced_advanced_attachments_option = get_option("seopress_advanced_option_name");
	if ( ! empty ( $seopress_advanced_advanced_attachments_option ) ) {
		foreach ($seopress_advanced_advanced_attachments_option as $key => $seopress_advanced_advanced_attachments_value)
			$options[$key] = $seopress_advanced_advanced_attachments_value;
		 if (isset($seopress_advanced_advanced_attachments_option['seopress_advanced_advanced_attachments'])) { 
		 	return $seopress_advanced_advanced_attachments_option['seopress_advanced_advanced_attachments'];
		 }
	}
};

function seopress_redirections_attachments(){
	if (seopress_advanced_advanced_attachments_option() =='1') {
		global $post;
		if ( is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent != 0) ) {
	    	wp_redirect( get_permalink( $post->post_parent ), 301 );
	    	exit();
		    wp_reset_postdata();
		} elseif (is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent == 0)) {
			wp_redirect(get_home_url(), 302);
			exit();
		}
	}
}
add_action( 'template_redirect', 'seopress_redirections_attachments', 1 );

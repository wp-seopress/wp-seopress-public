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

function seopress_redirections_term_enabled() {
	$_seopress_redirections_enabled = get_term_meta(get_queried_object()->{'term_id'},'_seopress_redirections_enabled',true);
	if ($_seopress_redirections_enabled != '') {
		return $_seopress_redirections_enabled;
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

function seopress_redirections_term_type() {
	$_seopress_redirections_type = get_term_meta(get_queried_object()->{'term_id'},'_seopress_redirections_type',true);
	if ($_seopress_redirections_type != '') {
		return $_seopress_redirections_type;
	}
}

//URL to redirect
function seopress_redirections_value() {
	global $post;
	if (is_singular() && get_post_meta($post->ID,'_seopress_redirections_value',true)) {
		$seopress_redirections_value = get_post_meta($post->ID,'_seopress_redirections_value',true);
		return $seopress_redirections_value;
 	} elseif ((is_tax() || is_category() || is_tag()) && get_term_meta(get_queried_object()->{'term_id'},'_seopress_redirections_value',true) !='') {
		$seopress_redirections_value = get_term_meta(get_queried_object()->{'term_id'},'_seopress_redirections_value',true);
		return $seopress_redirections_value;
	} else {
		$seopress_redirections_value = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$seopress_redirections_query = new WP_Query( array( 
				'post_type' => 'seopress_404', 
				'posts_per_page' => '-1',           
				'update_post_term_cache' => false, // don't retrieve post terms
				'update_post_meta_cache' => false, // don't retrieve post meta 
			) 
		);
		$all_titles = array();
		if ( $seopress_redirections_query->have_posts() ) {
			while ( $seopress_redirections_query->have_posts() ) {
				$seopress_redirections_query->the_post();
				array_push($all_titles, get_the_title());
			}
			if (in_array($seopress_redirections_value, $all_titles)) {
				//do_redirect
				return $seopress_redirections_value;
			}
			wp_reset_postdata();
		}
	}
}

function seopress_redirections_hook() {
 	if ((is_tax() || is_category() || is_tag()) && seopress_redirections_term_enabled() =='yes') {
		if (seopress_redirections_term_type() && seopress_redirections_value() !='') {
			wp_redirect(seopress_redirections_value(), seopress_redirections_term_type());
			exit();
		}
	} elseif (seopress_redirections_enabled() =='yes') {
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
}

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

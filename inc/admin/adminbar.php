<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Admin bar customization
 */

function seopress_admin_bar_links() {

	global $wp_admin_bar;

	// Adds a new top level admin bar link and a submenu to it
	$wp_admin_bar->add_menu( array(
		'parent'	=> false,
		'id'		=> 'seopress_custom_top_level',
		'title'		=> '<img style="height:22px;vertical-align:middle;margin-right:5px" src=" data:image/png;base64,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"/>'.__( 'SEO', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-option' ),
	));
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_titles',
		'title'		=> __( 'Titles & Metas', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-titles' ),
	));
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_xml_sitemap',
		'title'		=> __( 'XML / HTML Sitemap', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-xml-sitemap' ),
	));
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_social',
		'title'		=> __( 'Social', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-social' ),
	));	
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_google_analytics',
		'title'		=> __( 'Google Analytics', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-google-analytics' ),
	));
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_advanced',
		'title'		=> __( 'Advanced', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-advanced' ),
	));	
	$wp_admin_bar->add_menu( array(
		'parent'	=> 'seopress_custom_top_level',
		'id'		=> 'seopress_custom_sub_menu_import_export',
		'title'		=> __( 'Import / Export / Reset', 'wp-seopress' ),
		'href'		=> admin_url( 'admin.php?page=seopress-import-export' ),
	));
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent'	=> 'seopress_custom_top_level',
			'id'		=> 'seopress_custom_sub_menu_pro',
			'title'		=> __( 'PRO', 'wp-seopress' ),
			'href'		=> admin_url( 'admin.php?page=seopress-pro-page' ),
		));
	}
}
add_action( 'admin_bar_menu', 'seopress_admin_bar_links', 99 );

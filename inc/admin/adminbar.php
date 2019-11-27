<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Admin bar customization
 */

function seopress_admin_bar_links() {
	if (function_exists('seopress_advanced_appearance_adminbar_option') && seopress_advanced_appearance_adminbar_option() !='1') {
		global $wp_admin_bar;

		$title = '<span class="ab-icon icon-seopress-seopress"></span> '.__( 'SEO', 'wp-seopress' );
		$title = apply_filters('seopress_adminbar_icon',$title);

		// Adds a new top level admin bar link and a submenu to it
		$wp_admin_bar->add_menu( array(
			'parent'	=> false,
			'id'		=> 'seopress_custom_top_level',
			'title'		=> $title,
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
			'title'		=> __( 'Social Networks', 'wp-seopress' ),
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
			'title'		=> __( 'Tools', 'wp-seopress' ),
			'href'		=> admin_url( 'admin.php?page=seopress-import-export' ),
		));
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			if(seopress_get_toggle_bot_option()=='1') {
				$wp_admin_bar->add_menu( array(
					'parent'	=> 'seopress_custom_top_level',
					'id'		=> 'seopress_custom_sub_menu_bot',
					'title'		=> __( 'BOT', 'wp-seopress' ),
					'href'		=> admin_url( 'admin.php?page=seopress-bot-batch' ),
				));
			}
			$wp_admin_bar->add_menu( array(
				'parent'	=> 'seopress_custom_top_level',
				'id'		=> 'seopress_custom_sub_menu_license',
				'title'		=> __( 'License', 'wp-seopress' ),
				'href'		=> admin_url( 'admin.php?page=seopress-license' ),
			));
			$wp_admin_bar->add_menu( array(
				'parent'	=> 'seopress_custom_top_level',
				'id'		=> 'seopress_custom_sub_menu_pro',
				'title'		=> __( 'PRO', 'wp-seopress' ),
				'href'		=> admin_url( 'admin.php?page=seopress-pro-page' ),
			));
			if(seopress_get_toggle_rich_snippets_option()=='1') { 
				$wp_admin_bar->add_menu( array(
					'parent'	=> 'seopress_custom_top_level',
					'id'		=> 'seopress_custom_sub_menu_schemas',
					'title'		=> __( 'Schemas', 'wp-seopress' ),
					'href'		=> admin_url( 'edit.php?post_type=seopress_schemas' ),
				));
			}
			if(seopress_get_toggle_404_option()=='1') {
				$wp_admin_bar->add_menu( array(
					'parent'	=> 'seopress_custom_top_level',
					'id'		=> 'seopress_custom_sub_menu_404',
					'title'		=> __( 'Redirections', 'wp-seopress' ),
					'href'		=> admin_url( 'edit.php?post_type=seopress_404' ),
				));
			}
			if(seopress_get_toggle_bot_option()=='1') {
				$wp_admin_bar->add_menu( array(
					'parent'	=> 'seopress_custom_top_level',
					'id'		=> 'seopress_custom_sub_menu_broken_links',
					'title'		=> __( 'Broken Links', 'wp-seopress' ),
					'href'		=> admin_url( 'edit.php?post_type=seopress_bot' ),
				));
			}
			$wp_admin_bar->add_menu( array(
				'parent'	=> 'seopress_custom_top_level',
				'id'		=> 'seopress_custom_sub_menu_backlinks',
				'title'		=> __( 'Backlinks', 'wp-seopress' ),
				'href'		=> admin_url( 'edit.php?post_type=seopress_backlinks' ),
			));
		}
		$wp_admin_bar->add_menu( array(
			'parent'	=> 'seopress_custom_top_level',
			'id'		=> 'seopress_custom_sub_menu_wizard',
			'title'		=> __( 'Configuration wizard', 'wp-seopress' ),
			'href'		=> admin_url( 'admin.php?page=seopress-setup' ),
		));
	}
}
add_action( 'admin_bar_menu', 'seopress_admin_bar_links', 99 );

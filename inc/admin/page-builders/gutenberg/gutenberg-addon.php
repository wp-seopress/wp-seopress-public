<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! defined( 'SEOPRESS_GUTENBERG_ADDON_DIR' ) ) {
	define( 'SEOPRESS_GUTENBERG_ADDON_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'SEOPRESS_GUTENBERG_ADDON_URL' ) ) {
	define( 'SEOPRESS_GUTENBERG_ADDON_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

include_once __DIR__ . '/inc/blocks/faq-block/src/register-block.php';

add_action( 'init', 'seopress_register_blocks');

function seopress_register_blocks() {
	seopress_register_block_faq([
		'dependencies' => [
			'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-polyfill'
		], 
		'version' => '32cbd8f1ef6cd98f5a5b3e8855d79aec'
	]);
}


add_filter( 'block_categories', 'seopress_register_block_categories' );	

function seopress_register_block_categories( $categories ) {
	return array_merge(
		$categories,[
			[
				'slug'  => 'wpseopress',
				'title' => __( 'SEO', 'wp-seopress' ),
			],
		]
	);
}



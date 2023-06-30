<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

global $wp_version;
$hook_name = version_compare( $wp_version, '5.8' ) >= 0 ? 'block_categories_all' : 'block_categories';
add_filter( $hook_name, 'seopress_register_block_categories' );
/**
 * Declares a new category
 *
 * @param   array  $categories  Existing categories
 * @return  array  $categories
 */
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

/**
 * Register blocks
 */
add_action( 'init', 'seopress_register_blocks', 1000 );
function seopress_register_blocks() {
    require_once __DIR__ . '/blocks/faq/block.php';
    require_once __DIR__ . '/blocks/sitemap/block.php';

	// FAQ Block
	seopress_register_block_faq();

	// Sitemap Block
    register_block_type( __DIR__ . '/blocks/sitemap', [
        'render_callback' => 'seopress_sitemap_block',
        'attributes' => [
            'postTypes' => [
                'type'    => 'array',
                'default' => []
            ],
            'isSiteMapEnabled' => [
                'type'    => 'boolean',
                'default' => ( '1' == seopress_get_toggle_option( 'xml-sitemap' ) ) && ( '1' === seopress_get_service('SitemapOption')->getHtmlEnable() )
            ],
            'optionsPageUrl' => [
                'type'    => 'string',
                'default' => add_query_arg( 'page', 'seopress-xml-sitemap', admin_url( 'admin.php' ) )
            ],
            'fontSize'        => [ 'type' => 'string' ],
            'backgroundColor' => [ 'type' => 'string' ],
            'style'           => [ 'type' => 'object' ],
            'textColor'       => [ 'type' => 'string' ],
            'gradient'        => [ 'type' => 'string' ],
            'className'       => [ 'type' => 'string' ],
        ]
    ]);
    wp_set_script_translations( 'wpseopress/sitemap', 'wp-seopress' );
}

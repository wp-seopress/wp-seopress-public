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
add_action( 'init', 'seopress_register_blocks', 10 );
function seopress_register_blocks() {
    require_once __DIR__ . '/blocks/faq/block.php';
    require_once __DIR__ . '/blocks/sitemap/block.php';

	// FAQ Block
    register_block_type( SEOPRESS_PATH_PUBLIC . '/editor/blocks/faq', [
        'render_callback' => 'seopress_block_faq_render_frontend',
        'attributes' => array(
            'faqs' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array(
                    'type' => 'object',
                ),
            ),
            'listStyle' => array(
                'type' => 'string',
                'default' => 'none'
            ),
            'titleWrapper' => array(
                'type' => 'string',
                'default' => 'p'
            ),
            'imageSize' => array(
                'type' => 'string',
                'default' => 'thumbnail'
            ),
            'showFAQScheme' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'showAccordion' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'isProActive' => array(
                'type'    => 'boolean',
                'default' => is_plugin_active( 'wp-seopress-pro/seopress-pro.php' )
            ),
        ),
    ]);
    wp_set_script_translations( 'wpseopress/faq', 'wp-seopress' );

	// Sitemap Block
    register_block_type( SEOPRESS_PATH_PUBLIC . '/editor/blocks/sitemap', [
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

<?php
/**
 * Blocks
 *
 * @package Gutenberg
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

global $wp_version;
$hook_name = version_compare( $wp_version, '5.8' ) >= 0 ? 'block_categories_all' : 'block_categories';
add_filter( $hook_name, 'seopress_register_block_categories' );
/**
 * Declares a new category
 *
 * @param   array $categories  Existing categories.
 *
 * @return  array  $categories
 */
function seopress_register_block_categories( $categories ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'wpseopress',
				'title' => __( 'SEO', 'wp-seopress' ),
			),
		)
	);
}

add_action( 'init', 'seopress_register_blocks', 10 );
/**
 * Register blocks
 */
function seopress_register_blocks() {
	require_once __DIR__ . '/blocks/faq/block.php';
	require_once __DIR__ . '/blocks/sitemap/block.php';

	// FAQ Block.
	register_block_type(
		SEOPRESS_PATH_PUBLIC . '/editor/blocks/faq',
		array(
			'render_callback' => 'seopress_block_faq_render_frontend',
			'attributes'      => array(
				'faqs'          => array(
					'type'    => 'array',
					'default' => array(),
					'items'   => array(
						'type' => 'object',
					),
				),
				'listStyle'     => array(
					'type'    => 'string',
					'default' => 'none',
				),
				'titleWrapper'  => array(
					'type'    => 'string',
					'default' => 'p',
				),
				'imageSize'     => array(
					'type'    => 'string',
					'default' => 'thumbnail',
				),
				'showFAQScheme' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'showAccordion' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'isProActive'   => array(
					'type'    => 'boolean',
					'default' => is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ),
				),
			),
		)
	);
	wp_set_script_translations( 'wpseopress/faq', 'wp-seopress' );

	// Sitemap Block.
	register_block_type(
		SEOPRESS_PATH_PUBLIC . '/editor/blocks/sitemap',
		array(
			'render_callback' => 'seopress_sitemap_block',
			'attributes'      => array(
				'postTypes'        => array(
					'type'    => 'array',
					'default' => array(),
				),
				'isSiteMapEnabled' => array(
					'type'    => 'boolean',
					'default' => ( '1' == seopress_get_toggle_option( 'xml-sitemap' ) ) && ( '1' === seopress_get_service( 'SitemapOption' )->getHtmlEnable() ), // phpcs:ignore -- TODO: null comparison check.
				),
				'optionsPageUrl'   => array(
					'type'    => 'string',
					'default' => add_query_arg( 'page', 'seopress-xml-sitemap', admin_url( 'admin.php' ) ),
				),
				'fontSize'         => array(
					'type' => 'string',
				),
				'backgroundColor'  => array(
					'type' => 'string',
				),
				'style'            => array(
					'type' => 'object',
				),
				'textColor'        => array(
					'type' => 'string',
				),
				'gradient'         => array(
					'type' => 'string',
				),
				'className'        => array(
					'type' => 'string',
				),
			),
		)
	);
	wp_set_script_translations( 'wpseopress/sitemap', 'wp-seopress' );

	// FAQ v2.
	register_block_type(
		SEOPRESS_PATH_PUBLIC . '/editor/blocks/faq-v2',
		array(
			'attributes' => array(
				'printSchema' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'schema'      => array(
					'type'    => 'object',
					'default' => array(),
				),
			),
		)
	);
	wp_set_script_translations( 'wpseopress/faq-block-v2', 'wp-seopress' );
}


add_filter( 'block_type_metadata', 'seopress_block_type_metadata' );
/**
 * Filters block metadata
 *
 * @param   array $metadata  Block metadata, as in block.json.
 * @return  array  $metadata
 */
function seopress_block_type_metadata( $metadata ) {
	if ( isset( $metadata['name'] ) && 'core/details' === $metadata['name'] ) {
		$metadata['supports']['anchor'] = true;
	}
	return $metadata;
}

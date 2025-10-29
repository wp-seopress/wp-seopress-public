<?php
/**
 * Sitemap Block
 *
 * @package Gutenberg
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Sitemap block display callback
 *
 * @param   array    $attributes  Block attributes.
 * @param   string   $content     Inner block content.
 * @param   WP_Block $block       Actual block.
 *
 * @return  string    $html.
 */
function seopress_sitemap_block( $attributes, $content, $block ) {
	$attr = get_block_wrapper_attributes();
	$html = '';
	if ( '1' == seopress_get_toggle_option( 'xml-sitemap' ) && '1' === seopress_get_service( 'SitemapOption' )->getHtmlEnable() ) { // phpcs:ignore -- TODO: null comparison check.
		$atts                 = ! empty( $attributes['postTypes'] ) ? array( 'cpt' => join( ',', $attributes['postTypes'] ) ) : array();
		$html_sitemap_service = new \SEOPress\Services\HTMLSitemap\HTMLSitemapService( seopress_get_service( 'SitemapOption' ) );
		$html                 = sprintf( '<div %s>%s</div>', $attr, $html_sitemap_service->renderSitemap( $atts ) );
	}
	return $html;
}

<?php
/**
 * FAQ Block v2 — server-side schema output.
 *
 * @package Gutenberg
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Remove a JSON-LD payload left in the block body as visible text.
 *
 * Posts saved while the schema still lived in the block markup, by a user
 * without `unfiltered_html`, had their `<script>` removed by `wp_kses` and its
 * JSON left behind as a text node. The editor migrates those posts when they
 * are opened, but a migration alone never reaches the database: opening a post
 * does not mark it as modified, so nothing is rewritten until the author
 * happens to edit it. Until then visitors keep reading the raw JSON.
 *
 * Stripping it at render time fixes every affected post without anyone having
 * to touch it.
 *
 * The payload is located by scanning braces, ignoring those inside JSON
 * strings, and is only removed once it has been decoded and confirmed to be a
 * FAQPage. Anything that fails either check is left alone.
 *
 * @param string $block_content The rendered block markup.
 *
 * @return string HTML.
 */
function seopress_block_faq_v2_strip_leaked_schema( $block_content ) {
	$start = strpos( $block_content, '{"@context"' );

	if ( false === $start ) {
		return $block_content;
	}

	$length    = strlen( $block_content );
	$depth     = 0;
	$in_string = false;
	$escaped   = false;
	$end       = null;

	for ( $i = $start; $i < $length; $i++ ) {
		$char = $block_content[ $i ];

		if ( $in_string ) {
			if ( $escaped ) {
				$escaped = false;
			} elseif ( '\\' === $char ) {
				$escaped = true;
			} elseif ( '"' === $char ) {
				$in_string = false;
			}
			continue;
		}

		if ( '"' === $char ) {
			$in_string = true;
		} elseif ( '{' === $char ) {
			++$depth;
		} elseif ( '}' === $char ) {
			--$depth;

			if ( 0 === $depth ) {
				$end = $i;
				break;
			}
		}
	}

	if ( null === $end ) {
		return $block_content;
	}

	$candidate = substr( $block_content, $start, $end - $start + 1 );
	$decoded   = json_decode( $candidate, true );

	if ( ! is_array( $decoded ) || ! isset( $decoded['@type'] ) || 'FAQPage' !== $decoded['@type'] ) {
		return $block_content;
	}

	return substr_replace( $block_content, '', $start, $end - $start + 1 );
}

/**
 * Append the FAQ JSON-LD to the rendered block.
 *
 * The schema used to be part of the block's saved markup. WordPress strips
 * `<script>` from post content on save for anyone without the `unfiltered_html`
 * capability: every role below Administrator, every Administrator on multisite,
 * and any site running with `DISALLOW_UNFILTERED_HTML`. The JSON was then left
 * behind as visible text and the block was flagged as invalid. Printing it at
 * render time keeps the stored markup free of anything `wp_kses` can strip.
 *
 * The questions are already serialized into the `schema` attribute by the
 * editor, so nothing has to be re-derived from the inner blocks here.
 *
 * @param string $block_content The rendered block markup.
 * @param array  $block         The parsed block.
 *
 * @return string HTML.
 */
function seopress_block_faq_v2_render_schema( $block_content, $block ) {
	if ( ! isset( $block['blockName'] ) || 'wpseopress/faq-block-v2' !== $block['blockName'] ) {
		return $block_content;
	}

	// Done before anything else, so a post that leaked its schema stops showing
	// it even when the block prints no schema at all.
	$block_content = seopress_block_faq_v2_strip_leaked_schema( $block_content );

	$attributes = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();

	// printSchema defaults to true in block.json, so an absent key means enabled.
	if ( isset( $attributes['printSchema'] ) && ! $attributes['printSchema'] ) {
		return $block_content;
	}

	$schema = isset( $attributes['schema'] ) ? $attributes['schema'] : array();

	// An empty FAQ carries no question worth describing.
	if ( ! is_array( $schema ) || empty( $schema['mainEntity'] ) ) {
		return $block_content;
	}

	/**
	 * The questions and answers are RichText values, so they reach the schema
	 * attribute as HTML: an ampersand is `&amp;`, a quote the editor did not
	 * curl is `&quot;`. Left as they are, they would need a consumer to
	 * unescape the JSON-LD body to be read, which Google now does exactly once
	 * and asks publishers to stop relying on. seopress_json_ld_encode()
	 * decodes them and re-escapes every angle bracket, ampersand and quote as
	 * a JSON unicode escape.
	 */
	$json = seopress_json_ld_encode( $schema );

	if ( false === $json ) {
		return $block_content;
	}

	$schema_html = '<script type="application/ld+json">' . $json . '</script>';

	/**
	 * Filters the FAQ block JSON-LD markup, so a site already producing a
	 * FAQPage schema elsewhere can drop this one by returning an empty string.
	 *
	 * @param string $schema_html The script element.
	 * @param array  $schema      The decoded schema attribute.
	 * @param array  $block       The parsed block.
	 */
	$schema_html = apply_filters( 'seopress_schemas_faq_block_html', $schema_html, $schema, $block );

	return $block_content . $schema_html;
}

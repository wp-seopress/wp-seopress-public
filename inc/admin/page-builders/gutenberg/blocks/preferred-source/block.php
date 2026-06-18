<?php
/**
 * Google Preferred Sources Block
 *
 * @package Gutenberg
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Resolve the domain used for the Google preferred source deeplink.
 *
 * Google only accepts domains and subdomains (e.g. example.com or news.example.com),
 * never subdirectories, so we keep the host part only.
 *
 * @param string $domain Optional domain override.
 *
 * @return string The sanitized domain, or an empty string when none can be resolved.
 */
function seopress_preferred_source_get_domain( $domain = '' ) {
	$domain = trim( (string) $domain );

	if ( '' === $domain ) {
		$domain = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	} else {
		// Allow users to paste a full URL: keep the host only.
		$host   = wp_parse_url( $domain, PHP_URL_HOST );
		$domain = $host ? $host : $domain;
	}

	$domain = preg_replace( '#^www\.#i', '', $domain );

	return sanitize_text_field( $domain );
}

/**
 * Build the Google preferred source button markup.
 *
 * Shared between the Gutenberg block and the [seopress_preferred_source] shortcode.
 *
 * @param array  $args {
 *     Button arguments.
 *
 *     @type string $label     Button label. Falls back to a default when empty.
 *     @type string $domain    Domain override. Falls back to the site domain when empty.
 *     @type bool   $show_icon Whether to display the Google logo.
 * }
 * @param string $wrapper_attributes Optional wrapper attributes (block supports).
 *
 * @return string The button HTML, or an empty string when no domain is available.
 */
function seopress_preferred_source_render( $args = array(), $wrapper_attributes = '' ) {
	$defaults = array(
		'label'     => '',
		'domain'    => '',
		'show_icon' => true,
	);
	$args     = wp_parse_args( $args, $defaults );

	$domain = seopress_preferred_source_get_domain( $args['domain'] );
	if ( '' === $domain ) {
		return '';
	}

	$label = '' !== trim( (string) $args['label'] ) ? $args['label'] : __( 'Add as a preferred source', 'wp-seopress' );
	$url   = 'https://www.google.com/preferences/source?q=' . rawurlencode( $domain );

	$icon = '';
	if ( filter_var( $args['show_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
		$icon = '<svg class="seopress-preferred-source__icon" width="18" height="18" viewBox="0 0 48 48" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571.001-.001.002-.001.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>';
	}

	// A block-level wrapper (like core's <div class="wp-block-button">) lets the
	// parent layout center the button through auto margins; the inline <a> alone
	// would be ignored by those margins.
	$wrapper = '' !== $wrapper_attributes ? $wrapper_attributes : 'class="seopress-preferred-source"';

	$button = sprintf(
		'<div %1$s><a class="seopress-preferred-source__link" href="%2$s" target="_blank" rel="noopener nofollow">%3$s<span class="seopress-preferred-source__label">%4$s</span></a></div>',
		$wrapper,
		esc_url( $url ),
		$icon,
		esc_html( $label )
	);

	$button = seopress_preferred_source_inline_css() . $button;

	return apply_filters( 'seopress_preferred_source_html', $button, $args, $domain, $url );
}

/**
 * Output the button base styles once per request.
 *
 * Colors, typography and spacing are left to block supports / theme styles; we only
 * ship the minimal layout needed for a consistent button.
 *
 * @return string Inline <style> tag the first time it is called, an empty string afterwards.
 */
function seopress_preferred_source_inline_css() {
	static $printed = false;
	if ( $printed ) {
		return '';
	}
	$printed = true;

	$css = '<style>.seopress-preferred-source__link{display:inline-flex;align-items:center;gap:.5em;padding:.5em 1em;border:1px solid #c3c4c7;border-radius:9999px;line-height:1.4;text-decoration:none;}.seopress-preferred-source__icon{flex:0 0 auto;}</style>';

	return apply_filters( 'seopress_preferred_source_inline_css', $css );
}

/**
 * Google Preferred Sources block render callback.
 *
 * @param array $attributes Block attributes.
 *
 * @return string HTML.
 */
function seopress_preferred_source_block( $attributes ) {
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'seopress-preferred-source' ) );

	return seopress_preferred_source_render(
		array(
			'label'     => isset( $attributes['label'] ) ? $attributes['label'] : '',
			'domain'    => isset( $attributes['domain'] ) ? $attributes['domain'] : '',
			'show_icon' => isset( $attributes['showIcon'] ) ? $attributes['showIcon'] : true,
		),
		$wrapper_attributes
	);
}

/**
 * [seopress_preferred_source] shortcode callback.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string HTML.
 */
function seopress_preferred_source_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'label'  => '',
			'domain' => '',
			'icon'   => 'true',
		),
		$atts,
		'seopress_preferred_source'
	);

	return seopress_preferred_source_render(
		array(
			'label'     => $atts['label'],
			'domain'    => $atts['domain'],
			'show_icon' => filter_var( $atts['icon'], FILTER_VALIDATE_BOOLEAN ),
		)
	);
}

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
 * Sanitize the rendering mode.
 *
 * - custom: our own button, opening the Google flow in place (the visitor is
 *   returned to the page and never loses their reading position).
 * - google: the official button rendered by the Google library.
 * - link:   a plain deeplink, without any third party script.
 *
 * Anything else falls back to the plain link, which never loads a third party
 * script: that is what blocks and shortcodes created before this option existed
 * rendered, and they carry no mode at all.
 *
 * @param string $mode Requested mode.
 *
 * @return string
 */
function seopress_preferred_source_get_mode( $mode ) {
	$mode = strtolower( trim( (string) $mode ) );

	return in_array( $mode, array( 'custom', 'google', 'link' ), true ) ? $mode : 'link';
}

/**
 * Sanitize the button theme passed to Google.
 *
 * "auto" follows the visitor system preference, and is what Google falls back to
 * when no theme is set.
 *
 * @param string $theme Requested theme.
 *
 * @return string
 */
function seopress_preferred_source_get_theme( $theme ) {
	$theme = strtolower( trim( (string) $theme ) );

	return in_array( $theme, array( 'auto', 'light', 'dark' ), true ) ? $theme : 'auto';
}

/**
 * Sanitize the language override passed to Google.
 *
 * Left empty, Google uses the visitor language then the page language.
 *
 * @param string $lang Requested language code.
 *
 * @return string
 */
function seopress_preferred_source_get_lang( $lang ) {
	$lang = preg_replace( '/[^A-Za-z0-9-]/', '', (string) $lang );

	return substr( (string) $lang, 0, 10 );
}

/**
 * Enqueue the view script that lazy loads the Google library.
 *
 * WordPress already does it for the block itself; this is what makes the
 * shortcode behave the same way.
 *
 * @return void
 */
function seopress_preferred_source_enqueue_view_script() {
	if ( ! function_exists( 'generate_block_asset_handle' ) ) {
		return;
	}

	$handle = generate_block_asset_handle( 'wpseopress/preferred-source', 'viewScript' );

	if ( wp_script_is( $handle, 'registered' ) ) {
		wp_enqueue_script( $handle );
	}
}

/**
 * The Google logo shown inside our own button.
 *
 * @return string
 */
function seopress_preferred_source_icon() {
	return '<svg class="seopress-preferred-source__icon" width="18" height="18" viewBox="0 0 48 48" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571.001-.001.002-.001.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>';
}

/**
 * Build our own button markup.
 *
 * In "custom" mode the data attributes let the view script take over the click
 * and open the Google flow in place; the href stays a working deeplink so the
 * button keeps doing something useful when the library is blocked or slow.
 *
 * @param array  $args  Button arguments, as passed to seopress_preferred_source_render().
 * @param string $url   The Google deeplink.
 * @param string $mode  Rendering mode.
 * @param string $theme Button theme.
 * @param string $lang  Language override.
 *
 * @return string
 */
function seopress_preferred_source_link_html( $args, $url, $mode = 'link', $theme = 'auto', $lang = '' ) {
	$label = '' !== trim( (string) $args['label'] ) ? $args['label'] : __( 'Add as a preferred source', 'wp-seopress' );
	$icon  = filter_var( $args['show_icon'], FILTER_VALIDATE_BOOLEAN ) ? seopress_preferred_source_icon() : '';

	$data = '';
	if ( 'custom' === $mode ) {
		$data = ' data-seopress-preferred-source="custom" data-theme="' . esc_attr( $theme ) . '"';

		if ( '' !== $lang ) {
			$data .= ' data-lang="' . esc_attr( $lang ) . '"';
		}
	}

	return sprintf(
		'<a class="seopress-preferred-source__link" href="%1$s" target="_blank" rel="noopener nofollow"%2$s>%3$s<span class="seopress-preferred-source__label">%4$s</span></a>',
		esc_url( $url ),
		$data,
		$icon,
		esc_html( $label )
	);
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
 *     @type string $mode      custom|google|link. Defaults to link.
 *     @type string $theme     auto|light|dark, for the two JavaScript modes.
 *     @type string $lang      Language override, for the two JavaScript modes.
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
		'mode'      => 'link',
		'theme'     => 'auto',
		'lang'      => '',
	);
	$args     = wp_parse_args( $args, $defaults );

	$mode  = seopress_preferred_source_get_mode( $args['mode'] );
	$theme = seopress_preferred_source_get_theme( $args['theme'] );
	$lang  = seopress_preferred_source_get_lang( $args['lang'] );

	// Google resolves the site from the current page URL, so the official button
	// works without a domain; the other modes are nothing but that deeplink.
	$domain = seopress_preferred_source_get_domain( $args['domain'] );
	if ( '' === $domain && 'google' !== $mode ) {
		return '';
	}

	$url = '' !== $domain ? 'https://www.google.com/preferences/source?q=' . rawurlencode( $domain ) : '';

	// A block-level wrapper (like core's <div class="wp-block-button">) lets the
	// parent layout center the button through auto margins; the inline <a> alone
	// would be ignored by those margins.
	$wrapper = '' !== $wrapper_attributes ? $wrapper_attributes : 'class="seopress-preferred-source"';

	if ( 'link' !== $mode ) {
		seopress_preferred_source_enqueue_view_script();
	}

	if ( 'google' === $mode ) {
		// Google attaches a shadow root to this container, which hides our own
		// button as soon as theirs is rendered. Visitors without JavaScript, or
		// with the library blocked, keep a working deeplink.
		$button = sprintf(
			'<div %1$s><div google-add-preferred-source-btn data-seopress-preferred-source="google" data-theme="%2$s"%3$s>%4$s</div></div>',
			$wrapper,
			esc_attr( $theme ),
			'' !== $lang ? ' data-lang="' . esc_attr( $lang ) . '"' : '',
			'' !== $url ? seopress_preferred_source_link_html( $args, $url ) : ''
		);
	} else {
		$button = sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper,
			seopress_preferred_source_link_html( $args, $url, $mode, $theme, $lang )
		);
	}

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
			'mode'      => isset( $attributes['mode'] ) ? $attributes['mode'] : '',
			'theme'     => isset( $attributes['theme'] ) ? $attributes['theme'] : 'auto',
			'lang'      => isset( $attributes['lang'] ) ? $attributes['lang'] : '',
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
			'mode'   => 'link',
			'theme'  => 'auto',
			'lang'   => '',
		),
		$atts,
		'seopress_preferred_source'
	);

	return seopress_preferred_source_render(
		array(
			'label'     => $atts['label'],
			'domain'    => $atts['domain'],
			'show_icon' => filter_var( $atts['icon'], FILTER_VALIDATE_BOOLEAN ),
			'mode'      => $atts['mode'],
			'theme'     => $atts['theme'],
			'lang'      => $atts['lang'],
		)
	);
}

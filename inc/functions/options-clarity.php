<?php
/**
 * Options clarity
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Build the raw Clarity JavaScript code (no script tags).
 *
 * @return string Raw JS code, or empty string if disabled.
 */
function seopress_clarity_build_js() {
	if ( seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() === '' || seopress_get_service( 'GoogleAnalyticsOption' )->getClarityEnable() !== '1' ) {
		return '';
	}

	// Sanitize the Project ID (alphanumeric only, preserve case).
	$project_id = preg_replace( '/[^a-zA-Z0-9]/', '', seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() );

	// Build Clarity initialization script.
	$js = sprintf(
		'(function(c,l,a,r,i,t,y){
			c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
			t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=seopress";
			y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
		})(window, document, "clarity", "script", "%s");',
		$project_id
	);

	// User consent - Required for EEA, UK, and Switzerland (effective Oct 31, 2025).
	// Microsoft Clarity requires checking analytics_storage and ad_storage consent types.
	// Reference: https://learn.microsoft.com/en-us/clarity/setup-and-installation/consent-management#consent-type
	//
	// SEOPress cookie consent system grants BOTH analytics_storage AND ad_storage when user accepts.
	// This aligns with Google Consent Mode v2 implementation in options-google-analytics.php.
	$consent = '';

	$update = ( ! empty( $_POST['consent'] ) && 'update' === $_POST['consent'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( true === $update ) {
		if ( isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'] ) {
			$consent = "window.clarity('consent');";
		} elseif ( isset( $_COOKIE['seopress-user-consent-close'] ) && '1' === $_COOKIE['seopress-user-consent-close'] ) {
			$consent = "window.clarity('consent', false);";
		}
	} elseif ( isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'] ) {
		$consent = "window.clarity('consent');";
	} else {
		$consent = "window.clarity('consent', false);";
	}

	/**
	 * Filter Clarity consent signal.
	 *
	 * @since 9.3.0
	 *
	 * @param string $consent The consent JavaScript code to send to Clarity.
	 */
	$consent = apply_filters( 'seopress_clarity_user_consent', $consent );

	$js .= $consent;

	return $js;
}

/**
 * Output or return Clarity tracking code.
 *
 * When called with $output = true (default), prints the script via wp_print_inline_script_tag (wp_head).
 * When called with $output = false, returns the code wrapped in script tags for AJAX injection.
 *
 * Note: wp_enqueue_script cannot be used here because this function is loaded during wp_head
 * (via seopress_load_google_analytics_options at priority 0), which is after wp_enqueue_scripts
 * has already fired. wp_print_inline_script_tag is the WordPress API for this situation (WP 5.7+).
 *
 * @param bool $output True to print inline script, false to return script tag string.
 *
 * @return string|void
 */
function seopress_clarity_js( $output = true ) {
	$raw_js = seopress_clarity_build_js();

	if ( '' === $raw_js ) {
		return '';
	}

	/**
	 * Filter the complete Clarity tracking code.
	 *
	 * @since 8.0.0
	 *
	 * @param string $raw_js The complete Clarity JavaScript code (no script tags).
	 */
	$raw_js = apply_filters( 'seopress_clarity_tracking_js', $raw_js );

	if ( false === $output ) {
		// Return script tag string for AJAX cookie consent injection.
		return '<script>' . $raw_js . '</script>';
	}

	// Output inline script during wp_head using WordPress API (WP 5.7+).
	wp_print_inline_script_tag( $raw_js );
}

<?php
/**
 * Options clarity
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Build Clarity Tracking Code
 *
 * @param bool $output Output.
 *
 * @return string $js
 */
function seopress_clarity_js( $output ) {
	if ( seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() !== '' && seopress_get_service( 'GoogleAnalyticsOption' )->getClarityEnable() === '1' ) {
		// Init.
		$js  = "\n";
		$js .= '<script>';
		$js .= "\n";
		$js .= '(function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=seopress";
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "' . sanitize_key( seopress_get_service( 'GoogleAnalyticsOption' )->getClarityProjectId() ) . '");';

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
				// User has granted consent.
				// This means analytics_storage=granted AND ad_storage=granted per SEOPress consent model.
				$consent = "window.clarity('consent');";
			} elseif ( isset( $_COOKIE['seopress-user-consent-close'] ) && '1' === $_COOKIE['seopress-user-consent-close'] ) {
				// User has declined consent.
				// This means analytics_storage=denied AND ad_storage=denied per SEOPress consent model.
				$consent = "window.clarity('consent', false);";
			}
		} elseif ( isset( $_COOKIE['seopress-user-consent-accept'] ) && '1' === $_COOKIE['seopress-user-consent-accept'] ) {
			// User has previously granted consent.
			// This means analytics_storage=granted AND ad_storage=granted per SEOPress consent model.
			$consent = "window.clarity('consent');";
		} else {
			// Default to no consent if no cookie is set (GDPR-compliant).
			// This means analytics_storage=denied AND ad_storage=denied per SEOPress consent model.
			// This ensures compliance with Microsoft Clarity's requirements for EEA/UK/Swiss regions.
			$consent = "window.clarity('consent', false);";
		}

		/**
		 * Filter Clarity consent signal.
		 *
		 * Allows developers to customize consent behavior or integrate with third-party CMPs.
		 * Microsoft Clarity requires consent for analytics_storage and ad_storage.
		 *
		 * @since 9.3.0
		 *
		 * @param string $consent The consent JavaScript code to send to Clarity.
		 *
		 * @example Integrate with Google Consent Mode:
		 * add_filter(
		 *      'seopress_clarity_user_consent',
		 *      function( $consent ) {
		 *         // Your custom consent logic here.
		 *         // Note: Clarity requires analytics_storage and ad_storage consent.
		 *         return "window.clarity('consent');";
		 *      }
		 * );
		 */
		$consent = apply_filters( 'seopress_clarity_user_consent', $consent );

		$js .= $consent;

		$js .= "</script>\n";

		// Allow developers to modify the entire Clarity tracking code.
		$js = apply_filters( 'seopress_clarity_tracking_js', $js );

		if ( true === $output ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is safely built with sanitized data
			echo $js;
		} else {
			return $js;
		}
	}
}
add_action( 'seopress_clarity_html', 'seopress_clarity_js', 10, 1 );

/**
 * Clarity JS Arguments
 *
 * @return void
 */
function seopress_clarity_js_arguments() {
	$output = true;
	do_action( 'seopress_clarity_html', $output );
}

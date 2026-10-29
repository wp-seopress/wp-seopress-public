<?php
/**
 * X (Twitter) handle normalization.
 *
 * @package SEOPress
 */

namespace SEOPress\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Reads the handle out of an X account value, whatever form it was saved in.
 */
class XHandle {
	/**
	 * Extract the bare handle from an X account value.
	 *
	 * The site setting and the author "twitter" user meta accept free text, so
	 * both hold "@handle", "handle" or a full profile URL such as
	 * "https://twitter.com/handle". The site setting also prefixed URLs with
	 * "@" on save, giving "@https://twitter.com/handle".
	 *
	 * @param string $value Stored value.
	 * @return string Handle without "@", empty when none can be read.
	 */
	public static function extract( $value ) {
		$value = ltrim( trim( (string) $value ), '@' );

		if ( preg_match( '#^(?:https?://)?(?:www\.|mobile\.)?(?:twitter|x)\.com/+@?([^/?\#\s]+)#i', $value, $match ) ) {
			$value = $match[1];
		}

		return ltrim( $value, '@' );
	}
}

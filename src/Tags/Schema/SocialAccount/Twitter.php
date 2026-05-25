<?php // phpcs:ignore

namespace SEOPress\Tags\Schema\SocialAccount;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Twitter URL
 */
class Twitter implements GetTagValue {
	const NAME = 'social_account_twitter';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'X URL', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @since 4.5.0
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialAccountsTwitter();
		if ( ! empty( $value ) ) {
			// The stored value is normalized to "@handle" on save (Sanitize.php),
			// but the X profile URL is "https://x.com/handle" with no @ after
			// the slash. Strip a leading @ before composing the URL.
			$handle = ltrim( trim( $value ), '@' );
			$value  = sprintf( 'https://x.com/%s', $handle );
		}

		return apply_filters( 'seopress_get_tag_schema_social_account_twitter', $value, $context );
	}
}

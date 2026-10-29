<?php // phpcs:ignore

namespace SEOPress\Tags\Schema\SocialAccount;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\XHandle;
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
			// The stored value is "@handle", or a full profile URL saved by an
			// older version. The X profile URL takes the bare handle.
			$handle = XHandle::extract( $value );
			$value  = '' !== $handle ? sprintf( 'https://x.com/%s', $handle ) : '';
		}

		return apply_filters( 'seopress_get_tag_schema_social_account_twitter', $value, $context );
	}
}

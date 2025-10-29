<?php // phpcs:ignore

namespace SEOPress\Tags\Schema\SocialAccount;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Extra URL
 */
class Extra implements GetTagValue {
	const NAME = 'social_account_extra';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Extra URL', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @since 6.5.0
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;

		$value = seopress_get_service( 'SocialOption' )->getSocialAccountsExtra();

		return apply_filters( 'seopress_get_tag_schema_social_account_extra', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Schema Site URL
 */
class SiteUrl implements GetTagValue {
	const NAME = 'siteurl';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Site URL', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		// home_url(), not site_url(): this variable feeds schema.org url, @id
		// and sameAs fields, which all mean the site's public address. The two
		// are identical unless WordPress lives in a subdirectory of its own
		// (Bedrock, or the classic "give WordPress its own directory" setup),
		// where site_url() is the install path and publishing it names an
		// address the site does not answer on.
		$value = home_url();

		// Polylang intentionally leaves plugin calls to home_url() untranslated.
		// Its public API resolves both language directories and separate domains.
		if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_home_url' ) ) {
			$language = pll_current_language();
			$localized = $language ? pll_home_url( $language ) : '';
			if ( is_string( $localized ) && '' !== $localized ) {
				$value = untrailingslashit( $localized );
			}
		}

		return apply_filters( 'seopress_get_tag_site_url_value', $value );
	}
}

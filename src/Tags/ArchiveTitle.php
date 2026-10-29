<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Archive Title
 */
class ArchiveTitle implements GetTagValue {
	const NAME = 'archive_title';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Archive Title', 'wp-seopress' );
	}

	/**
	 * The title of the current archive as plain text, shared with the legacy
	 * %%archive_title%% replacement so both readers give the same value.
	 *
	 * WordPress wraps the archive name in a <span>, which ended up in
	 * the <title> tag and, escaped, in og:title.
	 *
	 * @since 10.4.0
	 *
	 * @return string
	 */
	public static function resolve() {
		return wp_strip_all_tags( get_the_archive_title() );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = self::resolve();

		return apply_filters( 'seopress_get_tag_archive_title_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Custom Post Type Plural
 */
class CustomPostTypePlural implements GetTagValue {
	const NAME = 'cpt_plural';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Plural Post Type Archive name', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		$value   = post_type_archive_title( '', false );

		return apply_filters( 'seopress_get_tag_cpt_plural_value', $value, $context );
	}
}

<?php // phpcs:ignore

namespace SEOPress\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Models\GetTagValue;

/**
 * Page
 */
class Page implements GetTagValue {
	const NAME = 'page';

	/**
	 * Get description
	 *
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Page number with context', 'wp-seopress' );
	}

	/**
	 * Get value
	 *
	 * @param array $args context, tag.
	 * @return string
	 */
	public function getValue( $args = null ) {
		$context = isset( $args[0] ) ? $args[0] : null;
		global $wp_query;

		$value = '';

		if ( ! $context ) {
			return $value;
		}

		if ( isset( $wp_query->max_num_pages ) ) {
			if ( $context['paged'] > 1 ) {
				$current_page = get_query_var( 'paged' );
			} else {
				$current_page = 1;
			}

			$value = /* translators: %1$d current page (e.g. 2) %2$d total number of pages (e.g. 30) */ sprintf( esc_attr__( 'Page %1$d of %2$d', 'wp-seopress' ), esc_attr( $current_page ), esc_attr( $wp_query->max_num_pages ) );

			/**
			 * Filter Page
			 *
			 * @deprecated 4.4.0
			 * Please use seopress_context_paged
			 */
			$value = apply_filters( 'seopress_context_paged', $value );
		}

		return apply_filters( 'seopress_get_tag_page_value', $value, $context );
	}
}

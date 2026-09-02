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

		// Mirror the legacy pipeline (dynamic-variables.php): this tag prints
		// something ONLY on page 2 and beyond. Without the guard it rendered
		// "Page 1 of 0" on every regular post, page and term, because a
		// singular query has max_num_pages = 0 and the surfaces that use this
		// engine (admin title column, metabox preview) resolve paged to 1.
		// Templates migrated from Yoast or Rank Math carry %%page%% on every
		// content type, so the noise showed up everywhere at once.
		$paged         = isset( $context['paged'] ) ? (int) $context['paged'] : 0;
		$max_num_pages = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 0;

		if ( $paged > 1 && $max_num_pages > 1 ) {
			$value = /* translators: %1$d current page (e.g. 2) %2$d total number of pages (e.g. 30) */ sprintf( esc_attr__( 'Page %1$d of %2$d', 'wp-seopress' ), esc_attr( $paged ), esc_attr( $max_num_pages ) );

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

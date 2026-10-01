<?php
namespace SEOPress\Helpers;
defined( 'ABSPATH' ) || exit;

/** Resolve configured static pages in the edited post's language. */
class StaticPages {
	public static function matches( $post_id, $option = 'page_on_front' ) {
		$post_id = (int) $post_id;
		if ( ! in_array( $option, array( 'page_on_front', 'page_for_posts' ), true ) || 'page' !== get_option( 'show_on_front' ) || ! $post_id || 'page' !== get_post_type( $post_id ) ) {
			return false;
		}
		$configured_id = (int) get_option( $option );
		if ( $configured_id && $post_id === $configured_id ) {
			return true;
		}
		if ( function_exists( 'pll_get_post_language' ) ) {
			$language = pll_get_post_language( $post_id );
			if ( $language && $configured_id && function_exists( 'pll_get_post' ) && $post_id === (int) pll_get_post( $configured_id, $language ) ) {
				return true;
			}
			// Polylang may filter page_on_front to zero in another admin language.
			if ( $language && 'page_on_front' === $option && function_exists( 'pll_home_url' ) ) {
				$home = pll_home_url( $language );
				$permalink = get_permalink( $post_id );
				if ( is_string( $home ) && '' !== $home && $permalink && untrailingslashit( $home ) === untrailingslashit( $permalink ) ) {
					return true;
				}
			}
		}
		if ( $configured_id && has_filter( 'wpml_object_id' ) ) {
			$details = apply_filters( 'wpml_post_language_details', null, $post_id );
			if ( is_array( $details ) && ! empty( $details['language_code'] ) ) {
				return $post_id === (int) apply_filters( 'wpml_object_id', $configured_id, 'page', false, $details['language_code'] );
			}
		}
		return false;
	}
}

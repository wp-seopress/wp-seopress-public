<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPressData
 */
class WordPressData {

	/**
	 * The getPostTypes function.
	 *
	 * @param bool  $return_all The return all.
	 * @param array $args The args.
	 *
	 * @return array
	 */
	public function getPostTypes( $return_all = false, $args = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wp_post_types;

		$default_args = array(
			'show_ui' => true,
			'public'  => true,
		);

		$args = wp_parse_args( $args, $default_args );

		if ( '' === $args['public'] ) {
			unset( $args['public'] );
		}

		$post_types = get_post_types( $args, 'objects', 'and' );

		if ( ! $return_all ) {
			unset(
				$post_types['attachment'],
				$post_types['seopress_rankings'],
				$post_types['seopress_backlinks'],
				$post_types['seopress_404'],
				$post_types['elementor_library'],
				$post_types['customer_discount'],
				$post_types['cuar_private_file'],
				$post_types['cuar_private_page'],
				$post_types['ct_template'],
				$post_types['bricks_template']
			);
		}

		$post_types = apply_filters( 'seopress_post_types', $post_types, $return_all, $args );

		return $post_types;
	}

	/**
	 * The getTaxonomies function.
	 *
	 * @param bool $with_terms The with terms.
	 * @param bool $return_all The return all.
	 *
	 * @return array
	 */
	public function getTaxonomies( $with_terms = false, $return_all = false ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$args = array(
			'show_ui' => true,
			'public'  => true,
		);
		$args = apply_filters( 'seopress_get_taxonomies_args', $args );

		$output     = 'objects'; // or objects.
		$operator   = 'and'; // 'and' or 'or'.
		$taxonomies = get_taxonomies( $args, $output, $operator );

		if ( ! $return_all ) {
			unset(
				$taxonomies['seopress_bl_competitors'],
				$taxonomies['template_tag'],
				$taxonomies['template_bundle']
			);
		}

		$taxonomies = apply_filters( 'seopress_get_taxonomies_list', $taxonomies, $return_all );

		if ( ! $with_terms ) {
			return $taxonomies;
		}

		foreach ( $taxonomies as $_tax_slug => &$_tax ) {
			$_tax->terms = get_terms( array( 'taxonomy' => $_tax_slug ) );
		}

		return $taxonomies;
	}
}

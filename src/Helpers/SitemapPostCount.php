<?php
namespace SEOPress\Helpers;
defined( 'ABSPATH' ) || exit;

class SitemapPostCount {
	/** Count matching posts without loading every ID for an unbounded query. */
	public static function get_count( $args ) {
		// Preserve integrations that intentionally constrain the returned page.
		if ( ! isset( $args['posts_per_page'] ) || -1 !== (int) $args['posts_per_page']
			|| array_intersect( array( 'include', 'exclude', 'category', 'numberposts', 'offset', 'nopaging', 'posts_per_archive_page' ), array_keys( $args ) ) ) {
			return count( get_posts( $args ) );
		}

		$args = wp_parse_args( $args, array( 'suppress_filters' => true ) );
		$args['posts_per_page'] = 1;
		$args['nopaging'] = false;
		$args['paged'] = 1;
		$args['offset'] = 0;
		$args['fields'] = 'ids';
		$args['ignore_sticky_posts'] = true;
		$args['no_found_rows'] = false;
		$query = new \WP_Query( $args );

		return (int) $query->found_posts;
	}
}

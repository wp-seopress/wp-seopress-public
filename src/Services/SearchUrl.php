<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SearchUrl
 */
class SearchUrl {

	/**
	 * The searchByPostName function.
	 *
	 * @param string $value The value.
	 *
	 * @return array
	 */
	public function searchByPostName( $value ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		global $wpdb;

		$limit = apply_filters( 'seopress_search_url_result_limit', 50 );
		if ( $limit > 200 ) {
			$limit = 200;
		}

		$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();

		$post_types = array_map(
			function ( $v ) {
				return "'" . esc_sql( $v ) . "'";
			},
			array_keys( $post_types )
		);

		$data = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT p.id, p.post_title
			FROM $wpdb->posts p
            WHERE (
                p.post_name LIKE %s
                OR p.post_title LIKE %s
            )
            AND p.post_status = 'publish'
            AND p.post_type IN (" . implode( ',', $post_types ) . ')
			LIMIT %d',
				'%' . $value . '%',
				'%' . $value . '%',
				$limit
			),
			ARRAY_A
		);

		foreach ( $data as $key => $value ) {
			$data[ $key ]['guid'] = get_permalink( $value['id'] );
		}
		return $data;
	}
}

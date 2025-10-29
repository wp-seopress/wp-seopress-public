<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CountTargetKeywordsUse
 */
class CountTargetKeywordsUse {

	/**
	 * The getCountByKeywords function.
	 *
	 * @param array $target_keywords The target keywords.
	 * @param int   $post_id The post id.
	 *
	 * @return array
	 */
	public function getCountByKeywords( $target_keywords, $post_id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( empty( $target_keywords ) ) {
			return array();
		}

		$hashed = md5( serialize( $target_keywords ) . $post_id ); // phpcs:ignore -- serialize is safe to use here.
		$cached = get_transient( 'seopress_content_analysis_count_target_keywords_use_' . $hashed );
		if ( false !== $cached ) {
			return $cached;
		}

		$target_keywords = array_map( 'trim', $target_keywords );

		global $wpdb;

		$query = "SELECT pm.post_id, pm.meta_value
            FROM {$wpdb->postmeta} AS pm
            JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_seopress_analysis_target_kw'
            AND p.post_type != 'elementor_library'
            AND pm.meta_value LIKE %s
            AND p.post_status IN ('publish', 'draft', 'pending', 'future') ";

		$data = array();

		foreach ( $target_keywords as $key => $keyword ) {
			$rows = $wpdb->get_results( $wpdb->prepare( $query, "%$keyword%" ), ARRAY_A ); // phpcs:ignore

			$data[] = array(
				'key'  => $keyword,
				'rows' => array_values(
					array_filter(
						array_map(
							function ( $row ) use ( $keyword, $post_id ) {
								$post             = get_post( $post_id );
								$post_type_object = get_post_type_object( $post->post_type );

								$values = array_map( 'trim', explode( ',', $row['meta_value'] ) );

								if ( ! in_array( $keyword, $values, true ) || $post_id === $row['post_id'] ) {
									return null;
								}

								return array(
									'post_id'   => absint( $row['post_id'] ),
									'edit_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', absint( $row['post_id'] ) ) ),
									'title'     => esc_html( get_the_title( $row['post_id'] ) ),
								);
							},
							$rows
						)
					)
				),
			);
		}

		set_transient( 'seopress_content_analysis_count_target_keywords_use_' . $hashed, $data, 5 * MINUTE_IN_SECONDS );

		return $data;
	}
}

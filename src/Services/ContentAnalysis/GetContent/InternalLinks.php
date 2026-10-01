<?php // phpcs:ignore

namespace SEOPress\Services\ContentAnalysis\GetContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * InternalLinks
 */
class InternalLinks {

	/**
	 * The getDataByXPath function.
	 *
	 * @param object $xpath The xpath.
	 * @param array  $options The options.
	 *
	 * @return array
	 */
	public function getDataByXPath( $xpath, $options ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data      = array();
		$post_id   = (int) $options['id'];
		$permalink = get_permalink( $post_id );
		if ( ! $permalink ) {
			return $data;
		}

		$args = array(
			's'                   => $permalink,
			'post_type'           => 'any',
			'post_status'         => 'publish',
			'post__not_in'        => array( $post_id ),
			'posts_per_page'      => -1,
			'no_found_rows'       => true,
			'fields'              => 'ids',
		);
		$items = new \WP_Query( $args );
		$ids   = $items->posts;

		$keys = $this->get_builder_meta_keys();
		if ( ! empty( $keys ) ) {
			unset( $args['s'] );
			// JSON builders can store escaped slashes; serialized PHP stores the URL literally.
			$values = array_unique( array( $permalink, str_replace( '/', '\\/', $permalink ) ) );
			$args['meta_query'] = array( 'relation' => 'OR' );
			foreach ( $values as $value ) {
				$args['meta_query'][] = array(
					'key'         => $keys,
					'compare_key' => 'IN',
					'value'       => $value,
					'compare'     => 'LIKE',
				);
			}
			$items = new \WP_Query( $args );
			$ids   = array_merge( $ids, $items->posts );
		}

		foreach ( array_unique( $ids ) as $id ) {
			$post_type_object = get_post_type_object( get_post_type( $id ) );
			$data[] = array(
				'id'             => $id,
				'edit_post_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $id ) ),
				'url'            => get_permalink( $id ),
				'value'          => get_the_title( $id ),
			);
		}

		return $data;
	}
	/** Only search storage belonging to builders active on this site. */
	private function get_builder_meta_keys() {
		$keys = array();
		$builders = array(
			'elementor/elementor.php'             => array( '_elementor_data' ),
			'bb-plugin/fl-builder.php'            => array( '_fl_builder_data' ),
			'beaver-builder-lite-version/fl-builder.php' => array( '_fl_builder_data' ),
			'oxygen/functions.php'               => array( 'ct_builder_shortcodes', 'ct_builder_json' ),
			'zionbuilder/zionbuilder.php'         => array( '_zionbuilder_page_elements' ),
			'breakdance/plugin.php'               => array( '_breakdance_data' ),
			'cornerstone/cornerstone.php'         => array( '_cornerstone_data' ),
		);
		foreach ( $builders as $plugin => $meta_keys ) {
			if ( is_plugin_active( $plugin ) ) {
				$keys = array_merge( $keys, $meta_keys );
			}
		}
		if ( defined( 'BRICKS_VERSION' ) || 'bricks' === wp_get_theme()->get_template() ) {
			$keys[] = '_bricks_page_content_2';
		}
		if ( defined( 'CS_VERSION' ) ) {
			$keys[] = '_cornerstone_data';
		}
		return array_values( array_unique( $keys ) );
	}

}

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
		$data = array();

		$permalink = get_permalink( (int) $options['id'] );

		$args  = array(
			's'         => $permalink,
			'post_type' => 'any',
		);
		$items = new \WP_Query( $args );

		if ( $items->have_posts() ) {
			while ( $items->have_posts() ) {
				$items->the_post();
				$post_type_object = get_post_type_object( get_post_type() );
				$data[]           = array(
					'id'             => get_the_ID(),
					'edit_post_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', get_the_ID() ) ),
					'url'            => get_the_permalink(),
					'value'          => get_the_title(),
				);
			}
		}

		wp_reset_postdata();

		// Internal links for Oxygen Builder.
		$oxygen_metabox_enabled = get_option( 'oxygen_vsb_ignore_post_type_' . get_post_type( $options['id'] ) ) ? false : true;
		if ( is_plugin_active( 'oxygen/functions.php' ) && function_exists( 'ct_template_output' ) && true === $oxygen_metabox_enabled ) {
			$args = array(
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'ct_builder_shortcodes',
						'value'   => $permalink,
						'compare' => 'LIKE',
					),
				),
				'post_type'      => 'any',
			);

			$items = new \WP_Query( $args );

			if ( $items->have_posts() ) {
				while ( $items->have_posts() ) {
					$items->the_post();
					$post_type_object = get_post_type_object( get_post_type() );
					$data[]           = array(
						'id'             => get_the_ID(),
						'edit_post_link' => admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', get_the_ID() ) ),
						'url'            => get_the_permalink(),
						'value'          => get_the_title(),
					);
				}
			}
			wp_reset_postdata();
		}

		return $data;
	}
}

<?php

namespace SEOPress\Actions\Admin\Importer;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Core\Hooks\ExecuteHooksBackend;

/**
 * SiteSEO importer
 */
class SiteSEO implements ExecuteHooksBackend {

	/**
	 * The SiteSEO hooks.
	 *
	 * @since 9.2.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_seopress_siteseo_migration', array( $this, 'process' ) );
	}

	/**
	 * Migrate the term query.
	 *
	 * @since 9.2.0
	 *
	 * @return string
	 */
	protected function migrateTermQuery() {
		wp_reset_postdata();

		$args                = array(
			'hide_empty' => false,
			'fields'     => 'ids',
		);
		$siteseo_query_terms = get_terms( $args );

		$get_term_metas = array(
			'_seopress_titles_title'               => '_siteseo_titles_title',
			'_seopress_titles_desc'                => '_siteseo_titles_desc',
			'_seopress_social_fb_title'            => '_siteseo_social_fb_title',
			'_seopress_social_fb_desc'             => '_siteseo_social_fb_desc',
			'_seopress_social_fb_img'              => '_siteseo_social_fb_img',
			'_seopress_social_twitter_title'       => '_siteseo_social_twitter_title',
			'_seopress_social_twitter_desc'        => '_siteseo_social_twitter_desc',
			'_seopress_social_twitter_img'         => '_siteseo_social_twitter_img',
			'_seopress_robots_index'               => '_siteseo_robots_index',
			'_seopress_robots_follow'              => '_siteseo_robots_follow',
			'_seopress_robots_imageindex'          => '_siteseo_robots_imageindex',
			'_seopress_robots_snippet'             => '_siteseo_robots_snippet',
			'_seopress_robots_canonical'           => '_siteseo_robots_canonical',
			'_seopress_analysis_target_kw'         => '_siteseo_analysis_target_kw',
			'_seopress_redirections_enabled'       => '_siteseo_redirections_enabled',
			'_seopress_redirections_value'         => '_siteseo_redirections_value',
			'_seopress_redirections_type'          => '_siteseo_redirections_type',
			'_seopress_redirections_param'         => '_siteseo_redirections_param',
			'_seopress_redirections_logged_status' => '_siteseo_redirections_logged_status',
			'_seopress_redirections_enabled_regex' => '_siteseo_redirections_enabled_regex',
		);
		if ( ! $siteseo_query_terms ) {
			wp_reset_postdata();

			return 'done';
		}

		foreach ( $siteseo_query_terms as $term_id ) {
			foreach ( $get_term_metas as $key => $value ) {
				$meta_site_seo = get_term_meta( $term_id, $value, true );
				if ( ! empty( $meta_site_seo ) ) {
					update_term_meta( $term_id, $key, $meta_site_seo );
				}
			}
		}

		wp_reset_postdata();

		return 'done';
	}

	/**
	 * Migrate the post query.
	 *
	 * @since 9.2.0
	 *
	 * @param int $offset    The offset.
	 * @param int $increment The increment.
	 */
	protected function migratePostQuery( $offset, $increment ) {
		global $wpdb;
		$args = array(
			'posts_per_page' => $increment,
			'post_type'      => 'any',
			'post_status'    => 'any',
			'offset'         => $offset,
		);

		$siteseo_query = get_posts( $args );

		if ( ! $siteseo_query ) {
			$offset += $increment;

			return $offset;
		}

		$get_post_metas = array(
			'_seopress_titles_title'               => '_siteseo_titles_title',
			'_seopress_titles_desc'                => '_siteseo_titles_desc',
			'_seopress_social_fb_title'            => '_siteseo_social_fb_title',
			'_seopress_social_fb_desc'             => '_siteseo_social_fb_desc',
			'_seopress_social_fb_img'              => '_siteseo_social_fb_img',
			'_seopress_social_twitter_title'       => '_siteseo_social_twitter_title',
			'_seopress_social_twitter_desc'        => '_siteseo_social_twitter_desc',
			'_seopress_social_twitter_img'         => '_siteseo_social_twitter_img',
			'_seopress_robots_index'               => '_siteseo_robots_index',
			'_seopress_robots_follow'              => '_siteseo_robots_follow',
			'_seopress_robots_imageindex'          => '_siteseo_robots_imageindex',
			'_seopress_robots_snippet'             => '_siteseo_robots_snippet',
			'_seopress_robots_canonical'           => '_siteseo_robots_canonical',
			'_seopress_analysis_target_kw'         => '_siteseo_analysis_target_kw',
			'_seopress_redirections_enabled'       => '_siteseo_redirections_enabled',
			'_seopress_redirections_value'         => '_siteseo_redirections_value',
			'_seopress_redirections_type'          => '_siteseo_redirections_type',
			'_seopress_redirections_param'         => '_siteseo_redirections_param',
			'_seopress_redirections_logged_status' => '_siteseo_redirections_logged_status',
			'_seopress_redirections_enabled_regex' => '_siteseo_redirections_enabled_regex',
		);

		foreach ( $siteseo_query as $post ) {
			foreach ( $get_post_metas as $key => $value ) {
				$meta_site_seo = get_post_meta( $post->ID, $value, true );
				if ( ! empty( $meta_site_seo ) ) {
					update_post_meta( $post->ID, $key, esc_html( $meta_site_seo ) );
				}
			}
		}

		$offset += $increment;

		return $offset;
	}

	/**
	 * Migrate the settings.
	 *
	 * @since 9.2.0
	 */
	protected function migrateSettings() {
	}

	/**
	 * Process the migration.
	 *
	 * @since 9.2.0
	 */
	public function process() {
		check_ajax_referer( 'seopress_siteseo_migrate_nonce', '_ajax_nonce', true );
		if ( ! is_admin() ) {
			wp_send_json_error();

			return;
		}

		if ( ! current_user_can( seopress_capability( 'manage_options', 'migration' ) ) ) { // phpcs:ignore
			wp_send_json_error();

			return;
		}

		if ( isset( $_POST['offset'] ) ) {
			$offset = absint( $_POST['offset'] );
		}

		global $wpdb;
		$total_count_posts = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$increment = 200;
		global $post;

		if ( $offset > $total_count_posts ) {
			$offset = $this->migrateTermQuery();
		} else {
			$offset = $this->migratePostQuery( $offset, $increment );
		}

		$data = array();

		$data['total'] = $total_count_posts;
		if ( $offset >= $total_count_posts ) {
			$data['count'] = $total_count_posts;
		} else {
			$data['count'] = $offset;
		}

		$data['offset'] = $offset;

		do_action( 'seopress_third_importer_siteseo', $offset, $increment );

		wp_send_json_success( $data );
		exit();
	}
}

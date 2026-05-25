<?php

namespace SEOPress\Actions\Admin\Importer;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\SureRank\Tags;

/**
 * SureRank importer.
 *
 * Skeleton: registers the AJAX endpoint and runs the batched migration loop
 * exactly like the other modern importers (RankMath, AIO, SiteSEO). The actual
 * post/term meta and global option mapping is implemented in subsequent PRs.
 */
class SureRank implements ExecuteHooksBackend {

	/**
	 * The SureRank tags translator.
	 *
	 * @var Tags
	 */
	protected $tags_surerank;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->tags_surerank = new Tags();
	}

	/**
	 * Register hooks.
	 *
	 * @since 9.8.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_seopress_surerank_migration', array( $this, 'process' ) );
	}

	/**
	 * Migrate term meta.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	protected function migrateTermQuery() {
		wp_reset_postdata();

		$term_ids = get_terms(
			array(
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);

		if ( empty( $term_ids ) || is_wp_error( $term_ids ) ) {
			wp_reset_postdata();

			return 'done';
		}

		foreach ( $term_ids as $term_id ) {
			$this->migrateObjectMeta( 'term', (int) $term_id );
		}

		wp_reset_postdata();

		return 'done';
	}

	/**
	 * Migrate post meta for the current batch.
	 *
	 * @since 9.8.0
	 *
	 * @param int $offset    Current offset.
	 * @param int $increment Batch size.
	 *
	 * @return int Next offset.
	 */
	protected function migratePostQuery( $offset, $increment ) {
		$posts = get_posts(
			array(
				'posts_per_page' => $increment,
				'post_type'      => 'any',
				'post_status'    => 'any',
				'offset'         => $offset,
			)
		);

		if ( empty( $posts ) ) {
			$offset += $increment;

			return $offset;
		}

		foreach ( $posts as $post ) {
			$this->migrateObjectMeta( 'post', (int) $post->ID );
		}

		$offset += $increment;

		return $offset;
	}

	/**
	 * Map a single SureRank post or term to its SEOPress equivalents.
	 *
	 * SureRank stores most metadata inside two serialized arrays
	 * (`surerank_settings_general` and `surerank_settings_social`) plus three
	 * scalar robots flags. The shape is identical for posts and terms — only
	 * the meta accessor differs.
	 *
	 * @since 9.8.0
	 *
	 * @param string $object_type 'post' or 'term'.
	 * @param int    $object_id   Post or term ID.
	 *
	 * @return void
	 */
	protected function migrateObjectMeta( $object_type, $object_id ) {
		$is_post     = 'post' === $object_type;
		$get_meta    = $is_post ? 'get_post_meta' : 'get_term_meta';
		$update_meta = $is_post ? 'update_post_meta' : 'update_term_meta';

		// 1. General: title, description, canonical, focus keyword.
		$general = $get_meta( $object_id, 'surerank_settings_general', true );
		if ( is_array( $general ) ) {
			if ( ! empty( $general['page_title'] ) ) {
				$update_meta(
					$object_id,
					'_seopress_titles_title',
					esc_html( $this->tags_surerank->replaceTags( $general['page_title'] ) )
				);
			}

			// Skip the raw "%content%" placeholder: SureRank's auto-description
			// has no SEOPress equivalent, and copying the literal token would
			// surface as visible text on the frontend.
			if ( ! empty( $general['page_description'] ) && '%content%' !== trim( $general['page_description'] ) ) {
				$update_meta(
					$object_id,
					'_seopress_titles_desc',
					esc_html( $this->tags_surerank->replaceTags( $general['page_description'] ) )
				);
			}

			if ( ! empty( $general['canonical_url'] ) ) {
				$update_meta(
					$object_id,
					'_seopress_robots_canonical',
					esc_url_raw( $general['canonical_url'] )
				);
			}

			if ( ! empty( $general['focus_keyword'] ) ) {
				$update_meta(
					$object_id,
					'_seopress_analysis_target_kw',
					sanitize_text_field( $general['focus_keyword'] )
				);
			}
		}

		// 2. Social: Facebook + Twitter overrides.
		$social = $get_meta( $object_id, 'surerank_settings_social', true );
		if ( is_array( $social ) ) {
			$social_map = array(
				'_seopress_social_fb_title'      => 'facebook_title',
				'_seopress_social_fb_desc'       => 'facebook_description',
				'_seopress_social_twitter_title' => 'twitter_title',
				'_seopress_social_twitter_desc'  => 'twitter_description',
			);
			foreach ( $social_map as $seopress_key => $surerank_key ) {
				if ( ! empty( $social[ $surerank_key ] ) ) {
					$update_meta(
						$object_id,
						$seopress_key,
						esc_html( $this->tags_surerank->replaceTags( $social[ $surerank_key ] ) )
					);
				}
			}

			if ( ! empty( $social['facebook_image_url'] ) ) {
				$update_meta( $object_id, '_seopress_social_fb_img', esc_url_raw( $social['facebook_image_url'] ) );
			}

			if ( ! empty( $social['twitter_image_url'] ) ) {
				$update_meta( $object_id, '_seopress_social_twitter_img', esc_url_raw( $social['twitter_image_url'] ) );
			}
		}

		// 3. Robots flags. SureRank stores them as separate "yes"/"" scalars.
		// Noarchive is intentionally skipped: SEOPress free has no equivalent.
		$robots_map = array(
			'_seopress_robots_index'  => 'surerank_settings_post_no_index',
			'_seopress_robots_follow' => 'surerank_settings_post_no_follow',
		);
		foreach ( $robots_map as $seopress_key => $surerank_key ) {
			if ( 'yes' === $get_meta( $object_id, $surerank_key, true ) ) {
				$update_meta( $object_id, $seopress_key, 'yes' );
			}
		}
	}

	/**
	 * Migrate global SureRank settings to SEOPress options.
	 *
	 * @since 9.8.0
	 *
	 * @return void
	 */
	protected function migrateSettings() {
		$surerank_settings   = get_option( 'surerank_settings' );
		$surerank_onboarding = get_option( 'surerank_settings_onboarding' );

		if ( empty( $surerank_settings ) && empty( $surerank_onboarding ) ) {
			return;
		}

		$seopress_titles   = get_option( 'seopress_titles_option_name' );
		$seopress_social   = get_option( 'seopress_social_option_name' );
		$seopress_sitemap  = get_option( 'seopress_xml_sitemap_option_name' );
		$seopress_advanced = get_option( 'seopress_advanced_option_name' );

		$seopress_titles   = is_array( $seopress_titles ) ? $seopress_titles : array();
		$seopress_social   = is_array( $seopress_social ) ? $seopress_social : array();
		$seopress_sitemap  = is_array( $seopress_sitemap ) ? $seopress_sitemap : array();
		$seopress_advanced = is_array( $seopress_advanced ) ? $seopress_advanced : array();

		if ( is_array( $surerank_settings ) ) {
			$this->mapTitlesAndMetas( $surerank_settings, $seopress_titles );
			$this->mapHomepage( $surerank_settings, $seopress_titles );
			$this->mapRobotsRules( $surerank_settings, $seopress_titles );
			$this->mapSitemap( $surerank_settings, $seopress_sitemap );
			$this->mapAdvanced( $surerank_settings, $seopress_advanced );
		}

		if ( is_array( $surerank_onboarding ) ) {
			$this->mapKnowledgeGraph( $surerank_onboarding, $seopress_social );
		}

		update_option( 'seopress_titles_option_name', $seopress_titles, false );
		update_option( 'seopress_social_option_name', $seopress_social, false );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_sitemap, false );
		update_option( 'seopress_advanced_option_name', $seopress_advanced, false );
	}

	/**
	 * Title separator + per-CPT/taxonomy default templates.
	 *
	 * SureRank uses a single global template for every singular and every term.
	 * SEOPress is per-CPT/per-taxonomy, so the same translated template is
	 * applied across all registered post types and taxonomies.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapTitlesAndMetas( $surerank, &$titles ) {
		if ( ! empty( $surerank['separator'] ) ) {
			$titles['seopress_titles_sep'] = esc_html( $surerank['separator'] );
		}

		$page_title = ! empty( $surerank['page_title'] )
			? esc_html( $this->tags_surerank->replaceTags( $surerank['page_title'] ) )
			: '';
		$page_desc  = ( ! empty( $surerank['page_description'] ) && '%content%' !== trim( $surerank['page_description'] ) )
			? esc_html( $this->tags_surerank->replaceTags( $surerank['page_description'] ) )
			: '';

		if ( '' !== $page_title || '' !== $page_desc ) {
			$wp_data    = function_exists( 'seopress_get_service' ) ? seopress_get_service( 'WordPressData' ) : null;
			$post_types = $wp_data && method_exists( $wp_data, 'getPostTypes' ) ? $wp_data->getPostTypes() : array();
			$taxonomies = $wp_data && method_exists( $wp_data, 'getTaxonomies' ) ? $wp_data->getTaxonomies() : array();

			foreach ( array_keys( (array) $post_types ) as $cpt_key ) {
				if ( '' !== $page_title ) {
					$titles['seopress_titles_single_titles'][ $cpt_key ]['title'] = $page_title;
				}
				if ( '' !== $page_desc ) {
					$titles['seopress_titles_single_titles'][ $cpt_key ]['description'] = $page_desc;
				}
			}

			foreach ( array_keys( (array) $taxonomies ) as $tax_key ) {
				if ( '' !== $page_title ) {
					$titles['seopress_titles_tax_titles'][ $tax_key ]['title'] = $page_title;
				}
				if ( '' !== $page_desc ) {
					$titles['seopress_titles_tax_titles'][ $tax_key ]['description'] = $page_desc;
				}
			}
		}

		if ( ! empty( $surerank['noindex_paginated_pages'] ) ) {
			$titles['seopress_titles_paged_noindex'] = '1';
		}
	}

	/**
	 * Homepage title + meta description.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapHomepage( $surerank, &$titles ) {
		if ( ! empty( $surerank['home_page_title'] ) ) {
			$titles['seopress_titles_home_site_title'] = esc_html( $this->tags_surerank->replaceTags( $surerank['home_page_title'] ) );
		}
		if ( ! empty( $surerank['home_page_description'] ) ) {
			$titles['seopress_titles_home_site_desc'] = esc_html( $this->tags_surerank->replaceTags( $surerank['home_page_description'] ) );
		}
	}

	/**
	 * Robots rules: SureRank's no_index / no_follow arrays target a mix of
	 * post types, taxonomies, and special archive pages.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapRobotsRules( $surerank, &$titles ) {
		$wp_data    = function_exists( 'seopress_get_service' ) ? seopress_get_service( 'WordPressData' ) : null;
		$post_types = $wp_data && method_exists( $wp_data, 'getPostTypes' ) ? array_keys( (array) $wp_data->getPostTypes() ) : array();
		$taxonomies = $wp_data && method_exists( $wp_data, 'getTaxonomies' ) ? array_keys( (array) $wp_data->getTaxonomies() ) : array();

		// SureRank uses these tokens for special archives.
		$special = array(
			'author' => 'seopress_titles_archives_author_noindex',
			'date'   => 'seopress_titles_archives_date_noindex',
			'search' => 'seopress_titles_archives_search_title_noindex',
		);

		$no_index = isset( $surerank['no_index'] ) && is_array( $surerank['no_index'] ) ? $surerank['no_index'] : array();
		foreach ( $no_index as $token ) {
			if ( isset( $special[ $token ] ) ) {
				$titles[ $special[ $token ] ] = '1';
			} elseif ( in_array( $token, $post_types, true ) ) {
				$titles['seopress_titles_single_titles'][ $token ]['noindex'] = '1';
			} elseif ( in_array( $token, $taxonomies, true ) ) {
				$titles['seopress_titles_tax_titles'][ $token ]['noindex'] = '1';
			}
		}

		$no_follow = isset( $surerank['no_follow'] ) && is_array( $surerank['no_follow'] ) ? $surerank['no_follow'] : array();
		foreach ( $no_follow as $token ) {
			if ( in_array( $token, $post_types, true ) ) {
				$titles['seopress_titles_single_titles'][ $token ]['nofollow'] = '1';
			} elseif ( in_array( $token, $taxonomies, true ) ) {
				$titles['seopress_titles_tax_titles'][ $token ]['nofollow'] = '1';
			}
		}
	}

	/**
	 * XML sitemap toggles.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $sitemap  SEOPress sitemap option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapSitemap( $surerank, &$sitemap ) {
		if ( ! empty( $surerank['enable_xml_sitemap'] ) ) {
			$sitemap['seopress_xml_sitemap_general_enable'] = '1';
		}
		if ( ! empty( $surerank['enable_xml_image_sitemap'] ) ) {
			$sitemap['seopress_xml_sitemap_img_enable'] = '1';
		}
	}

	/**
	 * Advanced settings (attachment redirect, etc.).
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $advanced SEOPress advanced option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapAdvanced( $surerank, &$advanced ) {
		if ( ! empty( $surerank['redirect_attachment_pages_to_post_parent'] ) ) {
			$advanced['seopress_advanced_advanced_attachments'] = '1';
		}
	}

	/**
	 * Knowledge Graph (Organization name, logo, social profiles).
	 *
	 * @param array $onboarding SureRank `surerank_settings_onboarding` option.
	 * @param array $social     SEOPress social option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapKnowledgeGraph( $onboarding, &$social ) {
		if ( ! empty( $onboarding['website_name'] ) ) {
			$social['seopress_social_knowledge_name'] = sanitize_text_field( $onboarding['website_name'] );
		} elseif ( ! empty( $onboarding['website_owner_name'] ) ) {
			$social['seopress_social_knowledge_name'] = sanitize_text_field( $onboarding['website_owner_name'] );
		}

		if ( ! empty( $onboarding['website_logo'] ) && is_array( $onboarding['website_logo'] ) && ! empty( $onboarding['website_logo']['url'] ) ) {
			$social['seopress_social_knowledge_img'] = esc_url_raw( $onboarding['website_logo']['url'] );
		}

		if ( ! empty( $onboarding['website_owner_phone'] ) ) {
			$social['seopress_social_knowledge_phone'] = sanitize_text_field( $onboarding['website_owner_phone'] );
		}

		// Social profiles. SureRank stores raw URLs / handles keyed by network.
		$profiles = isset( $onboarding['social_profiles'] ) && is_array( $onboarding['social_profiles'] ) ? $onboarding['social_profiles'] : array();

		$profile_map = array(
			'facebook'  => 'seopress_social_accounts_facebook',
			'twitter'   => 'seopress_social_accounts_twitter',
			'instagram' => 'seopress_social_accounts_instagram',
			'youtube'   => 'seopress_social_accounts_youtube',
			'linkedin'  => 'seopress_social_accounts_linkedin',
			'pinterest' => 'seopress_social_accounts_pinterest',
		);
		foreach ( $profile_map as $surerank_key => $seopress_key ) {
			if ( empty( $profiles[ $surerank_key ] ) ) {
				continue;
			}
			$social[ $seopress_key ] = 'twitter' === $surerank_key
				? sanitize_text_field( $profiles[ $surerank_key ] )
				: esc_url_raw( $profiles[ $surerank_key ] );
		}
	}

	/**
	 * AJAX entry point.
	 *
	 * @since 9.8.0
	 *
	 * @return void
	 */
	public function process() {
		check_ajax_referer( 'seopress_surerank_migrate_nonce', '_ajax_nonce', true );
		if ( ! is_admin() ) {
			wp_send_json_error();

			return;
		}

		if ( ! current_user_can( seopress_capability( 'manage_options', 'migration' ) ) ) { // phpcs:ignore
			wp_send_json_error();

			return;
		}

		$this->migrateSettings();

		$offset = 0;
		if ( isset( $_POST['offset'] ) ) {
			$offset = absint( $_POST['offset'] );
		}

		global $wpdb;
		$total_count_posts = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$increment = 200;

		if ( $offset > $total_count_posts ) {
			$offset = $this->migrateTermQuery();
		} else {
			$offset = $this->migratePostQuery( $offset, $increment );
		}

		$data = array(
			'total' => $total_count_posts,
		);

		if ( is_int( $offset ) && $offset >= $total_count_posts ) {
			$data['count'] = $total_count_posts;
		} else {
			$data['count'] = $offset;
		}

		$data['offset'] = $offset;

		do_action( 'seopress_third_importer_surerank', $offset, $increment );

		wp_send_json_success( $data );
		exit();
	}
}

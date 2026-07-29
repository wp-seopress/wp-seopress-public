<?php

namespace SEOPress\Actions\Admin\Importer;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\SureRank\Tags;

/**
 * SureRank importer.
 *
 * Registers the AJAX endpoint and runs the batched migration loop exactly like
 * the other modern importers (RankMath, AIO, SiteSEO).
 */
class SureRank implements ExecuteHooksBackend {

	/**
	 * The SureRank tags translator.
	 *
	 * @var Tags
	 */
	protected $tags_surerank;

	/**
	 * SureRank global defaults for the settings we import.
	 *
	 * SureRank persists only the keys the user actually saved and merges them
	 * over its defaults at runtime (Settings::get()). Reading the raw option
	 * would therefore miss settings that are live on the site but absent from
	 * the database, so the same defaults are applied here.
	 *
	 * @var array
	 */
	protected $settings_defaults = array(
		'separator'                                => '-',
		'auto_generate_description'                => true,
		'open_graph_tags'                          => true,
		'facebook_meta_tags'                       => true,
		'twitter_meta_tags'                        => true,
		'twitter_card_type'                        => 'summary_large_image',
		'twitter_same_as_facebook'                 => true,
		'author_archive'                           => false,
		'date_archive'                             => false,
		'noindex_paginated_pages'                  => false,
		'paginated_link_relationships'             => array( 'homepage', 'pages', 'archives' ),
		'no_index'                                 => array( 'post_format', 'attachment', 'author', 'date', 'search' ),
		'no_follow'                                => array(),
		'enable_xml_sitemap'                       => true,
		'enable_xml_image_sitemap'                 => true,
		'redirect_attachment_pages_to_post_parent' => true,
		'auto_set_image_title'                     => true,
		'auto_set_image_alt'                       => true,
		'remove_global_comments_feed'              => false,
		'remove_post_authors_feed'                 => false,
		'remove_post_types_feed'                   => false,
		'remove_category_feed'                     => false,
		'remove_tag_feeds'                         => false,
		'remove_custom_taxonomy_feeds'             => false,
		'remove_search_results_feed'               => false,
		'remove_atom_rdf_feeds'                    => false,
	);

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
		$context     = $is_post ? 'post' : 'term';

		// 1. General: title, description, canonical, focus keyword.
		$general = $get_meta( $object_id, 'surerank_settings_general', true );
		if ( is_array( $general ) ) {
			if ( ! empty( $general['page_title'] ) ) {
				$update_meta(
					$object_id,
					'_seopress_titles_title',
					esc_html( $this->tags_surerank->replaceTags( $general['page_title'], $context ) )
				);
			}

			$description = $this->getDescriptionTemplate( $general, $context );
			if ( '' !== $description ) {
				$update_meta( $object_id, '_seopress_titles_desc', $description );
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
						esc_html( $this->tags_surerank->replaceTags( $social[ $surerank_key ], $context ) )
					);
				}
			}

			$image_map = array(
				'_seopress_social_fb_img'      => array( 'facebook_image_url', 'facebook_image_id', '_seopress_social_fb_img_attachment_id' ),
				'_seopress_social_twitter_img' => array( 'twitter_image_url', 'twitter_image_id', '_seopress_social_twitter_img_attachment_id' ),
			);
			foreach ( $image_map as $seopress_key => $keys ) {
				list( $url_key, $id_key, $seopress_id_key ) = $keys;

				if ( empty( $social[ $url_key ] ) ) {
					continue;
				}

				$update_meta( $object_id, $seopress_key, esc_url_raw( $social[ $url_key ] ) );

				if ( ! empty( $social[ $id_key ] ) ) {
					$update_meta( $object_id, $seopress_id_key, absint( $social[ $id_key ] ) );
				}
			}
		}

		// 3. Robots flags. SureRank stores them as separate "yes"/"no"/"" scalars.
		// Noarchive is intentionally skipped: SEOPress has no equivalent.
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
	 * Return the meta description template to import, if any.
	 *
	 * SureRank ignores `page_description` when `auto_generate_description` is
	 * on: the description is then built from the content, and its default
	 * template is the raw `%content%` token. Neither has a SEOPress equivalent,
	 * and importing the literal token would surface as visible text on the
	 * frontend, so both cases are skipped.
	 *
	 * @since 9.8.0
	 *
	 * @param array  $general SureRank general settings (global or per object).
	 * @param string $context 'post' or 'term'.
	 *
	 * @return string Empty string when there is nothing to import.
	 */
	protected function getDescriptionTemplate( $general, $context = 'post' ) {
		if ( empty( $general['page_description'] ) ) {
			return '';
		}

		if ( ! empty( $general['auto_generate_description'] ) ) {
			return '';
		}

		if ( '%content%' === trim( $general['page_description'] ) ) {
			return '';
		}

		return esc_html( $this->tags_surerank->replaceTags( $general['page_description'], $context ) );
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
		$seopress_pro      = get_option( 'seopress_pro_option_name' );

		$seopress_titles   = is_array( $seopress_titles ) ? $seopress_titles : array();
		$seopress_social   = is_array( $seopress_social ) ? $seopress_social : array();
		$seopress_sitemap  = is_array( $seopress_sitemap ) ? $seopress_sitemap : array();
		$seopress_advanced = is_array( $seopress_advanced ) ? $seopress_advanced : array();
		$seopress_pro      = is_array( $seopress_pro ) ? $seopress_pro : array();

		$surerank = array_merge(
			$this->settings_defaults,
			is_array( $surerank_settings ) ? $surerank_settings : array()
		);

		$this->mapTitlesAndMetas( $surerank, $seopress_titles );
		$this->mapHomepage( $surerank, $seopress_titles, $seopress_social );
		$this->mapRobotsRules( $surerank, $seopress_titles );
		$this->mapSpecialPages( $surerank, $seopress_titles );
		$this->mapSocial( $surerank, $seopress_social );
		$this->mapSitemap( $surerank, $seopress_sitemap );
		$this->mapAdvanced( $surerank, $seopress_advanced );
		$this->mapFeeds( $surerank, $seopress_pro );
		$this->mapRobotsTxt( $seopress_pro );

		if ( is_array( $surerank_onboarding ) ) {
			$this->mapKnowledgeGraph( $surerank_onboarding, $seopress_social );
		}

		$this->mapSocialProfiles( $surerank, $surerank_onboarding, $seopress_social );

		update_option( 'seopress_titles_option_name', $seopress_titles, false );
		update_option( 'seopress_social_option_name', $seopress_social, false );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_sitemap, false );
		update_option( 'seopress_advanced_option_name', $seopress_advanced, false );
		update_option( 'seopress_pro_option_name', $seopress_pro, false );
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

		foreach ( array( 'post', 'term' ) as $context ) {
			$page_title = ! empty( $surerank['page_title'] )
				? esc_html( $this->tags_surerank->replaceTags( $surerank['page_title'], $context ) )
				: '';
			$page_desc  = $this->getDescriptionTemplate( $surerank, $context );

			if ( '' === $page_title && '' === $page_desc ) {
				continue;
			}

			$objects    = 'post' === $context ? $this->getPostTypes() : $this->getTaxonomies();
			$titles_key = 'post' === $context ? 'seopress_titles_single_titles' : 'seopress_titles_tax_titles';

			foreach ( $objects as $object_key ) {
				if ( '' !== $page_title ) {
					$titles[ $titles_key ][ $object_key ]['title'] = $page_title;
				}
				if ( '' !== $page_desc ) {
					$titles[ $titles_key ][ $object_key ]['description'] = $page_desc;
				}
			}
		}
	}

	/**
	 * Homepage title, meta description and social overrides.
	 *
	 * SEOPress has no global option for the homepage Open Graph / X card: it
	 * reads them from the front page post meta. SureRank stores them globally,
	 * so they are copied onto the static front page when there is one, and the
	 * images always feed the site-wide social defaults as a fallback (same
	 * approach as the SmartCrawl importer).
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 * @param array $social   SEOPress social option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapHomepage( $surerank, &$titles, &$social ) {
		if ( ! empty( $surerank['home_page_title'] ) ) {
			$titles['seopress_titles_home_site_title'] = esc_html( $this->tags_surerank->replaceTags( $surerank['home_page_title'] ) );
		}
		if ( ! empty( $surerank['home_page_description'] ) ) {
			$titles['seopress_titles_home_site_desc'] = esc_html( $this->tags_surerank->replaceTags( $surerank['home_page_description'] ) );
		}

		if ( ! empty( $surerank['home_page_facebook_image_url'] ) ) {
			$social['seopress_social_facebook_img'] = esc_url_raw( $surerank['home_page_facebook_image_url'] );
		}
		if ( ! empty( $surerank['home_page_twitter_image_url'] ) ) {
			$social['seopress_social_twitter_card_img'] = esc_url_raw( $surerank['home_page_twitter_image_url'] );
		}

		$front_page_id = 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;
		if ( ! $front_page_id ) {
			return;
		}

		$home_social_map = array(
			'_seopress_social_fb_title'      => 'home_page_facebook_title',
			'_seopress_social_fb_desc'       => 'home_page_facebook_description',
			'_seopress_social_twitter_title' => 'home_page_twitter_title',
			'_seopress_social_twitter_desc'  => 'home_page_twitter_description',
		);
		foreach ( $home_social_map as $seopress_key => $surerank_key ) {
			if ( ! empty( $surerank[ $surerank_key ] ) ) {
				update_post_meta(
					$front_page_id,
					$seopress_key,
					esc_html( $this->tags_surerank->replaceTags( $surerank[ $surerank_key ] ) )
				);
			}
		}

		$home_image_map = array(
			'_seopress_social_fb_img'      => 'home_page_facebook_image_url',
			'_seopress_social_twitter_img' => 'home_page_twitter_image_url',
		);
		foreach ( $home_image_map as $seopress_key => $surerank_key ) {
			if ( ! empty( $surerank[ $surerank_key ] ) ) {
				update_post_meta( $front_page_id, $seopress_key, esc_url_raw( $surerank[ $surerank_key ] ) );
			}
		}
	}

	/**
	 * Robots rules: SureRank's no_index / no_follow arrays target a mix of
	 * post types, taxonomies, and special archive pages.
	 *
	 * The `no_archive` array is skipped: SEOPress has no noarchive setting.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapRobotsRules( $surerank, &$titles ) {
		$post_types = $this->getPostTypes();
		$taxonomies = $this->getTaxonomies();

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
	 * Special pages: author/date archives, paginated pages.
	 *
	 * SureRank's `author_archive` / `date_archive` are enable flags: when they
	 * are off it 301-redirects those archives to the homepage, which is what
	 * SEOPress's `_disable` options do.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $titles   SEOPress titles option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapSpecialPages( $surerank, &$titles ) {
		if ( empty( $surerank['author_archive'] ) ) {
			$titles['seopress_titles_archives_author_disable'] = '1';
		}

		if ( empty( $surerank['date_archive'] ) ) {
			$titles['seopress_titles_archives_date_disable'] = '1';
		}

		if ( ! empty( $surerank['noindex_paginated_pages'] ) ) {
			$titles['seopress_titles_paged_noindex'] = '1';
		}

		if ( isset( $surerank['paginated_link_relationships'] ) && empty( $surerank['paginated_link_relationships'] ) ) {
			$titles['seopress_titles_paged_rel'] = '';
		}
	}

	/**
	 * Open Graph / X card settings.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $social   SEOPress social option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapSocial( $surerank, &$social ) {
		if ( ! empty( $surerank['open_graph_tags'] ) || ! empty( $surerank['facebook_meta_tags'] ) ) {
			$social['seopress_social_facebook_og'] = '1';
		}

		if ( ! empty( $surerank['twitter_meta_tags'] ) ) {
			$social['seopress_social_twitter_card'] = '1';
		}

		if ( ! empty( $surerank['twitter_card_type'] ) ) {
			$type = array(
				'summary_large_image' => 'large',
				'summary'             => 'default',
			);

			if ( isset( $type[ $surerank['twitter_card_type'] ] ) ) {
				$social['seopress_social_twitter_card_img_size'] = $type[ $surerank['twitter_card_type'] ];
			}
		}

		if ( ! empty( $surerank['twitter_same_as_facebook'] ) ) {
			$social['seopress_social_twitter_card_og'] = '1';
		}

		// Site-wide fallback image, used for both networks by SureRank.
		if ( ! empty( $surerank['fallback_image'] ) ) {
			$fallback = esc_url_raw( $surerank['fallback_image'] );

			if ( empty( $social['seopress_social_facebook_img'] ) ) {
				$social['seopress_social_facebook_img'] = $fallback;
			}
			if ( empty( $social['seopress_social_twitter_card_img'] ) ) {
				$social['seopress_social_twitter_card_img'] = $fallback;
			}
		}

		if ( ! empty( $surerank['twitter_profile_fallback'] ) ) {
			$social['seopress_social_twitter_card_creator'] = sanitize_text_field( $surerank['twitter_profile_fallback'] );
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
	 * Advanced settings: attachments, image attributes, site verification.
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

		if ( ! empty( $surerank['auto_set_image_alt'] ) ) {
			$advanced['seopress_advanced_advanced_image_auto_alt_editor'] = '1';
		}

		if ( ! empty( $surerank['auto_set_image_title'] ) ) {
			$advanced['seopress_advanced_advanced_image_auto_title_editor'] = '1';
		}

		$google_verification = get_option( 'surerank_gsc_verification_token' );
		if ( ! empty( $google_verification ) && is_string( $google_verification ) ) {
			$advanced['seopress_advanced_advanced_google'] = sanitize_text_field( $google_verification );
		}
	}

	/**
	 * RSS feeds removal (SEOPress PRO).
	 *
	 * SureRank exposes one toggle per feed type. SEOPress groups everything
	 * that is neither the posts feed nor the comments feed under a single
	 * "extra feeds" toggle.
	 *
	 * @param array $surerank Global SureRank settings.
	 * @param array $pro      SEOPress PRO option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapFeeds( $surerank, &$pro ) {
		if ( ! empty( $surerank['remove_post_types_feed'] ) ) {
			$pro['seopress_rss_disable_posts_feed'] = '1';
		}

		if ( ! empty( $surerank['remove_global_comments_feed'] ) ) {
			$pro['seopress_rss_disable_comments_feed'] = '1';
		}

		$extra_feeds = array(
			'remove_post_authors_feed',
			'remove_category_feed',
			'remove_tag_feeds',
			'remove_custom_taxonomy_feeds',
			'remove_search_results_feed',
			'remove_atom_rdf_feeds',
		);
		foreach ( $extra_feeds as $key ) {
			if ( ! empty( $surerank[ $key ] ) ) {
				$pro['seopress_rss_disable_extra_feed'] = '1';

				break;
			}
		}
	}

	/**
	 * Custom robots.txt content (SEOPress PRO).
	 *
	 * @param array $pro SEOPress PRO option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapRobotsTxt( &$pro ) {
		$robots_txt = get_option( 'surerank_robots_txt_content' );

		if ( empty( $robots_txt ) || ! is_string( $robots_txt ) ) {
			return;
		}

		$pro['seopress_robots_enable'] = '1';
		$pro['seopress_robots_file']   = esc_html( $robots_txt );
	}

	/**
	 * Knowledge Graph (Organization name, type, logo, phone).
	 *
	 * @param array $onboarding SureRank `surerank_settings_onboarding` option.
	 * @param array $social     SEOPress social option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapKnowledgeGraph( $onboarding, &$social ) {
		if ( ! empty( $onboarding['organization_type'] ) ) {
			$type = array(
				'Organization' => 'Organization',
				'Person'       => 'Person',
			);

			if ( isset( $type[ $onboarding['organization_type'] ] ) ) {
				$social['seopress_social_knowledge_type'] = $type[ $onboarding['organization_type'] ];
			}
		}

		if ( ! empty( $onboarding['website_name'] ) ) {
			$social['seopress_social_knowledge_name'] = sanitize_text_field( $onboarding['website_name'] );
		} elseif ( ! empty( $onboarding['website_owner_name'] ) ) {
			$social['seopress_social_knowledge_name'] = sanitize_text_field( $onboarding['website_owner_name'] );
		}

		if ( ! empty( $onboarding['business_description'] ) ) {
			$social['seopress_social_knowledge_desc'] = sanitize_textarea_field( $onboarding['business_description'] );
		}

		if ( ! empty( $onboarding['website_logo'] ) && is_array( $onboarding['website_logo'] ) && ! empty( $onboarding['website_logo']['url'] ) ) {
			$social['seopress_social_knowledge_img'] = esc_url_raw( $onboarding['website_logo']['url'] );
		}

		if ( ! empty( $onboarding['website_owner_phone'] ) ) {
			$social['seopress_social_knowledge_phone'] = sanitize_text_field( $onboarding['website_owner_phone'] );
		}
	}

	/**
	 * Social profiles.
	 *
	 * SureRank keeps them in the onboarding option and mirrors the Facebook and
	 * X ones into its global settings. The networks SEOPress has no dedicated
	 * field for are appended to the "extra accounts" textarea, one URL per line.
	 *
	 * @param array      $surerank   Global SureRank settings.
	 * @param array|bool $onboarding SureRank onboarding option.
	 * @param array      $social     SEOPress social option (passed by reference).
	 *
	 * @return void
	 */
	protected function mapSocialProfiles( $surerank, $onboarding, &$social ) {
		$profiles = array();

		if ( is_array( $onboarding ) && ! empty( $onboarding['social_profiles'] ) && is_array( $onboarding['social_profiles'] ) ) {
			$profiles = $onboarding['social_profiles'];
		}

		if ( ! empty( $surerank['social_profiles'] ) && is_array( $surerank['social_profiles'] ) ) {
			$profiles = array_merge( $profiles, array_filter( $surerank['social_profiles'] ) );
		}

		// SureRank keeps these two in sync with the matching social profile.
		if ( ! empty( $surerank['facebook_page_url'] ) ) {
			$profiles['facebook'] = $surerank['facebook_page_url'];
		}
		if ( ! empty( $surerank['twitter_profile_username'] ) ) {
			$profiles['twitter'] = $surerank['twitter_profile_username'];
		}

		if ( empty( $profiles ) ) {
			return;
		}

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

			// SEOPress stores the X account as a handle, not a URL.
			$social[ $seopress_key ] = 'twitter' === $surerank_key
				? sanitize_text_field( $profiles[ $surerank_key ] )
				: esc_url_raw( $profiles[ $surerank_key ] );
		}

		$extra = array();
		foreach ( $profiles as $key => $value ) {
			if ( isset( $profile_map[ $key ] ) || empty( $value ) || ! is_string( $value ) ) {
				continue;
			}

			$extra[] = esc_html( esc_url_raw( $value ) );
		}

		$extra = array_filter( $extra );
		if ( empty( $extra ) ) {
			return;
		}

		// Escape the new entries only: the stored value is already escaped, and
		// running it through esc_html() again would double-encode the ampersands
		// of any URL holding a query string on every import.
		$existing = ! empty( $social['seopress_social_accounts_extra'] )
			? array_filter( explode( "\n", $social['seopress_social_accounts_extra'] ) )
			: array();

		$social['seopress_social_accounts_extra'] = implode( "\n", array_unique( array_merge( $existing, $extra ) ) );
	}

	/**
	 * Registered post type names.
	 *
	 * @return array
	 */
	protected function getPostTypes() {
		$wp_data = function_exists( 'seopress_get_service' ) ? seopress_get_service( 'WordPressData' ) : null;

		if ( ! $wp_data || ! method_exists( $wp_data, 'getPostTypes' ) ) {
			return array();
		}

		return array_keys( (array) $wp_data->getPostTypes() );
	}

	/**
	 * Registered taxonomy names.
	 *
	 * @return array
	 */
	protected function getTaxonomies() {
		$wp_data = function_exists( 'seopress_get_service' ) ? seopress_get_service( 'WordPressData' ) : null;

		if ( ! $wp_data || ! method_exists( $wp_data, 'getTaxonomies' ) ) {
			return array();
		}

		return array_keys( (array) $wp_data->getTaxonomies() );
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

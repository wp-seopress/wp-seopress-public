<?php

namespace SEOPress\Actions\Admin\Importer;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\RankMath\Tags;

/**
 * RankMath importer
 */
class RankMath implements ExecuteHooksBackend {

	/**
	 * The RankMath tags.
	 *
	 * @var Tags
	 */
	protected $tags_rank_math;

	/**
	 * The RankMath constructor.
	 */
	public function __construct() {
		/**
		 * The RankMath tags.
		 *
		 * @var Tags
		 */
		$this->tags_rank_math = new Tags();
	}

	/**
	 * The RankMath hooks.
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_seopress_rk_migration', array( $this, 'process' ) );
	}

	/**
	 * Migrate the term query.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	protected function migrateTermQuery() {
		wp_reset_postdata();

		$args           = array(
			'hide_empty' => false,
			'fields'     => 'ids',
		);
		$rk_query_terms = get_terms( $args );

		$get_term_metas = array(
			'_seopress_titles_title'         => 'rank_math_title',
			'_seopress_titles_desc'          => 'rank_math_description',
			'_seopress_social_fb_title'      => 'rank_math_facebook_title',
			'_seopress_social_fb_desc'       => 'rank_math_facebook_description',
			'_seopress_social_fb_img'        => 'rank_math_facebook_image',
			'_seopress_social_twitter_title' => 'rank_math_twitter_title',
			'_seopress_social_twitter_desc'  => 'rank_math_twitter_description',
			'_seopress_social_twitter_img'   => 'rank_math_twitter_image',
			'_seopress_robots_canonical'     => 'rank_math_canonical_url',
			'_seopress_analysis_target_kw'   => 'rank_math_focus_keyword',
		);
		if ( ! $rk_query_terms ) {
			wp_reset_postdata();

			return 'done';
		}

		foreach ( $rk_query_terms as $term_id ) {
			foreach ( $get_term_metas as $key => $value ) {
				$meta_rank_math = get_term_meta( $term_id, $value, true );
				if ( ! empty( $meta_rank_math ) ) {
					update_term_meta( $term_id, $key, $this->tags_rank_math->replaceTags( $meta_rank_math ) );
				}
			}

			if ( '' !== get_term_meta( $term_id, 'rank_math_robots', true ) ) { // Import Robots NoIndex, NoFollow, NoImageIndex, NoSnippet.
				$rank_math_robots = get_term_meta( $term_id, 'rank_math_robots', true );

				if ( in_array( 'noindex', $rank_math_robots, true ) ) {
					update_term_meta( $term_id, '_seopress_robots_index', 'yes' );
				}
				if ( in_array( 'nofollow', $rank_math_robots, true ) ) {
					update_term_meta( $term_id, '_seopress_robots_follow', 'yes' );
				}
				if ( in_array( 'noimageindex', $rank_math_robots, true ) ) {
					update_term_meta( $term_id, '_seopress_robots_imageindex', 'yes' );
				}
				if ( in_array( 'nosnippet', $rank_math_robots, true ) ) {
					update_term_meta( $term_id, '_seopress_robots_snippet', 'yes' );
				}
			}
		}

		wp_reset_postdata();

		return 'done';
	}

	/**
	 * Migrate the post query.
	 *
	 * @since 4.3.0
	 *
	 * @param int $offset    The offset.
	 * @param int $increment The increment.
	 */
	protected function migratePostQuery( $offset, $increment ) {
		$args = array(
			'posts_per_page' => $increment,
			'post_type'      => 'any',
			'post_status'    => 'any',
			'offset'         => $offset,
		);

		$rk_query = get_posts( $args );

		if ( ! $rk_query ) {
			$offset += $increment;

			return $offset;
		}

		$get_post_metas = array(
			'_seopress_titles_title'         => 'rank_math_title',
			'_seopress_titles_desc'          => 'rank_math_description',
			'_seopress_social_fb_title'      => 'rank_math_facebook_title',
			'_seopress_social_fb_desc'       => 'rank_math_facebook_description',
			'_seopress_social_fb_img'        => 'rank_math_facebook_image',
			'_seopress_social_twitter_title' => 'rank_math_twitter_title',
			'_seopress_social_twitter_desc'  => 'rank_math_twitter_description',
			'_seopress_social_twitter_img'   => 'rank_math_twitter_image',
			'_seopress_robots_canonical'     => 'rank_math_canonical_url',
			'_seopress_analysis_target_kw'   => 'rank_math_focus_keyword',
		);

		foreach ( $rk_query as $post ) {
			foreach ( $get_post_metas as $key => $value ) {
				$meta_rank_math = get_post_meta( $post->ID, $value, true );
				if ( ! empty( $meta_rank_math ) ) {
					update_post_meta( $post->ID, $key, esc_html( $this->tags_rank_math->replaceTags( $meta_rank_math ) ) );
				}
			}

			if ( '' !== get_post_meta( $post->ID, 'rank_math_robots', true ) ) { // Import Robots NoIndex, NoFollow, NoImageIndex, NoSnippet.
				$rank_math_robots = get_post_meta( $post->ID, 'rank_math_robots', true );

				if ( is_array( $rank_math_robots ) ) {
					if ( in_array( 'noindex', $rank_math_robots, true ) ) {
						update_post_meta( $post->ID, '_seopress_robots_index', 'yes' );
					}
					if ( in_array( 'nofollow', $rank_math_robots, true ) ) {
						update_post_meta( $post->ID, '_seopress_robots_follow', 'yes' );
					}
					if ( in_array( 'noimageindex', $rank_math_robots, true ) ) {
						update_post_meta( $post->ID, '_seopress_robots_imageindex', 'yes' );
					}
					if ( in_array( 'nosnippet', $rank_math_robots, true ) ) {
						update_post_meta( $post->ID, '_seopress_robots_snippet', 'yes' );
					}
				}
			}
		}

		$offset += $increment;

		return $offset;
	}

	/**
	 * Migrate the settings.
	 *
	 * @since 8.0.0
	 *
	 * @return void
	 */
	protected function migrateSettings() {
		$seopress_titles           = get_option( 'seopress_titles_option_name' );
		$seopress_social           = get_option( 'seopress_social_option_name' );
		$seopress_sitemap          = get_option( 'seopress_xml_sitemap_option_name' );
		$seopress_advanced         = get_option( 'seopress_advanced_option_name' );
		$seopress_pro              = get_option( 'seopress_pro_option_name' );
		$seopress_instant_indexing = get_option( 'seopress_instant_indexing_option_name' );

		$rank_math_general          = get_option( 'rank-math-options-general' );
		$rank_math_titles           = get_option( 'rank-math-options-titles' );
		$rank_math_sitemap          = get_option( 'rank-math-options-sitemap' );
		$rank_math_instant_indexing = get_option( 'rank-math-options-instant-indexing' );

		if ( ! empty( $rank_math_instant_indexing ) ) {
			foreach ( $rank_math_instant_indexing as $key => $value ) {
				if ( 'indexnow_api_key' === $key ) {
					$seopress_instant_indexing['seopress_instant_indexing_bing_api_key'] = esc_html( $value );
				}
			}
		}

		if ( ! empty( $rank_math_general ) ) {
			foreach ( $rank_math_general as $key => $value ) {
				// Redirects 404 to.
				if ( 'redirections_post_redirect' === $key ) {
					$type                                       = array(
						'default' => 'none',
						'home'    => 'home',
						'custom'  => 'custom',
					);
					$seopress_pro['seopress_404_redirect_home'] = esc_html( $type[ $value ] );
				}
				// 404 custom URL.
				if ( 'redirections_custom_url' === $key ) {
					$seopress_pro['seopress_404_redirect_custom_url'] = esc_url( $value );
				}
				// Disable automatic redirects.
				if ( 'redirections_post_redirect' === $key ) {
					if ( 'on' === $value ) {
						unset( $seopress_pro['seopress_404_disable_automatic_redirects'] );
					} else {
						$seopress_pro['seopress_404_disable_automatic_redirects'] = '1';
					}
				}
				// Category URL.
				if ( 'strip_category_base' === $key && 'on' === $value ) {
					$seopress_advanced['seopress_advanced_advanced_category_url'] = '1';
				} elseif ( 'strip_category_base' === $key && 'off' === $value ) {
					unset( $seopress_advanced['seopress_advanced_advanced_category_url'] );
				}
				// Remove WC Product Category Base.
				if ( 'wc_remove_category_base' === $key && 'on' === $value ) {
					$seopress_advanced['seopress_advanced_advanced_product_cat_url'] = '1';
				} elseif ( 'wc_remove_category_base' === $key && 'off' === $value ) {
					unset( $seopress_advanced['seopress_advanced_advanced_product_cat_url'] );
				}
				// Remove WC generator tag.
				if ( 'wc_remove_generator' === $key && 'on' === $value ) {
					$seopress_pro['seopress_woocommerce_meta_generator'] = '1';
				} elseif ( 'wc_remove_generator' === $key && 'off' === $value ) {
					unset( $seopress_pro['seopress_woocommerce_meta_generator'] );
				}
				// Remove WC structured data.
				if ( 'remove_shop_snippet_data' === $key && 'on' === $value ) {
					$seopress_pro['seopress_woocommerce_schema_output'] = '1';
				} elseif ( 'remove_shop_snippet_data' === $key && 'off' === $value ) {
					unset( $seopress_pro['seopress_woocommerce_schema_output'] );
				}
				// Attachment Redirect URLs.
				if ( 'attachment_redirect_urls' === $key && 'on' === $value ) {
					$seopress_advanced['seopress_advanced_advanced_attachments'] = '1';
				} elseif ( 'attachment_redirect_urls' === $key && 'off' === $value ) {
					unset( $seopress_advanced['seopress_advanced_advanced_attachments'] );
				}
				// Breadcrumbs.
				if ( 'breadcrumbs' === $key && 'on' === $value ) {
					$seopress_pro['seopress_breadcrumbs_enable']      = '1';
					$seopress_pro['seopress_breadcrumbs_json_enable'] = '1';
				} elseif ( 'breadcrumbs' === $key && 'off' === $value ) {
					unset( $seopress_pro['seopress_breadcrumbs_enable'] );
					unset( $seopress_pro['seopress_breadcrumbs_json_enable'] );
				}
				// Breadcrumbs Separator.
				if ( 'breadcrumbs_separator' === $key ) {
					$seopress_pro['seopress_breadcrumbs_separator'] = esc_html( $value );
				}
				// Breadcrumbs Home.
				if ( 'breadcrumbs_home_label' === $key ) {
					$seopress_pro['seopress_breadcrumbs_i18n_home'] = esc_html( $value );
				}
				// Breadcrumbs Prefix.
				if ( 'breadcrumbs_prefix' === $key ) {
					$seopress_pro['seopress_breadcrumbs_i18n_here'] = esc_html( $value );
				}
				// Breadcrumbs Search Prefix.
				if ( 'breadcrumbs_search_format' === $key ) {
					$seopress_pro['seopress_breadcrumbs_i18n_search'] = esc_html( $value );
				}
				// Breadcrumbs 404 Crumbs.
				if ( 'breadcrumbs_404_label' === $key ) {
					$seopress_pro['seopress_breadcrumbs_i18n_404'] = esc_html( $value );
				}
				// Breadcrumbs Display Blog Page.
				if ( 'breadcrumbs_blog_page' === $key ) {
					if ( 'on' === $value ) {
						unset( $seopress_pro['seopress_breadcrumbs_remove_blog_page'] );
					} elseif ( 'off' === $value ) {
						$seopress_pro['seopress_breadcrumbs_remove_blog_page'] = '1';
					}
				}
				// Google ownership.
				if ( 'google_verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_google'] = esc_html( $value );
				}
				// Bing ownership.
				if ( 'bing_verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_bing'] = esc_html( $value );
				}
				// Yandex ownership.
				if ( 'yandex_verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_yandex'] = esc_html( $value );
				}
				// Baidu ownership.
				if ( 'baidu_verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_baidu'] = esc_html( $value );
				}
				// Pinterest ownership.
				if ( 'pinterest_verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html( $value );
				}
				// Custom Webmaster Tags.
				if ( 'custom_webmaster_tags' === $key ) {
					$accounts = array_filter( explode( "\n", $value ) );
					$accounts = implode( "\n", array_map( 'esc_url', $accounts ) );
					$seopress_social['seopress_social_accounts_extra'] = esc_html( $accounts );
				}
				// RSS.
				if ( 'rss_before_content' === $key || 'rss_after_content' === $key ) {
					$rss_vars = array(
						'%%AUTHORLINK%%'   => '<a href="%%author_permalink%%">%%post_author%%</a>',
						'%%POSTLINK%%'     => '<a href="%%post_permalink%%">%%post_title%%</a>',
						'%%BLOGLINK%%'     => '<a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a>',
						'%%BLOGDESCLINK%%' => '<a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' ) . '</a>',
					);
					$value    = str_replace( array_keys( $rss_vars ), array_values( $rss_vars ), $value );
				}
				if ( 'rss_before_content' === $key ) {
					$args                                     = array(
						'strong' => array(),
						'em'     => array(),
						'br'     => array(),
						'a'      => array(
							'href' => array(),
							'rel'  => array(),
						),
					);
					$seopress_pro['seopress_rss_before_html'] = wp_kses( $value, $args );
				}
				if ( 'rss_after_content' === $key ) {
					$args                                    = array(
						'strong' => array(),
						'em'     => array(),
						'br'     => array(),
						'a'      => array(
							'href' => array(),
							'rel'  => array(),
						),
					);
					$seopress_pro['seopress_rss_after_html'] = wp_kses( $value, $args );
				}
				// robots.txt file content.
				if ( 'robots_txt_content' === $key ) {
					if ( ! empty( $value ) ) {
						$seopress_pro['seopress_robots_enable'] = '1';
					}
					$seopress_pro['seopress_robots_file'] = esc_html( $value );
				}
			}
		}

		if ( ! empty( $rank_math_titles ) ) {
			foreach ( $rank_math_titles as $key => $value ) {
				// Global meta robots.
				if ( 'robots_global' === $key ) {
					if ( in_array( 'noindex', $rank_math_titles['robots_global'], true ) ) {
						$seopress_titles['seopress_titles_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_noindex'] );
					}
					if ( in_array( 'nofollow', $rank_math_titles['robots_global'], true ) ) {
						$seopress_titles['seopress_titles_nofollow'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_nofollow'] );
					}
					if ( in_array( 'noimageindex', $rank_math_titles['robots_global'], true ) ) {
						$seopress_titles['seopress_titles_noimageindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_noimageindex'] );
					}
					if ( in_array( 'nosnippet', $rank_math_titles['robots_global'], true ) ) {
						$seopress_titles['seopress_titles_nosnippet'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_nosnippet'] );
					}
				} else {
					unset( $seopress_titles['seopress_titles_noindex'] );
					unset( $seopress_titles['seopress_titles_nofollow'] );
					unset( $seopress_titles['seopress_titles_noimageindex'] );
					unset( $seopress_titles['seopress_titles_nosnippet'] );
				}
				// Title separator.
				if ( 'title_separator' === $key ) {
					$seopress_titles['seopress_titles_sep'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Open Graph image.
				if ( 'open_graph_image' === $key ) {
					$seopress_social['seopress_social_knowledge_img'] = esc_url( $value );
				}
				// Twitter card image size.
				if ( 'twitter_card_type' === $key ) {
					$seopress_social['seopress_social_twitter_card_img_size'] = esc_html( $value );
				}
				// Knowledge graph type.
				if ( 'knowledgegraph_type' === $key ) {
					$type = array(
						'company' => 'Organization',
						'person'  => 'Person',
					);
					$seopress_social['seopress_social_knowledge_type'] = esc_html( $type[ $value ] );
				}
				// Website name.
				if ( 'website_name' === $key ) {
					$seopress_titles['seopress_titles_home_site_title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Website alternate name.
				if ( 'website_alternate_name' === $key ) {
					$seopress_titles['seopress_titles_home_site_title_alt'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Knowledge graph name.
				if ( 'knowledgegraph_name' === $key ) {
					$seopress_social['seopress_social_knowledge_name'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Knowledge graph logo.
				if ( 'knowledgegraph_logo' === $key ) {
					$seopress_social['seopress_social_knowledge_img'] = esc_url( $value );
				}
				// Facebook URL.
				if ( 'social_url_facebook' === $key ) {
					$seopress_social['seopress_social_accounts_facebook'] = esc_url( $value );
				}
				// Twitter URL.
				if ( 'twitter_author_names' === $key ) {
					$seopress_social['seopress_social_accounts_twitter'] = esc_html( $value );
				}
				// Custom Webmaster Tags.
				if ( 'custom_webmaster_tags' === $key ) {
					$accounts = array_filter( explode( "\n", $value ) );
					$accounts = implode( "\n", array_map( 'esc_url', $accounts ) );
					$seopress_social['seopress_social_accounts_extra'] = esc_html( $accounts );
				}
				// Facebook Admin ID.
				if ( 'facebook_admin_id' === $key ) {
					$seopress_social['seopress_social_facebook_admin_id'] = esc_html( $value );
				}
				// Facebook App ID.
				if ( 'facebook_app_id' === $key ) {
					$seopress_social['seopress_social_facebook_app_id'] = esc_html( $value );
				}
				// Disable author archives.
				if ( 'disable_author_archives' === $key ) {
					if ( 'on' === $value ) {
						$seopress_titles['seopress_titles_archives_author_disable'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_author_disable'] );
					}
				}
				// Disable category archives.
				if ( 'disable_category_archives' === $key ) {
					if ( 'on' === $value ) {
						$seopress_titles['seopress_titles_archives_category_disable'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_category_disable'] );
					}
				}
				// Author archive title.
				if ( 'author_custom_robots' === $key ) {
					if ( 'on' === $value && in_array( 'noindex', $rank_math_titles['author_robots'], true ) ) {
						$seopress_titles['seopress_titles_archives_author_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_author_noindex'] );
					}
				}
				// Author archive title.
				if ( 'author_archive_title' === $key ) {
					$seopress_titles['seopress_titles_archives_author_title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Author archive description.
				if ( 'author_archive_description' === $key ) {
					$seopress_titles['seopress_titles_archives_author_desc'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Disable date archives.
				if ( 'disable_date_archives' === $key ) {
					if ( 'on' === $value ) {
						$seopress_titles['seopress_titles_archives_date_disable'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_date_disable'] );
					}
				}
				// Date archive title.
				if ( 'date_archive_title' === $key ) {
					$seopress_titles['seopress_titles_archives_date_title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Date archive description.
				if ( 'date_archive_description' === $key ) {
					$seopress_titles['seopress_titles_archives_date_desc'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Date archive noindex.
				if ( 'date_archive_robots' === $key && 'on' === $value ) {
					if ( in_array( 'noindex', $rank_math_titles['date_archive_robots'], true ) ) {
						$seopress_titles['seopress_titles_archives_date_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_date_noindex'] );
					}
				}
				// 404 title
				if ( '404_title' === $key ) {
					$seopress_titles['seopress_titles_archives_404_title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Search title.
				if ( 'search_title' === $key ) {
					$seopress_titles['seopress_titles_archives_search_title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
				}
				// Search noindex.
				if ( 'noindex_search' === $key ) {
					if ( 'on' === $value ) {
						$seopress_titles['seopress_titles_archives_search_title_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_search_title_noindex'] );
					}
				}
				// Import CPT settings.
				$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
				foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
					// Single title.
					if ( 'pt_' . $seopress_cpt_key . '_title' === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
					}
					// Single description.
					if ( 'pt_' . $seopress_cpt_key . '_description' === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['description'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
					}
					// Single noindex.
					if ( 'pt_' . $seopress_cpt_key . '_custom_robots' === $key && 'on' === $value ) {
						unset( $seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] );
						if ( ! empty( $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ] ) && is_array( $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ] ) && in_array( 'noindex', $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ], true ) ) {
							$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] = '1';
						}
						unset( $seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['nofollow'] );
						if ( ! empty( $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ] ) && is_array( $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ] ) && in_array( 'nofollow', $rank_math_titles[ 'pt_' . $seopress_cpt_key . '_robots' ], true ) ) {
							$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['nofollow'] = '1';
						}
					}
					// Add meta box.
					if ( 'pt_' . $seopress_cpt_key . '_add_meta_box' === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['enable'] = '1';
						if ( 'on' === $value ) {
							unset( $seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['enable'] );
						}
					}
				}
				// Import taxonomies settings.
				$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
				foreach ( $taxonomies as $seopress_tax_key => $seopress_tax_value ) {
					// Tax title.
					if ( 'tax_' . $seopress_tax_key . '_title' === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['title'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
					}
					// Tax description.
					if ( 'tax_' . $seopress_tax_key . '_description' === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['description'] = esc_html( $this->tags_rank_math->replaceTags( $value ) );
					}
					// Tax noindex.
					if ( 'tax_' . $seopress_tax_key . '_custom_robots' === $key && 'on' === $value ) {
						unset( $seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] );
						if ( ! empty( $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ] ) && is_array( $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ] ) && in_array( 'noindex', $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ], true ) ) {
							$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] = '1';
						}
						unset( $seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['nofollow'] );
						if ( ! empty( $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ] ) && is_array( $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ] ) && in_array( 'nofollow', $rank_math_titles[ 'tax_' . $seopress_tax_key . '_robots' ], true ) ) {
							$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['nofollow'] = '1';
						}
					}
					// Add meta box.
					if ( 'tax_' . $seopress_tax_key . '_add_meta_box' === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['enable'] = '1';
						if ( 'on' === $value ) {
							unset( $seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['enable'] );
						}
					}
				}
			}
		}

		if ( ! empty( $rank_math_sitemap ) ) {
			foreach ( $rank_math_sitemap as $key => $value ) {
				// Author archive sitemap.
				if ( 'authors_sitemap' === $key ) {
					if ( 'on' === $value ) {
						$seopress_sitemap['seopress_xml_sitemap_author_enable'] = '1';
					} else {
						unset( $seopress_sitemap['seopress_xml_sitemap_author_enable'] );
					}
				}
				// Include images.
				if ( 'include_images' === $key && 'on' === $value ) {
					$seopress_sitemap['seopress_xml_sitemap_img_enable'] = '1';
				} elseif ( 'include_images' === $key && 'off' === $value ) {
					unset( $seopress_sitemap['seopress_xml_sitemap_img_enable'] );
				}
				// HTML sitemap.
				if ( 'html_sitemap' === $key ) {
					if ( 'on' === $value ) {
						$seopress_sitemap['seopress_xml_sitemap_html_enable'] = '1';
					} else {
						unset( $seopress_sitemap['seopress_xml_sitemap_html_enable'] );
					}
				}
				// HTML sitemap page.
				if ( 'html_sitemap_page' === $key ) {
					$seopress_sitemap['seopress_xml_sitemap_html_mapping'] = esc_html( $value );
				}
				// HTML sitemap sort.
				if ( 'html_sitemap_sort' === $key ) {
					$sort = array(
						'published'    => 'date',
						'modified'     => 'modified',
						'alphabetical' => 'title',
						'post_id'      => 'ID',
					);
					$seopress_sitemap['seopress_xml_sitemap_html_orderby'] = esc_html( $sort[ $value ] );
				}
				// Import CPT settings.
				$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
				foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
					// Include CPT in sitemap.
					if ( 'pt_' . $seopress_cpt_key . '_sitemap' === $key && 'on' === $value ) {
						$seopress_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
					} elseif ( 'pt_' . $seopress_cpt_key . '_sitemap' === $key && 'off' === $value ) {
						unset( $seopress_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] );
					}
				}
				// Import taxonomies settings.
				$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
				foreach ( $taxonomies as $seopress_tax_key => $seopress_tax_value ) {
					// Include tax in sitemap.
					if ( 'tax_' . $seopress_tax_key . '_sitemap' === $key && 'on' === $value ) {
						$seopress_sitemap['seopress_xml_sitemap_taxonomies_list'][ $seopress_tax_key ]['include'] = '1';
					} elseif ( 'tax_' . $seopress_tax_key . '_sitemap' === $key && 'off' === $value ) {
						unset( $seopress_sitemap['seopress_xml_sitemap_taxonomies_list'][ $seopress_tax_key ]['include'] );
					}
				}
			}
		}

		update_option( 'seopress_titles_option_name', $seopress_titles, false );
		update_option( 'seopress_social_option_name', $seopress_social, false );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_sitemap, false );
		update_option( 'seopress_advanced_option_name', $seopress_advanced, false );
		update_option( 'seopress_pro_option_name', $seopress_pro, false );
		update_option( 'seopress_instant_indexing_option_name', $seopress_instant_indexing, false );
	}

	/**
	 * Process the migration.
	 *
	 * @since 4.3.0
	 */
	public function process() {
		check_ajax_referer( 'seopress_rk_migrate_nonce', '_ajax_nonce', true );
		if ( ! is_admin() ) {
			wp_send_json_error();

			return;
		}

		if ( ! current_user_can( seopress_capability( 'manage_options', 'migration' ) ) ) { // phpcs:ignore
			wp_send_json_error();

			return;
		}

		$this->migrateSettings();

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

		do_action( 'seopress_third_importer_rank_math', $offset, $increment );

		wp_send_json_success( $data );
		exit();
	}
}

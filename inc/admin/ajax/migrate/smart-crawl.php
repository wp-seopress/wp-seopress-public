<?php
/**
 * SmartCrawl migration.
 *
 * @package SEOPress
 * @subpackage Ajax
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * SmartCrawl migration.
 */
function seopress_smart_crawl_migration() {
	check_ajax_referer( 'seopress_smart_crawl_migrate_nonce', '_ajax_nonce', true );

	if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) ) && is_admin() ) {
		if ( isset( $_POST['offset'] ) && isset( $_POST['offset'] ) ) {
			$offset = absint( $_POST['offset'] );
		}

		global $wpdb;
		// phpcs:ignore
		$total_count_posts = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
		// phpcs:ignore
		$total_count_terms = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->terms}" );

		$increment = 200;
		global $post;

		// === Import settings ===//
		$wds_onpage_options = get_option( 'wds_onpage_options' );
		$wds_social_options = get_option( 'wds_social_options' );
		$wds_sitemap_options = get_option( 'wds_sitemap_options' );
		$wds_settings_options = get_option( 'wds_settings_options' );
		$wds_schema_options = get_option( 'wds_schema_options' );

		$seopress_titles   = get_option( 'seopress_titles_option_name' );
		$seopress_xml_sitemap = get_option( 'seopress_xml_sitemap_option_name' );
		$seopress_social   = get_option( 'seopress_social_option_name' );
		$seopress_advanced = get_option( 'seopress_advanced_option_name' );
		$seopress_pro      = get_option( 'seopress_pro_option_name' );
		$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
		$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
		if ( ! empty( $wds_onpage_options ) ) {
			foreach ( $wds_onpage_options as $key => $value ) {
				// Home title.
				if ( 'title-home' === $key ) {
					$seopress_titles['seopress_titles_home_site_title'] = esc_html( $value );
				}
				// Home description.
				if ( 'metadesc-home' === $key ) {
					$seopress_titles['seopress_titles_home_site_desc'] = esc_html( $value );
				}
				// Separator.
				if ( 'separator' === $key ) {
					$seopress_titles['seopress_titles_sep'] = esc_html( $value );
				}
				// Advanced.
				if ( 'meta_robots-noindex-main_blog_archive' === $key ) {
					if ( 1 === $value ) {
						$seopress_titles['seopress_titles_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_noindex'] );
					}
				}
				if ( 'meta_robots-nofollow-main_blog_archive' === $key ) {
					if ( 1 === $value ) {
						$seopress_titles['seopress_titles_nofollow'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_nofollow'] );
					}
				}
				// Import CPT settings.
				foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
					// Single title.
					if ( 'title-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['title'] = esc_html( $value );
					}
					// Single description.
					if ( 'metadesc-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['description'] = esc_html( $value );
					}
					// Single noindex.
					if ( 'meta_robots-noindex-' . $seopress_cpt_key === $key ) {
						unset( $seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['noindex'] = '1';
						}
					}
					// Single nofollow.
					if ( 'meta_robots-nofollow-' . $seopress_cpt_key === $key ) {
						unset( $seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['nofollow'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['nofollow'] = '1';
						}
					}
					// Default OG Image.
					if ( 'og-images-' . $seopress_cpt_key === $key ) {
						$image_id = $value[0];
						$img_url  = wp_get_attachment_url( $image_id );

						if ( isset( $img_url ) && '' !== $img_url ) {
							$seopress_social['seopress_social_facebook_img_cpt'][ $seopress_cpt_key ] = esc_url( $img_url );
						}
					}
					// Archive title.
					if ( 'title-pt-archive-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['title'] = esc_html( $value );
					}
					// Archive description.
					if ( 'metadesc-pt-archive-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['description'] = esc_html( $value );
					}
					// Archive noindex.
					if ( 'meta_robots-noindex-pt-archive-' . $seopress_cpt_key === $key ) {
						unset( $seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['noindex'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['noindex'] = '1';
						}
					}
					// Archive nofollow.
					if ( 'meta_robots-nofollow-pt-archive-' . $seopress_cpt_key === $key ) {
						unset( $seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['nofollow'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['nofollow'] = '1';
						}
					}
				}
				// Import taxonomies settings.
				foreach ( $taxonomies as $seopress_tax_key => $seopress_tax_value ) {
					// Tax title.
					if ( 'title-' . $seopress_tax_key === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['title'] = esc_html( $value );
					}
					// Tax description.
					if ( 'metadesc-' . $seopress_tax_key === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['description'] = esc_html( $value );
					}
					// Tax noindex.
					if ( 'meta_robots-noindex-' . $seopress_tax_key === $key ) {
						unset( $seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['noindex'] = '1';
						}
					}
					// Tax nofollow.
					if ( 'meta_robots-nofollow-' . $seopress_tax_key === $key ) {
						unset( $seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['nofollow'] );
						if ( 1 === $value ) {
							$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['nofollow'] = '1';
						}
					}
				}
				// Author.
				if ( 'enable-author-archive' === $key ) {
					if ( 1 === $value ) {
						unset( $seopress_titles['seopress_titles_archives_author_disable'] );
					} else {
						$seopress_titles['seopress_titles_archives_author_disable'] = '1';
					}
				}
				if ( 'meta_robots-noindex-author' === $key ) {
					if ( 1 === $value ) {
						$seopress_titles['seopress_titles_archives_author_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_author_noindex'] );
					}
				}
				if ( 'title-author' === $key ) {
					$seopress_titles['seopress_titles_archives_author_title'] = esc_html( $value );
				}
				if ( 'metadesc-author' === $key ) {
					$seopress_titles['seopress_titles_archives_author_desc'] = esc_html( $value );
				}
				// Date.
				if ( 'enable-date-archive' === $key ) {
					if ( 1 === $value ) {
						unset( $seopress_titles['seopress_titles_archives_date_disable'] );
					} else {
						$seopress_titles['seopress_titles_archives_date_disable'] = '1';
					}
				}
				if ( 'meta_robots-noindex-date' === $key ) {
					if ( 1 === $value ) {
						$seopress_titles['seopress_titles_archives_date_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_date_noindex'] );
					}
				}
				if ( 'title-date' === $key ) {
					$seopress_titles['seopress_titles_archives_date_title'] = esc_html( $value );
				}
				if ( 'metadesc-date' === $key ) {
					$seopress_titles['seopress_titles_archives_date_desc'] = esc_html( $value );
				}
				// Search.
				if ( 'meta_robots-noindex-search' === $key ) {
					if ( 1 === $value ) {
						$seopress_titles['seopress_titles_archives_search_title_noindex'] = '1';
					} else {
						unset( $seopress_titles['seopress_titles_archives_search_title_noindex'] );
					}
				}
				if ( 'title-search' === $key ) {
					$seopress_titles['seopress_titles_archives_search_title'] = esc_html( $value );
				}
				if ( 'metadesc-search' === $key ) {
					$seopress_titles['seopress_titles_archives_search_desc'] = esc_html( $value );
				}
				// 404.
				if ( 'title-404' === $key ) {
					$seopress_titles['seopress_titles_archives_404_title'] = esc_html( $value );
				}
				if ( 'metadesc-404' === $key ) {
					$seopress_titles['seopress_titles_archives_404_desc'] = esc_html( $value );
				}
			}
		}

		// Import social.
		if ( ! empty( $wds_social_options ) ) {
			foreach ( $wds_social_options as $key => $value ) {
				// OG enable.
				if ( 'og-enable' === $key ) {
					if ( 1 === $value ) {
						$seopress_social['seopress_social_facebook_og'] = '1';
					} else {
						unset( $seopress_social['seopress_social_facebook_og'] );
					}
				}
				// Twitter enable.
				if ( 'twitter-enable' === $key ) {
					if ( 1 === $value ) {
						$seopress_social['seopress_social_twitter_card'] = '1';
					} else {
						unset( $seopress_social['seopress_social_twitter_card'] );
					}
				}
				// Pinterest verify.
				if ( 'pinterest-verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html( $value );
				}
				// Organization logo.
				if ( 'organization_logo' === $key ) {
					$seopress_social['seopress_social_knowledge_img'] = esc_url( $value );
				}
			}
		}

		// Import XML sitemap.
		if ( ! empty( $wds_sitemap_options ) ) {
			foreach ( $wds_sitemap_options as $key => $value ) {
				foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
					// Post Types Sitemap.
					if ( 'post_types-' . $seopress_cpt_key . 'not_in_sitemap' === $key ) {
						$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
					}
				}

				// News Sitemap.
				if ( 'enable-news-sitemap' === $key ) {
					if ( 1 === $value ) {
						$seopress_pro['seopress_news_enable'] = '1';
					} else {
						unset( $seopress_pro['seopress_news_enable'] );
					}
				}
				// News Publication Name.
				if ( 'news-publication-name' === $key ) {
					$seopress_pro['seopress_news_name'] = esc_html( $value );
				}

				// News Post Types.
				if ( 'news-sitemap-included-post-types' === $key && ! empty( $value ) ) {
					foreach ( $value as $seopress_cpt_key => $seopress_cpt_value ) {
						$seopress_pro['seopress_news_name_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
					}
				}

				// Image Sitemap.
				if ( 'sitemap-images' === $key ) {
					if ( 1 === $value ) {
						$seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] = '1';
					} else {
						unset( $seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] );
					}
				}
			}
		}

		// Schema.
		if ( ! empty( $wds_schema_options ) ) {
			foreach ( $wds_schema_options as $key => $value ) {
				// Twitter username.
				if ( 'twitter_username' === $key ) {
					$seopress_social['seopress_social_accounts_twitter'] = esc_html( $value );
				}
				// Facebook URL.
				if ( 'facebook_url' === $key ) {
					$seopress_social['seopress_social_accounts_facebook'] = esc_url( $value );
				}
				// Instagram URL.
				if ( 'instagram_url' === $key ) {
					$seopress_social['seopress_social_accounts_instagram'] = esc_url( $value );
				}
				// LinkedIn URL.
				if ( 'linkedin_url' === $key ) {
					$seopress_social['seopress_social_accounts_linkedin'] = esc_url( $value );
				}
				// Pinterest URL.
				if ( 'pinterest_url' === $key ) {
					$seopress_social['seopress_social_accounts_pinterest'] = esc_url( $value );
				}
				// YouTube URL.
				if ( 'youtube_url' === $key ) {
					$seopress_social['seopress_social_accounts_youtube'] = esc_url( $value );
				}
				// Facebook App ID.
				if ( 'fb-app-id' === $key ) {
					$seopress_social['seopress_social_facebook_app_id'] = esc_html( $value );
				}
				// Schema type.
				if ( 'schema_type' === $key ) {
					$seopress_social['seopress_social_knowledge_type'] = esc_html( $value );
				}
				// Organization name.
				if ( 'organization_name' === $key ) {
					$seopress_social['seopress_social_knowledge_name'] = esc_html( $value );
				}
				// Organization description.
				if ( 'organization_description' === $key ) {
					$seopress_social['seopress_social_knowledge_desc'] = esc_html( $value );
				}
				// Organization contact type.
				if ( 'organization_contact_type' === $key ) {
					$type = array(
						'customer support' => 'customer support',
						'technical support' => 'technical support',
						'billing support' => 'billing support',
						'bill payment' => 'bill payment',
						'sales' => 'sales',
						'credit card support' => 'credit card support',
						'emergency' => 'emergency',
						'baggage tracking' => 'baggage tracking',
						'roadside assistance' => 'roadside assistance',
						'package tracking' => 'package tracking',
					);
					$seopress_social['seopress_social_knowledge_contact_type'] = esc_html( $type[ $value ] );
				}
				// Organization phone.
				if ( 'organization_phone_number' === $key ) {
					$seopress_social['seopress_social_knowledge_phone'] = esc_html( $value );
				}
			}
		}

		// Import advanced.
		if ( ! empty( $wds_settings_options ) ) {
			foreach ( $wds_settings_options as $key => $value ) {
				// Google verification.
				if ( 'verification-google-meta' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_google'] = esc_html( $value );
				}
				// Bing verification.
				if ( 'verification-bing-meta' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_bing'] = esc_html( $value );
				}
				// WordPress generator.
				if ( 'general-suppress-generator' === $key ) {
					if ( 1 === $value ) {
						$seopress_advanced['seopress_advanced_advanced_wp_generator'] = '1';
					} else {
						unset( $seopress_advanced['seopress_advanced_advanced_wp_generator'] );
					}
				}
			}
		}

		update_option( 'seopress_titles_option_name', $seopress_titles );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_xml_sitemap );
		update_option( 'seopress_social_option_name', $seopress_social );
		update_option( 'seopress_advanced_option_name', $seopress_advanced );
		update_option( 'seopress_pro_option_name', $seopress_pro );

		if ( $offset > $total_count_posts ) {
			wp_reset_postdata();
			$count_items = $total_count_posts;

			$smart_crawl_query_terms = get_option( 'wds_taxonomy_meta' );

			if ( $smart_crawl_query_terms ) {
				foreach ( $smart_crawl_query_terms as $taxonomies => $taxonomie ) {
					foreach ( $taxonomie as $term_id => $term_value ) {
						if ( ! empty( $term_value['wds_title'] ) ) { // Import title tag.
							update_term_meta( $term_id, '_seopress_titles_title', esc_html( $term_value['wds_title'] ) );
						}
						if ( ! empty( $term_value['wds_desc'] ) ) { // Import meta desc.
							update_term_meta( $term_id, '_seopress_titles_desc', esc_html( $term_value['wds_desc'] ) );
						}
						if ( ! empty( $term_value['opengraph']['title'] ) ) { // Import Facebook Title.
							update_term_meta( $term_id, '_seopress_social_fb_title', esc_html( $term_value['opengraph']['title'] ) );
						}
						if ( ! empty( $term_value['opengraph']['description'] ) ) { // Import Facebook Desc.
							update_term_meta( $term_id, '_seopress_social_fb_desc', esc_html( $term_value['opengraph']['description'] ) );
						}
						if ( ! empty( $term_value['opengraph']['images'] ) ) { // Import Facebook Image.
							$image_id = $term_value['opengraph']['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_term_meta( $term_id, '_seopress_social_fb_img', esc_url( $img_url ) );
							}
						}
						if ( ! empty( $term_value['twitter']['title'] ) ) { // Import Facebook Title.
							update_term_meta( $term_id, '_seopress_social_twitter_title', esc_html( $term_value['twitter']['title'] ) );
						}
						if ( ! empty( $term_value['twitter']['description'] ) ) { // Import Facebook Desc.
							update_term_meta( $term_id, '_seopress_social_twitter_desc', esc_html( $term_value['twitter']['description'] ) );
						}
						if ( ! empty( $term_value['twitter']['images'] ) ) { // Import Facebook Image.
							$image_id = $term_value['twitter']['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_term_meta( $term_id, '_seopress_social_twitter_img', esc_url( $img_url ) );
							}
						}
						if ( ! empty( $term_value['wds_noindex'] ) && 'noindex' === $term_value['wds_noindex'] ) { // Import Robots NoIndex.
							update_term_meta( $term_id, '_seopress_robots_index', 'yes' );
						}
						if ( ! empty( $term_value['wds_nofollow'] ) && 'nofollow' === $term_value['wds_nofollow'] ) { // Import Robots NoFollow.
							update_term_meta( $term_id, '_seopress_robots_follow', 'yes' );
						}
						if ( '' !== $term_value['wds_canonical'] ) { // Import Canonical URL.
							update_term_meta( $term_id, '_seopress_robots_canonical', esc_url( $term_value['wds_canonical'] ) );
						}
					}
				}
			}
			$offset = 'done';
			wp_reset_postdata();
		} else {
			$args = array(
				'posts_per_page' => $increment,
				'post_type'      => 'any',
				'post_status'    => 'any',
				'offset'         => $offset,
			);

			$smart_crawl_query = get_posts( $args );

			if ( $smart_crawl_query ) {
				foreach ( $smart_crawl_query as $post ) {
					if ( '' !== get_post_meta( $post->ID, '_wds_title', true ) ) { // Import title tag.
						update_post_meta( $post->ID, '_seopress_titles_title', esc_html( get_post_meta( $post->ID, '_wds_title', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_metadesc', true ) ) { // Import meta desc.
						update_post_meta( $post->ID, '_seopress_titles_desc', esc_html( get_post_meta( $post->ID, '_wds_metadesc', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_opengraph', true ) ) {
						$_wds_opengraph = get_post_meta( $post->ID, '_wds_opengraph', true );
						if ( ! empty( $_wds_opengraph['title'] ) ) {
							update_post_meta( $post->ID, '_seopress_social_fb_title', esc_html( $_wds_opengraph['title'] ) ); // Import Facebook Title.
						}
						if ( ! empty( $_wds_opengraph['description'] ) ) { // Import Facebook Desc.
							update_post_meta( $post->ID, '_seopress_social_fb_desc', esc_html( $_wds_opengraph['description'] ) );
						}
						if ( ! empty( $_wds_opengraph['images'] ) ) { // Import Facebook Image.
							$image_id = $_wds_opengraph['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_post_meta( $post->ID, '_seopress_social_fb_img', esc_url( $img_url ) );
							}
						}
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_twitter', true ) ) { // Import Twitter Title.
						$_wds_twitter = get_post_meta( $post->ID, '_wds_twitter', true );
						if ( ! empty( $_wds_twitter['title'] ) ) {
							update_post_meta( $post->ID, '_seopress_social_twitter_title', esc_html( $_wds_twitter['title'] ) ); // Import Twitter Title.
						}
						if ( ! empty( $_wds_twitter['description'] ) ) { // Import Twitter Desc.
							update_post_meta( $post->ID, '_seopress_social_twitter_desc', esc_html( $_wds_twitter['description'] ) );
						}
						if ( ! empty( $_wds_twitter['images'] ) ) { // Import Twitter Image.
							$image_id = $_wds_twitter['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_post_meta( $post->ID, '_seopress_social_twitter_img', esc_url( $img_url ) );
							}
						}
					}
					if ( '1' === get_post_meta( $post->ID, '_wds_meta-robots-noindex', true ) ) { // Import Robots NoIndex.
						update_post_meta( $post->ID, '_seopress_robots_index', 'yes' );
					}
					if ( '1' === get_post_meta( $post->ID, '_wds_meta-robots-nofollow', true ) ) { // Import Robots NoIndex.
						update_post_meta( $post->ID, '_seopress_robots_follow', 'yes' );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_meta-robots-adv', true ) ) {
						$robots = get_post_meta( $post->ID, '_wds_meta-robots-adv', true );
						if ( '' !== $robots ) {
							$robots = explode( ',', $robots );

							if ( in_array( 'nosnippet', $robots, true ) ) { // Import Robots NoSnippet.
								update_post_meta( $post->ID, '_seopress_robots_snippet', 'yes' );
							}
						}
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_canonical', true ) ) { // Import Canonical URL.
						update_post_meta( $post->ID, '_seopress_robots_canonical', esc_url( get_post_meta( $post->ID, '_wds_canonical', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_redirect', true ) ) { // Import Redirect URL.
						update_post_meta( $post->ID, '_seopress_redirections_enabled', 'yes' );
						update_post_meta( $post->ID, '_seopress_redirections_type', '301' );
						update_post_meta( $post->ID, '_seopress_redirections_value', esc_url( get_post_meta( $post->ID, '_wds_redirect', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_focus-keywords', true ) ) { // Import Focus Keywords.
						update_post_meta( $post->ID, '_seopress_analysis_target_kw', esc_html( get_post_meta( $post->ID, '_wds_focus-keywords', true ) ) );
					}
				}
			}
			$offset += $increment;

			if ( $offset >= $total_count_posts ) {
				$count_items = $total_count_posts;
			} else {
				$count_items = $offset;
			}
		}
		$data = array();

		$data['count'] = $count_items;
		$data['total'] = $total_count_posts + $total_count_terms;

		$data['offset'] = $offset;
		wp_send_json_success( $data );
		exit();
	}
}
add_action( 'wp_ajax_seopress_smart_crawl_migration', 'seopress_smart_crawl_migration' );

<?php

namespace SEOPress\Actions\Admin\Importer;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\AIO\Tags;

/**
 * AIO importer
 */
class AIO implements ExecuteHooksBackend {

	/**
	 * The AIO tags
	 *
	 * @var Tags
	 */
	protected $tags_aio;

	/**
	 * The AIO constructor
	 */
	public function __construct() {
		$this->tags_aio = new Tags();
	}

	/**
	 * The AIO hooks
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_seopress_aio_migration', array( $this, 'process' ) );
	}

	/**
	 * Migrate the post query
	 *
	 * @since 4.3.0
	 *
	 * @param int $offset    The offset.
	 * @param int $increment The increment.
	 *
	 * @return int The offset
	 */
	protected function migratePostQuery( $offset, $increment ) {
		global $wpdb;
		$args = array(
			'posts_per_page' => $increment,
			'post_type'      => 'any',
			'post_status'    => 'any',
			'offset'         => $offset,
		);

		$aio_query = get_posts( $args );

		if ( ! $aio_query ) {
			$offset += $increment;

			return $offset;
		}

		$get_post_metas = array(
			'_seopress_titles_title'         => '_aioseo_title',
			'_seopress_titles_desc'          => '_aioseo_description',
			'_seopress_social_fb_title'      => '_aioseo_og_title',
			'_seopress_social_fb_desc'       => '_aioseo_og_description',
			'_seopress_social_twitter_title' => '_aioseo_twitter_title',
			'_seopress_social_twitter_desc'  => '_aioseo_twitter_description',
		);

		foreach ( $aio_query as $post ) {
			foreach ( $get_post_metas as $key => $value ) {
				$meta_aio = get_post_meta( $post->ID, $value, true );
				if ( ! empty( $meta_aio ) ) {
					update_post_meta( $post->ID, $key, esc_html( $this->tags_aio->replaceTags( $meta_aio ) ) );
				}
			}

			// Canonical URL.
			$canonical_url = "SELECT p.canonical_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$canonical_url = $wpdb->get_results( $canonical_url, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $canonical_url[0]['canonical_url'] ) ) { // Import Canonical URL.
				update_post_meta( $post->ID, '_seopress_robots_canonical', esc_url( $canonical_url[0]['canonical_url'] ) );
			}

			// OG Image.
			$og_img_url = "SELECT p.og_image_custom_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.og_image_type = 'custom_image' AND p.post_id = $post->ID";

			$og_img_url = $wpdb->get_results( $og_img_url, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $og_img_url[0]['og_image_custom_url'] ) ) { // Import Facebook Image.
				update_post_meta( $post->ID, '_seopress_social_fb_img', esc_url( $og_img_url[0]['og_image_custom_url'] ) );
			} elseif ( '' !== get_post_meta( $post->ID, '_aioseop_opengraph_settings', true ) ) { // Import old Facebook Image.
				$_aioseop_opengraph_settings = get_post_meta( $post->ID, '_aioseop_opengraph_settings', true );
				if ( isset( $_aioseop_opengraph_settings['aioseop_opengraph_settings_image'] ) ) {
					update_post_meta( $post->ID, '_seopress_social_fb_img', esc_url( $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg'] ) );
				}
			}

			// Twitter Image.
			$tw_img_url = "SELECT p.twitter_image_custom_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.twitter_image_type = 'custom_image' AND p.post_id = $post->ID";

			$tw_img_url = $wpdb->get_results( $tw_img_url, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $tw_img_url[0]['twitter_image_custom_url'] ) ) { // Import Twitter Image.
				update_post_meta( $post->ID, '_seopress_social_twitter_img', esc_url( $tw_img_url[0]['twitter_image_custom_url'] ) );
			} elseif ( '' !== get_post_meta( $post->ID, '_aioseop_opengraph_settings', true ) ) { // Import old Twitter Image.
				$_aioseop_opengraph_settings = get_post_meta( $post->ID, '_aioseop_opengraph_settings', true );
				if ( isset( $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'] ) ) {
					update_post_meta( $post->ID, '_seopress_social_twitter_img', esc_url( $_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'] ) );
				}
			}

			// Meta robots "noindex".
			$robots_noindex = "SELECT p.robots_noindex, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_noindex = $wpdb->get_results( $robots_noindex, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $robots_noindex[0]['robots_noindex'] ) && '1' === $robots_noindex[0]['robots_noindex'] ) { // Import Robots NoIndex.
				update_post_meta( $post->ID, '_seopress_robots_index', 'yes' );
			} elseif ( 'on' === get_post_meta( $post->ID, '_aioseop_noindex', true ) ) { // Import old Robots NoIndex.
				update_post_meta( $post->ID, '_seopress_robots_index', 'yes' );
			}

			// Meta robots "nofollow".
			$robots_nofollow = "SELECT p.robots_nofollow, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_nofollow = $wpdb->get_results( $robots_nofollow, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $robots_nofollow[0]['robots_nofollow'] ) && '1' === $robots_nofollow[0]['robots_nofollow'] ) { // Import Robots NoFollow.
				update_post_meta( $post->ID, '_seopress_robots_follow', 'yes' );
			} elseif ( 'on' === get_post_meta( $post->ID, '_aioseop_nofollow', true ) ) { // Import old Robots NoFollow.
				update_post_meta( $post->ID, '_seopress_robots_follow', 'yes' );
			}

			// Meta robots "noimageindex".
			$robots_noimageindex = "SELECT p.robots_noimageindex, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_noimageindex = $wpdb->get_results( $robots_noimageindex, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $robots_noimageindex[0]['robots_noimageindex'] ) && '1' === $robots_noimageindex[0]['robots_noimageindex'] ) { // Import Robots NoImageIndex.
				update_post_meta( $post->ID, '_seopress_robots_imageindex', 'yes' );
			}

			// Meta robots "nosnippet".
			$robots_nosnippet = "SELECT p.robots_nosnippet, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_nosnippet = $wpdb->get_results( $robots_nosnippet, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $robots_nosnippet[0]['robots_nosnippet'] ) && '1' === $robots_nosnippet[0]['robots_nosnippet'] ) { // Import Robots NoSnippet.
				update_post_meta( $post->ID, '_seopress_robots_snippet', 'yes' );
			}

			// Target keywords.
			$keyphrases = "SELECT p.keyphrases, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$keyphrases = $wpdb->get_results( $keyphrases, ARRAY_A ); // phpcs:ignore

			if ( ! empty( $keyphrases ) && isset( $keyphrases[0]['keyphrases'] ) ) {
				$keyphrases = json_decode( $keyphrases[0]['keyphrases'] );

				if ( isset( $keyphrases->focus->keyphrase ) ) {
					$keyphrases = $keyphrases->focus->keyphrase;

					if ( '' !== $keyphrases ) { // Import focus kw.
						update_post_meta( $post->ID, '_seopress_analysis_target_kw', esc_html( $keyphrases ) );
					}
				}
			}
		}

		$offset += $increment;

		return $offset;
	}

	/**
	 * Migrate the settings
	 *
	 * @since 4.3.0
	 *
	 * @return void
	 */
	protected function migrateSettings() {
		$seopress_titles           = get_option( 'seopress_titles_option_name' );
		$seopress_social           = get_option( 'seopress_social_option_name' );
		$seopress_xml_sitemap      = get_option( 'seopress_xml_sitemap_option_name' );
		$seopress_google_analytics = get_option( 'seopress_google_analytics_option_name' );
		$seopress_advanced         = get_option( 'seopress_advanced_option_name' );
		$seopress_pro              = get_option( 'seopress_pro_option_name' );

		$aioseo_options     = get_option( 'aioseo_options' );
		$aioseo_options_pro = get_option( 'aioseo_options_pro' );
		$aioseo_options     = json_decode( $aioseo_options, true );
		$aioseo_options_pro = json_decode( $aioseo_options_pro, true );

		$aioseo_options_dynamic = get_option( 'aioseo_options_dynamic' );
		$aioseo_options_dynamic = json_decode( $aioseo_options_dynamic, true );

		foreach ( $aioseo_options as $key => $value ) {
			// Sitemap.
			if ( 'sitemap' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'general' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							// Enable Sitemap.
							if ( 'enable' === $__key && true === $__value ) {
								$seopress_xml_sitemap['seopress_xml_sitemap_general_enable'] = '1';
							} elseif ( 'enable' === $__key && false === $__value ) {
								unset( $seopress_xml_sitemap['seopress_xml_sitemap_general_enable'] );
							}
							// Post Types Sitemap.
							if ( 'postTypes' === $__key && ! empty( $__value ) ) {
								// Clear existing CPT keys first.
								if ( isset( $seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'] ) ) {
									foreach ( $seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'] as $cpt_key => $cpt_value ) {
										unset( $seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $cpt_key ]['include'] );
									}
								}

								foreach ( $__value as $___key => $___value ) {
									if ( 'all' === $___key && true === $___value ) {
										$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
										foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
											$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
										}
									} elseif ( 'included' === $___key ) {
										foreach ( $___value as $____key => $____value ) {
											$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $____value ]['include'] = '1';
										}
									}
								}
							}
							// Taxonomies Sitemap.
							if ( 'taxonomies' === $__key && ! empty( $__value ) ) {
								// Clear existing CPT keys first.
								if ( isset( $seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'] ) ) {
									foreach ( $seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'] as $tax_key => $tax_value ) {
										unset( $seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][ $tax_key ]['include'] );
									}
								}

								foreach ( $__value as $___key => $___value ) {
									if ( 'all' === $___key && true === $___value ) {
										$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();
										foreach ( $taxonomies as $seopress_tax_key => $seopress_tax_value ) {
											$seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][ $seopress_tax_key ]['include'] = '1';
										}
									} elseif ( 'included' === $___key ) {
										foreach ( $___value as $____key => $____value ) {
											$seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][ $____value ]['include'] = '1';
										}
									}
								}
							}
							// Author Sitemap.
							if ( 'author' === $__key && true === $__value ) {
								$seopress_xml_sitemap['seopress_xml_sitemap_author_enable'] = '1';
							} elseif ( 'author' === $__key && false === $__value ) {
								unset( $seopress_xml_sitemap['seopress_xml_sitemap_author_enable'] );
							}
							if ( 'advancedSettings' === $__key ) {
								foreach ( $__value as $___key => $___value ) {
									// Image Sitemap.
									if ( 'excludeImages' === $___key && false === $___value ) {
										$seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] = '1';
									} elseif ( 'excludeImages' === $___key && true === $___value ) {
										unset( $seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] );
									}
								}
							}
						}
					}
					// HTML Sitemap.
					if ( 'html' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							// Enable HTML Sitemap.
							if ( 'enable' === $__key && true === $__value ) {
								$seopress_xml_sitemap['seopress_xml_sitemap_html_enable'] = '1';
							} elseif ( 'enable' === $__key && false === $__value ) {
								unset( $seopress_xml_sitemap['seopress_xml_sitemap_html_enable'] );
							}
							// Publication date.
							if ( 'publicationDate' === $__key && true === $__value ) {
								unset( $seopress_xml_sitemap['seopress_xml_sitemap_html_date'] );
							} elseif ( 'publicationDate' === $__key && false === $__value ) {
								$seopress_xml_sitemap['seopress_xml_sitemap_html_date'] = '1';
							}
							// Sort order.
							if ( 'sortOrder' === $__key && ! empty( $__value ) ) {
								$sort = array(
									'publish_date' => 'date',
									'last_updated' => 'modified',
									'alphabetical' => 'title',
									'id'           => 'ID',
								);
								$seopress_xml_sitemap['seopress_xml_sitemap_html_orderby'] = esc_html( $sort[ $__value ] );
							}
							// Sort direction.
							if ( 'sortDirection' === $__key && ! empty( $__value ) ) {
								$sort = array(
									'asc'  => 'ASC',
									'desc' => 'DESC',
								);
								$seopress_xml_sitemap['seopress_xml_sitemap_html_order'] = esc_html( $sort[ $__value ] );
							}
							if ( 'advancedSettings' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'enable' === $___key && false === $___value ) {
										break;
									} elseif ( ( 'excludePosts' === $___key || 'excludeTerms' === $___key ) && ! empty( $___value ) ) {
										if ( ! isset( $seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] ) ) {
											$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = '';
										}
											$exclude_ids = array();
										foreach ( $___value as $exclude_item ) {
											$decoded = json_decode( $exclude_item, true );
											if ( $decoded && isset( $decoded['value'] ) ) {
												$exclude_ids[] = $decoded['value'];
											}
										}
										if ( ! empty( $exclude_ids ) ) {
											$current_exclude = $seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'];
											$new_exclude     = implode( ',', $exclude_ids );
											if ( ! empty( $current_exclude ) ) {
												$combined_ids = array_unique( array_merge( explode( ',', $current_exclude ), $exclude_ids ) );
												$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = esc_html( implode( ',', $combined_ids ) );
											} else {
												$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = esc_html( $new_exclude );
											}
										}
									}
								}
							}
						}
					}
				}
			}

			// Social.
			if ( 'social' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'profiles' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							if ( 'urls' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									// Facebook URL.
									if ( 'facebookPageUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_facebook'] = esc_url( $___value );
									}
									// Twitter URL.
									if ( 'twitterUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_twitter'] = esc_url( $___value );
									}
									// Instagram URL.
									if ( 'instagramUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_instagram'] = esc_url( $___value );
									}
									// LinkedIn URL.
									if ( 'linkedinUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_linkedin'] = esc_url( $___value );
									}
									// Pinterest URL.
									if ( 'pinterestUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_pinterest'] = esc_url( $___value );
									}
									// YouTube URL.
									if ( 'youtubeUrl' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_accounts_youtube'] = esc_url( $___value );
									}
								}
							}
							// Extra accounts.
							if ( 'additionalUrls' === $__key && ! empty( $__value ) ) {
								$accounts = array_filter( explode( "\n", $__value ) );
								$accounts = implode( "\n", array_map( 'esc_url', $accounts ) );
								$seopress_social['seopress_social_accounts_extra'] = esc_html( $accounts );
							}
						}
					}
					// Facebook.
					if ( 'facebook' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							// General.
							if ( 'general' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									// Enable OG.
									if ( 'enable' === $___key ) {
										if ( true === $___value ) {
											$seopress_social['seopress_social_facebook_og'] = '1';
										} else {
											unset( $seopress_social['seopress_social_facebook_og'] );
										}
									}
									// Default image.
									if ( 'defaultImagePosts' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_facebook_img'] = esc_url( $___value );
									}
									// Image size.
									if ( 'defaultImagePostsWidth' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_facebook_img_width'] = esc_html( $___value );
									}
									// Image width.
									if ( 'defaultImagePostsHeight' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_facebook_img_height'] = esc_html( $___value );
									}
								}
							}
							// Advanced.
							if ( 'advanced' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									// Admin ID.
									if ( 'adminId' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_facebook_admin_id'] = esc_html( $___value );
									}
									// App ID.
									if ( 'appId' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_facebook_app_id'] = esc_html( $___value );
									}
								}
							}
						}
					}
					// X.
					if ( 'twitter' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							// General.
							if ( 'general' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									// Enable X Cards.
									if ( 'enable' === $___key ) {
										if ( true === $___value ) {
											$seopress_social['seopress_social_twitter_card'] = '1';
										} else {
											unset( $seopress_social['seopress_social_twitter_card'] );
										}
									}
									// Use OG if no X Cards.
									if ( 'useOgData' === $___key ) {
										if ( true === $___value ) {
											$seopress_social['seopress_social_twitter_card_og'] = '1';
										} else {
											unset( $seopress_social['seopress_social_twitter_card_og'] );
										}
									}
									// Default image.
									if ( 'defaultImagePosts' === $___key && ! empty( $___value ) ) {
										$seopress_social['seopress_social_twitter_card_img'] = esc_url( $___value );
									}
									// Image size.
									if ( 'defaultCardType' === $___key && ! empty( $___value ) ) {
										$type = array(
											'summary_large_image' => 'large',
											'summary' => 'default',
										);
										$seopress_social['seopress_social_twitter_card_img_size'] = esc_html( $type[ $___value ] );
									}
								}
							}
						}
					}
				}
			}
			// Titles & metas.
			if ( 'searchAppearance' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'global' === $_key && ! empty( $_value ) ) {
						// Global title.
						foreach ( $_value as $__key => $__value ) {
							// Title separator.
							if ( 'separator' === $__key ) {
								$seopress_titles['seopress_titles_sep'] = esc_html( $this->tags_aio->replaceTags( $__value ) );
							}
							// Site title.
							if ( 'siteTitle' === $__key ) {
								$seopress_titles['seopress_titles_home_site_title'] = esc_html( $this->tags_aio->replaceTags( $__value ) );
							}
							// Site description.
							if ( 'metaDescription' === $__key ) {
								$seopress_titles['seopress_titles_home_site_desc'] = esc_html( $this->tags_aio->replaceTags( $__value ) );
							}

							// Website Schema.
							if ( 'schema' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'websiteName' === $___key ) {
										$seopress_social['seopress_social_knowledge_name'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									if ( 'websiteAlternateName' === $___key ) {
										$seopress_titles['seopress_titles_home_site_title_alt'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									if ( 'siteRepresents' === $___key ) {
										$type = array(
											'organization' => 'Organization',
											'person'       => 'Person',
										);
										$seopress_social['seopress_social_knowledge_type'] = esc_html( $type[ $___value ] );
									}
									if ( 'organizationDescription' === $___key ) {
										$seopress_social['seopress_social_knowledge_desc'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									if ( 'organizationLogo' === $___key ) {
										$seopress_social['seopress_social_knowledge_img'] = esc_url( $___value );
									}
									if ( 'contactType' === $___key ) {
										$type = array(
											'Customer Support' => 'customer support',
											'Technical Support' => 'technical support',
											'Billing Support' => 'billing support',
											'Sales' => 'sales',
										);
										$seopress_social['seopress_social_knowledge_contact_type'] = esc_html( $type[ $___value ] );
									}
								}
							}
						}
					}
					// Archives.
					if ( 'archives' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							// Author archive title.
							if ( 'author' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_archives_author_title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Author archive description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_archives_author_desc'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Noindex.
									if ( 'show' === $___key ) {
										if ( false === $___value ) {
											$seopress_titles['seopress_titles_archives_author_noindex'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_archives_author_noindex'] );
										}
									}
								}
							}
							// Date archive title.
							if ( 'date' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_archives_date_title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Author archive description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_archives_date_desc'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Noindex.
									if ( 'show' === $___key ) {
										if ( false === $___value ) {
											$seopress_titles['seopress_titles_archives_date_noindex'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_archives_date_noindex'] );
										}
									}
								}
							}
							// Search archive title.
							if ( 'search' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_archives_search_title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Author archive description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_archives_search_desc'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Noindex.
									if ( 'show' === $___key ) {
										if ( false === $___value ) {
											$seopress_titles['seopress_titles_archives_search_title_noindex'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_archives_search_title_noindex'] );
										}
									}
								}
							}
						}
					}
					// Advanced.
					if ( 'advanced' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							if ( 'globalRobotsMeta' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'default' === $___key && true === $___value ) {
										unset( $seopress_titles['seopress_titles_noindex'] );
										unset( $seopress_titles['seopress_titles_nosnippet'] );
										unset( $seopress_titles['seopress_titles_noimageindex'] );
										unset( $seopress_titles['seopress_titles_nofollow'] );
										break;
									}
									if ( 'noindex' === $___key ) {
										if ( true === $___value ) {
											$seopress_titles['seopress_titles_noindex'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_noindex'] );
										}
									}
									if ( 'nosnippet' === $___key ) {
										if ( true === $___value ) {
											$seopress_titles['seopress_titles_nosnippet'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_nosnippet'] );
										}
									}
									if ( 'noimageindex' === $___key ) {
										if ( true === $___value ) {
											$seopress_titles['seopress_titles_noimageindex'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_noimageindex'] );
										}
									}
									if ( 'nofollow' === $___key ) {
										if ( true === $___value ) {
											$seopress_titles['seopress_titles_nofollow'] = '1';
										} else {
											unset( $seopress_titles['seopress_titles_nofollow'] );
										}
									}
								}
							}
							if ( 'crawlCleanup' === $__key && ! empty( $__value ) ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'feeds' === $___key && ! empty( $___value ) ) {
										foreach ( $___value as $____key => $____value ) {
											if ( 'globalComments' === $____key && true === $____value ) {
												unset( $seopress_pro['seopress_rss_disable_comments_feed'] );
											} elseif ( 'globalComments' === $____key && false === $____value ) {
												$seopress_pro['seopress_rss_disable_comments_feed'] = '1';
											}
											if ( 'staticBlogPage' === $____key && true === $____value ) {
												unset( $seopress_pro['seopress_rss_disable_posts_feed'] );
											} elseif ( 'staticBlogPage' === $____key && false === $____value ) {
												$seopress_pro['seopress_rss_disable_posts_feed'] = '1';
											}
											if ( ( 'authors' === $____key || 'postComments' === $____key ) && true === $____value ) {
												unset( $seopress_pro['seopress_rss_disable_extra_feed'] );
											} elseif ( ( 'authors' === $____key || 'postComments' === $____key ) && false === $____value ) {
												$seopress_pro['seopress_rss_disable_extra_feed'] = '1';
											}
										}
									}
								}
							}
						}
					}
				}
			}
			// Webmaster Tools.
			if ( 'webmasterTools' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'google' === $_key ) {
						$seopress_advanced['seopress_advanced_advanced_google'] = esc_html( $_value );
					} elseif ( 'bing' === $_key ) {
						$seopress_advanced['seopress_advanced_advanced_bing'] = esc_html( $_value );
					} elseif ( 'yandex' === $_key ) {
						$seopress_advanced['seopress_advanced_advanced_yandex'] = esc_html( $_value );
					} elseif ( 'baidu' === $_key ) {
						$seopress_advanced['seopress_advanced_advanced_baidu'] = esc_html( $_value );
					} elseif ( 'pinterest' === $_key ) {
						$seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html( $_value );
					} elseif ( 'microsoftClarityProjectId' === $_key ) {
						$seopress_google_analytics['seopress_google_analytics_clarity_project_id'] = esc_html( $_value );
					}
				}
			}
			// Breadcrumbs.
			if ( 'breadcrumbs' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					// Enable Breadcrumbs.
					$seopress_pro['seopress_breadcrumbs_enable']      = '1';
					$seopress_pro['seopress_breadcrumbs_json_enable'] = '1';

					// Breadcrumbs Separator.
					if ( 'separator' === $_key ) {
						$seopress_pro['seopress_breadcrumbs_separator'] = esc_html( $_value );
					}
					// Breadcrumbs Home.
					if ( 'homepageLabel' === $_key ) {
						$seopress_pro['seopress_breadcrumbs_i18n_home'] = esc_html( $_value );
					}
					// Breadcrumbs Prefix.
					if ( 'breadcrumbPrefix' === $_key ) {
						$seopress_pro['seopress_breadcrumbs_i18n_here'] = esc_html( $_value );
					}
					// Breadcrumbs Search Prefix.
					if ( 'searchResultFormat' === $_key ) {
						$seopress_pro['seopress_breadcrumbs_i18n_search'] = esc_html( $_value );
					}
					// Breadcrumbs 404 Crumbs.
					if ( 'errorFormat404' === $_key ) {
						$seopress_pro['seopress_breadcrumbs_i18n_404'] = esc_html( $_value );
					}
					// Breadcrumbs Display Blog Page.
					if ( 'showBlogHome' === $_key ) {
						if ( true === $_value ) {
							unset( $seopress_pro['seopress_breadcrumbs_remove_blog_page'] );
						} elseif ( false === $_value ) {
							$seopress_pro['seopress_breadcrumbs_remove_blog_page'] = '1';
						}
					}
				}
			}
			// RSS Feed.
			if ( 'rssContent' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'before' === $_key ) {
						$args                                     = array(
							'strong' => array(),
							'em'     => array(),
							'br'     => array(),
							'a'      => array(
								'href' => array(),
								'rel'  => array(),
							),
						);
						$seopress_pro['seopress_rss_before_html'] = wp_kses( $this->tags_aio->replaceTags( $_value ), $args );
					} elseif ( 'after' === $_key ) {
						$args                                    = array(
							'strong' => array(),
							'em'     => array(),
							'br'     => array(),
							'a'      => array(
								'href' => array(),
								'rel'  => array(),
							),
						);
						$seopress_pro['seopress_rss_after_html'] = wp_kses( $this->tags_aio->replaceTags( $_value ), $args );
					}
				}
			}
			// robots.txt file content.
			if ( 'tools' === $key && ! empty( $value ) ) {
				foreach ( $value as $_key => $_value ) {
					if ( 'robots' === $_key && ! empty( $_value ) ) {
						foreach ( $_value as $__key => $__value ) {
							if ( 'enable' === $__key && true === $__value ) {
								$seopress_pro['seopress_robots_enable'] = '1';
							}
							if ( 'rules' === $__key && ! empty( $__value ) ) {
								$txt   = '';
								$rules = array();
								foreach ( $__value as $rule_json ) {
									$rule_data = json_decode( $rule_json, true );
									if ( $rule_data && isset( $rule_data['directive'] ) ) {
										$user_agent  = isset( $rule_data['userAgent'] ) && null !== $rule_data['userAgent'] ? $rule_data['userAgent'] : '*';
										$field_value = isset( $rule_data['fieldValue'] ) && null !== $rule_data['fieldValue'] ? $rule_data['fieldValue'] : '';

										if ( 'clean-param' === $rule_data['directive'] ) {
											// clean-param directive format: Clean-param: param1&param2.
											$rules[] = 'User-agent: ' . $user_agent . "\nClean-param: " . $field_value;
										} else {
											// Standard allow/disallow format: User-agent: directive path.
											$directive = ucfirst( $rule_data['directive'] );
											$rules[]   = 'User-agent: ' . $user_agent . "\n" . $directive . ': ' . $field_value;
										}
									}
								}
								$txt                                  = implode( "\n\n", $rules );
								$seopress_pro['seopress_robots_file'] = esc_html( $txt );
							}
						}
					}
				}
			}
		}

		if ( ! empty( $aioseo_options_pro ) ) {
			foreach ( $aioseo_options_pro as $key => $value ) {
				// Advanced.
				if ( 'advanced' === $key && ! empty( $value ) ) {
					foreach ( $value as $_key => $_value ) {
						// OpenAI API Key.
						if ( 'openAiKey' === $_key ) {
							$seopress_pro['seopress_ai_openai_api_key'] = esc_html( $_value );
						}
					}
				}
				// Local Business.
				if ( 'localBusiness' === $key && ! empty( $value ) ) {
					foreach ( $value as $_key => $_value ) {
						if ( 'locations' === $_key && ! empty( $_value ) ) {
							foreach ( $_value as $__key => $__value ) {
								if ( 'business' === $__key && ! empty( $__value ) ) {
									foreach ( $__value as $___key => $___value ) {
										// Local Business Type.
										if ( 'businessType' === $___key && ! empty( $___value ) ) {
											$seopress_pro['seopress_local_business_type'] = esc_html( $___value );
										}
										// Local Business Street Address.
										if ( 'address' === $___key && ! empty( $___value ) ) {
											$street_address = '';
											foreach ( $___value as $____key => $____value ) {
												if ( 'streetLine1' === $____key && ! empty( $____value ) ) {
													$street_address = $____value;
												}
												if ( 'streetLine2' === $____key && ! empty( $____value ) ) {
													$street_address .= ', ' . $____value;
												}
												// Local Business City.
												if ( 'city' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_address_locality'] = esc_html( $____value );
												}
												// Local Business State.
												if ( 'state' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_address_region'] = esc_html( $____value );
												}
												// Local Business Postal Code.
												if ( 'zipCode' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_postal_code'] = esc_html( $____value );
												}
												// Local Business Country.
												if ( 'country' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_address_country'] = esc_html( $____value );
												}
											}
											$seopress_pro['seopress_local_business_street_address'] = esc_html( $street_address );
										}
										// Local Business URL.
										if ( 'urls' === $___key && ! empty( $___value ) ) {
											foreach ( $___value as $____key => $____value ) {
												if ( 'website' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_url'] = esc_url( $____value );
												}
											}
										}
										// Local Business Phone.
										if ( 'contact' === $___key && ! empty( $___value ) ) {
											foreach ( $___value as $____key => $____value ) {
												if ( 'phone' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_phone'] = esc_html( $____value );
												}
											}
										}
										// Local Business Price Range.
										if ( 'payment' === $___key && ! empty( $___value ) ) {
											foreach ( $___value as $____key => $____value ) {
												if ( 'priceRange' === $____key && ! empty( $____value ) ) {
													$seopress_pro['seopress_local_business_price_range'] = esc_html( $____value );
												}
											}
										}
									}
								}
							}
						}
					}
				}

				// Sitemap.
				if ( 'sitemap' === $key && ! empty( $value ) ) {
					foreach ( $value as $_key => $_value ) {
						// Video.
						if ( 'video' === $_key && ! empty( $_value ) ) {
							foreach ( $_value as $__key => $__value ) {
								if ( 'enable' === $__key && true === $__value ) {
									$seopress_xml_sitemap['seopress_xml_sitemap_video_enable'] = '1';
								} elseif ( 'enable' === $__key && false === $__value ) {
									unset( $seopress_xml_sitemap['seopress_xml_sitemap_video_enable'] );
								}
							}
						}
						// News.
						if ( 'news' === $_key && ! empty( $_value ) ) {
							foreach ( $_value as $__key => $__value ) {
								if ( 'enable' === $__key && true === $__value ) {
									$seopress_pro['seopress_news_enable'] = '1';
								} elseif ( 'enable' === $__key && false === $__value ) {
									unset( $seopress_pro['seopress_news_enable'] );
								}
								// Publication name.
								if ( 'publicationName' === $__key ) {
									$seopress_pro['seopress_news_name'] = esc_html( $__value );
								} elseif ( 'publicationLanguage' === $__key ) {
									unset( $seopress_pro['seopress_news_name'] );
								}
								// Post Types Sitemap.
								if ( 'postTypes' === $__key && ! empty( $__value ) ) {
									// Clear existing CPT keys first.
									if ( isset( $seopress_pro['seopress_news_name_post_types_list'] ) ) {
										foreach ( $seopress_pro['seopress_news_name_post_types_list'] as $cpt_key => $cpt_value ) {
											unset( $seopress_pro['seopress_news_name_post_types_list'][ $cpt_key ]['include'] );
										}
									}

									foreach ( $__value as $___key => $___value ) {
										if ( 'all' === $___key && true === $___value ) {
											$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
											foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
												$seopress_pro['seopress_news_name_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
											}
										} elseif ( 'included' === $___key ) {
											foreach ( $___value as $____key => $____value ) {
												$seopress_pro['seopress_news_name_post_types_list'][ $____value ]['include'] = '1';
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ( ! empty( $aioseo_options_dynamic ) ) {
			foreach ( $aioseo_options_dynamic as $key => $value ) {
				if ( 'searchAppearance' === $key && ! empty( $value ) ) {
					foreach ( $value as $_key => $_value ) {
						if ( 'postTypes' === $_key ) {
							foreach ( $_value as $__key => $__value ) {
								foreach ( $__value as $___key => $___value ) {
									if ( 'attachment' === $__key && 'redirectAttachmentUrls' === $___key ) {
										if ( 'attachment_parent' === $___value ) {
											$seopress_advanced['seopress_advanced_advanced_attachments'] = '1';
											unset( $seopress_advanced['seopress_advanced_advanced_attachments_file'] );
										} elseif ( 'attachment' === $___value ) {
											$seopress_advanced['seopress_advanced_advanced_attachments_file'] = '1';
											unset( $seopress_advanced['seopress_advanced_advanced_attachments'] );
										} elseif ( 'disabled' === $___value ) {
											unset( $seopress_advanced['seopress_advanced_advanced_attachments'] );
											unset( $seopress_advanced['seopress_advanced_advanced_attachments_file'] );
										}
									}
									// Single title.
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_single_titles'][ $__key ]['title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Single description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_single_titles'][ $__key ]['description'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}

									// Advanced.
									if ( 'show' === $___key ) {
										$show = $___value;
									}
									if ( 'advanced' === $___key ) {
										foreach ( $___value as $____key => $____value ) {
											// Robots Meta.
											if ( 'robotsMeta' === $____key ) {
												foreach ( $____value as $_____key => $_____value ) {
													if ( true === $show ) {
														unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['noindex'] );
													} else {
														if ( 'noindex' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_single_titles'][ $__key ]['noindex'] = '1';
														} elseif ( 'noindex' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['noindex'] );
														}
														if ( 'nofollow' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_single_titles'][ $__key ]['nofollow'] = '1';
														} elseif ( 'nofollow' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['nofollow'] );
														}
													}
													if ( 'default' === $_____key && ( true === $_____value || empty( $_____value ) ) ) {
														unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['noindex'] );
														unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['nofollow'] );
													}
												}
											}
											// Show meta box.
											if ( 'showMetaBox' === $____key ) {
												if ( true === $____value ) {
													unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['enable'] );
												} elseif ( false === $____value ) {
													$seopress_titles['seopress_titles_single_titles'][ $__key ]['enable'] = '1';
												}
											}
											// Show Date.
											if ( 'showDateInGooglePreview' === $____key ) {
												if ( true === $____value ) {
													$seopress_titles['seopress_titles_single_titles'][ $__key ]['date'] = '1';
												} elseif ( false === $____value ) {
													unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['date'] );
												}
											}
											// Show Post Thumbnail in Search.
											if ( 'showPostThumbnailInSearch' === $____key ) {
												if ( true === $____value ) {
													$seopress_titles['seopress_titles_single_titles'][ $__key ]['thumb_gcs'] = '1';
												} elseif ( false === $____value ) {
													unset( $seopress_titles['seopress_titles_single_titles'][ $__key ]['thumb_gcs'] );
												}
											}
										}
									}
								}
							}
						}
						if ( 'archives' === $_key ) {
							foreach ( $_value as $__key => $__value ) {
								foreach ( $__value as $___key => $___value ) {
									// Single title.
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_archive_titles'][ $__key ]['title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Single description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_archive_titles'][ $__key ]['description'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}

									// Advanced.
									if ( 'show' === $___key ) {
										$show = $___value;
									}
									if ( 'advanced' === $___key ) {
										foreach ( $___value as $____key => $____value ) {
											// Robots Meta.
											if ( 'robotsMeta' === $____key ) {
												foreach ( $____value as $_____key => $_____value ) {
													if ( true === $show ) {
														unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['noindex'] );
													} else {
														if ( 'noindex' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_archive_titles'][ $__key ]['noindex'] = '1';
														} elseif ( 'noindex' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['noindex'] );
														}
														if ( 'nofollow' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_archive_titles'][ $__key ]['nofollow'] = '1';
														} elseif ( 'nofollow' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['nofollow'] );
														}
													}
													if ( 'default' === $_____key && ( true === $_____value || empty( $_____value ) ) ) {
														unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['noindex'] );
														unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['nofollow'] );
													}
												}
											}
											// Show meta box.
											if ( 'showMetaBox' === $____key ) {
												if ( true === $____value ) {
													unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['enable'] );
												} elseif ( false === $____value ) {
													$seopress_titles['seopress_titles_archive_titles'][ $__key ]['enable'] = '1';
												}
											}
											// Show Date.
											if ( 'showDateInGooglePreview' === $____key ) {
												if ( true === $____value ) {
													$seopress_titles['seopress_titles_archive_titles'][ $__key ]['date'] = '1';
												} elseif ( false === $____value ) {
													unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['date'] );
												}
											}
											// Show Post Thumbnail in Search.
											if ( 'showPostThumbnailInSearch' === $____key ) {
												if ( true === $____value ) {
													$seopress_titles['seopress_titles_archive_titles'][ $__key ]['thumb_gcs'] = '1';
												} elseif ( false === $____value ) {
													unset( $seopress_titles['seopress_titles_archive_titles'][ $__key ]['thumb_gcs'] );
												}
											}
										}
									}
								}
							}
						}
						if ( 'taxonomies' === $_key ) {
							foreach ( $_value as $__key => $__value ) {
								foreach ( $__value as $___key => $___value ) {
									// Single title.
									if ( 'title' === $___key ) {
										$seopress_titles['seopress_titles_tax_titles'][ $__key ]['title'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}
									// Single description.
									if ( 'metaDescription' === $___key ) {
										$seopress_titles['seopress_titles_tax_titles'][ $__key ]['description'] = esc_html( $this->tags_aio->replaceTags( $___value ) );
									}

									// Advanced.
									if ( 'show' === $___key ) {
										$show = $___value;
									}
									if ( 'advanced' === $___key ) {
										foreach ( $___value as $____key => $____value ) {
											// Robots Meta.
											if ( 'robotsMeta' === $____key ) {
												foreach ( $____value as $_____key => $_____value ) {
													if ( true === $show ) {
														unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['noindex'] );
													} else {
														if ( 'noindex' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_tax_titles'][ $__key ]['noindex'] = '1';
														} elseif ( 'noindex' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['noindex'] );
														}
														if ( 'nofollow' === $_____key && true === $_____value ) {
															$seopress_titles['seopress_titles_tax_titles'][ $__key ]['nofollow'] = '1';
														} elseif ( 'nofollow' === $_____key && false === $_____value ) {
															unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['nofollow'] );
														}
													}
													if ( 'default' === $_____key && ( true === $_____value || empty( $_____value ) ) ) {
														unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['noindex'] );
														unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['nofollow'] );
													}
												}
											}
											// Show meta box.
											if ( 'showMetaBox' === $____key ) {
												if ( true === $____value ) {
													unset( $seopress_titles['seopress_titles_tax_titles'][ $__key ]['enable'] );
												} elseif ( false === $____value ) {
													$seopress_titles['seopress_titles_tax_titles'][ $__key ]['enable'] = '1';
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		update_option( 'seopress_titles_option_name', $seopress_titles );
		update_option( 'seopress_social_option_name', $seopress_social );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_xml_sitemap );
		update_option( 'seopress_advanced_option_name', $seopress_advanced );
		update_option( 'seopress_google_analytics_option_name', $seopress_google_analytics );
		update_option( 'seopress_pro_option_name', $seopress_pro );
	}

	/**
	 * Process the migration.
	 *
	 * @since 4.3.0
	 */
	public function process() {
		check_ajax_referer( 'seopress_aio_migrate_nonce', '_ajax_nonce', true );
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
			$offset = 'done';
		} else {
			$offset = $this->migratePostQuery( $offset, $increment );
		}

		$data          = array();
		$data['total'] = $total_count_posts;

		if ( $offset >= $total_count_posts ) {
			$data['count'] = $total_count_posts;
		} else {
			$data['count'] = $offset;
		}
		$data['offset'] = $offset;

		do_action( 'seopress_third_importer_aio', $offset, $increment );

		wp_send_json_success( $data );
		exit();
	}
}

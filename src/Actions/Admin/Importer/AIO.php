<?php

namespace SEOPress\Actions\Admin\Importer;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Thirds\AIO\Tags;

class AIO implements ExecuteHooksBackend {

	protected $tagsAIO;

	public function __construct() {
		$this->tagsAIO = new Tags();
	}

	/**
	 * @since 4.3.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action('wp_ajax_seopress_aio_migration', [$this, 'process']);
	}

	/**
	 * @since 4.3.0
	 *
	 * @param int $offset
	 * @param int $increment
	 */
	protected function migratePostQuery($offset, $increment) {
		global $wpdb;
		$args = [
			'posts_per_page' => $increment,
			'post_type'      => 'any',
			'post_status'    => 'any',
			'offset'         => $offset,
		];

		$aio_query = get_posts($args);

		if ( ! $aio_query) {
			$offset += $increment;

			return $offset;
		}

		$getPostMetas = [
			'_seopress_titles_title'         => '_aioseo_title',
			'_seopress_titles_desc'          => '_aioseo_description',
			'_seopress_social_fb_title'      => '_aioseo_og_title',
			'_seopress_social_fb_desc'       => '_aioseo_og_description',
			'_seopress_social_twitter_title' => '_aioseo_twitter_title',
			'_seopress_social_twitter_desc'  => '_aioseo_twitter_description',
		];

		foreach ($aio_query as $post) {
			foreach ($getPostMetas as $key => $value) {
				$metaAIO = get_post_meta($post->ID, $value, true);
				if ( ! empty($metaAIO)) {
					update_post_meta($post->ID, $key, esc_html($this->tagsAIO->replaceTags($metaAIO)));
				}
			}

			//Canonical URL
			$canonical_url = "SELECT p.canonical_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$canonical_url = $wpdb->get_results($canonical_url, ARRAY_A);

			if (! empty($canonical_url[0]['canonical_url'])) {//Import Canonical URL
				update_post_meta($post->ID, '_seopress_robots_canonical', esc_url($canonical_url[0]['canonical_url']));
			}

			//OG Image
			$og_img_url = "SELECT p.og_image_custom_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.og_image_type = 'custom_image' AND p.post_id = $post->ID";

			$og_img_url = $wpdb->get_results($og_img_url, ARRAY_A);

			if (! empty($og_img_url[0]['og_image_custom_url'])) {//Import Facebook Image
				update_post_meta($post->ID, '_seopress_social_fb_img', esc_url($og_img_url[0]['og_image_custom_url']));
			} elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Facebook Image
				$_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
				if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_image'])) {
					update_post_meta($post->ID, '_seopress_social_fb_img', esc_url($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg']));
				}
			}

			//Twitter Image
			$tw_img_url = "SELECT p.twitter_image_custom_url, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.twitter_image_type = 'custom_image' AND p.post_id = $post->ID";

			$tw_img_url = $wpdb->get_results($tw_img_url, ARRAY_A);

			if (! empty($tw_img_url[0]['twitter_image_custom_url'])) {//Import Twitter Image
				update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url($tw_img_url[0]['twitter_image_custom_url']));
			} elseif ('' != get_post_meta($post->ID, '_aioseop_opengraph_settings', true)) { //Import old Twitter Image
				$_aioseop_opengraph_settings = get_post_meta($post->ID, '_aioseop_opengraph_settings', true);
				if (isset($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter'])) {
					update_post_meta($post->ID, '_seopress_social_twitter_img', esc_url($_aioseop_opengraph_settings['aioseop_opengraph_settings_customimg_twitter']));
				}
			}

			//Meta robots "noindex"
			$robots_noindex = "SELECT p.robots_noindex, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_noindex = $wpdb->get_results($robots_noindex, ARRAY_A);

			if (! empty($robots_noindex[0]['robots_noindex']) && '1' === $robots_noindex[0]['robots_noindex']) {//Import Robots NoIndex
				update_post_meta($post->ID, '_seopress_robots_index', 'yes');
			} elseif ('on' == get_post_meta($post->ID, '_aioseop_noindex', true)) { //Import old Robots NoIndex
				update_post_meta($post->ID, '_seopress_robots_index', 'yes');
			}

			//Meta robots "nofollow"
			$robots_nofollow = "SELECT p.robots_nofollow, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_nofollow = $wpdb->get_results($robots_nofollow, ARRAY_A);

			if (! empty($robots_nofollow[0]['robots_nofollow']) && '1' === $robots_nofollow[0]['robots_nofollow']) {//Import Robots NoFollow
				update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
			} elseif ('on' == get_post_meta($post->ID, '_aioseop_nofollow', true)) { //Import old Robots NoFollow
				update_post_meta($post->ID, '_seopress_robots_follow', 'yes');
			}

			//Meta robots "noimageindex"
			$robots_noimageindex = "SELECT p.robots_noimageindex, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_noimageindex = $wpdb->get_results($robots_noimageindex, ARRAY_A);

			if (! empty($robots_noimageindex[0]['robots_noimageindex']) && '1' === $robots_noimageindex[0]['robots_noimageindex']) {//Import Robots NoImageIndex
				update_post_meta($post->ID, '_seopress_robots_imageindex', 'yes');
			}

			//Meta robots "nosnippet"
			$robots_nosnippet = "SELECT p.robots_nosnippet, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$robots_nosnippet = $wpdb->get_results($robots_nosnippet, ARRAY_A);

			if (! empty($robots_nosnippet[0]['robots_nosnippet']) && '1' === $robots_nosnippet[0]['robots_nosnippet']) {//Import Robots NoSnippet
				update_post_meta($post->ID, '_seopress_robots_snippet', 'yes');
			}

			//Target keywords
			$keyphrases = "SELECT p.keyphrases, p.post_id
			FROM {$wpdb->prefix}aioseo_posts p
			WHERE p.post_id = $post->ID";

			$keyphrases = $wpdb->get_results($keyphrases, ARRAY_A);

			if (! empty($keyphrases) && isset($keyphrases[0]['keyphrases'])) {
				$keyphrases = json_decode($keyphrases[0]['keyphrases']);

				if (isset($keyphrases->focus->keyphrase)) {
					$keyphrases = $keyphrases->focus->keyphrase;

					if ('' != $keyphrases) { //Import focus kw
						update_post_meta($post->ID, '_seopress_analysis_target_kw', esc_html($keyphrases));
					}
				}
			}
		}

		$offset += $increment;

		return $offset;
	}

	protected function migrateSettings() {
		$seopress_titles = get_option('seopress_titles_option_name');
		$seopress_social = get_option('seopress_social_option_name');
		$seopress_xml_sitemap = get_option('seopress_xml_sitemap_option_name');
		$seopress_google_analytics = get_option('seopress_google_analytics_option_name');
		$seopress_advanced = get_option('seopress_advanced_option_name');
		$seopress_pro = get_option('seopress_pro_option_name');

		$aioseo_options = get_option('aioseo_options');
		$aioseo_options_pro = get_option('aioseo_options_pro');
		$aioseo_options = json_decode($aioseo_options, true);
		$aioseo_options_pro = json_decode($aioseo_options_pro, true);

		$aioseo_options_dynamic = get_option('aioseo_options_dynamic');
		$aioseo_options_dynamic = json_decode($aioseo_options_dynamic, true);

		foreach ($aioseo_options as $key => $value) {
			// Sitemap
			if ($key === 'sitemap' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'general' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							// Enable Sitemap
							if ($__key === 'enable' && $__value === true) {
								$seopress_xml_sitemap['seopress_xml_sitemap_general_enable'] = '1';
							} elseif ($__key === 'enable' && $__value === false) {
								unset($seopress_xml_sitemap['seopress_xml_sitemap_general_enable']);
							}
							// Post Types Sitemap
							if ($__key === 'postTypes' && ! empty($__value)) {
								// Clear existing CPT keys first
								if (isset($seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'])) {
									foreach ($seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'] as $cpt_key => $cpt_value) {
										unset($seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][$cpt_key]['include']);
									}
								}
								 
								foreach ($__value as $___key => $___value) {
									if ( $___key === 'all' && $___value === true ) {
										$postTypes = seopress_get_service('WordPressData')->getPostTypes();
										foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
											$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][$seopress_cpt_key]['include'] = '1';
										}
									} elseif ($___key === 'included') {
										foreach ($___value as $____key => $____value) {
											$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][$____value]['include'] = '1';
										}
									}
								}
							}
							// Taxonomies Sitemap
							if ($__key === 'taxonomies' && ! empty($__value)) {
								// Clear existing CPT keys first
								if (isset($seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'])) {
									foreach ($seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'] as $tax_key => $tax_value) {
										unset($seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][$tax_key]['include']);
									}
								}
								 
								foreach ($__value as $___key => $___value) {
									if ( $___key === 'all' && $___value === true ) {
										$taxonomies = seopress_get_service('WordPressData')->getTaxonomies();
										foreach ($taxonomies as $seopress_tax_key => $seopress_tax_value) {
											$seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][$seopress_tax_key]['include'] = '1';
										}
									} elseif ($___key === 'included') {
										foreach ($___value as $____key => $____value) {
											$seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][$____value]['include'] = '1';
										}
									}
								}
							}
							// Author Sitemap
							if ($__key === 'author' && $__value === true) {
								$seopress_xml_sitemap['seopress_xml_sitemap_author_enable'] = '1';
							} elseif ($__key === 'author' && $__value === false) {
								unset($seopress_xml_sitemap['seopress_xml_sitemap_author_enable']);
							}
							if ($__key === 'advancedSettings') {
								foreach ($__value as $___key => $___value) {
									// Image Sitemap
									if ($___key === 'excludeImages' && $___value === false) {
										$seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] = '1';
									} elseif ($___key === 'excludeImages' && $___value === true) {
										unset($seopress_xml_sitemap['seopress_xml_sitemap_img_enable']);
									}
								}
							}
						}
					}
					// HTML Sitemap
					if ($_key === 'html' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							// Enable HTML Sitemap
							if ($__key === 'enable' && $__value === true) {
								$seopress_xml_sitemap['seopress_xml_sitemap_html_enable'] = '1';
							} elseif ($__key === 'enable' && $__value === false) {
								unset($seopress_xml_sitemap['seopress_xml_sitemap_html_enable']);
							}
							// Publication date
							if ($__key === 'publicationDate' && $__value === true) {
								unset($seopress_xml_sitemap['seopress_xml_sitemap_html_date']);
							} elseif ($__key === 'publicationDate' && $__value === false) {
								$seopress_xml_sitemap['seopress_xml_sitemap_html_date'] = '1';
							}
							// Sort order
							if ($__key === 'sortOrder' && ! empty($__value)) {
								$sort = [
									'publish_date' => 'date',
									'last_updated' => 'modified',
									'alphabetical' => 'title',
									'id' => 'ID'
								];
								$seopress_xml_sitemap['seopress_xml_sitemap_html_orderby'] = esc_html($sort[$__value]);
							}
							// Sort direction
							if ($__key === 'sortDirection' && ! empty($__value)) {
								$sort = [
									'asc' => 'ASC',
									'desc' => 'DESC'
								];
								$seopress_xml_sitemap['seopress_xml_sitemap_html_order'] = esc_html($sort[$__value]);
							}
							if ($__key === 'advancedSettings' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'enable' && $___value === false) {
										break;
									} else {
										if (($___key === 'excludePosts' || $___key === 'excludeTerms') && ! empty($___value)) {
											if (!isset($seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'])) {
												$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = '';
											}
											$exclude_ids = [];
											foreach ($___value as $exclude_item) {
												$decoded = json_decode($exclude_item, true);
												if ($decoded && isset($decoded['value'])) {
													$exclude_ids[] = $decoded['value'];
												}
											}
											if (!empty($exclude_ids)) {
												$current_exclude = $seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'];
												$new_exclude = implode(',', $exclude_ids);
												if (!empty($current_exclude)) {
													$combined_ids = array_unique(array_merge(explode(',', $current_exclude), $exclude_ids));
													$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = esc_html(implode(',', $combined_ids));
												} else {
													$seopress_xml_sitemap['seopress_xml_sitemap_html_exclude'] = esc_html($new_exclude);
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

			// Social
			if ($key === 'social' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'profiles' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							if ($__key === 'urls' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									// Facebook URL
									if ($___key === 'facebookPageUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_facebook'] = esc_url($___value);
									}
									// Twitter URL
									if ($___key === 'twitterUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_twitter'] = esc_url($___value);
									}
									// Instagram URL
									if ($___key === 'instagramUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_instagram'] = esc_url($___value);
									}
									// LinkedIn URL
									if ($___key === 'linkedinUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_linkedin'] = esc_url($___value);
									}
									// Pinterest URL
									if ($___key === 'pinterestUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_pinterest'] = esc_url($___value);
									}
									// YouTube URL
									if ($___key === 'youtubeUrl' && ! empty($___value)) {
										$seopress_social['seopress_social_accounts_youtube'] = esc_url($___value);
									}
								}
							}
							// Extra accounts
							if ($__key === 'additionalUrls' && ! empty($__value)) {
								$accounts = array_filter(explode("\n", $__value));
								$accounts = implode("\n", array_map('esc_url', $accounts));
								$seopress_social['seopress_social_accounts_extra'] = esc_html($accounts);
							}
						}
					}
					// Facebook
					if ($_key === 'facebook' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							// General
							if ($__key === 'general' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									// Enable OG
									if ($___key === 'enable') {
										if ($___value === true) {
											$seopress_social['seopress_social_facebook_og'] = '1';
										} else {
											unset($seopress_social['seopress_social_facebook_og']);
										}
									}
									// Default image
									if ($___key === 'defaultImagePosts' && ! empty($___value)) {
										$seopress_social['seopress_social_facebook_img'] = esc_url($___value);
									}
									// Image size
									if ($___key === 'defaultImagePostsWidth' && ! empty($___value)) {
										$seopress_social['seopress_social_facebook_img_width'] = esc_html($___value);
									}
									// Image width
									if ($___key === 'defaultImagePostsHeight' && ! empty($___value)) {
										$seopress_social['seopress_social_facebook_img_height'] = esc_html($___value);
									}
								}
							}
							// Advanced
							if ($__key === 'advanced' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									// Admin ID
									if ($___key === 'adminId' && ! empty($___value)) {
										$seopress_social['seopress_social_facebook_admin_id'] = esc_html($___value);
									}
									// App ID
									if ($___key === 'appId' && ! empty($___value)) {
										$seopress_social['seopress_social_facebook_app_id'] = esc_html($___value);
									}
								}
							}
						}
					}
					// X
					if ($_key === 'twitter' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							// General
							if ($__key === 'general' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									// Enable X Cards
									if ($___key === 'enable') {
										if ($___value === true) {
											$seopress_social['seopress_social_twitter_card'] = '1';
										} else {
											unset($seopress_social['seopress_social_twitter_card']);
										}
									}
									// Use OG if no X Cards
									if ($___key === 'useOgData') {
										if ($___value === true) {
											$seopress_social['seopress_social_twitter_card_og'] = '1';
										} else {
											unset($seopress_social['seopress_social_twitter_card_og']);
										}
									}
									// Default image
									if ($___key === 'defaultImagePosts' && ! empty($___value)) {
										$seopress_social['seopress_social_twitter_card_img'] = esc_url($___value);
									}
									// Image size
									if ($___key === 'defaultCardType' && ! empty($___value)) {
										$type = [
											'summary_large_image' => 'large',
											'summary' => 'default'
										];
										$seopress_social['seopress_social_twitter_card_img_size'] = esc_html($type[$___value]);
									}
								}
							}
						}
					}
				}
			}
			// Titles & metas
			if ($key === 'searchAppearance' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'global' && ! empty($_value)) {
						// Global title
						foreach ($_value as $__key => $__value) {
							// Title separator
							if ($__key === 'separator') {
								$seopress_titles['seopress_titles_sep'] = esc_html($this->tagsAIO->replaceTags($__value));
							}
							// Site title
							if ($__key === 'siteTitle') {
								$seopress_titles['seopress_titles_home_site_title'] = esc_html($this->tagsAIO->replaceTags($__value));
							}
							// Site description
							if ($__key === 'metaDescription') {
								$seopress_titles['seopress_titles_home_site_desc'] = esc_html($this->tagsAIO->replaceTags($__value));
							}

							// Website Schema
							if ($__key === 'schema' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'websiteName') {
										$seopress_social['seopress_social_knowledge_name'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									if ($___key === 'websiteAlternateName') {
										$seopress_titles['seopress_titles_home_site_title_alt'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									if ($___key === 'siteRepresents') {
										$type = [
											'organization' => 'Organization',
											'person' => 'Person',
										];
										$seopress_social['seopress_social_knowledge_type'] = esc_html($type[$___value]);
									}
									if ($___key === 'organizationDescription') {
										$seopress_social['seopress_social_knowledge_desc'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									if ($___key === 'organizationLogo') {
										$seopress_social['seopress_social_knowledge_img'] = esc_url($___value);
									}
									if ($___key === 'contactType') {
										$type = [
											'Customer Support' => 'customer support',
											'Technical Support' => 'technical support',
											'Billing Support' => 'billing support',
											'Sales' => 'sales'
										];
										$seopress_social['seopress_social_knowledge_contact_type'] = esc_html($type[$___value]);
									}
								}
							}
						}
					}
					// Archives
					if ($_key === 'archives' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							// Author archive title
							if ($__key === 'author' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'title') {
										$seopress_titles['seopress_titles_archives_author_title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Author archive description
									if ($___key === 'metaDescription') {
										$seopress_titles['seopress_titles_archives_author_desc'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Noindex
									if ($___key === 'show') {
										if ($___value === false) {
											$seopress_titles['seopress_titles_archives_author_noindex'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_archives_author_noindex']);
										}
									}
								}
							}
							// Date archive title
							if ($__key === 'date' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'title') {
										$seopress_titles['seopress_titles_archives_date_title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Author archive description
									if ($___key === 'metaDescription') {
										$seopress_titles['seopress_titles_archives_date_desc'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Noindex
									if ($___key === 'show') {
										if ($___value === false) {
											$seopress_titles['seopress_titles_archives_date_noindex'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_archives_date_noindex']);
										}
									}
								}
							}
							// Search archive title
							if ($__key === 'search' && ! empty($__value)) {
								
								foreach ($__value as $___key => $___value) {
									if ($___key === 'title') {
										$seopress_titles['seopress_titles_archives_search_title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Author archive description
									if ($___key === 'metaDescription') {
										$seopress_titles['seopress_titles_archives_search_desc'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Noindex
									if ($___key === 'show') {
										if ($___value === false) {
											$seopress_titles['seopress_titles_archives_search_title_noindex'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_archives_search_title_noindex']);
										}
									}   
									
								}
							}
						}
					}
					// Advanced
					if ($_key === 'advanced' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							if ($__key === 'globalRobotsMeta' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'default' && $___value === true) {
										unset($seopress_titles['seopress_titles_noindex']);
										unset($seopress_titles['seopress_titles_nosnippet']);
										unset($seopress_titles['seopress_titles_noimageindex']);
										unset($seopress_titles['seopress_titles_nofollow']);
										break;
									}
									if ($___key === 'noindex') {
										if ($___value === true) {
											$seopress_titles['seopress_titles_noindex'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_noindex']);
										}
									}
									if ($___key === 'nosnippet') {
										if ($___value === true) {
											$seopress_titles['seopress_titles_nosnippet'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_nosnippet']);
										}
									}
									if ($___key === 'noimageindex') {
										if ($___value === true) {
										$seopress_titles['seopress_titles_noimageindex'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_noimageindex']);
										}
									}
									if ($___key === 'nofollow') {
										if ($___value === true) {
											$seopress_titles['seopress_titles_nofollow'] = '1';
										} else {
											unset($seopress_titles['seopress_titles_nofollow']);
										}
									}
								}
							}
							if ($__key === 'crawlCleanup' && ! empty($__value)) {
								foreach ($__value as $___key => $___value) {
									if ($___key === 'feeds' && ! empty($___value)) {
										foreach ($___value as $____key => $____value) {
											if ($____key === 'globalComments' && $____value === true) {
												unset($seopress_pro['seopress_rss_disable_comments_feed']);
											} elseif ($____key === 'globalComments' && $____value === false) {
												$seopress_pro['seopress_rss_disable_comments_feed'] = '1';
											}
											if ($____key === 'staticBlogPage' && $____value === true) {
												unset($seopress_pro['seopress_rss_disable_posts_feed']);
											} elseif ($____key === 'staticBlogPage' && $____value === false) {
												$seopress_pro['seopress_rss_disable_posts_feed'] = '1';
											}
											if (($____key === 'authors' || $____key === 'postComments') && $____value === true) {
												unset($seopress_pro['seopress_rss_disable_extra_feed']);
											} elseif (($____key === 'authors' || $____key === 'postComments') && $____value === false) {
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
			// Webmaster Tools
			if ($key === 'webmasterTools' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'google') {
						$seopress_advanced['seopress_advanced_advanced_google'] = esc_html($_value);
					} elseif ($_key === 'bing') {
						$seopress_advanced['seopress_advanced_advanced_bing'] = esc_html($_value);
					} elseif ($_key === 'yandex') {
						$seopress_advanced['seopress_advanced_advanced_yandex'] = esc_html($_value);
					} elseif ($_key === 'baidu') {
						$seopress_advanced['seopress_advanced_advanced_baidu'] = esc_html($_value);
					} elseif ($_key === 'pinterest') {
						$seopress_advanced['seopress_advanced_advanced_pinterest'] = esc_html($_value);
					} elseif ($_key === 'microsoftClarityProjectId') {
						$seopress_google_analytics['seopress_google_analytics_clarity_project_id'] = esc_html($_value);
					}
				}
			}
			// Breadcrumbs
			if ($key === 'breadcrumbs' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					// Enable Breadcrumbs
					$seopress_pro['seopress_breadcrumbs_enable'] = '1';
					$seopress_pro['seopress_breadcrumbs_json_enable'] = '1';
					
					// Breadcrumbs Separator
					if ($_key === 'separator') {
						$seopress_pro['seopress_breadcrumbs_separator'] = esc_html($_value);
					}
					// Breadcrumbs Home
					if ($_key === 'homepageLabel') {
						$seopress_pro['seopress_breadcrumbs_i18n_home'] = esc_html($_value);
					}
					// Breadcrumbs Prefix
					if ($_key === 'breadcrumbPrefix') {
						$seopress_pro['seopress_breadcrumbs_i18n_here'] = esc_html($_value);
					}
					// Breadcrumbs Search Prefix
					if ($_key === 'searchResultFormat') {
						$seopress_pro['seopress_breadcrumbs_i18n_search'] = esc_html($_value);
					}
					// Breadcrumbs 404 Crumbs
					if ($_key === 'errorFormat404') {
						$seopress_pro['seopress_breadcrumbs_i18n_404'] = esc_html($_value);
					}
					// Breadcrumbs Display Blog Page
					if ($_key === 'showBlogHome') {
						if ($_value === true) {
							unset($seopress_pro['seopress_breadcrumbs_remove_blog_page']);
						} elseif ($_value === false) {
							$seopress_pro['seopress_breadcrumbs_remove_blog_page'] = '1';
						}
					}
				}
			}
			// RSS Feed
			if ($key === 'rssContent' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'before') {
						$args = [
							'strong' => [],
							'em' => [],
							'br' => [],
							'a' => ['href' => [], 'rel' => []],
						];
						$seopress_pro['seopress_rss_before_html'] = wp_kses($this->tagsAIO->replaceTags($_value), $args);
					} elseif ($_key === 'after') {
						$args = [
							'strong' => [],
							'em' => [],
							'br' => [],
							'a' => ['href' => [], 'rel' => []],
						];
						$seopress_pro['seopress_rss_after_html'] = wp_kses($this->tagsAIO->replaceTags($_value), $args);
					}
				}
			}
			// robots.txt file content
			if ($key === 'tools' && ! empty($value)) {
				foreach ($value as $_key => $_value) {
					if ($_key === 'robots' && ! empty($_value)) {
						foreach ($_value as $__key => $__value) {
							if ($__key === 'enable' && true === $__value) {
								$seopress_pro['seopress_robots_enable'] = '1';
							}
							if ($__key === 'rules' && ! empty($__value)) {
								$txt = '';
								$rules = [];
								foreach ($__value as $rule_json) {
									$rule_data = json_decode($rule_json, true);
									if ($rule_data && isset($rule_data['directive'])) {
										$user_agent = isset($rule_data['userAgent']) && $rule_data['userAgent'] !== null ? $rule_data['userAgent'] : '*';
										$field_value = isset($rule_data['fieldValue']) && $rule_data['fieldValue'] !== null ? $rule_data['fieldValue'] : '';
										
										if ($rule_data['directive'] === 'clean-param') {
											// clean-param directive format: Clean-param: param1&param2
											$rules[] = 'User-agent: ' . $user_agent . "\nClean-param: " . $field_value;
										} else {
											// Standard allow/disallow format: User-agent: directive path
											$directive = ucfirst($rule_data['directive']);
											$rules[] = 'User-agent: ' . $user_agent . "\n" . $directive . ": " . $field_value;
										}
									}
								}
								$txt = implode("\n\n", $rules);
								$seopress_pro['seopress_robots_file'] = esc_html($txt);
							}
						}
					}
				}
			}
		}

		if (! empty($aioseo_options_pro)) {
			foreach ($aioseo_options_pro as $key => $value) {
				// Advanced
				if ($key === 'advanced' && ! empty($value)) {
					foreach ($value as $_key => $_value) {
						// OpenAI API Key
						if ($_key === 'openAiKey') {
							$seopress_pro['seopress_ai_openai_api_key'] = esc_html($_value);
						}
					}
				}
				// Local Business
				if ($key === 'localBusiness' && ! empty($value)) {
					foreach ($value as $_key => $_value) {
						if ($_key === 'locations' && ! empty($_value)) {
							foreach ($_value as $__key => $__value) {
								if ($__key === 'business' && ! empty($__value)) {
									foreach ($__value as $___key => $___value) {
										// Local Business Type
										if ($___key === 'businessType' && ! empty($___value)) {
											$seopress_pro['seopress_local_business_type'] = esc_html($___value);
										}
										// Local Business Street Address
										if ($___key === 'address' && ! empty($___value)) {
											$street_address = '';
											foreach ($___value as $____key => $____value) {
												if ($____key === 'streetLine1' && ! empty($____value)) {
													$street_address = $____value;
												}
												if ($____key === 'streetLine2' && ! empty($____value)) {
													$street_address .= ', ' . $____value;
												}
												// Local Business City
												if ($____key === 'city' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_address_locality'] = esc_html($____value);
												}
												// Local Business State
												if ($____key === 'state' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_address_region'] = esc_html($____value);
												}
												// Local Business Postal Code
												if ($____key === 'zipCode' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_postal_code'] = esc_html($____value);
												}
												// Local Business Country
												if ($____key === 'country' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_address_country'] = esc_html($____value);
												}
											}
											$seopress_pro['seopress_local_business_street_address'] = esc_html($street_address);
										}
										// Local Business URL
										if ($___key === 'urls' && ! empty($___value)) {
											foreach ($___value as $____key => $____value) {
												if ($____key === 'website' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_url'] = esc_url($____value);
												}
											}
										}
										// Local Business Phone
										if ($___key === 'contact' && ! empty($___value)) {
											foreach ($___value as $____key => $____value) {
												if ($____key === 'phone' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_phone'] = esc_html($____value);
												}
											}
										}
										// Local Business Price Range
										if ($___key === 'payment' && ! empty($___value)) {
											foreach ($___value as $____key => $____value) {
												if ($____key === 'priceRange' && ! empty($____value)) {
													$seopress_pro['seopress_local_business_price_range'] = esc_html($____value);
												}
											}
										}
									}
								}
							}
						}
					}
				}
				// Sitemap
				if ($key === 'sitemap' && ! empty($value)) {
					foreach ($value as $_key => $_value) {
						// Video
						if ($_key === 'video' && ! empty($_value)) {
							foreach ($_value as $__key => $__value) {
								if ($__key === 'enable' && $__value === true) {
									$seopress_xml_sitemap['seopress_xml_sitemap_video_enable'] = '1';
								} elseif ($__key === 'enable' && $__value === false) {
									unset($seopress_xml_sitemap['seopress_xml_sitemap_video_enable']);
								}
							}
						}
						// News
						if ($_key === 'news' && ! empty($_value)) {
							foreach ($_value as $__key => $__value) {
								if ($__key === 'enable' && $__value === true) {
									$seopress_pro['seopress_news_enable'] = '1';
								} elseif ($__key === 'enable' && $__value === false) {
									unset($seopress_pro['seopress_news_enable']);
								}
								// Publication name
								if ($__key === 'publicationName') {
									$seopress_pro['seopress_news_name'] = esc_html($__value);
								} elseif ($__key === 'publicationLanguage') {
									unset($seopress_pro['seopress_news_name']);
								}
								// Post Types Sitemap
								if ($__key === 'postTypes' && ! empty($__value)) {
									// Clear existing CPT keys first
									if (isset($seopress_pro['seopress_news_name_post_types_list'])) {
										foreach ($seopress_pro['seopress_news_name_post_types_list'] as $cpt_key => $cpt_value) {
											unset($seopress_pro['seopress_news_name_post_types_list'][$cpt_key]['include']);
										}
									}
									
									foreach ($__value as $___key => $___value) {
										if ( $___key === 'all' && $___value === true ) {
											$postTypes = seopress_get_service('WordPressData')->getPostTypes();
											foreach ($postTypes as $seopress_cpt_key => $seopress_cpt_value) {
												$seopress_pro['seopress_news_name_post_types_list'][$seopress_cpt_key]['include'] = '1';
											}
										} elseif ($___key === 'included') {
											foreach ($___value as $____key => $____value) {
												$seopress_pro['seopress_news_name_post_types_list'][$____value]['include'] = '1';
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

		if (! empty($aioseo_options_dynamic)) {
			foreach ($aioseo_options_dynamic as $key => $value) {
				if ($key === 'searchAppearance' && ! empty($value)) {
					foreach ($value as $_key => $_value) {
						if ($_key === 'postTypes') {
							foreach ($_value as $__key => $__value) {
								foreach ($__value as $___key => $___value) {
									if ($__key === 'attachment' && $___key === 'redirectAttachmentUrls') {
										if ($___value === 'attachment_parent') {
											$seopress_advanced['seopress_advanced_advanced_attachments'] = '1';
											unset($seopress_advanced['seopress_advanced_advanced_attachments_file']);
										} elseif ($___value === 'attachment') {
											$seopress_advanced['seopress_advanced_advanced_attachments_file'] = '1';
											unset($seopress_advanced['seopress_advanced_advanced_attachments']);
										} elseif ($___value === 'disabled') {
											unset($seopress_advanced['seopress_advanced_advanced_attachments']);
											unset($seopress_advanced['seopress_advanced_advanced_attachments_file']);
										}
									}
									// Single title
									if ( $___key === 'title' ) {
										$seopress_titles['seopress_titles_single_titles'][$__key]['title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Single description
									if ( $___key === 'metaDescription' ) {
										$seopress_titles['seopress_titles_single_titles'][$__key]['description'] = esc_html($this->tagsAIO->replaceTags($___value));
									}

									// Advanced
									if ($___key === 'show') {
										$show = $___value;
									} 
									if ($___key === 'advanced') {
										foreach ($___value as $____key => $____value) {
											// Robots Meta
											if ($____key === 'robotsMeta') {
												foreach ($____value as $_____key => $_____value) {
													if ($show === true) {
														unset($seopress_titles['seopress_titles_single_titles'][$__key]['noindex']);
													} else {
														if ($_____key === 'noindex' && $_____value === true) {
															$seopress_titles['seopress_titles_single_titles'][$__key]['noindex'] = '1';
														} elseif ($_____key === 'noindex' && $_____value === false) {
															unset($seopress_titles['seopress_titles_single_titles'][$__key]['noindex']);
														}
														if ($_____key === 'nofollow' && $_____value === true) {
															$seopress_titles['seopress_titles_single_titles'][$__key]['nofollow'] = '1';
														} elseif ($_____key === 'nofollow' && $_____value === false) {
															unset($seopress_titles['seopress_titles_single_titles'][$__key]['nofollow']);
														} 
													}
													if ($_____key === 'default' && ($_____value === true || empty($_____value))) {
														unset($seopress_titles['seopress_titles_single_titles'][$__key]['noindex']);
														unset($seopress_titles['seopress_titles_single_titles'][$__key]['nofollow']);
													}
												}
											}
											// Show meta box
											if ($____key === 'showMetaBox') {
												if ($____value === true) {
													unset($seopress_titles['seopress_titles_single_titles'][$__key]['enable']);
												} elseif ($____value === false) {
													$seopress_titles['seopress_titles_single_titles'][$__key]['enable'] = '1';
												}
											}
											// Show Date
											if ($____key === 'showDateInGooglePreview') {
												if ($____value === true) {
													$seopress_titles['seopress_titles_single_titles'][$__key]['date'] = '1';
												} elseif ($____value === false) {
													unset($seopress_titles['seopress_titles_single_titles'][$__key]['date']);
												}
											}
											// Show Post Thumbnail in Search
											if ($____key === 'showPostThumbnailInSearch') {
												if ($____value === true) {
													$seopress_titles['seopress_titles_single_titles'][$__key]['thumb_gcs'] = '1';
												} elseif ($____value === false) {
													unset($seopress_titles['seopress_titles_single_titles'][$__key]['thumb_gcs']);
												}
											}
										}
									}
								}
							}
						}
						if ($_key === 'archives') {
							foreach ($_value as $__key => $__value) {
								foreach ($__value as $___key => $___value) {
									// Single title
									if ( $___key === 'title' ) {
										$seopress_titles['seopress_titles_archive_titles'][$__key]['title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Single description
									if ( $___key === 'metaDescription' ) {
										$seopress_titles['seopress_titles_archive_titles'][$__key]['description'] = esc_html($this->tagsAIO->replaceTags($___value));
									}

									// Advanced
									if ($___key === 'show') {
										$show = $___value;
									} 
									if ($___key === 'advanced') {
										foreach ($___value as $____key => $____value) {
											// Robots Meta
											if ($____key === 'robotsMeta') {
												foreach ($____value as $_____key => $_____value) {
													if ($show === true) {
														unset($seopress_titles['seopress_titles_archive_titles'][$__key]['noindex']);
													} else {
														if ($_____key === 'noindex' && $_____value === true) {
															$seopress_titles['seopress_titles_archive_titles'][$__key]['noindex'] = '1';
														} elseif ($_____key === 'noindex' && $_____value === false) {
															unset($seopress_titles['seopress_titles_archive_titles'][$__key]['noindex']);
														}
														if ($_____key === 'nofollow' && $_____value === true) {
															$seopress_titles['seopress_titles_archive_titles'][$__key]['nofollow'] = '1';
														} elseif ($_____key === 'nofollow' && $_____value === false) {
															unset($seopress_titles['seopress_titles_archive_titles'][$__key]['nofollow']);
														} 
													}
													if ($_____key === 'default' && ($_____value === true || empty($_____value))) {
														unset($seopress_titles['seopress_titles_archive_titles'][$__key]['noindex']);
														unset($seopress_titles['seopress_titles_archive_titles'][$__key]['nofollow']);
													}
												}
											}
											// Show meta box
											if ($____key === 'showMetaBox') {
												if ($____value === true) {
													unset($seopress_titles['seopress_titles_archive_titles'][$__key]['enable']);
												} elseif ($____value === false) {
													$seopress_titles['seopress_titles_archive_titles'][$__key]['enable'] = '1';
												}
											}
											// Show Date
											if ($____key === 'showDateInGooglePreview') {
												if ($____value === true) {
													$seopress_titles['seopress_titles_archive_titles'][$__key]['date'] = '1';
												} elseif ($____value === false) {
													unset($seopress_titles['seopress_titles_archive_titles'][$__key]['date']);
												}
											}
											// Show Post Thumbnail in Search
											if ($____key === 'showPostThumbnailInSearch') {
												if ($____value === true) {
													$seopress_titles['seopress_titles_archive_titles'][$__key]['thumb_gcs'] = '1';
												} elseif ($____value === false) {
													unset($seopress_titles['seopress_titles_archive_titles'][$__key]['thumb_gcs']);
												}
											}
										}
									}
								}
							}
						}
						if ($_key === 'taxonomies') {
							foreach ($_value as $__key => $__value) {
								foreach ($__value as $___key => $___value) {
									// Single title
									if ( $___key === 'title' ) {
										$seopress_titles['seopress_titles_tax_titles'][$__key]['title'] = esc_html($this->tagsAIO->replaceTags($___value));
									}
									// Single description
									if ( $___key === 'metaDescription' ) {
										$seopress_titles['seopress_titles_tax_titles'][$__key]['description'] = esc_html($this->tagsAIO->replaceTags($___value));
									}

									// Advanced
									if ($___key === 'show') {
										$show = $___value;
									} 
									if ($___key === 'advanced') {
										foreach ($___value as $____key => $____value) {
											// Robots Meta
											if ($____key === 'robotsMeta') {
												foreach ($____value as $_____key => $_____value) {
													if ($show === true) {
														unset($seopress_titles['seopress_titles_tax_titles'][$__key]['noindex']);
													} else {
														if ($_____key === 'noindex' && $_____value === true) {
															$seopress_titles['seopress_titles_tax_titles'][$__key]['noindex'] = '1';
														} elseif ($_____key === 'noindex' && $_____value === false) {
															unset($seopress_titles['seopress_titles_tax_titles'][$__key]['noindex']);
														}
														if ($_____key === 'nofollow' && $_____value === true) {
															$seopress_titles['seopress_titles_tax_titles'][$__key]['nofollow'] = '1';
														} elseif ($_____key === 'nofollow' && $_____value === false) {
															unset($seopress_titles['seopress_titles_tax_titles'][$__key]['nofollow']);
														} 
													}
													if ($_____key === 'default' && ($_____value === true || empty($_____value))) {
														unset($seopress_titles['seopress_titles_tax_titles'][$__key]['noindex']);
														unset($seopress_titles['seopress_titles_tax_titles'][$__key]['nofollow']);
													}
												}
											}
											// Show meta box
											if ($____key === 'showMetaBox') {
												if ($____value === true) {
													unset($seopress_titles['seopress_titles_tax_titles'][$__key]['enable']);
												} elseif ($____value === false) {
													$seopress_titles['seopress_titles_tax_titles'][$__key]['enable'] = '1';
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

		update_option('seopress_titles_option_name', $seopress_titles);
		update_option('seopress_social_option_name', $seopress_social);
		update_option('seopress_xml_sitemap_option_name', $seopress_xml_sitemap);
		update_option('seopress_advanced_option_name', $seopress_advanced);
		update_option('seopress_google_analytics_option_name', $seopress_google_analytics);
		update_option('seopress_pro_option_name', $seopress_pro);
	}

	/**
	 * @since 4.3.0
	 */
	public function process() {
		check_ajax_referer('seopress_aio_migrate_nonce', '_ajax_nonce', true);
		if ( ! is_admin()) {
			wp_send_json_error();

			return;
		}

		if ( ! current_user_can(seopress_capability('manage_options', 'migration'))) {
			wp_send_json_error();

			return;
		}

		$this->migrateSettings();

		if (isset($_POST['offset'])) {
			$offset = absint($_POST['offset']);
		}

		global $wpdb;
		$total_count_posts = (int) $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts}");

		$increment = 200;
		global $post;

		if ($offset > $total_count_posts) {
			$offset = 'done';
		} else {
			$offset = $this->migratePostQuery($offset, $increment);
		}

		$data           = [];
		$data['total'] = $total_count_posts;

		if ($offset >= $total_count_posts) {
			$data['count'] = $total_count_posts;
		} else {
			$data['count'] = $offset;
		}
		$data['offset'] = $offset;

		do_action('seopress_third_importer_aio', $offset, $increment);

		wp_send_json_success($data);
		exit();
	}
}

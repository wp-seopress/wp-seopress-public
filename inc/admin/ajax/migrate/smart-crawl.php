<?php
/**
 * SmartCrawl migration.
 *
 * @package SEOPress
 * @subpackage Ajax
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Translate SmartCrawl template variables to SEOPress canonical names.
 *
 * SmartCrawl and SEOPress overlap on common variables (`%%sep%%`, `%%currentdate%%`),
 * and SEOPress's front-end renderer aliases a handful of them (`%%title%%`,
 * `%%sitename%%`, `%%sitedesc%%`, `%%excerpt%%`). The rest (`%%searchphrase%%`,
 * `%%category%%`, `%%tag%%`, `%%name%%`, `%%user_description%%`, `%%pt_plural%%`)
 * are not aliased and would render literally on migrated templates.
 *
 * We rewrite them to SEOPress canonical names so both the front-end render
 * and the React admin token labels work out of the box after migration.
 *
 * Also runs `sanitize_text_field()` so callers can drop their own sanitize call.
 *
 * @param mixed $value Raw SmartCrawl template string (or any other scalar).
 *
 * @return string Translated and sanitized value.
 */
function seopress_smart_crawl_translate_template( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return sanitize_text_field( (string) $value );
	}

	static $map = null;
	if ( null === $map ) {
		$map = array(
			'%%title%%'                => '%%post_title%%',
			'%%sitename%%'             => '%%sitetitle%%',
			'%%sitedesc%%'             => '%%tagline%%',
			'%%excerpt%%'              => '%%post_excerpt%%',
			'%%searchphrase%%'         => '%%search_keywords%%',
			'%%category%%'             => '%%_category_title%%',
			'%%category_description%%' => '%%_category_description%%',
			'%%tag%%'                  => '%%tag_title%%',
			'%%name%%'                 => '%%post_author%%',
			'%%user_description%%'     => '%%author_bio%%',
			'%%pt_plural%%'            => '%%cpt_plural%%',
		);
	}

	$translated = strtr( $value, $map );

	return sanitize_text_field( $translated );
}

/**
 * SmartCrawl migration.
 */
function seopress_smart_crawl_migration() {
	check_ajax_referer( 'seopress_smart_crawl_migrate_nonce', '_ajax_nonce', true );

	if ( current_user_can( seopress_capability( 'manage_options', 'migration' ) ) && is_admin() ) {
		// Offset can be either an integer (posts phase) or a sentinel string ('redirects:N', 'done').
		$raw_offset = isset( $_POST['offset'] ) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '0';

		$in_redirects_phase = ( 0 === strpos( $raw_offset, 'redirects:' ) );
		if ( $in_redirects_phase ) {
			$redirect_offset = absint( substr( $raw_offset, strlen( 'redirects:' ) ) );
			$offset          = 0;
		} else {
			$offset          = is_numeric( $raw_offset ) ? absint( $raw_offset ) : 0;
			$redirect_offset = 0;
		}

		global $wpdb;
		// phpcs:ignore
		$total_count_posts = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->posts}" );
		// phpcs:ignore
		$total_count_terms = (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->terms}" );

		// Detect SmartCrawl redirects table and SEOPress redirections CPT.
		$redirects_table        = $wpdb->prefix . 'smartcrawl_redirects';
		$has_redirects_table    = ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $redirects_table ) ) === $redirects_table );
		$has_redirections_cpt   = post_type_exists( 'seopress_404' );
		$can_import_redirects   = $has_redirects_table && $has_redirections_cpt;
		$total_count_redirects  = 0;
		if ( $can_import_redirects ) {
			// SEOPress requires WP 6.5+, so the %i identifier placeholder is always available.
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
			$total_count_redirects = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i', $redirects_table ) );
		}

		$increment = 200;
		global $post;

		// === Import settings ===//
		$wds_onpage_options   = get_option( 'wds_onpage_options' );
		$wds_social_options   = get_option( 'wds_social_options' );
		$wds_sitemap_options  = get_option( 'wds_sitemap_options' );
		$wds_settings_options = get_option( 'wds_settings_options' );
		$wds_schema_options   = get_option( 'wds_schema_options' );
		// `wds-advanced` is a single option containing several SmartCrawl sub-modules
		// (autolinks, redirects, woocommerce, breadcrumbs, ...). We only consume the
		// breadcrumbs slice today.
		$wds_advanced_options = get_option( 'wds-advanced' );

		$seopress_titles   = get_option( 'seopress_titles_option_name' );
		$seopress_xml_sitemap = get_option( 'seopress_xml_sitemap_option_name' );
		$seopress_social   = get_option( 'seopress_social_option_name' );
		$seopress_advanced = get_option( 'seopress_advanced_option_name' );
		$seopress_pro      = get_option( 'seopress_pro_option_name' );
		$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
		$taxonomies = seopress_get_service( 'WordPressData' )->getTaxonomies();

		// SmartCrawl's separator is either a literal character or a preset key (e.g. "pipe").
		// We collect both inside the loop and resolve once after.
		$smart_crawl_separator_literal = null;
		$smart_crawl_separator_preset  = null;

		if ( ! empty( $wds_onpage_options ) ) {
			foreach ( $wds_onpage_options as $key => $value ) {
				// Home title.
				if ( 'title-home' === $key ) {
					$seopress_titles['seopress_titles_home_site_title'] = seopress_smart_crawl_translate_template( $value );
				}
				// Home description.
				if ( 'metadesc-home' === $key ) {
					$seopress_titles['seopress_titles_home_site_desc'] = seopress_smart_crawl_translate_template( $value );
				}
				// Home default OG image. Used as the site-wide fallback when no per-post image is set.
				if ( 'og-images-home' === $key && is_array( $value ) && ! empty( $value[0] ) ) {
					$home_og_attachment_id = (int) $value[0];
					$home_og_url           = wp_get_attachment_url( $home_og_attachment_id );
					if ( ! empty( $home_og_url ) ) {
						$seopress_social['seopress_social_facebook_img']               = esc_url_raw( $home_og_url );
						$seopress_social['seopress_social_facebook_img_attachment_id'] = $home_og_attachment_id;
					}
				}
				// Home default X (Twitter) card image.
				if ( 'twitter-images-home' === $key && is_array( $value ) && ! empty( $value[0] ) ) {
					$home_tw_url = wp_get_attachment_url( (int) $value[0] );
					if ( ! empty( $home_tw_url ) ) {
						$seopress_social['seopress_social_twitter_card_img'] = esc_url_raw( $home_tw_url );
					}
				}
				// Separator. SmartCrawl 3.x stores a preset key (`pipe`, `dash`, ...) in
				// `preset-separator` and leaves `separator` empty; older versions store
				// the literal character in `separator`. We collect both and resolve below,
				// preferring the literal when present.
				if ( 'separator' === $key && '' !== $value && null !== $value ) {
					$smart_crawl_separator_literal = (string) $value;
				}
				if ( 'preset-separator' === $key && ! empty( $value ) ) {
					$smart_crawl_separator_preset = (string) $value;
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
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['title'] = seopress_smart_crawl_translate_template( $value );
					}
					// Single description.
					if ( 'metadesc-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_single_titles'][ $seopress_cpt_key ]['description'] = seopress_smart_crawl_translate_template( $value );
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
					// Default OG Image per CPT.
					if ( 'og-images-' . $seopress_cpt_key === $key && is_array( $value ) && ! empty( $value[0] ) ) {
						$img_url = wp_get_attachment_url( (int) $value[0] );
						if ( ! empty( $img_url ) ) {
							$seopress_social['seopress_social_facebook_img_cpt'][ $seopress_cpt_key ] = esc_url_raw( $img_url );
						}
					}
					// Archive title.
					if ( 'title-pt-archive-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['title'] = seopress_smart_crawl_translate_template( $value );
					}
					// Archive description.
					if ( 'metadesc-pt-archive-' . $seopress_cpt_key === $key ) {
						$seopress_titles['seopress_titles_archive_titles'][ $seopress_cpt_key ]['description'] = seopress_smart_crawl_translate_template( $value );
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
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['title'] = seopress_smart_crawl_translate_template( $value );
					}
					// Tax description.
					if ( 'metadesc-' . $seopress_tax_key === $key ) {
						$seopress_titles['seopress_titles_tax_titles'][ $seopress_tax_key ]['description'] = seopress_smart_crawl_translate_template( $value );
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
					$seopress_titles['seopress_titles_archives_author_title'] = seopress_smart_crawl_translate_template( $value );
				}
				if ( 'metadesc-author' === $key ) {
					$seopress_titles['seopress_titles_archives_author_desc'] = seopress_smart_crawl_translate_template( $value );
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
					$seopress_titles['seopress_titles_archives_date_title'] = seopress_smart_crawl_translate_template( $value );
				}
				if ( 'metadesc-date' === $key ) {
					$seopress_titles['seopress_titles_archives_date_desc'] = seopress_smart_crawl_translate_template( $value );
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
					$seopress_titles['seopress_titles_archives_search_title'] = seopress_smart_crawl_translate_template( $value );
				}
				if ( 'metadesc-search' === $key ) {
					$seopress_titles['seopress_titles_archives_search_desc'] = seopress_smart_crawl_translate_template( $value );
				}
				// 404.
				if ( 'title-404' === $key ) {
					$seopress_titles['seopress_titles_archives_404_title'] = seopress_smart_crawl_translate_template( $value );
				}
				if ( 'metadesc-404' === $key ) {
					$seopress_titles['seopress_titles_archives_404_desc'] = seopress_smart_crawl_translate_template( $value );
				}
			}

			// SmartCrawl's "Homepage" tab edits the per-post meta of the static front page
			// when one is configured, not the global `title-home` option. SEOPress separates
			// the two: post-level meta lives on the post, global home title/desc live in the
			// options. To match SmartCrawl's UX expectation (override visible in the homepage
			// settings panel), copy the static front page's `_wds_*` overrides into the
			// global SEOPress home options as well. The per-post copy is handled by the
			// post pagination loop below.
			if ( 'page' === get_option( 'show_on_front' ) ) {
				$front_id = (int) get_option( 'page_on_front' );
				if ( $front_id > 0 ) {
					$front_title    = get_post_meta( $front_id, '_wds_title', true );
					$front_metadesc = get_post_meta( $front_id, '_wds_metadesc', true );
					if ( '' !== $front_title ) {
						$seopress_titles['seopress_titles_home_site_title'] = seopress_smart_crawl_translate_template( $front_title );
					}
					if ( '' !== $front_metadesc ) {
						$seopress_titles['seopress_titles_home_site_desc'] = seopress_smart_crawl_translate_template( $front_metadesc );
					}

					$front_og = get_post_meta( $front_id, '_wds_opengraph', true );
					if ( is_array( $front_og ) && ! empty( $front_og['images'][0] ) ) {
						$front_og_id  = (int) $front_og['images'][0];
						$front_og_url = wp_get_attachment_url( $front_og_id );
						if ( ! empty( $front_og_url ) ) {
							$seopress_social['seopress_social_facebook_img']               = esc_url_raw( $front_og_url );
							$seopress_social['seopress_social_facebook_img_attachment_id'] = $front_og_id;
						}
					}

					$front_tw = get_post_meta( $front_id, '_wds_twitter', true );
					if ( is_array( $front_tw ) && ! empty( $front_tw['images'][0] ) ) {
						$front_tw_url = wp_get_attachment_url( (int) $front_tw['images'][0] );
						if ( ! empty( $front_tw_url ) ) {
							$seopress_social['seopress_social_twitter_card_img'] = esc_url_raw( $front_tw_url );
						}
					}
				}
			}

			// Resolve the SmartCrawl separator. Literal value wins over preset; if only the
			// preset is set, map it to the matching character. We override whatever SEOPress's
			// init put there since the customer's preference takes precedence after a migration.
			if ( null !== $smart_crawl_separator_literal ) {
				$seopress_titles['seopress_titles_sep'] = sanitize_text_field( $smart_crawl_separator_literal );
			} elseif ( null !== $smart_crawl_separator_preset ) {
				$separator_presets = array(
					'pipe'       => '|',
					'dash'       => '-',
					'mdash'      => '—',
					'ndash'      => '–',
					'bullet'     => '•',
					'middle-dot' => '·',
					'colon'      => ':',
					'tilde'      => '~',
					'greater'    => '>',
					'arrow'      => '→',
				);
				if ( isset( $separator_presets[ $smart_crawl_separator_preset ] ) ) {
					$seopress_titles['seopress_titles_sep'] = $separator_presets[ $smart_crawl_separator_preset ];
				}
			}
		}

		// Import social.
		// Note: in SmartCrawl 3.x, social accounts (Twitter handle, Facebook URL, ...),
		// Facebook App ID, organization name and schema type live in `wds_social_options`.
		// Older SmartCrawl versions stored some of these in `wds_schema_options`,
		// so we keep both reads (this one for 3.x, the `wds_schema_options` loop below for legacy).
		if ( ! empty( $wds_social_options ) ) {
			foreach ( $wds_social_options as $key => $value ) {
				// OG enable.
				if ( 'og-enable' === $key ) {
					if ( true === $value || 1 === $value || '1' === $value ) {
						$seopress_social['seopress_social_facebook_og'] = '1';
					} else {
						unset( $seopress_social['seopress_social_facebook_og'] );
					}
				}
				// Twitter Cards enable. SmartCrawl 3.x renamed `twitter-enable` to `twitter-card-enable`.
				if ( 'twitter-card-enable' === $key || 'twitter-enable' === $key ) {
					if ( true === $value || 1 === $value || '1' === $value ) {
						$seopress_social['seopress_social_twitter_card'] = '1';
					} else {
						unset( $seopress_social['seopress_social_twitter_card'] );
					}
				}
				// Pinterest verify.
				if ( 'pinterest-verify' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_pinterest'] = sanitize_text_field( $value );
				}
				// Organization logo. In 3.x this is already a URL string; in older versions it was an attachment ID.
				if ( 'organization_logo' === $key && ! empty( $value ) ) {
					if ( is_numeric( $value ) ) {
						$img_url = wp_get_attachment_url( (int) $value );
						if ( ! empty( $img_url ) ) {
							$seopress_social['seopress_social_knowledge_img'] = esc_url_raw( $img_url );
						}
					} else {
						$seopress_social['seopress_social_knowledge_img'] = esc_url_raw( $value );
					}
				}
				// Organization name.
				if ( 'organization_name' === $key ) {
					$seopress_social['seopress_social_knowledge_name'] = sanitize_text_field( $value );
				}
				// Schema type discriminator (Organization or Person). The subtype (Corporation, NGO, ...)
				// is resolved below from `wds_schema_options.organization_type`.
				if ( 'schema_type' === $key && ! empty( $value ) ) {
					$seopress_social['seopress_social_knowledge_type'] = sanitize_text_field( $value );
				}
				// Twitter username.
				if ( 'twitter_username' === $key ) {
					$seopress_social['seopress_social_accounts_twitter'] = sanitize_text_field( $value );
				}
				// Social profile URLs.
				if ( 'facebook_url' === $key ) {
					$seopress_social['seopress_social_accounts_facebook'] = esc_url_raw( $value );
				}
				if ( 'instagram_url' === $key ) {
					$seopress_social['seopress_social_accounts_instagram'] = esc_url_raw( $value );
				}
				if ( 'linkedin_url' === $key ) {
					$seopress_social['seopress_social_accounts_linkedin'] = esc_url_raw( $value );
				}
				if ( 'pinterest_url' === $key ) {
					$seopress_social['seopress_social_accounts_pinterest'] = esc_url_raw( $value );
				}
				if ( 'youtube_url' === $key ) {
					$seopress_social['seopress_social_accounts_youtube'] = esc_url_raw( $value );
				}
				// Facebook App ID.
				if ( 'fb-app-id' === $key ) {
					$seopress_social['seopress_social_facebook_app_id'] = sanitize_text_field( $value );
				}
			}
		}

		// Import XML sitemap.
		if ( ! empty( $wds_sitemap_options ) ) {
			// Master enable. SmartCrawl exposes its sitemap module through `override-native`
			// (replaces WordPress's core sitemap) or a top-level `active` flag in older versions.
			// Either implies the user wants a SEOPress sitemap once migrated.
			$smart_crawl_sitemap_on = false;
			if ( isset( $wds_sitemap_options['override-native'] ) && ! empty( $wds_sitemap_options['override-native'] ) ) {
				$smart_crawl_sitemap_on = true;
			}
			if ( isset( $wds_sitemap_options['active'] ) && ! empty( $wds_sitemap_options['active'] ) ) {
				$smart_crawl_sitemap_on = true;
			}
			if ( $smart_crawl_sitemap_on ) {
				$seopress_xml_sitemap['seopress_xml_sitemap_general_enable'] = '1';
			}

			foreach ( $wds_sitemap_options as $key => $value ) {
				// Post types in sitemap. SmartCrawl stores `not_in_sitemap=true` to exclude a CPT,
				// so we include in SEOPress only when the SmartCrawl flag is false/empty.
				foreach ( $post_types as $seopress_cpt_key => $seopress_cpt_value ) {
					if ( 'post_types-' . $seopress_cpt_key . '-not_in_sitemap' === $key ) {
						if ( empty( $value ) ) {
							$seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] = '1';
						} else {
							unset( $seopress_xml_sitemap['seopress_xml_sitemap_post_types_list'][ $seopress_cpt_key ]['include'] );
						}
					}
				}

				// Taxonomies in sitemap.
				foreach ( $taxonomies as $seopress_tax_key => $seopress_tax_value ) {
					if ( 'taxonomies-' . $seopress_tax_key . '-not_in_sitemap' === $key ) {
						if ( empty( $value ) ) {
							$seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][ $seopress_tax_key ]['include'] = '1';
						} else {
							unset( $seopress_xml_sitemap['seopress_xml_sitemap_taxonomies_list'][ $seopress_tax_key ]['include'] );
						}
					}
				}

				// News Sitemap.
				if ( 'enable-news-sitemap' === $key ) {
					if ( true === $value || 1 === $value || '1' === $value ) {
						$seopress_pro['seopress_news_enable'] = '1';
					} else {
						unset( $seopress_pro['seopress_news_enable'] );
					}
				}
				// News publication name. SmartCrawl 3.x renamed `news-publication-name` to `news-publication`.
				if ( 'news-publication' === $key || 'news-publication-name' === $key ) {
					$seopress_pro['seopress_news_name'] = sanitize_text_field( $value );
				}

				// News post types. The value is a list of CPT slugs.
				if ( 'news-sitemap-included-post-types' === $key && is_array( $value ) && ! empty( $value ) ) {
					foreach ( $value as $news_cpt ) {
						if ( ! is_string( $news_cpt ) || '' === $news_cpt ) {
							continue;
						}
						$seopress_pro['seopress_news_name_post_types_list'][ $news_cpt ]['include'] = '1';
					}
				}

				// Image Sitemap.
				if ( 'sitemap-images' === $key ) {
					if ( true === $value || 1 === $value || '1' === $value ) {
						$seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] = '1';
					} else {
						unset( $seopress_xml_sitemap['seopress_xml_sitemap_img_enable'] );
					}
				}
			}
		}

		// Schema (organization metadata that lives in `wds_schema_options`).
		// Account URLs and `schema_type` are handled above from `wds_social_options` for
		// SmartCrawl 3.x; we keep the same reads here as a fallback for older versions.
		if ( ! empty( $wds_schema_options ) ) {
			$allowed_knowledge_types = array(
				'Person'                  => 'Person',
				'Organization'            => 'Organization',
				'Corporation'             => 'Corporation',
				'LocalBusiness'           => 'LocalBusiness',
				'OnlineBusiness'          => 'OnlineBusiness',
				'OnlineStore'             => 'OnlineStore',
				'EducationalOrganization' => 'EducationalOrganization',
				'GovernmentOrganization'  => 'GovernmentOrganization',
				'NGO'                     => 'NGO',
				'NewsMediaOrganization'   => 'NewsMediaOrganization',
			);

			foreach ( $wds_schema_options as $key => $value ) {
				// Legacy: account URLs in `wds_schema_options` (older SmartCrawl).
				if ( 'twitter_username' === $key && empty( $seopress_social['seopress_social_accounts_twitter'] ) ) {
					$seopress_social['seopress_social_accounts_twitter'] = sanitize_text_field( $value );
				}
				if ( 'facebook_url' === $key && empty( $seopress_social['seopress_social_accounts_facebook'] ) ) {
					$seopress_social['seopress_social_accounts_facebook'] = esc_url_raw( $value );
				}
				if ( 'instagram_url' === $key && empty( $seopress_social['seopress_social_accounts_instagram'] ) ) {
					$seopress_social['seopress_social_accounts_instagram'] = esc_url_raw( $value );
				}
				if ( 'linkedin_url' === $key && empty( $seopress_social['seopress_social_accounts_linkedin'] ) ) {
					$seopress_social['seopress_social_accounts_linkedin'] = esc_url_raw( $value );
				}
				if ( 'pinterest_url' === $key && empty( $seopress_social['seopress_social_accounts_pinterest'] ) ) {
					$seopress_social['seopress_social_accounts_pinterest'] = esc_url_raw( $value );
				}
				if ( 'youtube_url' === $key && empty( $seopress_social['seopress_social_accounts_youtube'] ) ) {
					$seopress_social['seopress_social_accounts_youtube'] = esc_url_raw( $value );
				}
				if ( 'fb-app-id' === $key && empty( $seopress_social['seopress_social_facebook_app_id'] ) ) {
					$seopress_social['seopress_social_facebook_app_id'] = sanitize_text_field( $value );
				}
				if ( 'schema_type' === $key && empty( $seopress_social['seopress_social_knowledge_type'] ) ) {
					$seopress_social['seopress_social_knowledge_type'] = sanitize_text_field( $value );
				}
				if ( 'organization_name' === $key && empty( $seopress_social['seopress_social_knowledge_name'] ) ) {
					$seopress_social['seopress_social_knowledge_name'] = sanitize_text_field( $value );
				}
				// Organization subtype (SmartCrawl 3.x stores the Schema.org subtype here,
				// e.g. Corporation, NGO, LocalBusiness). Only apply when the discriminator is Organization.
				if ( 'organization_type' === $key && ! empty( $value ) ) {
					$current_type = isset( $seopress_social['seopress_social_knowledge_type'] ) ? $seopress_social['seopress_social_knowledge_type'] : '';
					if ( 'Person' !== $current_type && isset( $allowed_knowledge_types[ $value ] ) ) {
						$seopress_social['seopress_social_knowledge_type'] = $allowed_knowledge_types[ $value ];
					}
				}
				// Organization description.
				if ( 'organization_description' === $key ) {
					$seopress_social['seopress_social_knowledge_desc'] = sanitize_text_field( $value );
				}
				// Organization contact type.
				if ( 'organization_contact_type' === $key ) {
					$type = array(
						'customer support'    => 'customer support',
						'technical support'   => 'technical support',
						'billing support'     => 'billing support',
						'bill payment'        => 'bill payment',
						'sales'               => 'sales',
						'credit card support' => 'credit card support',
						'emergency'           => 'emergency',
						'baggage tracking'    => 'baggage tracking',
						'roadside assistance' => 'roadside assistance',
						'package tracking'    => 'package tracking',
					);
					if ( isset( $type[ $value ] ) ) {
						$seopress_social['seopress_social_knowledge_contact_type'] = sanitize_text_field( $type[ $value ] );
					}
				}
				// Organization phone.
				if ( 'organization_phone_number' === $key ) {
					$seopress_social['seopress_social_knowledge_phone'] = sanitize_text_field( $value );
				}
			}
		}

		// Import advanced.
		if ( ! empty( $wds_settings_options ) ) {
			foreach ( $wds_settings_options as $key => $value ) {
				// Google verification.
				if ( 'verification-google-meta' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_google'] = sanitize_text_field( $value );
				}
				// Bing verification.
				if ( 'verification-bing-meta' === $key ) {
					$seopress_advanced['seopress_advanced_advanced_bing'] = sanitize_text_field( $value );
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

		// Breadcrumbs (PRO). SmartCrawl stores its breadcrumb config inside the
		// `wds-advanced` option under the `breadcrumbs` key. SEOPress breadcrumb settings
		// live in `seopress_pro_option_name` under the `seopress_breadcrumbs_*` keys.
		if ( is_array( $wds_advanced_options ) && ! empty( $wds_advanced_options['breadcrumbs'] ) && is_array( $wds_advanced_options['breadcrumbs'] ) ) {
			$wds_bc = $wds_advanced_options['breadcrumbs'];

			// Active toggle.
			if ( ! empty( $wds_bc['active'] ) ) {
				$seopress_pro['seopress_breadcrumbs_enable']      = '1';
				$seopress_pro['seopress_breadcrumbs_json_enable'] = '1';
			}

			// Separator: prefer the literal `custom_sep` when the preset is "custom",
			// otherwise map the preset key to a character.
			$bc_separator_presets = array(
				'pipe'         => '|',
				'dash'         => '-',
				'mdash'        => '—',
				'ndash'        => '–',
				'bullet'       => '•',
				'middle-dot'   => '·',
				'colon'        => ':',
				'tilde'        => '~',
				'greater-than' => '>',
				'less-than'    => '<',
				'arrow'        => '→',
				'slash'        => '/',
				'backslash'    => '\\',
			);
			if ( ! empty( $wds_bc['custom_sep'] ) ) {
				$seopress_pro['seopress_breadcrumbs_separator'] = sanitize_text_field( $wds_bc['custom_sep'] );
			} elseif ( ! empty( $wds_bc['separator'] ) && isset( $bc_separator_presets[ $wds_bc['separator'] ] ) ) {
				$seopress_pro['seopress_breadcrumbs_separator'] = $bc_separator_presets[ $wds_bc['separator'] ];
			}

			// Home label (free text).
			if ( ! empty( $wds_bc['home_label'] ) ) {
				$seopress_pro['seopress_breadcrumbs_i18n_home'] = sanitize_text_field( $wds_bc['home_label'] );
			}

			// 404 label (lives inside the `labels` sub-array in SmartCrawl).
			if ( ! empty( $wds_bc['labels']['404'] ) ) {
				$seopress_pro['seopress_breadcrumbs_i18n_404'] = sanitize_text_field( $wds_bc['labels']['404'] );
			}
		}

		update_option( 'seopress_titles_option_name', $seopress_titles );
		update_option( 'seopress_xml_sitemap_option_name', $seopress_xml_sitemap );
		update_option( 'seopress_social_option_name', $seopress_social );
		update_option( 'seopress_advanced_option_name', $seopress_advanced );
		update_option( 'seopress_pro_option_name', $seopress_pro );

		if ( $in_redirects_phase ) {
			// Phase 3: import SmartCrawl redirects table into the seopress_404 CPT.
			$count_items = $total_count_posts + $total_count_terms + $redirect_offset;

			if ( $can_import_redirects ) {
				// SEOPress requires WP 6.5+, so the %i identifier placeholder is always available.
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
						'SELECT id, title, source, path, destination, type, options FROM %i ORDER BY id ASC LIMIT %d OFFSET %d',
						$redirects_table,
						$increment,
						$redirect_offset
					)
				);

				if ( ! empty( $rows ) ) {
					$valid_status_codes = array( '301', '302', '307', '308', '410', '451' );

					foreach ( $rows as $row ) {
						$source = '';
						if ( ! empty( $row->source ) ) {
							$source = $row->source;
						} elseif ( ! empty( $row->path ) ) {
							$source = $row->path;
						}

						if ( '' === $source ) {
							continue;
						}

						// Resolve destination: JSON-encoded object for internal targets, JSON string URL for external.
						$destination_url = '';
						$decoded         = json_decode( $row->destination, true );
						if ( is_array( $decoded ) && isset( $decoded['id'] ) ) {
							$permalink = get_permalink( (int) $decoded['id'] );
							if ( ! empty( $permalink ) ) {
								$destination_url = $permalink;
							}
						} elseif ( is_string( $decoded ) && '' !== $decoded ) {
							$destination_url = $decoded;
						} elseif ( null === $decoded && '' !== $row->destination ) {
							// Some rows store the URL as a plain (non-JSON) string.
							$destination_url = $row->destination;
						}

						if ( '' === $destination_url ) {
							continue;
						}

						// Skip if a redirection with the same source already exists (idempotent re-runs).
						$existing = get_posts(
							array(
								'post_type'      => 'seopress_404',
								'post_status'    => 'any',
								'posts_per_page' => 1,
								'title'          => $source,
								'fields'         => 'ids',
								'no_found_rows'  => true,
							)
						);
						if ( ! empty( $existing ) ) {
							continue;
						}

						// SEOPress stores the matched URL pattern in `post_title`.
						$post_id = wp_insert_post(
							array(
								'post_title'  => $source,
								'post_name'   => sanitize_title( $source ),
								'post_type'   => 'seopress_404',
								'post_status' => 'publish',
							),
							true
						);

						if ( is_wp_error( $post_id ) || ! $post_id ) {
							continue;
						}

						$status = (string) $row->type;
						if ( ! in_array( $status, $valid_status_codes, true ) ) {
							$status = '301';
						}

						update_post_meta( $post_id, '_seopress_redirections_value', esc_url_raw( $destination_url ) );
						update_post_meta( $post_id, '_seopress_redirections_type', $status );
						update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );

						// Regex flag from the `options` JSON, when present.
						if ( ! empty( $row->options ) ) {
							$opts = json_decode( $row->options, true );
							if ( is_array( $opts ) && ! empty( $opts['regex'] ) ) {
								update_post_meta( $post_id, '_seopress_redirections_enabled_regex', 'yes' );
							}
						}
					}
				}
			}

			$redirect_offset += $increment;

			if ( ! $can_import_redirects || $redirect_offset >= $total_count_redirects ) {
				$offset      = 'done';
				$count_items = $total_count_posts + $total_count_terms + $total_count_redirects;
			} else {
				$offset      = 'redirects:' . $redirect_offset;
				$count_items = $total_count_posts + $total_count_terms + $redirect_offset;
			}
		} elseif ( $offset > $total_count_posts ) {
			wp_reset_postdata();
			$count_items = $total_count_posts;

			$smart_crawl_query_terms = get_option( 'wds_taxonomy_meta' );

			if ( $smart_crawl_query_terms ) {
				foreach ( $smart_crawl_query_terms as $taxonomies => $taxonomie ) {
					foreach ( $taxonomie as $term_id => $term_value ) {
						if ( ! empty( $term_value['wds_title'] ) ) { // Import title tag.
							update_term_meta( $term_id, '_seopress_titles_title', seopress_smart_crawl_translate_template( $term_value['wds_title'] ) );
						}
						if ( ! empty( $term_value['wds_desc'] ) ) { // Import meta desc.
							update_term_meta( $term_id, '_seopress_titles_desc', seopress_smart_crawl_translate_template( $term_value['wds_desc'] ) );
						}
						if ( ! empty( $term_value['opengraph']['title'] ) ) { // Import Facebook Title.
							update_term_meta( $term_id, '_seopress_social_fb_title', seopress_smart_crawl_translate_template( $term_value['opengraph']['title'] ) );
						}
						if ( ! empty( $term_value['opengraph']['description'] ) ) { // Import Facebook Desc.
							update_term_meta( $term_id, '_seopress_social_fb_desc', seopress_smart_crawl_translate_template( $term_value['opengraph']['description'] ) );
						}
						if ( ! empty( $term_value['opengraph']['images'] ) ) { // Import Facebook Image.
							$image_id = $term_value['opengraph']['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_term_meta( $term_id, '_seopress_social_fb_img', esc_url_raw( $img_url ) );
							}
						}
						if ( ! empty( $term_value['twitter']['title'] ) ) { // Import Facebook Title.
							update_term_meta( $term_id, '_seopress_social_twitter_title', seopress_smart_crawl_translate_template( $term_value['twitter']['title'] ) );
						}
						if ( ! empty( $term_value['twitter']['description'] ) ) { // Import Facebook Desc.
							update_term_meta( $term_id, '_seopress_social_twitter_desc', seopress_smart_crawl_translate_template( $term_value['twitter']['description'] ) );
						}
						if ( ! empty( $term_value['twitter']['images'] ) ) { // Import Facebook Image.
							$image_id = $term_value['twitter']['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_term_meta( $term_id, '_seopress_social_twitter_img', esc_url_raw( $img_url ) );
							}
						}
						if ( ! empty( $term_value['wds_noindex'] ) && 'noindex' === $term_value['wds_noindex'] ) { // Import Robots NoIndex.
							update_term_meta( $term_id, '_seopress_robots_index', 'yes' );
						}
						if ( ! empty( $term_value['wds_nofollow'] ) && 'nofollow' === $term_value['wds_nofollow'] ) { // Import Robots NoFollow.
							update_term_meta( $term_id, '_seopress_robots_follow', 'yes' );
						}
						if ( '' !== $term_value['wds_canonical'] ) { // Import Canonical URL.
							update_term_meta( $term_id, '_seopress_robots_canonical', esc_url_raw( $term_value['wds_canonical'] ) );
						}
					}
				}
			}
			wp_reset_postdata();

			// Move on to redirects phase if available, otherwise we're done.
			if ( $can_import_redirects && $total_count_redirects > 0 ) {
				$offset      = 'redirects:0';
				$count_items = $total_count_posts + $total_count_terms;
			} else {
				$offset      = 'done';
				$count_items = $total_count_posts + $total_count_terms;
			}
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
						update_post_meta( $post->ID, '_seopress_titles_title', seopress_smart_crawl_translate_template( get_post_meta( $post->ID, '_wds_title', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_metadesc', true ) ) { // Import meta desc.
						update_post_meta( $post->ID, '_seopress_titles_desc', seopress_smart_crawl_translate_template( get_post_meta( $post->ID, '_wds_metadesc', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_opengraph', true ) ) {
						$_wds_opengraph = get_post_meta( $post->ID, '_wds_opengraph', true );
						if ( ! empty( $_wds_opengraph['title'] ) ) {
							update_post_meta( $post->ID, '_seopress_social_fb_title', seopress_smart_crawl_translate_template( $_wds_opengraph['title'] ) ); // Import Facebook Title.
						}
						if ( ! empty( $_wds_opengraph['description'] ) ) { // Import Facebook Desc.
							update_post_meta( $post->ID, '_seopress_social_fb_desc', seopress_smart_crawl_translate_template( $_wds_opengraph['description'] ) );
						}
						if ( ! empty( $_wds_opengraph['images'] ) ) { // Import Facebook Image.
							$image_id = $_wds_opengraph['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_post_meta( $post->ID, '_seopress_social_fb_img', esc_url_raw( $img_url ) );
							}
						}
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_twitter', true ) ) { // Import Twitter Title.
						$_wds_twitter = get_post_meta( $post->ID, '_wds_twitter', true );
						if ( ! empty( $_wds_twitter['title'] ) ) {
							update_post_meta( $post->ID, '_seopress_social_twitter_title', seopress_smart_crawl_translate_template( $_wds_twitter['title'] ) ); // Import Twitter Title.
						}
						if ( ! empty( $_wds_twitter['description'] ) ) { // Import Twitter Desc.
							update_post_meta( $post->ID, '_seopress_social_twitter_desc', seopress_smart_crawl_translate_template( $_wds_twitter['description'] ) );
						}
						if ( ! empty( $_wds_twitter['images'] ) ) { // Import Twitter Image.
							$image_id = $_wds_twitter['images'][0];
							$img_url  = wp_get_attachment_url( $image_id );

							if ( isset( $img_url ) && '' !== $img_url ) {
								update_post_meta( $post->ID, '_seopress_social_twitter_img', esc_url_raw( $img_url ) );
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
						update_post_meta( $post->ID, '_seopress_robots_canonical', esc_url_raw( get_post_meta( $post->ID, '_wds_canonical', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_redirect', true ) ) { // Import Redirect URL.
						update_post_meta( $post->ID, '_seopress_redirections_enabled', 'yes' );
						update_post_meta( $post->ID, '_seopress_redirections_type', '301' );
						update_post_meta( $post->ID, '_seopress_redirections_value', esc_url_raw( get_post_meta( $post->ID, '_wds_redirect', true ) ) );
					}
					if ( '' !== get_post_meta( $post->ID, '_wds_focus-keywords', true ) ) { // Import Focus Keywords.
						update_post_meta( $post->ID, '_seopress_analysis_target_kw', sanitize_text_field( get_post_meta( $post->ID, '_wds_focus-keywords', true ) ) );
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
		$data['total'] = $total_count_posts + $total_count_terms + $total_count_redirects;

		$data['offset'] = $offset;
		wp_send_json_success( $data );
		exit();
	}
}
add_action( 'wp_ajax_seopress_smart_crawl_migration', 'seopress_smart_crawl_migration' );

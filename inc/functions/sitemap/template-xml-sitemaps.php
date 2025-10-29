<?php
/**
 * XML Sitemap index template
 *
 * @package Sitemaps
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

// Headers.
seopress_get_service( 'SitemapHeaders' )->printHeaders();

// WPML - Home URL.
if ( 2 === apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
	add_filter(
		'seopress_sitemaps_home_url',
		function ( $home_url ) {
			$home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
			return trailingslashit( $home_url );
		}
	);
} else {
	add_filter( 'wpml_get_home_url', 'seopress_remove_wpml_home_url_filter', 20, 5 );
}

add_filter(
	'seopress_sitemaps_index_cpt_query',
	function ( $args ) {
		global $sitepress, $sitepress_settings;

		$sitepress_settings['auto_adjust_ids'] = 0;
		remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		remove_filter( 'category_link', array( $sitepress, 'category_link_adjust_id' ), 1 );

		return $args;
	}
);

add_action(
	'the_post',
	function ( $post ) {
		$language = apply_filters(
			'wpml_element_language_code',
			null,
			array(
				'element_id'   => $post->ID,
				'element_type' => 'page',
			)
		);
		do_action( 'wpml_switch_language', $language );
	}
);

/**
 * Polylang: remove hidden languages
 *
 * @param array $args Arguments.
 * @return array Arguments.
 */
function seopress_pll_exclude_hidden_lang( $args ) {
	if ( function_exists( 'get_languages_list' ) && is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
		$languages = PLL()->model->get_languages_list();
		if ( wp_list_filter( $languages, array( 'active' => false ) ) ) {
			$args['lang'] = wp_list_pluck( wp_list_filter( $languages, array( 'active' => false ), 'NOT' ), 'slug' );
		}
	}
	return $args;
}

/**
 * WPML: remove hidden languages
 *
 * @param string $url URL.
 * @return string URL.
 */
function seopress_wpml_exclude_hidden_lang( $url ) {
	// @credits WPML compatibility team
	if ( function_exists( 'get_setting' ) && is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) { // WPML.
		global $sitepress, $sitepress_settings;

		// Check that at least ID is set in post object.
		if ( ! isset( $post->ID ) ) {
			return $url;
		}

		// Get list of hidden languages.
		$hidden_languages = $sitepress->get_setting( 'hidden_languages', array() );

		// If there are no hidden languages return original URL.
		if ( empty( $hidden_languages ) ) {
			return $url;
		}

		// Get language information for post.
		$language_info = $sitepress->post_translations()->get_element_lang_code( $post->ID );

		// If language code is one of the hidden languages return null to skip the post.
		if ( in_array( $language_info, $hidden_languages, true ) ) {
			return null;
		}
	}
}

/**
 * XML Sitemap index template
 *
 * @return string XML Sitemap index template
 */
function seopress_xml_sitemap_index() {
	remove_all_filters( 'pre_get_posts' );

	$home_url = home_url() . '/';

	$home_url = apply_filters( 'seopress_sitemaps_home_url', $home_url );

	$seopress_sitemaps  = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .= '<?xml-stylesheet type="text/xsl" href="' . $home_url . 'sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	// CPT.
	if ( '' !== seopress_get_service( 'SitemapOption' )->getPostTypesList() ) {
		if ( ! empty( seopress_get_service( 'SitemapOption' )->getPostTypesList() ) ) {
			foreach ( seopress_get_service( 'SitemapOption' )->getPostTypesList() as $cpt_key => $cpt_value ) {
				foreach ( $cpt_value as $_cpt_key => $_cpt_value ) {
					if ( '1' === $_cpt_value ) {
						$args = array(
							'posts_per_page' => -1,
							'post_type'      => $cpt_key,
							'post_status'    => 'publish',
							'fields'         => 'ids',
							'lang'           => '',
							'has_password'   => false,
						);

						// Polylang: exclude hidden languages.
						$args = seopress_pll_exclude_hidden_lang( $args );

						$args = apply_filters( 'seopress_sitemaps_index_post_types_query', $args, $cpt_key );

						$count_posts = count( get_posts( $args ) );

						// Max posts per paginated sitemap.
						$max = 1000;
						$max = apply_filters( 'seopress_sitemaps_max_posts_per_sitemap', $max );

						if ( $count_posts >= $max ) {
							$max_loop = $count_posts / $max;
						} else {
							$max_loop = 1;
						}

						// Performance optimization: Get all lastmod dates in a single query instead of one query per page.
						// This avoids expensive OFFSET queries that become slower with each page. Updated in 9.3.0.
						global $wpdb;
						$cache_key_all     = 'seopress_sitemap_lastmod_all_' . $cpt_key . '_' . $max;
						$all_lastmod_dates = get_transient( $cache_key_all );

						if ( false === $all_lastmod_dates ) {
							// Get the first post_modified for each sitemap page in one query.
							// Then use a subquery with row numbering to get every Nth post efficiently.
							$sql = $wpdb->prepare(
								"SELECT post_modified
								FROM (
									SELECT p.post_modified,
										@row_num := @row_num + 1 AS row_num
									FROM $wpdb->posts p
									LEFT JOIN $wpdb->postmeta pm_canonical ON p.ID = pm_canonical.post_id
										AND pm_canonical.meta_key = '_seopress_robots_canonical'
									LEFT JOIN $wpdb->postmeta pm_noindex ON p.ID = pm_noindex.post_id
										AND pm_noindex.meta_key = '_seopress_robots_index'
									CROSS JOIN (SELECT @row_num := 0) AS init
									WHERE p.post_type = %s
									AND p.post_status = 'publish'
									AND (pm_canonical.meta_value IS NULL OR pm_canonical.meta_value = %s)
									AND (pm_noindex.meta_value IS NULL OR pm_noindex.meta_value != 'yes')
									ORDER BY p.post_modified DESC
								) AS numbered_posts
								WHERE (row_num - 1) %% %d = 0",
								$cpt_key,
								htmlspecialchars( urldecode( get_permalink( get_the_ID() ) ) ),
								$max
							);

							$all_lastmod_dates = $wpdb->get_col( $sql ); // phpcs:ignore

							// Cache for 1 hour to match existing behavior.
							$cache_duration = apply_filters( 'seopress_sitemaps_cache_duration', HOUR_IN_SECONDS );
							set_transient( $cache_key_all, $all_lastmod_dates, $cache_duration );
						}

						$paged = '';
						$i     = '';
						for ( $i = 0; $i < $max_loop; ++$i ) {
							if ( $i >= 1 && $i <= $max_loop ) {
								$paged = $i + 1;
							} else {
								$paged = 1;
							}

							// Get lastmod date from cached array (already fetched above).
							$last_mod_date = isset( $all_lastmod_dates[ $i ] ) ? $all_lastmod_dates[ $i ] : null;

							$seopress_sitemaps .= "\n";
							$seopress_sitemaps .= '<sitemap>';
							$seopress_sitemaps .= "\n";
							$seopress_sitemaps .= '<loc>';
							$seopress_sitemaps .= $home_url . $cpt_key . '-sitemap' . $paged . '.xml';
							$seopress_sitemaps .= '</loc>';
							$seopress_sitemaps .= "\n";

							if ( $last_mod_date ) {
								$seopress_sitemaps .= '<lastmod>';
								$seopress_sitemaps .= esc_html( mysql2date( 'c', $last_mod_date ) );
								$seopress_sitemaps .= '</lastmod>';
								$seopress_sitemaps .= "\n";
							}

							$seopress_sitemaps .= '</sitemap>';
						}
					}
				}
			}
		}
	}

	// Taxonomies.
	if ( '' !== seopress_get_service( 'SitemapOption' )->getTaxonomiesList() ) {
		// Init.
		$seopress_xml_terms_list = array();
		if ( ! empty( seopress_get_service( 'SitemapOption' )->getTaxonomiesList() ) ) {
			foreach ( seopress_get_service( 'SitemapOption' )->getTaxonomiesList() as $tax_key => $tax_value ) {
				foreach ( $tax_value as $_tax_key => $_tax_value ) {
					if ( '1' === $_tax_value ) {
						$args = array(
							'taxonomy'   => $tax_key,
							'hide_empty' => true,
							'lang'       => '',
							'fields'     => 'ids',
							'meta_query' => array(
								'relation' => 'OR',
								array(
									'key'     => '_seopress_robots_index',
									'value'   => '',
									'compare' => 'NOT EXISTS',
								),
								array(
									'key'     => '_seopress_robots_index',
									'value'   => 'yes',
									'compare' => '!=',
								),
							),
						);

						// Polylang: exclude hidden languages.
						$args = seopress_pll_exclude_hidden_lang( $args );

						$args = apply_filters( 'seopress_sitemaps_index_tax_query', $args, $tax_key );

						$terms_data  = get_terms( $args );
						$count_terms = 0;
						if ( is_array( $terms_data ) && ! is_wp_error( $terms_data ) ) {
							$count_terms = count( $terms_data );
						}

						// Max terms per paginated sitemap.
						$max = 1000;
						$max = apply_filters( 'seopress_sitemaps_max_terms_per_sitemap', $max );

						if ( $count_terms >= $max ) {
							$max_loop = $count_terms / $max;
						} else {
							$max_loop = 1;
						}

						$paged = '';
						$i     = '';
						for ( $i = 0; $i < $max_loop; ++$i ) {
							if ( $i >= 1 && $i <= $max_loop ) {
								$paged = $i + 1;
							} else {
								$paged = 1;
							}

							$seopress_sitemaps .= "\n";
							$seopress_sitemaps .= '<sitemap>';
							$seopress_sitemaps .= "\n";
							$seopress_sitemaps .= '<loc>';
							$seopress_sitemaps .= $home_url . $tax_key . '-sitemap' . $paged . '.xml';
							$seopress_sitemaps .= '</loc>';
							$seopress_sitemaps .= "\n";
							$seopress_sitemaps .= '</sitemap>';
						}
					}
				}
			}
		}
	}

	// Author sitemap.
	if ( '1' === seopress_get_service( 'SitemapOption' )->authorIsEnable() ) {
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<sitemap>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '<loc>';
		$seopress_sitemaps .= $home_url . 'author.xml';
		$seopress_sitemaps .= '</loc>';
		$seopress_sitemaps .= "\n";
		$seopress_sitemaps .= '</sitemap>';
	}

	$seopress_sitemaps = apply_filters( 'seopress_sitemaps_xml_index_item', $seopress_sitemaps, $home_url );

	// Custom sitemap.
	$custom_sitemap = null;
	$custom_sitemap = apply_filters( 'seopress_sitemaps_external_link', $custom_sitemap );
	if ( isset( $custom_sitemap ) ) {
		foreach ( $custom_sitemap as $key => $sitemap ) {
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '<sitemap>';
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '<loc>';
			$seopress_sitemaps .= $sitemap['sitemap_url'];
			$seopress_sitemaps .= '</loc>';
			if ( isset( $sitemap['sitemap_last_mod'] ) ) {
				$seopress_sitemaps .= "\n";
				$seopress_sitemaps .= '<lastmod>';
				$seopress_sitemaps .= $sitemap['sitemap_last_mod'];
				$seopress_sitemaps .= '</lastmod>';
			}
			$seopress_sitemaps .= "\n";
			$seopress_sitemaps .= '</sitemap>';
		}
	}

	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '</sitemapindex>';

	$seopress_sitemaps = apply_filters( 'seopress_sitemaps_xml_index', $seopress_sitemaps );

	return $seopress_sitemaps;
}
echo seopress_xml_sitemap_index(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XML output is properly escaped within function.

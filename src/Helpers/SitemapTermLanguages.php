<?php
/**
 * Sitemap term queries across every WPML language.
 *
 * @package SEOPress\Helpers
 */

namespace SEOPress\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Runs the term queries of the XML sitemaps over every WPML language.
 *
 * A taxonomy sitemap lists the terms of every language in the same file, so
 * the index has to count them the same way. WPML narrows a term query to the
 * current language in two places:
 *
 * - `SitePress::terms_clauses`, which joins `icl_translations` on the current
 *   language. The index counted with it on, so it listed too few pages.
 * - `pre_option_{$taxonomy}_children`, through which WPML serves the
 *   parent => children map of the current language only. WordPress reads that
 *   map to keep an empty parent whose children have posts. A sitemap request
 *   runs in the default language, so every translated parent looked childless
 *   and was dropped.
 *
 * Switching WPML to 'all' does not help on the front end: WPML resolves the
 * language from the request URL again. Both filters are therefore set aside
 * for the length of the query, then put back.
 *
 * A multidomain setup serves one sitemap per domain and language, so its term
 * queries keep the language filter.
 *
 * @since 10.4.0
 */
class SitemapTermLanguages {

	/**
	 * All-language hierarchy per taxonomy, for the current request.
	 *
	 * @var array<string, array{last_changed: string, children: array}>
	 */
	private static $hierarchies = array();

	/**
	 * Run a sitemap term query over every language.
	 *
	 * Without WPML, or on a multidomain WPML setup, the query runs untouched.
	 *
	 * @since 10.4.0
	 *
	 * @param array    $args  Term query arguments, `taxonomy` included.
	 * @param callable $query Receives the arguments and runs the query.
	 *
	 * @return mixed What `$query` returned.
	 */
	public static function run( array $args, callable $query ) {
		$taxonomies = isset( $args['taxonomy'] ) ? array_filter( (array) $args['taxonomy'], 'is_string' ) : array();

		if ( empty( $taxonomies ) || ! self::spans_all_languages( $taxonomies ) ) {
			return $query( $args );
		}

		global $sitepress;

		$clauses          = is_object( $sitepress ) ? array( $sitepress, 'terms_clauses' ) : null;
		$clauses_priority = $clauses ? has_filter( 'terms_clauses', $clauses ) : false;

		if ( false !== $clauses_priority ) {
			remove_filter( 'terms_clauses', $clauses, $clauses_priority );
		}

		$children_filters = array();
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
				continue;
			}

			// After WPML's own filter (priority 10), which serves the map of
			// the current language only.
			$hook     = "pre_option_{$taxonomy}_children";
			$callback = function () use ( $taxonomy ) {
				return self::get_hierarchy( $taxonomy );
			};

			add_filter( $hook, $callback, PHP_INT_MAX, 0 );
			$children_filters[ $hook ] = $callback;
		}

		// WP_Term_Query caches its result once empty parents are pruned, under
		// a key built from the SQL and the known arguments only. Without a
		// distinct key, a result pruned with the per-language map would be
		// served again from a persistent object cache.
		$args['cache_domain'] = ( isset( $args['cache_domain'] ) ? $args['cache_domain'] : 'core' ) . ':seopress_sitemaps_all_languages';

		try {
			return $query( $args );
		} finally {
			foreach ( $children_filters as $hook => $callback ) {
				remove_filter( $hook, $callback, PHP_INT_MAX );
			}

			if ( false !== $clauses_priority ) {
				add_filter( 'terms_clauses', $clauses, $clauses_priority, 3 );
			}
		}
	}

	/**
	 * Whether the sitemap term queries must span every language.
	 *
	 * @since 10.4.0
	 *
	 * @param string[] $taxonomies Taxonomies queried.
	 *
	 * @return bool
	 */
	public static function spans_all_languages( array $taxonomies ) {
		$spans = defined( 'ICL_SITEPRESS_VERSION' )
			&& 2 !== (int) apply_filters( 'wpml_setting', false, 'language_negotiation_type' );

		/**
		 * Filter whether the sitemap term queries are run over every WPML
		 * language.
		 *
		 * @since 10.4.0
		 *
		 * @param bool     $spans      True with WPML active, except on a
		 *                             multidomain setup.
		 * @param string[] $taxonomies Taxonomies queried.
		 */
		return (bool) apply_filters( 'seopress_sitemaps_terms_all_languages', $spans, $taxonomies );
	}

	/**
	 * Parent => children map of a taxonomy, every language included.
	 *
	 * Same shape as the `{$taxonomy}_children` option WordPress builds, read
	 * from the term rows without any language join, as WPML does for 'all'.
	 * Kept for the request, until a term changes.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array<int, int[]>
	 */
	public static function get_hierarchy( $taxonomy ) {
		global $wpdb;

		$last_changed = wp_cache_get_last_changed( 'terms' );

		if ( isset( self::$hierarchies[ $taxonomy ] ) && self::$hierarchies[ $taxonomy ]['last_changed'] === $last_changed ) {
			return self::$hierarchies[ $taxonomy ]['children'];
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- the WordPress API would apply the very language filter this works around; kept for the request below.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT term_id, parent FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s AND parent > 0 ORDER BY term_id",
				$taxonomy
			)
		);

		$children = array();
		foreach ( (array) $rows as $row ) {
			$children[ (int) $row->parent ][] = (int) $row->term_id;
		}

		self::$hierarchies[ $taxonomy ] = array(
			'last_changed' => $last_changed,
			'children'     => $children,
		);

		return $children;
	}
}

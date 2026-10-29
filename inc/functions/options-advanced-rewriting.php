<?php
/**
 * Options advanced rewriting
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

if ( ! function_exists( 'seopress_get_category_rewrite_terms' ) ) {
	/**
	 * Every term of a category taxonomy, read straight from the tables.
	 *
	 * The rules without a category base are built from these rows, in memory.
	 * A term query goes through the filters of translation plugins: WPML
	 * narrows it to the current language, and outside wp-admin it also swaps
	 * each term for its translation in that language (get_term_adjust_id), so
	 * a flush from the front end, cron or REST dropped every translated
	 * category, and those answered 404. get_term() also answers from the
	 * object cache, which still holds the old slug while edited_term_taxonomy
	 * runs. The rows give every language and the current slugs, whatever the
	 * context of the flush.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array[] Rows keyed by term ID, each with term_id, slug, parent and name.
	 */
	function seopress_get_category_rewrite_terms( $taxonomy ) {
		global $wpdb;

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- must bypass term query filters and the term cache, see above.
			$wpdb->prepare(
				"SELECT t.term_id, t.slug, t.name, tt.parent FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id WHERE tt.taxonomy = %s ORDER BY t.term_id",
				$taxonomy
			),
			ARRAY_A
		);

		$terms = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$terms[ (int) $row['term_id'] ] = array(
				'term_id' => (int) $row['term_id'],
				'slug'    => (string) $row['slug'],
				'parent'  => (int) $row['parent'],
				'name'    => (string) $row['name'],
			);
		}

		return $terms;
	}
}

if ( ! function_exists( 'seopress_get_category_rewrite_paths' ) ) {
	/**
	 * The full slug path of every term of a category taxonomy, deepest first.
	 *
	 * A child gets parent/child, built by walking the parents in the rows
	 * themselves, so a translated child gets the path of its own language.
	 * Deepest first, so a nested category's exact rules come before the
	 * generic slug/(.+?) rules of its ancestors, then by name, the order
	 * get_terms() used to give.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string[]
	 */
	function seopress_get_category_rewrite_paths( $taxonomy ) {
		$terms = seopress_get_category_rewrite_terms( $taxonomy );
		$paths = array();

		foreach ( $terms as $term_id => $term ) {
			if ( '' === $term['slug'] ) {
				continue;
			}

			$slugs  = array( $term['slug'] );
			$seen   = array( $term_id => true );
			$parent = $term['parent'];

			// A parent outside the taxonomy, or a loop, ends the path.
			while ( $parent && isset( $terms[ $parent ] ) && ! isset( $seen[ $parent ] ) ) {
				$seen[ $parent ] = true;
				array_unshift( $slugs, $terms[ $parent ]['slug'] );
				$parent = $terms[ $parent ]['parent'];
			}

			$paths[] = array(
				'path'  => implode( '/', $slugs ),
				'depth' => count( $slugs ),
				'name'  => $term['name'],
			);
		}

		usort(
			$paths,
			static function ( $a, $b ) {
				if ( $a['depth'] !== $b['depth'] ) {
					return $b['depth'] - $a['depth'];
				}

				return strcmp( $a['name'], $b['name'] );
			}
		);

		return array_column( $paths, 'path' );
	}
}

if ( ! function_exists( 'seopress_category_slugs_signature' ) ) {
	/**
	 * A fingerprint of every slug and parent of a category taxonomy.
	 *
	 * The rewrite rules depend on nothing else, so a flush is only needed when
	 * this changes. Same rows as the rules themselves.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	function seopress_category_slugs_signature( $taxonomy ) {
		$rows = array();
		foreach ( seopress_get_category_rewrite_terms( $taxonomy ) as $term ) {
			$rows[] = array( $term['term_id'], $term['slug'], $term['parent'] );
		}

		return md5( wp_json_encode( $rows ) );
	}
}

if ( ! function_exists( 'seopress_register_category_flush_hooks' ) ) {
	/**
	 * Flush the rules of a category taxonomy when, and only when, its slugs change.
	 *
	 * Every hook only queues a check, run once on shutdown: one fingerprint
	 * query per request, however many terms a product save or an import
	 * touches, and one flush at most.
	 *
	 * edited_term_taxonomy is kept for saves that bypass edited_{$taxonomy}
	 * (#1153). WordPress also fires it from every term count update, which used
	 * to flush the rules on each product or post save; those change no slug,
	 * so they now flush nothing.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return void
	 */
	function seopress_register_category_flush_hooks( $taxonomy ) {
		$GLOBALS['seopress_category_rules_taxonomies'][ $taxonomy ] = true;

		add_action( 'created_' . $taxonomy, 'seopress_queue_category_rules_check_on_term_hook' );
		add_action( 'edited_' . $taxonomy, 'seopress_queue_category_rules_check_on_term_hook' );
		add_action( 'delete_' . $taxonomy, 'seopress_queue_category_rules_check_on_term_hook' );
		add_action( 'edited_term_taxonomy', 'seopress_maybe_queue_category_rules_check', 10, 2 );
	}
}

if ( ! function_exists( 'seopress_queue_category_rules_check' ) ) {
	/**
	 * Queue a fingerprint check for a category taxonomy, run on shutdown.
	 *
	 * @since 10.4.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return void
	 */
	function seopress_queue_category_rules_check( $taxonomy ) {
		$GLOBALS['seopress_category_rules_pending'][ $taxonomy ] = true;

		add_action( 'shutdown', 'seopress_flush_category_rules_if_changed' );
	}
}

if ( ! function_exists( 'seopress_queue_category_rules_check_on_term_hook' ) ) {
	/**
	 * Queue a check from created_, edited_ or delete_{$taxonomy}.
	 *
	 * Those hooks do not pass the taxonomy, the hook name carries it.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	function seopress_queue_category_rules_check_on_term_hook() {
		seopress_queue_category_rules_check( (string) preg_replace( '/^(created|edited|delete)_/', '', (string) current_action() ) );
	}
}

if ( ! function_exists( 'seopress_maybe_queue_category_rules_check' ) ) {
	/**
	 * Queue a check from edited_term_taxonomy, for the watched taxonomies only.
	 *
	 * WordPress has passed the taxonomy to this hook since 2.9.
	 *
	 * @since 10.4.0
	 *
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return void
	 */
	function seopress_maybe_queue_category_rules_check( $tt_id, $taxonomy = '' ) {
		if ( empty( $GLOBALS['seopress_category_rules_taxonomies'][ (string) $taxonomy ] ) ) {
			return;
		}

		seopress_queue_category_rules_check( (string) $taxonomy );
	}
}

if ( ! function_exists( 'seopress_flush_category_rules' ) ) {
	/**
	 * Flush the rewrite rules and remember the slugs they were built from.
	 *
	 * Soft flush, like the rest of the plugin: .htaccess is left alone.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	function seopress_flush_category_rules() {
		$taxonomies = isset( $GLOBALS['seopress_category_rules_taxonomies'] ) ? array_keys( $GLOBALS['seopress_category_rules_taxonomies'] ) : array();

		foreach ( $taxonomies as $taxonomy ) {
			update_option( 'seopress_' . $taxonomy . '_slugs_signature', seopress_category_slugs_signature( $taxonomy ), false );
		}

		flush_rewrite_rules( false );
	}
}

if ( ! function_exists( 'seopress_flush_category_rules_if_changed' ) ) {
	/**
	 * On shutdown, flush when a queued taxonomy's slugs differ from the last flush.
	 *
	 * An edit that changes no slug, a description or a count, flushes nothing.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	function seopress_flush_category_rules_if_changed() {
		$pending = isset( $GLOBALS['seopress_category_rules_pending'] ) ? array_keys( $GLOBALS['seopress_category_rules_pending'] ) : array();

		$GLOBALS['seopress_category_rules_pending'] = array();

		foreach ( $pending as $taxonomy ) {
			if ( get_option( 'seopress_' . $taxonomy . '_slugs_signature' ) !== seopress_category_slugs_signature( $taxonomy ) ) {
				seopress_flush_category_rules();
				return;
			}
		}
	}
}

// No /category/ in URL.
if ( ! empty( seopress_get_service( 'AdvancedOption' )->getAdvancedRemoveCategoryURL() ) ) {
	// Flush permalinks when the post category slugs change.
	seopress_register_category_flush_hooks( 'category' );

	// @credits : WordPress VIP.
	add_filter( 'category_rewrite_rules', 'seopress_filter_category_rewrite_rules' );
	/**
	 * Filter category rewrite rules
	 *
	 * @param array $rules Rewrite rules.
	 *
	 * @return array $rules
	 */
	function seopress_filter_category_rewrite_rules( $rules ) {
		// Every language, current slugs: see seopress_get_category_rewrite_terms().
		$slugs = seopress_get_category_rewrite_paths( 'category' );

		if ( ! empty( $slugs ) ) {
			$rules = array_reduce(
				$slugs,
				function ( $carry, $slug ) {
					$carry[ '(' . $slug . ')/feed/(feed|rdf|rss|rss2|atom)?/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
					$carry[ '(' . $slug . ')/(feed|rdf|rss|rss2|atom)/?$' ]       = 'index.php?category_name=$matches[1]&feed=$matches[2]';
					$carry[ '(' . $slug . ')(/page/(\d+))?/?$' ]                  = 'index.php?category_name=$matches[1]&paged=$matches[3]';
					return $carry;
				},
				array()
			);
		}
		return apply_filters( 'seopress_category_rewrite_rules', $rules );
	}

	/**
	 * Remove category base
	 *
	 * @param string $termlink Term link.
	 * @param string $term Term.
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return string $termlink
	 */
	function seopress_remove_category_base( $termlink, $term, $taxonomy ) {
		if ( 'category' !== $taxonomy ) {
			return $termlink;
		}

		$category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		if ( class_exists( 'Sitepress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$category_base = apply_filters( 'wpml_translate_single_string', $category_base, 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE );
		}

		$category_base = apply_filters( 'seopress_remove_category_base', $category_base );
		$category_base = ltrim( (string) $category_base, '/' ) . '/';

		return preg_replace( '`' . preg_quote( $category_base, '`' ) . '`u', '', $termlink, 1 );
	}
	add_filter( 'term_link', 'seopress_remove_category_base', 10, 3 );

	add_action( 'template_redirect', 'seopress_category_redirect', 1 );
	/**
	 * Category redirect
	 *
	 * @return void
	 */
	function seopress_category_redirect() {
		if ( ! is_404() ) {
			return;
		}

		global $wp;
		$current_url = user_trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );

		$category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		if ( class_exists( 'Sitepress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$category_base = apply_filters( 'wpml_translate_single_string', $category_base, 'WordPress', 'URL category tax slug', ICL_LANGUAGE_CODE );
		}

		$category_base = apply_filters( 'seopress_remove_category_base', $category_base );
		$regex         = sprintf( '/\/%s\//', str_replace( '/', '\/', $category_base ) );

		if ( preg_match( $regex, $current_url ) ) {
			$new_url = str_replace( '/' . $category_base, '', $current_url );
			wp_redirect( $new_url, 301 );
			exit();
		}
	}
}

// No /product-category/ in URL.
if ( ! empty( seopress_get_service( 'AdvancedOption' )->getAdvancedRemoveProductCategoryURL() ) ) {
	// Flush permalinks when the product category slugs change.
	seopress_register_category_flush_hooks( 'product_cat' );

	add_filter( 'product_cat_rewrite_rules', 'seopress_filter_product_category_rewrite_rules' );
	/**
	 * Filter product category rewrite rules
	 *
	 * @param array $rules Rewrite rules.
	 *
	 * @return array $rules
	 */
	function seopress_filter_product_category_rewrite_rules( $rules ) {
		// Every language, current slugs: see seopress_get_category_rewrite_terms().
		$slugs = seopress_get_category_rewrite_paths( 'product_cat' );

		if ( ! empty( $slugs ) ) {
			$rules = array_reduce(
				$slugs,
				function ( $carry, $slug ) {
					$carry[ '(' . $slug . ')(/page/(\d+))?/?$' ]                = 'index.php?product_cat=$matches[1]&paged=$matches[3]';
					$carry[ $slug . '/(.+?)/page/?([0-9]{1,})/?$' ]             = 'index.php?product_cat=$matches[1]&paged=$matches[2]';
					$carry[ $slug . '/(.+?)/?$' ]                               = 'index.php?product_cat=$matches[1]';
					$carry[ $slug . '/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
					$carry[ $slug . '/(.+?)/(feed|rdf|rss|rss2|atom)/?$' ]      = 'index.php?product_cat=$matches[1]&feed=$matches[2]';
					$carry[ $slug . '/(.+?)/embed/?$' ]                         = 'index.php?product_cat=$matches[1]&embed=true';
					return $carry;
				},
				array()
			);
		}
		return apply_filters( 'seopress_product_category_rewrite_rules', $rules );
	}

	/**
	 * Remove product category base
	 *
	 * @param string $termlink Term link.
	 * @param string $term Term.
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return string $termlink
	 */
	function seopress_remove_product_category_base( $termlink, $term, $taxonomy ) {
		if ( 'product_cat' !== $taxonomy ) {
			return $termlink;
		}

		$permalinks    = get_option( 'woocommerce_permalinks' );
		$category_base = ( isset( $permalinks['category_base'] ) && $permalinks['category_base'] ) ? $permalinks['category_base'] : 'product-category';
		if ( class_exists( 'Sitepress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$category_base = apply_filters( 'wpml_translate_single_string', $category_base, 'WordPress', 'URL product_cat tax slug', ICL_LANGUAGE_CODE );
		}

		$category_base = apply_filters( 'seopress_remove_product_category_base', $category_base );
		$category_base = ltrim( (string) $category_base, '/' ) . '/';

		return preg_replace( '`' . preg_quote( $category_base, '`' ) . '`u', '', $termlink, 1 );
	}
	add_filter( 'term_link', 'seopress_remove_product_category_base', 10, 3 );

	add_action( 'template_redirect', 'seopress_product_category_redirect', 1 );
	/**
	 * Product category redirect
	 *
	 * @return void
	 */
	function seopress_product_category_redirect() {
		if ( ! is_404() ) {
			return;
		}

		global $wp;
		$current_url = user_trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );

		$permalinks    = get_option( 'woocommerce_permalinks' );
		$category_base = ( isset( $permalinks['category_base'] ) && $permalinks['category_base'] ) ? $permalinks['category_base'] : 'product-category';
		if ( class_exists( 'Sitepress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$category_base = apply_filters( 'wpml_translate_single_string', $category_base, 'WordPress', 'URL product_cat tax slug', ICL_LANGUAGE_CODE );
		}

		$category_base = apply_filters( 'seopress_remove_product_category_base', $category_base );
		$regex         = sprintf( '/\/%s\//', str_replace( '/', '\/', $category_base ) );

		if ( preg_match( $regex, $current_url ) ) {
			$new_url = str_replace( '/' . $category_base, '', $current_url );
			wp_redirect( $new_url, 301 );
			exit();
		}
	}
}

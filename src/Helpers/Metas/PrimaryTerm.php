<?php // phpcs:ignore

namespace SEOPress\Helpers\Metas;

defined( 'ABSPATH' ) || exit;

/**
 * Primary term helper.
 *
 * SEOPress historically shipped a single "primary category" stored under
 * `_seopress_robots_primary_cat`, hardcoded to the core `category`
 * taxonomy (and to WooCommerce's `product_cat` for products). This helper
 * generalizes that value to any hierarchical taxonomy: every other
 * taxonomy gets its own `_seopress_robots_primary_term_{taxonomy}` post
 * meta while the historical taxonomy keeps the legacy key, so stored
 * values, importers and PRO consumers keep working untouched.
 *
 * @since 10.4.0
 */
abstract class PrimaryTerm {

	/**
	 * Meta key used by the taxonomy SEOPress supported before 10.4.0.
	 *
	 * @var string
	 */
	const LEGACY_META_KEY = '_seopress_robots_primary_cat';

	/**
	 * Meta key prefix used by every other taxonomy.
	 *
	 * @var string
	 */
	const META_KEY_PREFIX = '_seopress_robots_primary_term_';

	/**
	 * Taxonomies that keep the legacy meta key.
	 *
	 * @return string[]
	 */
	public static function get_legacy_taxonomies() {
		return array( 'category', 'product_cat' );
	}

	/**
	 * The taxonomy of a post type that owns the legacy meta key, if any.
	 *
	 * One key, one taxonomy. Before this, `_seopress_robots_primary_cat` held
	 * the primary `product_cat` on a product and the primary `category`
	 * everywhere else, and the two never met on the same post type. A post type
	 * carrying both, which a WooCommerce site can register, would give them the
	 * same key: two selects with the same name in the Classic Editor, where the
	 * last one wins, and two Block Editor pickers writing over each other. So
	 * the first of them that the post type has keeps the key and the other one
	 * gets its own, which is also what the stored data already looks like.
	 *
	 * @since 10.4.0
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string|null
	 */
	public static function get_legacy_taxonomy( $post_type ) {
		if ( empty( $post_type ) || ! post_type_exists( $post_type ) ) {
			return null;
		}

		$attached = get_object_taxonomies( $post_type, 'names' );

		// On a product the legacy key has always held a `product_cat` id,
		// and WooCommerce's permalink hook and the sitemap still read it as
		// one. A plugin attaching `category` to products must not take it.
		$order = 'product' === $post_type
			? array( 'product_cat', 'category' )
			: self::get_legacy_taxonomies();

		foreach ( $order as $taxonomy ) {
			if ( in_array( $taxonomy, $attached, true ) ) {
				return $taxonomy;
			}
		}

		return null;
	}

	/**
	 * The post meta key holding the primary term of a taxonomy.
	 *
	 * @param string $taxonomy  Taxonomy name.
	 * @param string $post_type Post type the key is read or written for. Given
	 *                          one, only the single legacy taxonomy that post
	 *                          type has keeps the legacy key. Without it the
	 *                          answer is the historical one, so a caller that
	 *                          does not know the post type behaves as before.
	 *
	 * @return string
	 */
	public static function get_meta_key( $taxonomy, $post_type = '' ) {
		if ( ! in_array( $taxonomy, self::get_legacy_taxonomies(), true ) ) {
			return self::META_KEY_PREFIX . $taxonomy;
		}

		if ( '' === $post_type ) {
			return self::LEGACY_META_KEY;
		}

		return $taxonomy === self::get_legacy_taxonomy( $post_type )
			? self::LEGACY_META_KEY
			: self::META_KEY_PREFIX . $taxonomy;
	}

	/**
	 * The taxonomy a per-taxonomy primary term meta key belongs to.
	 *
	 * @since 10.4.0
	 *
	 * @param string $meta_key Meta key.
	 *
	 * @return string|null Null for the legacy key and for any other key.
	 */
	public static function get_taxonomy_from_meta_key( $meta_key ) {
		if ( ! is_string( $meta_key ) || 0 !== strpos( $meta_key, self::META_KEY_PREFIX ) ) {
			return null;
		}

		$taxonomy = substr( $meta_key, strlen( self::META_KEY_PREFIX ) );

		return '' !== $taxonomy ? $taxonomy : null;
	}

	/**
	 * A primary term value as stored: a positive term id, or nothing.
	 *
	 * Every write path goes through it, the core REST meta included, so `none`,
	 * `0`, free text or a negative number all mean "no primary term" and are
	 * stored the same way.
	 *
	 * @since 10.4.0
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return string A term id, or an empty string.
	 */
	public static function sanitize_term_id( $value ) {
		if ( is_int( $value ) ) {
			$value = (string) $value;
		}

		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( $value );

		return ctype_digit( $value ) && 0 < (int) $value ? (string) (int) $value : '';
	}

	/**
	 * The legacy value, for a taxonomy that has no key of its own on a post yet.
	 *
	 * Before 10.4.0, `_seopress_robots_primary_cat` was the only key, and the
	 * CSV import, the robots REST route, the ability and sites offering their
	 * own taxonomy through `seopress_primary_category_list` stored the term of
	 * any taxonomy in it. Hooked on `default_post_metadata`, so as long as the
	 * taxonomy's own key does not exist, reading it answers that term: the
	 * selectors, `%%_ct_<taxonomy>%%` and PRO's breadcrumbs then agree on it.
	 *
	 * The term is only checked to belong to the taxonomy here. Whether it is
	 * still assigned to the post is get_primary_term()'s job, as for a value
	 * stored under the taxonomy's own key.
	 *
	 * @since 10.4.0
	 *
	 * @param mixed  $value     The default value.
	 * @param int    $object_id Post ID.
	 * @param string $meta_key  Meta key.
	 * @param bool   $single    Whether a single value was asked for.
	 *
	 * @return mixed
	 */
	public static function inherit_legacy_value( $value, $object_id, $meta_key, $single ) {
		$taxonomy = self::get_taxonomy_from_meta_key( $meta_key );

		// Only in place of the empty default, never over a default someone
		// else registered or filtered in.
		if ( null === $taxonomy || ( '' !== $value && array() !== $value ) ) {
			return $value;
		}

		$legacy = self::get_legacy_term_id( $object_id, $taxonomy );

		if ( '' === $legacy ) {
			return $value;
		}

		return $single ? $legacy : array( $legacy );
	}

	/**
	 * The legacy value of a post when it is a term of the given taxonomy.
	 *
	 * @since 10.4.0
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string A term id, or an empty string.
	 */
	protected static function get_legacy_term_id( $post_id, $taxonomy ) {
		$post_id = (int) $post_id;

		if ( $post_id < 1 || self::LEGACY_META_KEY === self::get_meta_key( $taxonomy, (string) get_post_type( $post_id ) ) ) {
			return '';
		}

		$legacy = self::sanitize_term_id( get_post_meta( $post_id, self::LEGACY_META_KEY, true ) );

		if ( '' === $legacy || ! get_term( (int) $legacy, $taxonomy ) instanceof \WP_Term ) {
			return '';
		}

		return $legacy;
	}

	/**
	 * Remove the primary term of a post for a taxonomy: "None".
	 *
	 * Deleting the taxonomy's own key alone would let the legacy value it
	 * inherits come back, so "None" in the Classic Editor, the universal
	 * metabox or the REST route would not mean what it means in the Block
	 * Editor, which stores an empty value. The legacy value goes too when it
	 * is a term of this taxonomy, and only then: on a post type where another
	 * taxonomy owns the legacy key, that taxonomy's choice is left alone.
	 *
	 * @since 10.4.0
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return void
	 */
	public static function clear( $post_id, $taxonomy ) {
		$post_id = (int) $post_id;

		delete_post_meta( $post_id, self::get_meta_key( $taxonomy, (string) get_post_type( $post_id ) ) );

		if ( '' !== self::get_legacy_term_id( $post_id, $taxonomy ) ) {
			delete_post_meta( $post_id, self::LEGACY_META_KEY );
		}
	}

	/**
	 * The form field name / DOM id used by the Classic Editor selector.
	 *
	 * Derived from the meta key so `category` keeps the historical
	 * `seopress_robots_primary_cat` name the React metabox reads.
	 *
	 * @param string $taxonomy  Taxonomy name.
	 * @param string $post_type Post type, see get_meta_key().
	 *
	 * @return string
	 */
	public static function get_field_name( $taxonomy, $post_type = '' ) {
		return ltrim( self::get_meta_key( $taxonomy, $post_type ), '_' );
	}

	/**
	 * The DOM id of the WordPress metabox rendering a taxonomy.
	 *
	 * Mirrors the id core passes to `add_meta_box()` for hierarchical
	 * taxonomies: `categorydiv` for the core category, `{taxonomy}div`
	 * for every other one.
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	public static function get_metabox_id( $taxonomy ) {
		return 'category' === $taxonomy ? 'categorydiv' : $taxonomy . 'div';
	}

	/**
	 * The name attribute of the term checkboxes inside that metabox.
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	public static function get_checkbox_name( $taxonomy ) {
		if ( 'category' === $taxonomy ) {
			return 'post_category[]';
		}

		return sprintf( 'tax_input[%s][]', $taxonomy );
	}

	/**
	 * Taxonomies of a post type that offer a primary term selector.
	 *
	 * Hierarchical, public and visible in the admin: a primary term only
	 * makes sense where the editor picks several terms from a list, which
	 * is also the surface the Classic Editor injector and the Block Editor
	 * panel hook into.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return \WP_Taxonomy[] Keyed by taxonomy name.
	 */
	public static function get_taxonomies( $post_type ) {
		$taxonomies = array();

		if ( ! empty( $post_type ) && post_type_exists( $post_type ) ) {
			foreach ( get_object_taxonomies( $post_type, 'objects' ) as $name => $taxonomy ) {
				// Loosely, the way core does: WP_Taxonomy stores whatever was
				// passed to register_taxonomy(), so a taxonomy registered with
				// 1 rather than true is still public.
				if ( empty( $taxonomy->public ) || empty( $taxonomy->show_ui ) || empty( $taxonomy->hierarchical ) ) {
					continue;
				}

				$taxonomies[ $name ] = $taxonomy;
			}
		}

		/**
		 * Filter the taxonomies offering a primary term selector.
		 *
		 * Use it to opt a non-hierarchical taxonomy in, or to opt a
		 * hierarchical one out.
		 *
		 * @since 10.4.0
		 *
		 * @param \WP_Taxonomy[] $taxonomies Keyed by taxonomy name.
		 * @param string         $post_type  Post type the list was built for.
		 */
		$taxonomies = apply_filters( 'seopress_primary_term_taxonomies', $taxonomies, $post_type );

		return is_array( $taxonomies ) ? $taxonomies : array();
	}

	/**
	 * Every primary term meta key registered across the site.
	 *
	 * @return string[]
	 */
	public static function get_meta_keys() {
		$keys = array();

		// Every post type the selector can appear on, not only the public
		// ones. A `public => false, show_ui => true` CPT showed the selector,
		// and the REST route then dropped the unregistered key without a word,
		// so the choice was lost on reload.
		$post_types = array_unique(
			array_merge(
				get_post_types( array( 'public' => true ), 'names' ),
				get_post_types( array( 'show_ui' => true ), 'names' ),
				get_post_types( array( 'show_in_rest' => true ), 'names' )
			)
		);

		foreach ( $post_types as $post_type ) {
			foreach ( array_keys( self::get_taxonomies( $post_type ) ) as $taxonomy ) {
				$keys[] = self::get_meta_key( $taxonomy, $post_type );
			}
		}

		return array_values( array_unique( $keys ) );
	}

	/**
	 * The stored primary term id of a post for a taxonomy, unvalidated.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	public static function get_raw_value( $post_id, $taxonomy ) {
		$value = get_post_meta( (int) $post_id, self::get_meta_key( $taxonomy, get_post_type( (int) $post_id ) ), true );

		return is_scalar( $value ) ? (string) $value : '';
	}

	/**
	 * The primary term of a post for a taxonomy.
	 *
	 * Returns null unless the stored term still exists, still belongs to
	 * the taxonomy and is still assigned to the post: a term removed from
	 * the post must fall back to the default WordPress behaviour rather
	 * than keep steering breadcrumbs and permalinks.
	 *
	 * A taxonomy without a key of its own on the post yet reads the legacy
	 * value when it holds one of its terms, see inherit_legacy_value().
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return \WP_Term|null
	 */
	public static function get_primary_term( $post_id, $taxonomy ) {
		$value = self::get_raw_value( $post_id, $taxonomy );

		if ( '' === $value || 'none' === $value || '0' === $value ) {
			return null;
		}

		$term = get_term( (int) $value, $taxonomy );

		if ( ! $term instanceof \WP_Term ) {
			return null;
		}

		if ( ! has_term( $term->term_id, $taxonomy, $post_id ) ) {
			return null;
		}

		return $term;
	}

	/**
	 * The term a post shows for a taxonomy: its primary term, else its first.
	 *
	 * Every reader of `%%_ct_<taxonomy>%%` goes through this, so the metabox
	 * preview, the schemas and the published title and meta description
	 * cannot disagree on which term a post shows.
	 *
	 * @since 10.4.0
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return \WP_Term|null
	 */
	public static function get_display_term( $post_id, $taxonomy ) {
		$primary = self::get_primary_term( $post_id, $taxonomy );

		if ( $primary instanceof \WP_Term ) {
			return $primary;
		}

		$terms = wp_get_post_terms( (int) $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || ! isset( $terms[0] ) || ! $terms[0] instanceof \WP_Term ) {
			return null;
		}

		return $terms[0];
	}

	/**
	 * Assigned terms the term list filters take out of a post's selector.
	 *
	 * The Classic and Block Editor selectors rebuild their list in the
	 * browser from the checked terms, so a PHP filter narrowing it had no
	 * effect there. They are handed these ids to leave out instead. Terms
	 * checked after the page loaded never went through the filters and stay
	 * offered.
	 *
	 * @since 10.4.0
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return int[]
	 */
	public static function get_excluded_term_ids( $post_id, $taxonomy ) {
		$post = get_post( (int) $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return array();
		}

		$assigned = get_the_terms( $post, $taxonomy );

		if ( ! is_array( $assigned ) || empty( $assigned ) ) {
			return array();
		}

		$offered = $assigned;

		if ( in_array( $taxonomy, self::get_legacy_taxonomies(), true ) ) {
			/** This filter is documented in inc/admin/metaboxes/admin-metaboxes.php */
			$offered = apply_filters( 'seopress_primary_category_list', $offered );
		}

		/** This filter is documented in inc/admin/metaboxes/admin-metaboxes.php */
		$offered = apply_filters( 'seopress_primary_term_list', $offered, $taxonomy, $post );

		$offered_ids = array();
		foreach ( is_array( $offered ) ? $offered : array() as $term ) {
			// Any object carrying a term id, not only a WP_Term: a callback
			// written for the old `seopress_primary_category_list` may return
			// plain objects, and refusing them excluded every term.
			if ( is_object( $term ) && isset( $term->term_id ) ) {
				$offered_ids[] = (int) $term->term_id;
			}
		}

		$excluded = array();
		foreach ( $assigned as $term ) {
			if ( ! in_array( (int) $term->term_id, $offered_ids, true ) ) {
				$excluded[] = (int) $term->term_id;
			}
		}

		return $excluded;
	}

	/**
	 * Store the primary term of a post for a taxonomy.
	 *
	 * An empty value, the `none` sentinel, and anything that is not a term of
	 * this taxonomy assigned to this post, all remove the primary term, see
	 * clear().
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy name.
	 * @param string $value    Term id, `none` or an empty string.
	 *
	 * @return void
	 */
	public static function save( $post_id, $taxonomy, $value ) {
		$post_id  = (int) $post_id;
		$meta_key = self::get_meta_key( $taxonomy, get_post_type( $post_id ) );
		// (int) rather than absint(): absint( '-12' ) is 12, so a negative id
		// would have been stored as whichever term happens to carry that
		// number.
		$term_id  = is_scalar( $value ) ? (int) $value : 0;

		// Anything that is not a term id of this taxonomy assigned to this post
		// removes the meta rather than being stored. The read already refused
		// to act on a stale value, so nothing was exploitable, but the REST
		// route stored the `none` sentinel and arbitrary text where this
		// deletes, which left two shapes meaning the same thing.
		if ( $term_id < 1 ) {
			self::clear( $post_id, $taxonomy );

			return;
		}

		$term = get_term( $term_id, $taxonomy );

		if ( ! $term instanceof \WP_Term || ! has_term( $term->term_id, $taxonomy, $post_id ) ) {
			self::clear( $post_id, $taxonomy );

			return;
		}

		update_post_meta( $post_id, $meta_key, (string) $term->term_id );
	}

	/**
	 * Declare every per-taxonomy primary term key to WPML.
	 *
	 * wpml-config.xml lists the legacy key only, as a copied term id that WPML
	 * converts to the term of the translation's language. The other keys
	 * depend on the taxonomies of the site, so they are added to the parsed
	 * configuration the same way, and a translation inherits the converted
	 * term instead of nothing.
	 *
	 * @since 10.4.0
	 *
	 * @param mixed $config WPML configuration, as parsed from every wpml-config.xml.
	 *
	 * @return mixed
	 */
	public static function add_wpml_config( $config ) {
		if ( ! is_array( $config ) || ! isset( $config['wpml-config'] ) || ! is_array( $config['wpml-config'] ) ) {
			return $config;
		}

		$fields = isset( $config['wpml-config']['custom-fields']['custom-field'] ) ? $config['wpml-config']['custom-fields']['custom-field'] : array();

		// One field on its own is not wrapped in a list.
		if ( isset( $fields['value'] ) ) {
			$fields = array( $fields );
		}

		if ( ! is_array( $fields ) ) {
			$fields = array();
		}

		$declared = array();
		foreach ( $fields as $field ) {
			if ( isset( $field['value'] ) && is_string( $field['value'] ) ) {
				$declared[] = trim( $field['value'] );
			}
		}

		foreach ( self::get_meta_keys() as $key ) {
			if ( in_array( $key, $declared, true ) ) {
				continue;
			}

			$fields[] = array(
				'value' => $key,
				'attr'  => array(
					'action' => 'copy',
					'type'   => 'taxonomy-ids',
				),
			);
		}

		if ( ! isset( $config['wpml-config']['custom-fields'] ) || ! is_array( $config['wpml-config']['custom-fields'] ) ) {
			$config['wpml-config']['custom-fields'] = array();
		}

		$config['wpml-config']['custom-fields']['custom-field'] = $fields;

		return $config;
	}

	/**
	 * The label of the primary term selector for a taxonomy.
	 *
	 * @param \WP_Taxonomy $taxonomy Taxonomy object.
	 *
	 * @return string
	 */
	public static function get_label( $taxonomy ) {
		if ( in_array( $taxonomy->name, self::get_legacy_taxonomies(), true ) ) {
			return __( 'Select a primary category', 'wp-seopress' );
		}

		return sprintf(
			/* translators: %s: taxonomy singular name, e.g. "Genre". */
			__( 'Select a primary %s', 'wp-seopress' ),
			$taxonomy->labels->singular_name
		);
	}

	/**
	 * The description of the primary term selector for a taxonomy.
	 *
	 * @param \WP_Taxonomy $taxonomy Taxonomy object.
	 *
	 * @return string
	 */
	public static function get_description( $taxonomy ) {
		if ( in_array( $taxonomy->name, self::get_legacy_taxonomies(), true ) ) {
			return sprintf(
				/* translators: %s: the %category% permalink tag. */
				__( 'Set the category that gets used in the %s permalink and in our breadcrumbs if you have multiple categories.', 'wp-seopress' ),
				'<code>%category%</code>'
			);
		}

		return sprintf(
			/* translators: 1: taxonomy singular name, e.g. "Genre". 2: the matching dynamic variable, e.g. %%_ct_genre%%. */
			__( 'Set the %1$s that gets used in our breadcrumbs and in the %2$s dynamic variable when several of them are assigned.', 'wp-seopress' ),
			$taxonomy->labels->singular_name,
			'<code>%%_ct_' . $taxonomy->name . '%%</code>'
		);
	}
}

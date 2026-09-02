<?php // phpcs:ignore

namespace SEOPress\Compose;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UseArchivePostType
 *
 * One resolution of "which post type is this archive for", shared by every
 * settings group keyed on the post type: Titles & Metas > Archives, and the
 * per post type og:image.
 */
trait UseArchivePostType {

	/**
	 * The post type the current archive is for.
	 *
	 * The settings keyed on the post type used to read
	 * `get_queried_object()->name`. `WP_Post_Type` has that property and holds
	 * the slug in it, so this worked whenever WordPress set the queried object
	 * to the post type. But the queried object is not guaranteed to be one: on
	 * a WooCommerce shop page a theme, a builder or a plugin can leave it as
	 * the shop `WP_Post`, and `WP_Post` has no `name` property. Its `__get()`
	 * has no case for it, so it answers empty without a warning, the lookup
	 * runs against an empty key and every setting for that post type is dropped
	 * at once.
	 *
	 * Silent in both directions: the settings screen shows the values saved and
	 * reloading confirms them, while the front end behaves as if they had never
	 * been set. One report had `Archives > Products > noindex` on for nine days
	 * with no effect.
	 *
	 * `get_query_var( 'post_type' )` is what the query itself was resolved
	 * against, so it stays correct when the queried object is not the post type.
	 * It is only read on a post type archive: the query var also carries the
	 * slug on a single post of that post type, and answering there would hand
	 * the archive settings to pages they were never meant for.
	 *
	 * @since 10.2.0
	 *
	 * @return string The post type slug, or an empty string.
	 */
	protected function getCurrentArchivePostType() { // phpcs:ignore -- matches the naming of the classes using this trait.
		$queried_object = get_queried_object();

		if ( $queried_object instanceof \WP_Post_Type ) {
			return (string) $queried_object->name;
		}

		if ( ! is_post_type_archive() ) {
			return '';
		}

		// A taxonomy archive can carry the post type in the query at the same
		// time: The Events Calendar registers its category rules with both
		// `tribe_events_cat` and `post_type=tribe_events`, so an event category
		// is a post type archive and a taxonomy archive at once. WP_Query
		// resolves the term first there, and the Titles & Metas title and
		// description already give the taxonomy priority, so the fallback stays
		// out of it rather than serving the Archives noindex and nofollow on
		// every term page.
		if ( is_tax() || is_category() || is_tag() ) {
			return '';
		}

		$post_type = get_query_var( 'post_type' );

		// A plugin can broaden the archive query to several post types through
		// `pre_get_posts`, which runs after `is_post_type_archive` is set, so
		// the query var is an array while the page is still an archive.
		// WP_Query resolves its own queried object by taking the first one
		// (`class-wp-query.php`, `get_queried_object()`), so taking the first
		// one here keeps both halves of the same request in agreement.
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}

		return is_string( $post_type ) ? $post_type : '';
	}
}

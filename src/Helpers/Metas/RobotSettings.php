<?php // phpcs:ignore

namespace SEOPress\Helpers\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RobotSettings
 */
abstract class RobotSettings {
	/**
	 * The getRobotPrimaryCats function.
	 *
	 * @param int|null          $id        The ID.
	 * @param string            $post_type The post type.
	 * @param string|null|false $taxonomy  The taxonomy owning the legacy key,
	 *                                     when the caller already resolved it.
	 *
	 * @return array
	 */
	protected static function getRobotPrimaryCats( $id, $post_type, $taxonomy = false ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( false === $taxonomy ) {
			$taxonomy = self::get_legacy_taxonomy( $post_type );
		}
		$cats = $id && null !== $taxonomy ? get_the_terms( $id, $taxonomy ) : array();

		$cats = apply_filters( 'seopress_primary_category_list', $cats );

		if ( null !== $taxonomy ) {
			/** This filter is documented in inc/admin/metaboxes/admin-metaboxes.php */
			$cats = apply_filters( 'seopress_primary_term_list', $cats, $taxonomy, $id ? get_post( $id ) : null );
		}

		if ( ! is_array( $cats ) ) {
			$cats = array();
		}

		array_unshift( $cats, self::get_none_option() );

		return $cats;
	}

	/**
	 * The `none` option prepended to every primary term list.
	 *
	 * @since 10.4.0
	 *
	 * @return array
	 */
	protected static function get_none_option() {
		return array(
			'term_id' => 'none',
			'name'    => __( 'None (will disable this feature)', 'wp-seopress' ),
		);
	}

	/**
	 * The taxonomy of a post type that still uses the legacy
	 * `_seopress_robots_primary_cat` meta key, if any.
	 *
	 * Narrower than `PrimaryTerm::get_legacy_taxonomy()`: the selector only
	 * exists for a taxonomy that offers one, so an attached but hidden
	 * taxonomy must not claim the field.
	 *
	 * @since 10.4.0
	 *
	 * @param string              $post_type  The post type.
	 * @param \WP_Taxonomy[]|null $taxonomies The taxonomies offering a primary
	 *                                        term, when already resolved.
	 *
	 * @return string|null
	 */
	protected static function get_legacy_taxonomy( $post_type, $taxonomies = null ) {
		if ( ! is_array( $taxonomies ) ) {
			$taxonomies = PrimaryTerm::get_taxonomies( $post_type );
		}

		$owner = PrimaryTerm::get_legacy_taxonomy( $post_type );

		return null !== $owner && isset( $taxonomies[ $owner ] ) ? $owner : null;
	}

	/**
	 * One select field per taxonomy that does not use the legacy meta key.
	 *
	 * @since 10.4.0
	 *
	 * @param int|null       $id         The post ID.
	 * @param string         $post_type  The post type.
	 * @param \WP_Taxonomy[] $taxonomies The taxonomies offering a primary term.
	 * @param string|null    $legacy     The one of them owning the legacy key.
	 *
	 * @return array[]
	 */
	protected static function get_primary_term_fields( $id, $post_type, $taxonomies, $legacy ) {
		$fields = array();

		$post = $id ? get_post( $id ) : null;

		foreach ( $taxonomies as $name => $taxonomy ) {
			// Only the one taxonomy that owns the legacy key is handled by
			// getRobotPrimaryCats(). A post type carrying both `category` and
			// `product_cat` gets a field of its own for the second one rather
			// than two fields writing the same meta.
			if ( $name === $legacy ) {
				continue;
			}

			$terms = $id ? get_the_terms( $id, $name ) : array();

			/**
			 * Filter the terms offered by the primary term selector.
			 *
			 * Applied here as well as in the Classic Editor selector, so a site
			 * narrowing the list sees it narrowed in the universal metabox too.
			 *
			 * @since 10.4.0
			 *
			 * @param \WP_Term[]|false|\WP_Error $terms         The terms.
			 * @param string                     $taxonomy_name Taxonomy name.
			 * @param \WP_Post|null              $post          The post being edited.
			 */
			$terms = apply_filters( 'seopress_primary_term_list', $terms, $name, $post );

			if ( ! is_array( $terms ) ) {
				$terms = array();
			}

			array_unshift( $terms, self::get_none_option() );

			$fields[] = array(
				'key'         => PrimaryTerm::get_meta_key( $name, $post_type ),
				'type'        => 'select',
				'use_default' => '',
				'placeholder' => '',
				'default'     => '',
				'taxonomy'    => $name,
				'label'       => PrimaryTerm::get_label( $taxonomy ),
				'description' => wp_kses_post( PrimaryTerm::get_description( $taxonomy ) ),
				'options'     => $terms,
				'excluded'    => $id ? PrimaryTerm::get_excluded_term_ids( $id, $name ) : array(),
				'visible'     => true,
			);
		}

		return $fields;
	}

	/**
	 * The getMetaKeys function.
	 *
	 * @since 5.0.0
	 *
	 * @param int|null $id The ID.
	 *
	 * @return array[]
	 *
	 *    key: string post meta
	 *    use_default: default value need to use
	 *    default: default value
	 *    label: string label
	 *    placeholder
	 */
	public static function getMetaKeys( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$title_option_service = seopress_get_service( 'TitleOption' );

		$post_type = get_post_type( $id );

		// Resolved once: every helper below needs them, and each resolution
		// runs the `seopress_primary_term_taxonomies` filter again.
		$primary_taxonomies = PrimaryTerm::get_taxonomies( $post_type );
		$legacy_taxonomy    = self::get_legacy_taxonomy( $post_type, $primary_taxonomies );
		$legacy_object      = null !== $legacy_taxonomy ? get_taxonomy( $legacy_taxonomy ) : null;

		$fields = array(
			array(
				'key'         => '_seopress_robots_index',
				'type'        => 'checkbox',
				'use_default' => $title_option_service->getSingleCptNoIndex( $id ) || $title_option_service->getTitleNoIndex() || true === post_password_required( $id ),
				'default'     => 'yes',
				'label'       => __( 'Do not display this page in search engine results / XML - HTML sitemaps (noindex)', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_follow',
				'type'        => 'checkbox',
				'use_default' => $title_option_service->getSingleCptNoFollow( $id ) || $title_option_service->getTitleNoFollow(),
				'default'     => 'yes',
				'label'       => __( 'Do not follow links for this page (nofollow)', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_snippet',
				'type'        => 'checkbox',
				'use_default' => $title_option_service->getTitleNoSnippet(),
				'default'     => 'yes',
				'label'       => __( 'Do not display a description in search results for this page (nosnippet)', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_imageindex',
				'type'        => 'checkbox',
				'use_default' => $title_option_service->getTitleNoImageIndex(),
				'default'     => 'yes',
				'label'       => __( 'Do not index images for this page (noimageindex)', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_canonical',
				'type'        => 'input',
				'use_default' => '',
				'placeholder' => sprintf( '%s %s', __( 'Default value: ', 'wp-seopress' ), urldecode( get_permalink( $id ) ) ),
				'default'     => '',
				'label'       => __( 'Canonical URL', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_primary_cat',
				'type'        => 'select',
				'use_default' => '',
				'placeholder' => '',
				'default'     => '',
				// Through the helper, so the Classic Editor selector and this
				// field cannot say two different things about the same meta.
				// The copy here also carried a typo the helper does not.
				'label'       => $legacy_object instanceof \WP_Taxonomy ? PrimaryTerm::get_label( $legacy_object ) : __( 'Select a primary category', 'wp-seopress' ),
				'description' => $legacy_object instanceof \WP_Taxonomy ? wp_kses_post( PrimaryTerm::get_description( $legacy_object ) ) : '',
				'taxonomy'    => $legacy_taxonomy,
				'options'     => self::getRobotPrimaryCats( $id, $post_type, $legacy_taxonomy ),
				'excluded'    => $id && null !== $legacy_taxonomy ? PrimaryTerm::get_excluded_term_ids( $id, $legacy_taxonomy ) : array(),
				'visible'     => null !== $legacy_taxonomy,
			),
			array(
				'key'         => '_seopress_robots_freeze_modified_date',
				'type'        => 'checkbox',
				'use_default' => '1' === seopress_get_service( 'AdvancedOption' )->getAppearanceFreezeModifiedDate(),
				'default'     => 'yes',
				'label'       => __( 'Freeze the last modified date (recommended for minor updates)', 'wp-seopress' ),
				'description' => __( 'Prevent the last modified date from being updated on save. Recommended for minor updates like typos or formatting.', 'wp-seopress' ),
				'visible'     => true,
			),
			array(
				'key'         => '_seopress_robots_custom_modified_date',
				'type'        => 'input',
				'use_default' => '',
				'placeholder' => 'YYYY-MM-DD',
				'default'     => '',
				'label'       => __( 'Set a custom last modified date', 'wp-seopress' ),
				'description' => __( 'Override the last modified date with a custom value. Leave empty to use the current post modified date.', 'wp-seopress' ),
				'visible'     => true,
			),
		);

		$extra_primary_fields = self::get_primary_term_fields( $id, $post_type, $primary_taxonomies, $legacy_taxonomy );

		if ( ! empty( $extra_primary_fields ) ) {
			$offset = count( $fields );

			foreach ( $fields as $index => $field ) {
				if ( '_seopress_robots_primary_cat' === $field['key'] ) {
					$offset = $index + 1;
					break;
				}
			}

			array_splice( $fields, $offset, 0, $extra_primary_fields );
		}

		$data = apply_filters(
			'seopress_api_meta_robot_settings',
			$fields,
			$id
		);

		return $data;
	}
}

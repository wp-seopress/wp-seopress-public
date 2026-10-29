<?php // phpcs:ignore

namespace SEOPress\Helpers\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TermRobotSettings
 *
 * The robots fields of the term SEO metabox, see seopress_tax() and
 * seopress_tax_save_term(). A term has no primary category, breadcrumbs or
 * modified date, so this is a narrower list than RobotSettings::getMetaKeys().
 */
abstract class TermRobotSettings {

	/**
	 * The robots fields of a term.
	 *
	 * A checkbox the global settings force on is locked the way the metabox
	 * disables it: `use_default` is true, and `locked_reason` says which
	 * setting holds it, since writing the term meta would change nothing.
	 *
	 * @since 10.4.0
	 *
	 * @param \WP_Term $term The term.
	 *
	 * @return array[] { key, type, use_default, default, label, locked_reason, placeholder? }
	 */
	public static function getMetaKeys( $term ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$title_option = seopress_get_service( 'TitleOption' );
		$taxonomy     = $term->taxonomy;

		$site_wide    = __( 'Forced on for the whole site by the global robots settings (Titles & Metas, Advanced tab).', 'wp-seopress' );
		$per_taxonomy = __( 'Forced on for every term of this taxonomy by the global settings (Titles & Metas, Taxonomies tab).', 'wp-seopress' );

		// The same tests as seopress_tax(), with the taxonomy of this term
		// instead of the one of the screen.
		$locks = array(
			'_seopress_robots_index'      => $title_option->getTitleNoIndex() ? $site_wide : ( $title_option->getTaxonomyNoIndex( $taxonomy ) ? $per_taxonomy : '' ),
			'_seopress_robots_follow'     => $title_option->getTitleNoFollow() ? $site_wide : ( $title_option->getTaxonomyNoFollow( $taxonomy ) ? $per_taxonomy : '' ),
			'_seopress_robots_snippet'    => $title_option->getTitleNoSnippet() ? $site_wide : '',
			'_seopress_robots_imageindex' => $title_option->getTitleNoImageIndex() ? $site_wide : '',
		);

		$labels = array(
			'_seopress_robots_index'      => __( 'Do not display this page in search engine results / XML - HTML sitemaps (noindex)', 'wp-seopress' ),
			'_seopress_robots_follow'     => __( 'Do not follow links for this page (nofollow)', 'wp-seopress' ),
			'_seopress_robots_snippet'    => __( 'Do not display a description in search results for this page (nosnippet)', 'wp-seopress' ),
			'_seopress_robots_imageindex' => __( 'Do not index images for this page (noimageindex)', 'wp-seopress' ),
		);

		$fields = array();

		foreach ( $labels as $key => $label ) {
			$fields[] = array(
				'key'           => $key,
				'type'          => 'checkbox',
				'use_default'   => '' !== $locks[ $key ],
				'default'       => 'yes',
				'label'         => $label,
				'locked_reason' => $locks[ $key ],
			);
		}

		$permalink = get_term_link( $term );

		$fields[] = array(
			'key'           => '_seopress_robots_canonical',
			'type'          => 'input',
			'use_default'   => false,
			'placeholder'   => is_wp_error( $permalink ) ? '' : sprintf( '%s %s', __( 'Default value: ', 'wp-seopress' ), urldecode( $permalink ) ),
			'default'       => '',
			'label'         => __( 'Canonical URL', 'wp-seopress' ),
			'locked_reason' => '',
		);

		return $fields;
	}
}

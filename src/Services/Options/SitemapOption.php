<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * SitemapOption
 */
class SitemapOption {

	const NAME_SERVICE = 'SitemapOption';

	/**
	 * SEOPress internal post types (free, PRO, Insights): non-public storage
	 * that must never be exposed in a sitemap, whatever the stored option says.
	 *
	 * @since 10.1
	 * @var array
	 */
	const INTERNAL_POST_TYPES = array(
		'seopress_404',
		'seopress_schemas',
		'seopress_bot',
		'seopress_rankings',
		'seopress_backlinks',
		'seopress_p1_rankings',
	);

	/**
	 * The getOption function.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_SITEMAP );
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|nul
	 *
	 * @param string $key The key.
	 */
	protected function searchOptionByKey( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->getOption();

		if ( empty( $data ) ) {
			return null;
		}

		if ( ! isset( $data[ $key ] ) ) {
			return null;
		}

		return $data[ $key ];
	}

	/**
	 * The isEnabled function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function isEnabled() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_general_enable' );
	}

	/**
	 * The getPostTypesList function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function getPostTypesList() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$post_types = $this->searchOptionByKey( 'seopress_xml_sitemap_post_types_list' );

		// SEOPress internal post types (redirections, schemas, broken links,
		// Insights rankings/backlinks) are non-public storage: never expose them
		// in a sitemap, even if a hand-edited or imported option flags them as
		// included. The settings UI never offers them, so such an entry can only
		// come from outside the UI.
		if ( is_array( $post_types ) ) {
			foreach ( self::INTERNAL_POST_TYPES as $internal_post_type ) {
				unset( $post_types[ $internal_post_type ] );
			}
		}

		return $this->normalizeIncludeList( $post_types );
	}

	/**
	 * The getTaxonomiesList function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function getTaxonomiesList() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->normalizeIncludeList(
			$this->searchOptionByKey( 'seopress_xml_sitemap_taxonomies_list' )
		);
	}

	/**
	 * Normalize a post type / taxonomy include list to the shape the settings
	 * screen writes, `key => array( 'include' => '1' )`.
	 *
	 * An imported, migrated or hand-edited option can hold a bare scalar
	 * instead, `key => '1'`. Consumers then either loop over a string, which
	 * raises `foreach() argument must be of type array|object`, or read
	 * `$list[ $key ]['include']` off a string and silently get nothing, which
	 * is how a sitemap ends up advertised in the index while answering 404.
	 *
	 * Both plugins read these two lists from six different places. Normalizing
	 * once here fixes all of them, rather than guarding each loop and missing
	 * the next one.
	 *
	 * @since 10.2.0
	 *
	 * @param mixed $list Raw option value.
	 *
	 * @return mixed The list with scalar entries wrapped, untouched otherwise.
	 */
	public function normalizeIncludeList( $list ) { // phpcs:ignore -- matches the camelCase used across this service.
		if ( ! is_array( $list ) ) {
			return $list;
		}

		foreach ( $list as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$list[ $key ] = array( 'include' => $value );
			}
		}

		return $list;
	}

	/**
	 * Normalize both include lists inside a whole sitemap option group.
	 *
	 * Callers that hand the option to the settings screen, rather than reading
	 * a single list through this service, need the same rule applied to the
	 * two keys at once. Keeping it here avoids a second copy drifting.
	 *
	 * @since 10.2.0
	 *
	 * @param mixed $options The sitemap option group.
	 *
	 * @return mixed The group with both lists normalized, untouched otherwise.
	 */
	public function normalizeIncludeLists( $options ) { // phpcs:ignore -- matches the camelCase used across this service.
		if ( ! is_array( $options ) ) {
			return $options;
		}

		foreach ( array( 'seopress_xml_sitemap_post_types_list', 'seopress_xml_sitemap_taxonomies_list' ) as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$options[ $key ] = $this->normalizeIncludeList( $options[ $key ] );
			}
		}

		return $options;
	}

	/**
	 * The authorIsEnable function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function authorIsEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_author_enable' );
	}

	/**
	 * The imageIsEnable function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function imageIsEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_img_enable' );
	}

	/**
	 * The getHtmlEnable function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_enable' );
	}

	/**
	 * The getHtmlMapping function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlMapping() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_mapping' );
	}

	/**
	 * The getHtmlExclude function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlExclude() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_exclude' );
	}

	/**
	 * The getHtmlOrder function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlOrder() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_order' );
	}

	/**
	 * The getHtmlOrderBy function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlOrderBy() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_orderby' );
	}

	/**
	 * The getHtmlDate function.
	 *
	 * @since 5.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlDate() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_date' );
	}

	/**
	 * The getHtmlNoHierarchy function.
	 *
	 * @since 7.3.0
	 *
	 * @return string|null
	 */
	public function getHtmlNoHierarchy() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_no_hierarchy' );
	}

	/**
	 * The getHtmlPostTypeArchive function.
	 *
	 * @since 8.9.0
	 *
	 * @return string|null
	 */
	public function getHtmlPostTypeArchive() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_html_post_type_archive' );
	}
}

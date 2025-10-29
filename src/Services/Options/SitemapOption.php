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
		return $this->searchOptionByKey( 'seopress_xml_sitemap_post_types_list' );
	}

	/**
	 * The getTaxonomiesList function.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null
	 */
	public function getTaxonomiesList() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_xml_sitemap_taxonomies_list' );
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

<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * TitleOption
 */
class TitleOption {

	/**
	 * The getOption function.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_TITLE );
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 4.3.0
	 *
	 * @param string $key The key.
	 *
	 * @return mixed
	 */
	public function searchOptionByKey( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
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
	 * The getTitlesCptNoIndexByPath function.
	 *
	 * @since 4.3.0
	 *
	 * @param string $path The path.
	 *
	 * @return string|null
	 */
	public function getTitlesCptNoIndexByPath( $path ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->searchOptionByKey( 'seopress_titles_archive_titles' );

		if ( ! isset( $data[ $path ]['noindex'] ) ) {
			return null;
		}

		return $data[ $path ]['noindex'];
	}

	/**
	 * The getSeparator function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getSeparator() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$separator = $this->searchOptionByKey( 'seopress_titles_sep' );
		if ( ! $separator ) {
			return '-';
		}

		return $separator;
	}

	/**
	 * The getHomeSiteTitle function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getHomeSiteTitle() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_home_site_title' );
	}

	/**
	 * The getHomeSiteTitleAlt function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getHomeSiteTitleAlt() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_home_site_title_alt' );
	}

	/**
	 * The getHomeDescriptionTitle function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getHomeDescriptionTitle() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_home_site_desc' );
	}

	/**
	 * The getSingleCptTitle function.
	 *
	 * @param int|null $id The ID.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getSingleCptTitle( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['title'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['title'];
	}

	/**
	 * The getSingleCptDesc function.
	 *
	 * @param int|null $id The ID.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getSingleCptDesc( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['description'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['description'];
	}

	/**
	 * The getSingleCptNoIndex function.
	 *
	 * @since 5.0.0
	 *
	 * @param int|null $id The ID.
	 *
	 * @return string
	 */
	public function getSingleCptNoIndex( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['noindex'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['noindex'];
	}

	/**
	 * The getSingleCptNoFollow function.
	 *
	 * @since 5.0.0
	 *
	 * @param int|null $id The ID.
	 *
	 * @return string
	 */
	public function getSingleCptNoFollow( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );
		if ( ! isset( $option[ $current_cpt ]['nofollow'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['nofollow'];
	}

	/**
	 * The getArchiveCptTitle function.
	 *
	 * @param string|null $post_type The post type.
	 *
	 * @return string
	 */
	public function getArchiveCptTitle( $post_type = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( null === $post_type ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$post_type = get_post_type( $post );
		}

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );

		if ( ! isset( $option[ $post_type ]['title'] ) ) {
			return;
		}

		return $option[ $post_type ]['title'];
	}

	/**
	 * The getArchiveCptDescription function.
	 *
	 * @param string|null $post_type The post type.
	 *
	 * @return string
	 */
	public function getArchiveCptDescription( $post_type = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( null === $post_type ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$post_type = get_post_type( $post );
		}

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );

		if ( ! isset( $option[ $post_type ]['description'] ) ) {
			return;
		}

		return $option[ $post_type ]['description'];
	}

	/**
	 * The getTaxonomyCptTitle function.
	 *
	 * @param string|null $taxonomy The taxonomy.
	 *
	 * @return string
	 */
	public function getTaxonomyCptTitle( $taxonomy = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( null === $taxonomy ) {
			$queried_object = get_queried_object();
			$taxonomy       = null !== $queried_object ? $queried_object->taxonomy : '';
		}

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $taxonomy ]['title'] ) ) {
			return;
		}

		return $option[ $taxonomy ]['title'];
	}

	/**
	 * The getTaxonomyCptDescription function.
	 *
	 * @param string|null $taxonomy The taxonomy.
	 *
	 * @return string
	 */
	public function getTaxonomyCptDescription( $taxonomy = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( null === $taxonomy ) {
			$queried_object = get_queried_object();
			$taxonomy       = null !== $queried_object ? $queried_object->taxonomy : '';
		}

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $taxonomy ]['description'] ) ) {
			return;
		}

		return $option[ $taxonomy ]['description'];
	}

	/**
	 * The getSingleCptDate function.
	 *
	 * @since 5.7
	 *
	 * @param int|null $id The ID.
	 *
	 * @return string
	 */
	public function getSingleCptDate( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['date'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['date'];
	}

	/**
	 * The getTitleFromSingle function.
	 *
	 * @param string|null $post_type The post type.
	 *
	 * @return string
	 */
	public function getTitleFromSingle( $post_type = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( null === $post_type ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$post_type = get_post_type( $post );
		}

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $post_type ]['title'] ) ) {
			return;
		}

		return $option[ $post_type ]['title'];
	}

	/**
	 * The getSingleCptThumb function.
	 *
	 * @since 6.6.0
	 *
	 * @param int|null $id The ID.
	 *
	 * @return string
	 */
	public function getSingleCptThumb( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['thumb_gcs'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['thumb_gcs'];
	}

	/**
	 * The getSingleCptEnable function.
	 *
	 * @since 6.5.0
	 *
	 * @param int|null $current_cpt The current CPT.
	 *
	 * @return string
	 */
	public function getSingleCptEnable( $current_cpt ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( null === $current_cpt ) {
			return;
		}

		$option = $this->searchOptionByKey( 'seopress_titles_single_titles' );

		if ( ! isset( $option[ $current_cpt ]['enable'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['enable'];
	}

	/**
	 * The getTitleNoIndex function.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function getTitleNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_noindex' );
	}

	/**
	 * The getTitleNoFollow function.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function getTitleNoFollow() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_nofollow' );
	}

	/**
	 * The getTitleNoSnippet function.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function getTitleNoSnippet() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_nosnippet' );
	}

	/**
	 * The getTitleNoImageIndex function.
	 *
	 * @since 5.0.0
	 *
	 * @return string
	 */
	public function getTitleNoImageIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_noimageindex' );
	}

	/**
	 * The getArchivesCPTTitle function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getArchivesCPTTitle() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_cpt    = null !== $queried_object ? $queried_object->name : '';

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );

		if ( ! isset( $option[ $current_cpt ]['title'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['title'];
	}

	/**
	 * The getArchivesCPTDesc function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getArchivesCPTDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_cpt    = null !== $queried_object ? $queried_object->name : '';

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );

		if ( ! isset( $option[ $current_cpt ]['description'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['description'];
	}

	/**
	 * The getArchivesCPTNoIndex function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getArchivesCPTNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_cpt    = null !== $queried_object ? $queried_object->name : '';

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );
		if ( ! isset( $option[ $current_cpt ]['noindex'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['noindex'];
	}

		/**
		 * The getArchivesCPTNoFollow function.
		 *
		 * @since 6.6.0
		 *
		 * @return string
		 */
	public function getArchivesCPTNoFollow() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_cpt    = null !== $queried_object ? $queried_object->name : '';

		$option = $this->searchOptionByKey( 'seopress_titles_archive_titles' );
		if ( ! isset( $option[ $current_cpt ]['nofollow'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['nofollow'];
	}

	/**
	 * The getArchivesAuthorTitle function.
	 *
	 * @since 5.4.1
	 *
	 * @return string
	 */
	public function getArchivesAuthorTitle() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_author_title' );
	}

	/**
	 * The getArchivesAuthorDescription function.
	 *
	 * @since 5.4.1
	 *
	 * @return string
	 */
	public function getArchivesAuthorDescription() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_author_desc' );
	}

	/**
	 * The getArchiveAuthorDisable function.
	 *
	 * @since 6.0.0
	 */
	public function getArchiveAuthorDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_author_disable' );
	}

	/**
	 * The getArchiveAuthorNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getArchiveAuthorNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_author_noindex' );
	}

	/**
	 * The getArchiveDateDisable function.
	 *
	 * @since 6.0.0
	 */
	public function getArchiveDateDisable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_date_disable' );
	}

	/**
	 * The getTitleArchivesDate function.
	 *
	 * @since 5.4.0
	 */
	public function getTitleArchivesDate() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_date_title' );
	}

	/**
	 * The getTitleArchivesSearch function.
	 *
	 * @since 5.4.0
	 */
	public function getTitleArchivesSearch() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_search_title' );
	}

	/**
	 * The getTitleArchives404 function.
	 *
	 * @since 5.4.0
	 */
	public function getTitleArchives404() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_404_title' );
	}

	/**
	 * The getTaxTitle function.
	 *
	 * @since 6.6.0
	 */
	public function getTaxTitle() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_tax    = null !== $queried_object ? $queried_object->taxonomy : '';

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $current_tax ]['title'] ) ) {
			return;
		}

		return $option[ $current_tax ]['title'];
	}

	/**
	 * The getTaxDesc function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getTaxDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$queried_object = get_queried_object();
		$current_tax    = null !== $queried_object ? $queried_object->taxonomy : '';

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $current_tax ]['description'] ) ) {
			return;
		}

		return $option[ $current_tax ]['description'];
	}

	/**
	 * The getTaxNoIndex function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getTaxNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( is_search() ) {
			return;
		}

		$queried_object = get_queried_object();
		$current_tax    = null !== $queried_object ? $queried_object->taxonomy : '';

		if ( null === $queried_object ) {
			global $tax;
			if ( null !== $tax && property_exists( $tax, 'name' ) ) {
				$current_tax = $tax->name;
			}
		}

		if ( null !== $queried_object && 'yes' === get_term_meta( $queried_object->term_id, '_seopress_robots_index', true ) ) {
			return get_term_meta( $queried_object->term_id, '_seopress_robots_index', true );
		}

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $current_tax ]['noindex'] ) ) {
			return;
		}

		return $option[ $current_tax ]['noindex'];
	}

	/**
	 * The getTaxNoFollow function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getTaxNoFollow() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( is_search() ) {
			return;
		}

		$queried_object = get_queried_object();
		$current_tax    = null !== $queried_object ? $queried_object->taxonomy : '';

		if ( null === $queried_object ) {
			global $tax;
			if ( null !== $tax && property_exists( $tax, 'name' ) ) {
				$current_tax = $tax->name;
			}
		}

		if ( null !== $queried_object && 'yes' === get_term_meta( $queried_object->term_id, '_seopress_robots_follow', true ) ) {
			return get_term_meta( $queried_object->term_id, '_seopress_robots_follow', true );
		}

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $current_tax ]['nofollow'] ) ) {
			return;
		}

		return $option[ $current_tax ]['nofollow'];
	}

	/**
	 * The getTaxEnable function.
	 *
	 * @since 6.6.0
	 *
	 * @param int|null $current_tax The current taxonomy.
	 *
	 * @return string
	 */
	public function getTaxEnable( $current_tax ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( null === $current_tax ) {
			return;
		}

		$option = $this->searchOptionByKey( 'seopress_titles_tax_titles' );

		if ( ! isset( $option[ $current_tax ]['enable'] ) ) {
			return;
		}

		return $option[ $current_tax ]['enable'];
	}

	/**
	 * The getPagedRel function.
	 *
	 * @since 5.4.0
	 *
	 * @return string
	 */
	public function getPagedRel() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_paged_rel' );
	}

	/**
	 * The getTitleBpGroups function.
	 *
	 * @since 5.4.0
	 */
	public function getTitleBpGroups() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_bp_groups_title' );
	}

	/**
	 * The getBpGroupsDesc function.
	 *
	 * @since 5.9.0
	 */
	public function getBpGroupsDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_bp_groups_desc' );
	}

	/**
	 * The getBpGroupsNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getBpGroupsNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_bp_groups_noindex' );
	}

	/**
	 * The getArchivesDateDesc function.
	 *
	 * @since 5.9.0
	 */
	public function getArchivesDateDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_date_desc' );
	}

	/**
	 * The getArchivesDateNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getArchivesDateNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_date_noindex' );
	}

	/**
	 * The getArchivesSearchDesc function.
	 *
	 * @since 5.9.0
	 */
	public function getArchivesSearchDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_search_desc' );
	}

	/**
	 * The getArchivesSearchNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getArchivesSearchNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_search_title_noindex' );
	}

	/**
	 * The getArchives404Desc function.
	 *
	 * @since 5.9.0
	 */
	public function getArchives404Desc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_archives_404_desc' );
	}

	/**
	 * The getNoSiteLinksSearchBox function.
	 *
	 * @since 5.9.0
	 */
	public function getNoSiteLinksSearchBox() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_nositelinkssearchbox' );
	}

	/**
	 * The getAttachmentsNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getAttachmentsNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_attachments_noindex' );
	}

	/**
	 * The getPagedNoIndex function.
	 *
	 * @since 6.6.0
	 */
	public function getPagedNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_titles_paged_noindex' );
	}
}

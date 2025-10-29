<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use SEOPress\Constants\Options;

/**
 * NoticeOption
 */
class NoticeOption {

	/**
	 * The getOption function.
	 *
	 * @since 6.0.0
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_NOTICE );
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 6.0.0
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
	 * The getNoticeGetStarted function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getNoticeGetStarted() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-get-started' );
	}

	/**
	 * The getNoticeTasks function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getNoticeTasks() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-tasks' );
	}

	/**
	 * The getNoticeReview function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeReview() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-review' );
	}

	/**
	 * The getNoticeUSM function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeUSM() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-usm' );
	}

	/**
	 * The getNoticeWizard function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getNoticeWizard() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-wizard' );
	}

	/**
	 * The getNoticeAMPAnalytics function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeAMPAnalytics() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-amp-analytics' );
	}

	/**
	 * The getNoticeLiteSpeedCache function.
	 *
	 * @since 8.1.0
	 *
	 * @return string
	 */
	public function getNoticeLiteSpeedCache() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-litespeed-cache' );
	}

	/**
	 * The getNoticeTitleTag function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeTitleTag() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-title-tag' );
	}

	/**
	 * The getNoticeWPMLActive function.
	 *
	 * @since 7.6.0
	 *
	 * @return string
	 */
	public function getNoticeWPMLActive() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-wpml-active' );
	}

	/**
	 * The getNoticeCacheSitemap function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeCacheSitemap() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-cache-sitemap' );
	}

	/**
	 * The getNoticeSwift function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeSwift() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-swift' );
	}

	/**
	 * The getNoticeEnfold function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeEnfold() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-enfold' );
	}

	/**
	 * The getNoticeSSL function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeSSL() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-ssl' );
	}

	/**
	 * The getNoticeNoIndex function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeNoIndex() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-noindex' );
	}

	/**
	 * The getNoticeRSSUseExcerpt function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeRSSUseExcerpt() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-rss-use-excerpt' );
	}

	/**
	 * The getNoticeGAIds function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeGAIds() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-ga-ids' );
	}

	/**
	 * The getNoticeDivideComments function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeDivideComments() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-divide-comments' );
	}

	/**
	 * The getNoticePostsNumber function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticePostsNumber() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-posts-number' );
	}

	/**
	 * The getNoticeXMLSitemaps function.
	 *
	 * @since 8.3.0
	 *
	 * @return string
	 */
	public function getNoticeXMLSitemaps() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-xml-sitemaps' );
	}

	/**
	 * The getNoticeGoogleBusiness function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeGoogleBusiness() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-google-business' );
	}

	/**
	 * The getNoticeSearchConsole function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeSearchConsole() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-search-console' );
	}

	/**
	 * The getNoticeEbooks function.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function getNoticeEbooks() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-ebooks' );
	}

	/**
	 * The getNoticeIntegrations function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getNoticeIntegrations() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-integrations' );
	}

	/**
	 * The getNoticeInsights function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getNoticeInsights() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'notice-insights' );
	}
}

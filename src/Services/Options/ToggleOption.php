<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * ToggleOption
 */
class ToggleOption {

	/**
	 * The getOption function.
	 *
	 * @param bool $is_multisite The is multisite.
	 *
	 * @since 4.3.0
	 *
	 * @return array
	 */
	public function getOption( $is_multisite ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( true === $is_multisite && function_exists( 'get_network' ) ) {
			$network         = get_network();
			$main_network_id = $network->site_id;
			return get_blog_option( $main_network_id, Options::KEY_TOGGLE_OPTION );
		} else {
			return get_option( Options::KEY_TOGGLE_OPTION );
		}
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 4.3.0
	 *
	 * @param string $key The key.
	 * @param bool   $is_multisite The is multisite.
	 *
	 * @return mixed
	 */
	public function searchOptionByKey( $key, $is_multisite = false ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->getOption( $is_multisite );

		if ( empty( $data ) ) {
			return null;
		}

		$key_composed = sprintf( 'toggle-%s', $key );
		if ( ! isset( $data[ $key_composed ] ) ) {
			return null;
		}

		return $data[ $key_composed ];
	}

	/**
	 * The getToggleLocalBusiness function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getToggleLocalBusiness() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'local-business' );
	}

	/**
	 * The getToggleGoogleNews function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getToggleGoogleNews() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'news' );
	}

	/**
	 * The getToggleInspectUrl function.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function getToggleInspectUrl() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'inspect-url' );
	}

	/**
	 * The getToggleAi function.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function getToggleAi() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'ai' );
	}

	/**
	 * The getToggleAlerts function.
	 *
	 * @since 7.8.0
	 *
	 * @return string
	 */
	public function getToggleAlerts() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'alerts' );
	}

	/**
	 * The getToggleBot function.
	 *
	 * @since 8.2
	 *
	 * @return string
	 */
	public function getToggleBot() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'bot' );
	}

	/**
	 * The getToggleWhiteLabel function.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getToggleWhiteLabel() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( is_network_admin() || is_multisite() ) {
			return $this->searchOptionByKey( 'white-label', true );
		}
		return $this->searchOptionByKey( 'white-label' );
	}
}

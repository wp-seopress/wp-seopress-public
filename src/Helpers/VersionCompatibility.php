<?php

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Version Compatibility Helper
 *
 * Centralized version checking for cross-plugin features that require
 * both FREE and PRO versions to meet minimum requirements.
 *
 * @since 9.4.0
 */
abstract class VersionCompatibility {

	/**
	 * Check if both FREE and PRO meet minimum version requirements.
	 *
	 * @since 9.4.0
	 *
	 * @param string $min_free_version Minimum FREE version required.
	 * @param string $min_pro_version  Minimum PRO version required.
	 * @return array Array with 'compatible' (bool) and 'message' (string) keys.
	 */
	public static function checkVersions( $min_free_version, $min_pro_version ) {
		$free_version = defined( 'SEOPRESS_VERSION' ) ? SEOPRESS_VERSION : '0';
		$pro_version  = defined( 'SEOPRESS_PRO_VERSION' ) ? SEOPRESS_PRO_VERSION : '0';

		// Dev mode bypass - versions are {VERSION} in development.
		if ( '{VERSION}' === $free_version || '{VERSION}' === $pro_version ) {
			return array(
				'compatible' => true,
				'message'    => '',
			);
		}

		// Check FREE version.
		if ( version_compare( $free_version, $min_free_version, '<' ) ) {
			return array(
				'compatible' => false,
				'message'    => sprintf(
					/* translators: 1: required version, 2: current version */
					__( 'This feature requires SEOPress (free) version %1$s or higher. Current version: %2$s. Please update SEOPress.', 'wp-seopress' ),
					$min_free_version,
					$free_version
				),
			);
		}

		// Check PRO version.
		if ( version_compare( $pro_version, $min_pro_version, '<' ) ) {
			return array(
				'compatible' => false,
				'message'    => sprintf(
					/* translators: 1: required version, 2: current version */
					__( 'This feature requires SEOPress PRO version %1$s or higher. Current version: %2$s. Please update SEOPress PRO.', 'wp-seopress-pro' ),
					$min_pro_version,
					$pro_version
				),
			);
		}

		return array(
			'compatible' => true,
			'message'    => '',
		);
	}

	/**
	 * Get version check response for REST API.
	 *
	 * Convenience method for REST API endpoints to check version compatibility
	 * and return an error response if versions don't meet requirements.
	 *
	 * @since 9.4.0
	 *
	 * @param string $min_free_version Minimum FREE version required.
	 * @param string $min_pro_version  Minimum PRO version required.
	 * @return \WP_REST_Response|null Returns error response if incompatible, null if compatible.
	 */
	public static function getRestErrorResponse( $min_free_version, $min_pro_version ) {
		$check = self::checkVersions( $min_free_version, $min_pro_version );

		if ( ! $check['compatible'] ) {
			return new \WP_REST_Response(
				array(
					'message' => $check['message'],
					'content' => '',
				),
				400
			);
		}

		return null;
	}
}

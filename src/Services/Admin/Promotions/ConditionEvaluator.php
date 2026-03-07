<?php // phpcs:ignore

namespace SEOPress\Services\Admin\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ConditionEvaluator
 *
 * Evaluates display conditions for promotions against current environment.
 *
 * @since 9.6.0
 */
class ConditionEvaluator {

	/**
	 * Evaluate all conditions for a promotion.
	 *
	 * @since 9.6.0
	 *
	 * @param array $conditions The conditions to evaluate.
	 *
	 * @return bool True if all conditions pass, false otherwise.
	 */
	public function evaluate( array $conditions ): bool {
		if ( empty( $conditions ) ) {
			return true;
		}

		$evaluators = $this->getEvaluators();

		foreach ( $conditions as $key => $value ) {
			// Skip null values (condition not set).
			if ( null === $value ) {
				continue;
			}

			if ( isset( $evaluators[ $key ] ) ) {
				if ( ! call_user_func( $evaluators[ $key ], $value ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get the condition evaluators.
	 *
	 * @since 9.6.0
	 *
	 * @return array Associative array of condition evaluators.
	 */
	protected function getEvaluators(): array {
		return array(
			'pro_installed'             => array( $this, 'checkProInstalled' ),
			'pro_license_active'        => array( $this, 'checkLicenseActive' ),
			'pro_license_expired'       => array( $this, 'checkLicenseExpired' ),
			'seopress_version_min'      => array( $this, 'checkVersionMin' ),
			'seopress_version_max'      => array( $this, 'checkVersionMax' ),
			'locale'                    => array( $this, 'checkLocale' ),
			'plugins_active'            => array( $this, 'checkPluginsActive' ),
			'plugins_inactive'          => array( $this, 'checkPluginsInactive' ),
			'show_after_days_installed' => array( $this, 'checkInstallAge' ),
			'max_dismissals'            => array( $this, 'checkDismissalCount' ),
			'user_role'                 => array( $this, 'checkUserRole' ),
			'is_multisite'              => array( $this, 'checkIsMultisite' ),
		);
	}

	/**
	 * Check if PRO plugin is installed.
	 *
	 * @since 9.6.0
	 *
	 * @param bool $expected Expected value.
	 *
	 * @return bool
	 */
	protected function checkProInstalled( $expected ): bool {
		$is_installed = is_plugin_active( 'wp-seopress-pro/seopress-pro.php' );
		return $expected === $is_installed;
	}

	/**
	 * Check if PRO license is active.
	 *
	 * @since 9.6.0
	 *
	 * @param bool $expected Expected value.
	 *
	 * @return bool
	 */
	protected function checkLicenseActive( $expected ): bool {
		$license_status = get_option( 'seopress_pro_license_status' );
		$is_active      = 'valid' === $license_status;

		// Also check expiry date - license is NOT active if expired.
		if ( $is_active ) {
			$license_expiry = get_option( 'seopress_pro_license_expiry' );
			if ( is_numeric( $license_expiry ) && $license_expiry < time() ) {
				$is_active = false;
			}
		}

		return $expected === $is_active;
	}

	/**
	 * Check if PRO license is expired.
	 *
	 * @since 9.6.0
	 *
	 * @param bool $expected Expected value.
	 *
	 * @return bool
	 */
	protected function checkLicenseExpired( $expected ): bool {
		$license_status = get_option( 'seopress_pro_license_status' );
		$is_expired     = 'expired' === $license_status;

		// Also check expiry date.
		if ( ! $is_expired ) {
			$license_expiry = get_option( 'seopress_pro_license_expiry' );
			if ( is_numeric( $license_expiry ) && $license_expiry < time() ) {
				$is_expired = true;
			}
		}

		return $expected === $is_expired;
	}

	/**
	 * Check minimum SEOPress version.
	 *
	 * @since 9.6.0
	 *
	 * @param string $min_version Minimum version required.
	 *
	 * @return bool
	 */
	protected function checkVersionMin( $min_version ): bool {
		return version_compare( SEOPRESS_VERSION, $min_version, '>=' );
	}

	/**
	 * Check maximum SEOPress version.
	 *
	 * @since 9.6.0
	 *
	 * @param string $max_version Maximum version allowed.
	 *
	 * @return bool
	 */
	protected function checkVersionMax( $max_version ): bool {
		return version_compare( SEOPRESS_VERSION, $max_version, '<=' );
	}

	/**
	 * Check if current locale matches.
	 *
	 * @since 9.6.0
	 *
	 * @param array|string $locales Allowed locales.
	 *
	 * @return bool
	 */
	protected function checkLocale( $locales ): bool {
		$current_locale = get_locale();
		$locales        = (array) $locales;

		// Empty array means all locales are allowed.
		if ( empty( $locales ) ) {
			return true;
		}

		return in_array( $current_locale, $locales, true );
	}

	/**
	 * Check if required plugins are active.
	 *
	 * @since 9.6.0
	 *
	 * @param array $plugins List of plugin paths.
	 *
	 * @return bool True if all plugins are active.
	 */
	protected function checkPluginsActive( $plugins ): bool {
		$plugins = (array) $plugins;

		foreach ( $plugins as $plugin ) {
			if ( ! is_plugin_active( $plugin ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if required plugins are inactive.
	 *
	 * @since 9.6.0
	 *
	 * @param array $plugins List of plugin paths.
	 *
	 * @return bool True if all plugins are inactive.
	 */
	protected function checkPluginsInactive( $plugins ): bool {
		$plugins = (array) $plugins;

		foreach ( $plugins as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if plugin has been installed for required number of days.
	 *
	 * @since 9.6.0
	 *
	 * @param int $days Number of days since installation.
	 *
	 * @return bool
	 */
	protected function checkInstallAge( $days ): bool {
		$install_date = get_option( 'seopress_activated' );

		if ( ! $install_date ) {
			// If no install date, assume it's new.
			return 0 === $days;
		}

		$install_timestamp = strtotime( $install_date );
		if ( ! $install_timestamp ) {
			return true;
		}

		$days_installed = ( time() - $install_timestamp ) / DAY_IN_SECONDS;

		return $days_installed >= $days;
	}

	/**
	 * Check if max dismissals have been reached for a promotion.
	 *
	 * @since 9.6.0
	 *
	 * @param int $max_dismissals Maximum number of dismissals allowed.
	 *
	 * @return bool True if under the limit.
	 */
	protected function checkDismissalCount( $max_dismissals ): bool {
		// This will be checked in conjunction with a specific promo_id.
		// The PromotionService will handle passing the count to this evaluator.
		// For now, return true - actual check happens in PromotionService.
		return true;
	}

	/**
	 * Check if current user has required role.
	 *
	 * @since 9.6.0
	 *
	 * @param array|string $roles Required roles.
	 *
	 * @return bool
	 */
	protected function checkUserRole( $roles ): bool {
		$user = wp_get_current_user();
		if ( ! $user || ! $user->exists() ) {
			return false;
		}

		$roles      = (array) $roles;
		$user_roles = (array) $user->roles;

		// Empty array means all roles are allowed.
		if ( empty( $roles ) ) {
			return true;
		}

		return ! empty( array_intersect( $roles, $user_roles ) );
	}

	/**
	 * Check if WordPress is a multisite installation.
	 *
	 * @since 9.6.0
	 *
	 * @param bool $expected Expected value.
	 *
	 * @return bool
	 */
	protected function checkIsMultisite( $expected ): bool {
		return $expected === is_multisite();
	}
}

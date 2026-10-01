<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for the White Label state in the free plugin.
 *
 * White Label is a PRO feature, but most of the surfaces it has to hide or
 * rename live in the free plugin — the Dashboard, the settings screens, the
 * review prompt. Before this helper each of those re-implemented the same
 * "is PRO active, is the toggle on, does it define a custom title" dance
 * inline, which is why the React screens were simply forgotten.
 *
 * Everything is exposed through filters so PRO (or a site owner) can drive
 * the state without the free plugin reaching into PRO services.
 *
 * @since 10.3.0
 */
class WhiteLabel {

	/**
	 * Product names used as the substitution source in `rebrand()` and as the
	 * default returned by the name getters.
	 *
	 * Order matters: the PRO name has to be replaced before the free one, or
	 * "SEOPress PRO" would be rewritten as "<free name> PRO".
	 *
	 * @var string
	 */
	const DEFAULT_NAME = 'SEOPress';

	/**
	 * Default PRO product name.
	 *
	 * @var string
	 */
	const DEFAULT_NAME_PRO = 'SEOPress PRO';

	/**
	 * Hosts that identify the vendor. A link to any of these reveals the real
	 * product to a client the agency is white labeling it away from.
	 *
	 * @var string[]
	 */
	const VENDOR_HOSTS = array( 'seopress.org', 'www.seopress.org' );

	/**
	 * Whether White Label is currently active.
	 *
	 * Reads the PRO toggle directly so the free plugin behaves correctly with
	 * the PRO versions already in the wild, which do not hook the filter below.
	 *
	 * @return bool
	 */
	public static function isEnabled() {
		$enabled = false;

		if ( self::isProActive() ) {
			$toggle = seopress_get_service( 'ToggleOption' );
			if ( method_exists( $toggle, 'getToggleWhiteLabel' ) && '1' === $toggle->getToggleWhiteLabel() ) {
				$enabled = true;
			}
		}

		/**
		 * Filter whether White Label is active.
		 *
		 * @since 10.3.0
		 *
		 * @param bool $enabled Whether White Label is active.
		 */
		return (bool) apply_filters( 'seopress_white_label_enabled', $enabled );
	}

	/**
	 * Effective name of the free plugin — the custom title when White Label is
	 * on and one is configured, "SEOPress" otherwise.
	 *
	 * @return string
	 */
	public static function pluginName() {
		$name = self::DEFAULT_NAME;

		if ( self::isEnabled() ) {
			// "Change plugin title in plugins list" is the field that names the
			// product, so it wins. A site that only renamed the admin menu
			// entry still gets its own name rather than the brand.
			$custom = self::proOption( 'getWhiteLabelListTitle' );
			if ( '' === $custom ) {
				$custom = self::proOption( 'getWhiteLabelAdminTitle' );
			}
			if ( '' !== $custom ) {
				$name = $custom;
			}
		}

		/**
		 * Filter the effective plugin name shown across the admin.
		 *
		 * @since 10.3.0
		 *
		 * @param string $name Effective plugin name.
		 */
		return (string) apply_filters( 'seopress_white_label_plugin_name', $name );
	}

	/**
	 * Effective name of the PRO plugin.
	 *
	 * @return string
	 */
	public static function proPluginName() {
		$name = self::DEFAULT_NAME_PRO;

		if ( self::isEnabled() ) {
			$custom = self::proOption( 'getWhiteLabelListTitlePro' );

			// No PRO name configured: fall back to the free one rather than to
			// "SEOPress PRO", which would be the last place the brand survives
			// for a site that only renamed the free plugin. Only when that free
			// name is itself custom — otherwise nothing is configured at all
			// and "SEOPress PRO" stays the more accurate label.
			if ( '' === $custom ) {
				$free = self::pluginName();
				if ( self::DEFAULT_NAME !== $free ) {
					$custom = $free;
				}
			}

			if ( '' !== $custom ) {
				$name = $custom;
			}
		}

		/**
		 * Filter the effective PRO plugin name shown across the admin.
		 *
		 * @since 10.3.0
		 *
		 * @param string $name Effective PRO plugin name.
		 */
		return (string) apply_filters( 'seopress_white_label_plugin_name_pro', $name );
	}

	/**
	 * Substitute the product names in a display string.
	 *
	 * A no-op when White Label is off, so the wording users see today never
	 * changes. When it is on this catches the brand mentions baked into
	 * translated strings, which cannot be parameterized after the fact without
	 * invalidating every existing translation.
	 *
	 * @param string $text Display string.
	 *
	 * @return string
	 */
	public static function rebrand( $text ) {
		if ( ! is_string( $text ) || '' === $text || ! self::isEnabled() ) {
			return $text;
		}

		return str_replace(
			array( self::DEFAULT_NAME_PRO, self::DEFAULT_NAME ),
			array( self::proPluginName(), self::pluginName() ),
			$text
		);
	}

	/**
	 * Whether a URL points at the vendor and would give the product away.
	 *
	 * Covers both the marketing/docs site and the wordpress.org listing, whose
	 * slug names the plugin outright.
	 *
	 * @param string $url URL to test.
	 *
	 * @return bool
	 */
	public static function isVendorUrl( $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			return false;
		}

		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( $host && in_array( strtolower( $host ), self::VENDOR_HOSTS, true ) ) {
			return true;
		}

		return false !== strpos( $url, 'wp-seopress' );
	}

	/**
	 * Whether SEOPress PRO is active.
	 *
	 * `is_plugin_active()` lives in an admin include that is not always loaded
	 * this early, so fall back to the constant PRO defines on boot.
	 *
	 * @return bool
	 */
	private static function isProActive() {
		if ( function_exists( 'is_plugin_active' ) ) {
			return is_plugin_active( 'wp-seopress-pro/seopress-pro.php' );
		}

		return defined( 'SEOPRESS_PRO_VERSION' );
	}

	/**
	 * Read a White Label option from the PRO service, if it is reachable.
	 *
	 * @param string $method OptionPro getter name.
	 *
	 * @return string Empty string when PRO or the getter is unavailable.
	 */
	private static function proOption( $method ) {
		if ( ! function_exists( 'seopress_pro_get_service' ) ) {
			return '';
		}

		$options = seopress_pro_get_service( 'OptionPro' );
		if ( ! is_object( $options ) || ! method_exists( $options, $method ) ) {
			return '';
		}

		$value = $options->$method();

		return is_string( $value ) ? trim( $value ) : '';
	}
}

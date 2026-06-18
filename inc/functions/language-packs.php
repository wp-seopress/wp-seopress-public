<?php
/**
 * On-demand language pack installation.
 *
 * WordPress only downloads plugin language packs from translate.wordpress.org
 * during its twice-daily translation-update cron (or a manual visit to
 * Dashboard -> Updates). On a fresh install the .mo / -js .json files do not
 * exist yet, so the admin UI - and especially the setup wizard the user is
 * redirected to right after activation - stays in English on a non-English site.
 *
 * This module fetches the language pack metadata for the current admin locale
 * straight from the translations API and installs it through core's
 * Language_Pack_Upgrader, synchronously, right before the post-activation
 * redirect and on first load of any SEOPress admin screen. The translation
 * object is built directly from the API instead of relying on the
 * update_plugins transient, which can be wiped by background wp_update_plugins()
 * calls (see the Pro #1357 note in seopress-pro.php).
 *
 * @package SEOPress
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hooked early on admin_init so it runs before seopress_redirect_after_activation()
 * (priority 10): on the post-activation request the pack is installed first, then
 * the redirect to the wizard happens, so the wizard renders already localized.
 *
 * @return void
 */
function seopress_maybe_install_language_packs() {
	if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'install_languages' ) ) {
		return;
	}

	$locale = get_user_locale();

	if ( '' === $locale || 'en_US' === $locale ) {
		return;
	}

	// Act on SEOPress admin screens, and - right after activation - on whatever
	// admin screen WordPress lands on (usually plugins.php) so the wizard is
	// localized on first view. The activation state is resolved last and only when
	// off a SEOPress screen, because the seopress_force_install_language_packs
	// filter can read a transient (Pro) we don't want to hit on every screen.
	$current_page       = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$is_seopress_screen = '' !== $current_page && 0 === strpos( $current_page, 'seopress' );

	if ( ! $is_seopress_screen ) {
		$is_activation = ( 'yes' === get_option( 'seopress_activated' ) )
			|| (bool) apply_filters( 'seopress_force_install_language_packs', false );

		if ( ! $is_activation ) {
			return;
		}
	}

	/**
	 * Filters the list of language packs SEOPress installs on demand.
	 *
	 * Each entry is an array with:
	 *  - slug    (string) Plugin directory slug used to name the .mo file.
	 *  - version (string) Plugin version (used by the wp.org translations API).
	 *  - source  (string) 'wporg' for translate.wordpress.org, or 'url' for a
	 *                     custom GlotPress-style packages.json endpoint.
	 *  - api_url (string) Required when source is 'url'.
	 *
	 * SEOPress PRO uses this to register its TranslationsPress pack.
	 *
	 * @param array  $packs  Packs to install.
	 * @param string $locale Current admin locale (e.g. fr_FR).
	 */
	$packs = apply_filters(
		'seopress_language_packs',
		array(
			array(
				'slug'    => 'wp-seopress',
				'version' => SEOPRESS_VERSION,
				'source'  => 'wporg',
			),
		),
		$locale
	);

	if ( empty( $packs ) || ! is_array( $packs ) ) {
		return;
	}

	foreach ( $packs as $pack ) {
		if ( empty( $pack['slug'] ) ) {
			continue;
		}

		// Already installed for this locale - cheap check that runs every request.
		if ( seopress_language_pack_installed( $pack['slug'], $locale ) ) {
			continue;
		}

		// Throttle remote lookups per slug + locale so an unavailable pack or a
		// failing API does not get hit on every admin pageload.
		$throttle_key = 'seopress_langpack_' . md5( $pack['slug'] . '|' . $locale );

		if ( false !== get_transient( $throttle_key ) ) {
			continue;
		}

		set_transient( $throttle_key, 1, 12 * HOUR_IN_SECONDS );

		seopress_install_language_pack( $pack, $locale );
	}
}
add_action( 'admin_init', 'seopress_maybe_install_language_packs', 5 );

/**
 * Whether the .mo language pack for a slug + locale is already present.
 *
 * @param string $slug   Plugin directory slug.
 * @param string $locale Locale, e.g. fr_FR.
 * @return bool
 */
function seopress_language_pack_installed( $slug, $locale ) {
	return file_exists( WP_LANG_DIR . '/plugins/' . $slug . '-' . $locale . '.mo' );
}

/**
 * Downloads and installs a single language pack for the given locale.
 *
 * @param array  $pack   Pack descriptor (see seopress_maybe_install_language_packs()).
 * @param string $locale Locale, e.g. fr_FR.
 * @return bool True on success.
 */
function seopress_install_language_pack( $pack, $locale ) {
	$translation = seopress_get_remote_language_pack( $pack, $locale );

	if ( false === $translation ) {
		return false;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( ! class_exists( 'Language_Pack_Upgrader' ) || ! class_exists( 'Automatic_Upgrader_Skin' ) ) {
		return false;
	}

	$upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->bulk_upgrade( array( (object) $translation ), array( 'clear_update_cache' => false ) );

	// bulk_upgrade() returns false on filesystem failure, or an array with one
	// result per pack; a per-pack failure is false or a WP_Error.
	$item    = is_array( $result ) && isset( $result[0] ) ? $result[0] : false;
	$success = ! empty( $item ) && ! is_wp_error( $item );

	/**
	 * Fires after SEOPress attempts to install a language pack on demand.
	 *
	 * @param string $slug    Plugin slug.
	 * @param string $locale  Locale.
	 * @param bool   $success Whether the install succeeded.
	 */
	do_action( 'seopress_language_pack_installed', $pack['slug'], $locale, $success );

	return $success;
}

/**
 * Resolves the translation object for a pack + locale from the remote API.
 *
 * Returns an object-ready array shaped for Language_Pack_Upgrader::bulk_upgrade()
 * or false when no pack is available for the locale.
 *
 * @param array  $pack   Pack descriptor.
 * @param string $locale Locale, e.g. fr_FR.
 * @return array|false
 */
function seopress_get_remote_language_pack( $pack, $locale ) {
	$source = isset( $pack['source'] ) ? $pack['source'] : 'wporg';

	if ( 'wporg' === $source ) {
		$args = array( 'slug' => $pack['slug'] );

		// SEOPRESS_VERSION is a build placeholder in source; only send a real version.
		if ( ! empty( $pack['version'] ) && false === strpos( (string) $pack['version'], '{' ) ) {
			$args['version'] = $pack['version'];
		}

		$url = add_query_arg( $args, 'https://api.wordpress.org/translations/plugins/1.0/' );
	} elseif ( ! empty( $pack['api_url'] ) ) {
		$url = $pack['api_url'];
	} else {
		return false;
	}

	$response = wp_remote_get( $url, array( 'timeout' => 8 ) );

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $body['translations'] ) || ! is_array( $body['translations'] ) ) {
		return false;
	}

	foreach ( $body['translations'] as $translation ) {
		if ( ! isset( $translation['language'] ) || $translation['language'] !== $locale ) {
			continue;
		}

		if ( empty( $translation['package'] ) ) {
			return false;
		}

		return array(
			'type'       => 'plugin',
			'slug'       => $pack['slug'],
			'language'   => $translation['language'],
			'version'    => isset( $translation['version'] ) ? $translation['version'] : '',
			'updated'    => isset( $translation['updated'] ) ? $translation['updated'] : '',
			'package'    => $translation['package'],
			'autoupdate' => true,
		);
	}

	return false;
}

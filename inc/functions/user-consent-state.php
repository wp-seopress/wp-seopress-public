<?php
/**
 * Consent state, resolved in a way a full-page cache cannot freeze.
 *
 * The decision lives in a browser cookie, but the page reacting to it is very
 * often served from a full-page cache: WP Rocket, LiteSpeed, W3 Total Cache and
 * friends store one HTML document and hand it to every later visitor. Anything
 * the front-end renders out of $_COOKIE is therefore the answer of whoever
 * happened to prime the cache, replayed to everybody else. A cache primed by a
 * visitor who accepted grants Consent Mode to visitors who never answered, and
 * that is a regulatory problem rather than a cosmetic one.
 *
 * So the front-end always renders the same variant, the "no decision yet" one,
 * and the browser resolves the real state from its own cookies. Only responses
 * a page cache never stores -- admin-ajax, the REST API -- read the cookie
 * server-side.
 *
 * @package Functions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read the decision the browser sent with the request.
 *
 * Only meaningful on a response no page cache can store. Everything rendering
 * front-end HTML wants seopress_user_consent_state() instead.
 *
 * @since 10.3.0
 *
 * @return string 'accept', 'decline', or 'unknown' when the visitor has yet to answer.
 */
function seopress_user_consent_cookie_state() {
	$accept = isset( $_COOKIE['seopress-user-consent-accept'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['seopress-user-consent-accept'] ) ) : '';

	if ( '1' === $accept ) {
		return 'accept';
	}

	$decline = isset( $_COOKIE['seopress-user-consent-close'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['seopress-user-consent-close'] ) ) : '';

	if ( '1' === $decline ) {
		return 'decline';
	}

	return 'unknown';
}

/**
 * Whether the current response is one a full-page cache may store and replay.
 *
 * @since 10.3.0
 *
 * @return bool
 */
function seopress_user_consent_is_cacheable_response() {
	if ( wp_doing_ajax() || is_admin() ) {
		return false;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}

	return true;
}

/**
 * Read or set the flag saying the deferred tracking payload is being built.
 *
 * That payload is embedded inert in the page and only ever executed by a
 * browser holding the acceptance cookie, so it has to be built as if consent
 * had been granted no matter who is asking for the page.
 *
 * @since 10.3.0
 *
 * @param bool|null $building True or false to set the flag, null to read it.
 *
 * @return bool
 */
function seopress_user_consent_building_payload( $building = null ) {
	static $flag = false;

	if ( is_bool( $building ) ) {
		$flag = $building;
	}

	return $flag;
}

/**
 * The consent state the tracking snippets must be built for.
 *
 * @since 10.3.0
 *
 * @return string 'accept', 'decline' or 'unknown'.
 */
function seopress_user_consent_state() {
	if ( seopress_user_consent_building_payload() ) {
		return 'accept';
	}

	if ( seopress_user_consent_is_cacheable_response() ) {
		return 'unknown';
	}

	return seopress_user_consent_cookie_state();
}

/**
 * Whether the current user holds a role excluded from analytics tracking.
 *
 * Excluded roles see neither the banner nor any tracker, so they have nothing
 * to consent to.
 *
 * @since 10.3.0
 *
 * @return bool
 */
function seopress_user_consent_role_is_excluded() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$excluded = seopress_get_service( 'GoogleAnalyticsOption' )->getRoles();

	if ( empty( $excluded ) ) {
		return false;
	}

	$roles = wp_get_current_user()->roles;

	if ( ! isset( $roles[0] ) ) {
		return false;
	}

	return array_key_exists( $roles[0], $excluded );
}

/**
 * A JavaScript expression that is true when the browser holds the acceptance cookie.
 *
 * Anchored on the cookie boundaries rather than a substring search, so a
 * third-party cookie whose name merely ends with ours cannot pass for consent.
 *
 * @since 10.3.0
 *
 * @return string
 */
function seopress_user_consent_js_test() {
	return '/(?:^|;\s*)seopress-user-consent-accept=1(?:;|$)/.test(document.cookie)';
}

/**
 * The Google Consent Mode v2 update call for a decision.
 *
 * An update rather than a default: the page has already set its defaults, and
 * gtag ignores a second default for a signal it already knows.
 *
 * @since 10.3.0
 *
 * @param bool $granted Whether the visitor accepted.
 *
 * @return string
 */
function seopress_user_consent_gtag_update( $granted ) {
	$value = $granted ? 'granted' : 'denied';

	return "gtag('consent', 'update', {
    'ad_storage': '" . $value . "',
    'ad_user_data': '" . $value . "',
    'ad_personalization': '" . $value . "',
    'analytics_storage': '" . $value . "'
});\n";
}

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
 * Name of the cookie holding the categories a visitor granted.
 *
 * @since 10.4.0
 */
const SEOPRESS_USER_CONSENT_COOKIE = 'seopress-user-consent';

/**
 * The consent categories a visitor can answer, and the signals each one grants.
 *
 * Necessary is always granted and carries no signal: it is listed so the banner
 * can say so, and so the cookie is never empty once the visitor has answered.
 * The four Google Consent Mode v2 signals split into the two ideas people
 * actually recognise, measuring the audience and advertising.
 *
 * @since 10.4.0
 *
 * @return array Categories keyed by slug, each with a label, a description and its signals.
 */
function seopress_user_consent_categories() {
	$categories = array(
		'necessary'   => array(
			'label'       => __( 'Necessary', 'wp-seopress' ),
			'description' => __( 'Required for the site to work, such as remembering this choice.', 'wp-seopress' ),
			'signals'     => array(),
		),
		'analytics'   => array(
			'label'       => __( 'Analytics', 'wp-seopress' ),
			'description' => __( 'Help us understand how visitors use the site, with audience measurement and visit statistics.', 'wp-seopress' ),
			'signals'     => array( 'analytics_storage' ),
		),
		'advertising' => array(
			'label'       => __( 'Advertising', 'wp-seopress' ),
			'description' => __( 'Used to measure ad performance and show you relevant ads, including on other sites.', 'wp-seopress' ),
			'signals'     => array( 'ad_storage', 'ad_user_data', 'ad_personalization' ),
		),
	);

	/**
	 * Filter the labels and descriptions of the consent categories.
	 *
	 * Only the label and description of the existing categories can be changed:
	 * the slugs and the signals they grant are what the rest of the consent
	 * flow, in PHP and in the browser, relies on.
	 *
	 * @since 10.4.0
	 *
	 * @param array $categories Categories keyed by slug.
	 */
	$filtered = apply_filters( 'seopress_user_consent_categories', $categories );

	if ( is_array( $filtered ) ) {
		foreach ( $categories as $slug => $category ) {
			foreach ( array( 'label', 'description' ) as $field ) {
				if ( isset( $filtered[ $slug ][ $field ] ) && is_string( $filtered[ $slug ][ $field ] ) ) {
					$categories[ $slug ][ $field ] = $filtered[ $slug ][ $field ];
				}
			}
		}
	}

	return $categories;
}

/**
 * Every category slug, Necessary included.
 *
 * @since 10.4.0
 *
 * @return string[]
 */
function seopress_user_consent_category_slugs() {
	return array( 'necessary', 'analytics', 'advertising' );
}

/**
 * Which categories each deferred tracker needs before it may start.
 *
 * A tracker starts once at least one of its categories is granted. Google's tag
 * starts on either, because Consent Mode then applies each signal on its own.
 * The custom tracking code is arbitrary markup pasted by the site owner, a Meta
 * Pixel as easily as a heatmap, so it is the one thing that can only start once
 * everything has been granted: listed as a single combined requirement.
 *
 * @since 10.4.0
 *
 * @return array Payload keys mapped to a list of alternatives, each a list of categories that must all be granted.
 */
function seopress_user_consent_snippet_categories() {
	$everything = array( array( 'analytics', 'advertising' ) );

	$requirements = array(
		'gtag_js'        => array( array( 'analytics' ), array( 'advertising' ) ),
		'matomo_js'      => array( array( 'analytics' ) ),
		'matomo_body_js' => array( array( 'analytics' ) ),
		'clarity_js'     => array( array( 'analytics' ) ),
		'custom'         => $everything,
		'head_js'        => $everything,
		'body_js'        => $everything,
		'footer_js'      => $everything,
	);

	/**
	 * Filter which consent categories each deferred tracker needs.
	 *
	 * Each value is a list of alternatives, and each alternative a list of
	 * categories that must all be granted. `array( array( 'analytics' ) )`
	 * starts a tracker on Analytics alone, `array( array( 'analytics',
	 * 'advertising' ) )` only once both are granted.
	 *
	 * @since 10.4.0
	 *
	 * @param array $requirements Payload keys mapped to their alternatives.
	 */
	$filtered = apply_filters( 'seopress_user_consent_snippet_categories', $requirements );

	if ( ! is_array( $filtered ) ) {
		return $requirements;
	}

	$known = seopress_user_consent_category_slugs();
	$clean = array();

	foreach ( $filtered as $key => $alternatives ) {
		if ( ! is_string( $key ) || ! is_array( $alternatives ) ) {
			continue;
		}

		foreach ( $alternatives as $alternative ) {
			$alternative = array_values( array_intersect( (array) $alternative, $known ) );
			if ( ! empty( $alternative ) ) {
				$clean[ $key ][] = $alternative;
			}
		}

		// A tracker whose every alternative was unknown must not start on nothing.
		if ( empty( $clean[ $key ] ) ) {
			$clean[ $key ] = $everything;
		}
	}

	return $clean;
}

/**
 * Turn a raw cookie value into the categories it grants.
 *
 * @since 10.4.0
 *
 * @param string $value Categories separated by a pipe, as the banner writes them.
 *
 * @return string[] Known categories, Necessary always first.
 */
function seopress_user_consent_parse_categories( $value ) {
	$granted = preg_split( '/\||%7C/i', (string) $value );
	$granted = array_intersect( seopress_user_consent_category_slugs(), $granted );

	return array_values( array_unique( array_merge( array( 'necessary' ), $granted ) ) );
}

/**
 * Read the categories the browser sent with the request.
 *
 * Only meaningful on a response no page cache can store. Everything rendering
 * front-end HTML wants seopress_user_consent_granted_categories() instead.
 *
 * Visitors who answered before categories existed still carry one of the two
 * older cookies, and keep their answer: acceptance grants every category, a
 * refusal none. They are not asked again, and the lifetime they agreed to is
 * left as it was.
 *
 * @since 10.4.0
 *
 * @return string[]|null The granted categories, or null when the visitor has yet to answer.
 */
function seopress_user_consent_cookie_categories() {
	if ( isset( $_COOKIE[ SEOPRESS_USER_CONSENT_COOKIE ] ) ) {
		// Decoded before sanitizing: a cookie library may have percent-encoded
		// the separator, which the text sanitizer would strip. Only known slugs
		// come out of the parser anyway.
		$value = rawurldecode( wp_unslash( $_COOKIE[ SEOPRESS_USER_CONSENT_COOKIE ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized on the next line, once decoded.

		return seopress_user_consent_parse_categories( sanitize_text_field( $value ) );
	}

	$accept = isset( $_COOKIE['seopress-user-consent-accept'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['seopress-user-consent-accept'] ) ) : '';

	if ( '1' === $accept ) {
		return seopress_user_consent_category_slugs();
	}

	$decline = isset( $_COOKIE['seopress-user-consent-close'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['seopress-user-consent-close'] ) ) : '';

	if ( '1' === $decline ) {
		return array( 'necessary' );
	}

	return null;
}

/**
 * Summarise a set of granted categories as one word.
 *
 * @since 10.4.0
 *
 * @param string[]|null $granted The granted categories, or null for no answer.
 *
 * @return string 'accept' when everything is granted, 'decline' when nothing optional is, 'partial' in between, 'unknown' for no answer.
 */
function seopress_user_consent_summarize( $granted ) {
	if ( null === $granted ) {
		return 'unknown';
	}

	$optional = array_diff( seopress_user_consent_category_slugs(), array( 'necessary' ) );
	$given    = array_intersect( $optional, $granted );

	if ( empty( $given ) ) {
		return 'decline';
	}

	return count( $given ) === count( $optional ) ? 'accept' : 'partial';
}

/**
 * Read the decision the browser sent with the request.
 *
 * Only meaningful on a response no page cache can store. Everything rendering
 * front-end HTML wants seopress_user_consent_state() instead.
 *
 * @since 10.3.0
 * @since 10.4.0 Returns 'partial' when only some categories were granted.
 *
 * @return string 'accept', 'decline', 'partial', or 'unknown' when the visitor has yet to answer.
 */
function seopress_user_consent_cookie_state() {
	return seopress_user_consent_summarize( seopress_user_consent_cookie_categories() );
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
 * The categories the tracking snippets must be built for.
 *
 * @since 10.4.0
 *
 * @return string[]|null The granted categories, or null when the answer has to be read by the browser.
 */
function seopress_user_consent_granted_categories() {
	if ( seopress_user_consent_building_payload() ) {
		return seopress_user_consent_category_slugs();
	}

	if ( seopress_user_consent_is_cacheable_response() ) {
		return null;
	}

	return seopress_user_consent_cookie_categories();
}

/**
 * Whether the visitor granted a consent category.
 *
 * Always false while rendering a page a cache may store, since the answer is
 * not the server's to read there: the browser resolves it, see
 * seopress_user_consent_js_categories().
 *
 * @since 10.4.0
 *
 * @param string $category 'necessary', 'analytics' or 'advertising'.
 *
 * @return bool
 */
function seopress_user_consent_has( $category ) {
	$granted = seopress_user_consent_granted_categories();

	return null !== $granted && in_array( $category, $granted, true );
}

/**
 * The consent state the tracking snippets must be built for.
 *
 * @since 10.3.0
 * @since 10.4.0 Returns 'partial' when only some categories were granted.
 *
 * @return string 'accept', 'decline', 'partial' or 'unknown'.
 */
function seopress_user_consent_state() {
	return seopress_user_consent_summarize( seopress_user_consent_granted_categories() );
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
 * A JavaScript expression evaluating to the categories the browser granted.
 *
 * The same reading as seopress_user_consent_cookie_categories(), done by the
 * browser on its own cookies so the served HTML stays the same for everyone.
 * Anchored on the cookie boundaries rather than a substring search, so a
 * third-party cookie whose name merely ends with ours cannot pass for consent.
 *
 * @since 10.4.0
 *
 * @return string An expression yielding an array of slugs, or null when the visitor has yet to answer.
 */
function seopress_user_consent_js_categories() {
	return '(function(c){var m=c.match(/(?:^|;\s*)' . SEOPRESS_USER_CONSENT_COOKIE . '=([^;]*)/);'
		. "if(m){return ['necessary'].concat(m[1].split(/\\||%7C/i));}"
		. 'if(/(?:^|;\s*)seopress-user-consent-accept=1(?:;|$)/.test(c)){return ' . wp_json_encode( seopress_user_consent_category_slugs() ) . ';}'
		. "if(/(?:^|;\s*)seopress-user-consent-close=1(?:;|$)/.test(c)){return ['necessary'];}"
		. 'return null;})(document.cookie)';
}

/**
 * A JavaScript expression that is true when the browser granted every category.
 *
 * @since 10.3.0
 * @since 10.4.0 Also reads the per-category cookie.
 *
 * @return string
 */
function seopress_user_consent_js_test() {
	$test = array();

	foreach ( array_diff( seopress_user_consent_category_slugs(), array( 'necessary' ) ) as $category ) {
		$test[] = 'g.indexOf(' . wp_json_encode( $category ) . ')>-1';
	}

	return '(function(g){return !!g&&' . implode( '&&', $test ) . ';})(' . seopress_user_consent_js_categories() . ')';
}

/**
 * The Consent Mode v2 values for a set of granted categories.
 *
 * @since 10.4.0
 *
 * @param string[] $granted The granted categories.
 *
 * @return array Signal names mapped to 'granted' or 'denied'.
 */
function seopress_user_consent_signals( array $granted ) {
	$signals = array();

	foreach ( seopress_user_consent_categories() as $slug => $category ) {
		foreach ( $category['signals'] as $signal ) {
			$signals[ $signal ] = in_array( $slug, $granted, true ) ? 'granted' : 'denied';
		}
	}

	return $signals;
}

/**
 * The Google Consent Mode v2 update call for a set of granted categories.
 *
 * An update rather than a default: the page has already set its defaults, and
 * gtag ignores a second default for a signal it already knows.
 *
 * @since 10.4.0
 *
 * @param string[] $granted The granted categories.
 *
 * @return string
 */
function seopress_user_consent_gtag_update_for( array $granted ) {
	$lines = array();

	foreach ( seopress_user_consent_signals( $granted ) as $signal => $value ) {
		$lines[] = "    '" . $signal . "': '" . $value . "'";
	}

	return "gtag('consent', 'update', {\n" . implode( ",\n", $lines ) . "\n});\n";
}

/**
 * The Google Consent Mode v2 update call, resolved by the browser.
 *
 * For snippets every visitor receives: each signal is read from the visitor's
 * own cookie when the script runs, and nothing is sent before an answer.
 *
 * @since 10.4.0
 *
 * @return string
 */
function seopress_user_consent_gtag_update_js() {
	$lines = array();

	foreach ( seopress_user_consent_categories() as $slug => $category ) {
		foreach ( $category['signals'] as $signal ) {
			$lines[] = "    '" . $signal . "': g.indexOf('" . $slug . "') > -1 ? 'granted' : 'denied'";
		}
	}

	return "(function(g){if(!g){return;}\ngtag('consent', 'update', {\n" . implode( ",\n", $lines ) . "\n});\n" . '})(' . seopress_user_consent_js_categories() . ");\n";
}

/**
 * The Google Consent Mode v2 update call for a decision.
 *
 * @since 10.3.0
 * @since 10.4.0 Kept for an all-or-nothing answer; see seopress_user_consent_gtag_update_for().
 *
 * @param bool $granted Whether the visitor accepted every category.
 *
 * @return string
 */
function seopress_user_consent_gtag_update( $granted ) {
	return seopress_user_consent_gtag_update_for( $granted ? seopress_user_consent_category_slugs() : array( 'necessary' ) );
}

/**
 * The Microsoft Clarity consent call for a set of granted categories.
 *
 * Consent API v2: analytics and ad storage are answered separately.
 *
 * @since 10.4.0
 *
 * @param string[] $granted The granted categories.
 *
 * @return string
 */
function seopress_user_consent_clarity_for( array $granted ) {
	return "window.clarity('consentv2', " . wp_json_encode(
		array(
			'ad_Storage'        => in_array( 'advertising', $granted, true ) ? 'granted' : 'denied',
			'analytics_Storage' => in_array( 'analytics', $granted, true ) ? 'granted' : 'denied',
		)
	) . ');';
}

/**
 * The Microsoft Clarity consent call, resolved by the browser.
 *
 * @since 10.4.0
 *
 * @return string
 */
function seopress_user_consent_clarity_js() {
	return "(function(g){g=g||[];window.clarity('consentv2', {"
		. "ad_Storage: g.indexOf('advertising') > -1 ? 'granted' : 'denied', "
		. "analytics_Storage: g.indexOf('analytics') > -1 ? 'granted' : 'denied'"
		. '});})(' . seopress_user_consent_js_categories() . ');';
}

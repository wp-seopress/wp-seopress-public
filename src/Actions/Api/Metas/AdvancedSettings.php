<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;


/**
 * Advanced Settings
 */
class AdvancedSettings implements ExecuteHooks {
	/**
	 * The Advanced Settings hooks.
	 *
	 * @since 5.0.0
	 */

	/**
	 * Meta keys registered as integers, collected by register_int_meta().
	 *
	 * Kept so the WP 7 REST compatibility shim (see
	 * tolerate_empty_int_metas) can stay in sync automatically with every
	 * key that goes through register_int_meta().
	 *
	 * @since 10.0.0
	 *
	 * @var string[]
	 */
	protected $int_meta_keys = array();

	/**
	 * The Advanced Settings register.
	 *
	 * Every meta key registered here is exposed to Gutenberg via the standard
	 * `/wp/v2/<type>/<id>` REST endpoint. The React metabox mirrors its Formik
	 * state into `core/editor` (see app/react/components/SyncMetaToEditor) so
	 * a plain Block Editor "Update" persists the SEO fields without needing a
	 * dedicated /seopress/v1 PUT. Each `sanitize_callback` mirrors the
	 * normalization already done by the corresponding dedicated PUT endpoint
	 * (RobotSettings::processPut, SocialSettings::processPut, …) so both
	 * paths converge on the same DB value.
	 *
	 * Integer keys go through a WP 7 REST compatibility shim
	 * (tolerate_empty_int_metas) so a cleared/unset value sent as an empty
	 * string no longer trips the stricter schema validation.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Title & Description tab.
		$this->register_string_meta( '_seopress_titles_title' );
		$this->register_string_meta( '_seopress_titles_desc' );

		// Advanced tab — robots flags and related.
		$this->register_string_meta( '_seopress_robots_index' );
		$this->register_string_meta( '_seopress_robots_follow' );
		$this->register_string_meta( '_seopress_robots_imageindex' );
		$this->register_string_meta( '_seopress_robots_snippet' );
		$this->register_string_meta( '_seopress_robots_primary_cat' );
		$this->register_string_meta( '_seopress_robots_breadcrumbs' );
		$this->register_string_meta( '_seopress_robots_freeze_modified_date' );
		$this->register_string_meta( '_seopress_robots_custom_modified_date' );
		$this->register_url_meta( '_seopress_robots_canonical' );

		// Social tab — Facebook.
		$this->register_string_meta( '_seopress_social_fb_title' );
		$this->register_string_meta( '_seopress_social_fb_desc' );
		$this->register_url_meta( '_seopress_social_fb_img' );
		$this->register_int_meta( '_seopress_social_fb_img_attachment_id' );
		$this->register_int_meta( '_seopress_social_fb_img_width' );
		$this->register_int_meta( '_seopress_social_fb_img_height' );

		// Social tab — Twitter / X.
		$this->register_string_meta( '_seopress_social_twitter_title' );
		$this->register_string_meta( '_seopress_social_twitter_desc' );
		$this->register_url_meta( '_seopress_social_twitter_img' );
		$this->register_int_meta( '_seopress_social_twitter_img_attachment_id' );
		$this->register_int_meta( '_seopress_social_twitter_img_width' );
		$this->register_int_meta( '_seopress_social_twitter_img_height' );

		// Redirections tab.
		$this->register_url_meta( '_seopress_redirections_value' );
		$this->register_string_meta( '_seopress_redirections_enabled' );
		$this->register_string_meta( '_seopress_redirections_enabled_regex' );
		$this->register_string_meta( '_seopress_redirections_logged_status' );
		$this->register_string_meta( '_seopress_redirections_param' );
		$this->register_int_meta( '_seopress_redirections_type' );

		// Content analysis tab. Sanitizer mirrors
		// TargetKeywords::processPut() so the value stored through Gutenberg
		// matches the dedicated PUT endpoint and the Classic Editor fallback.
		register_post_meta(
			'',
			'_seopress_analysis_target_kw',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => array( $this, 'sanitize_target_keywords' ),
			)
		);

		// WP 7 compatibility: coerce empty integer metas before validation.
		add_filter( 'rest_request_before_callbacks', array( $this, 'tolerate_empty_int_metas' ), 10, 3 );
	}

	/**
	 * Register a scalar text meta key with the standard auth callback and
	 * `sanitize_text_field` as the sanitizer.
	 *
	 * @param string $key Meta key.
	 *
	 * @return void
	 */
	protected function register_string_meta( $key ) {
		register_post_meta(
			'',
			$key,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Register a URL meta key — value is sanitized through `sanitize_url`.
	 *
	 * @param string $key Meta key.
	 *
	 * @return void
	 */
	protected function register_url_meta( $key ) {
		register_post_meta(
			'',
			$key,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => 'sanitize_url',
			)
		);
	}

	/**
	 * Register an integer meta key — a positive value is kept as an int,
	 * anything else (0, empty string, …) normalizes to an empty string so the
	 * stored value matches the "no value" state the dedicated PUT endpoints
	 * produce when they delete the meta.
	 *
	 * @param string $key Meta key.
	 *
	 * @return void
	 */
	protected function register_int_meta( $key ) {
		$this->int_meta_keys[] = $key;

		register_post_meta(
			'',
			$key,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'integer',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => array( $this, 'sanitize_int_meta' ),
			)
		);
	}

	/**
	 * Normalize a comma-separated target keywords string: trim each token,
	 * drop empties, rejoin without surrounding spaces, then sanitize as text.
	 *
	 * @param mixed $value Raw value submitted via REST or update_post_meta.
	 *
	 * @return string
	 */
	public function sanitize_target_keywords( $value ) { // phpcs:ignore
		if ( ! is_string( $value ) ) {
			return '';
		}
		$parts = array_filter(
			array_map( 'trim', explode( ',', $value ) ),
			static function ( $token ) {
				return '' !== $token;
			}
		);
		return sanitize_text_field( implode( ',', $parts ) );
	}

	/**
	 * Sanitize an integer meta value.
	 *
	 * Positive values are kept as integers; everything else (0, an empty
	 * string sent when an image is cleared, null) collapses to an empty
	 * string so a cleared field reads back the same way as one the dedicated
	 * PUT endpoint deleted.
	 *
	 * @since 10.0.0
	 *
	 * @param mixed $value Raw value submitted via REST or update_post_meta.
	 *
	 * @return int|string
	 */
	public function sanitize_int_meta( $value ) {
		$int = absint( $value );

		return $int > 0 ? $int : '';
	}

	/**
	 * WordPress 7 tightened REST schema validation and runs it *before* the
	 * registered `sanitize_callback`. An integer meta whose value arrives as
	 * an empty string — a social image that was never set or has just been
	 * cleared in the Block Editor — therefore fails the `integer` type check
	 * and the whole post update/autosave is rejected with
	 * "meta._seopress_social_fb_img_attachment_id is not of type integer."
	 *
	 * Coerce those empty strings to 0 on the incoming request, before the
	 * controller validates the meta, so the update succeeds; the
	 * `sanitize_callback` then stores it back as an empty string. Hooked on
	 * `rest_request_before_callbacks` so it also covers the autosave route,
	 * not just `/wp/v2/<type>/<id>` updates.
	 *
	 * @since 10.0.0
	 *
	 * @param \WP_REST_Response|\WP_Error|mixed $response The current response.
	 * @param array                             $handler  The matched route handler.
	 * @param \WP_REST_Request                  $request  The current request.
	 *
	 * @return \WP_REST_Response|\WP_Error|mixed
	 */
	public function tolerate_empty_int_metas( $response, $handler, $request ) { // phpcs:ignore -- $handler is required by the filter signature.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$meta = $request['meta'];
		if ( ! is_array( $meta ) ) {
			return $response;
		}

		$changed = false;
		foreach ( $this->int_meta_keys as $key ) {
			if ( array_key_exists( $key, $meta ) && '' === $meta[ $key ] ) {
				$meta[ $key ] = 0;
				$changed      = true;
			}
		}

		if ( $changed ) {
			$request['meta'] = $meta;
		}

		return $response;
	}

	/**
	 * Auth callback is required for protected meta keys.
	 *
	 * @param   bool   $allowed  Is allowed.
	 * @param   string $meta_key The meta key.
	 * @param   int    $id The id.
	 *
	 * @return  bool   $allowed The allowed.
	 */
	public function meta_auth( $allowed, $meta_key, $id ) {
		return current_user_can( 'edit_posts', $id );
	}
}

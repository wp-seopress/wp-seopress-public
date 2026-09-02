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
	 * Meta keys the Advanced > Security role restrictions apply to, mapped to
	 * the restriction area that governs them.
	 *
	 * Collected by the register_*_meta() helpers so
	 * discard_restricted_meta_write() covers each key automatically rather
	 * than through a list that would drift, then passed through
	 * `seopress_restricted_meta_keys` so PRO registers its own keys into the
	 * same mechanism instead of duplicating the drop logic.
	 *
	 * @since 10.2.0
	 *
	 * @var array<string, string> Meta key => `GLOBAL` or `CONTENT_ANALYSIS`.
	 */
	protected $restricted_meta_keys = array();

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
		$this->restricted_meta_keys['_seopress_analysis_target_kw'] = 'CONTENT_ANALYSIS';

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

		// Advanced > Security role restrictions, applied to the value rather
		// than to the request, so a restricted user can still save the post.
		// add_post_metadata is covered too: without it, XML-RPC's
		// set_custom_fields() could write the restricted keys back through
		// add_post_meta(), which never reaches update_post_metadata.
		add_filter( 'add_post_metadata', array( $this, 'discard_restricted_meta_write' ), 10, 3 );
		add_filter( 'update_post_metadata', array( $this, 'discard_restricted_meta_write' ), 10, 3 );
		add_filter( 'delete_post_metadata', array( $this, 'discard_restricted_meta_write' ), 10, 3 );

		// Same restriction on the meta-id based paths, which carry a meta_id
		// instead of a post id and are the other half of what
		// set_custom_fields() uses.
		add_filter( 'update_post_metadata_by_mid', array( $this, 'discard_restricted_meta_write_by_mid' ), 10, 2 );
		add_filter( 'delete_post_metadata_by_mid', array( $this, 'discard_restricted_meta_write_by_mid' ), 10, 2 );
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
		$this->restricted_meta_keys[ $key ] = 'GLOBAL';

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
		$this->restricted_meta_keys[ $key ] = 'GLOBAL';

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
		$this->int_meta_keys[]              = $key;
		$this->restricted_meta_keys[ $key ] = 'GLOBAL';

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
		// Deliberately does not enforce the Advanced > Security role
		// restrictions. Denying here fails the whole request rather than the
		// field: core/editor sends every registered meta as one object as soon
		// as any of them changes, so a restricted user who merely typed a meta
		// title could no longer save the post at all, with an error naming an
		// internal meta key. The restriction is applied in
		// discard_restricted_meta_write() instead, which drops the value and
		// lets the save through.
		return current_user_can( 'edit_post', $id );
	}

	/**
	 * The restricted key map, resolved on first use.
	 *
	 * Filtered here rather than in hooks() so PRO's callback is registered by
	 * the time it runs: both plugins declare their metas from hooks() and the
	 * order between them is not guaranteed.
	 *
	 * @return array<string, string> Meta key => restriction area.
	 */
	protected function get_restricted_meta_keys() {
		static $resolved = null;

		if ( null !== $resolved ) {
			return $resolved;
		}

		/**
		 * Meta keys the Advanced > Security role restrictions apply to.
		 *
		 * PRO registers its own metabox keys here so a single implementation
		 * of the drop logic covers both plugins.
		 *
		 * @since 10.2.0
		 *
		 * @param array<string, string> $keys Meta key => restriction area,
		 *                                    `GLOBAL` or `CONTENT_ANALYSIS`.
		 */
		$resolved = (array) apply_filters( 'seopress_restricted_meta_keys', $this->restricted_meta_keys );

		return $resolved;
	}

	/**
	 * Whether the current role may not write the given key on the given post.
	 *
	 * @param int    $object_id Post ID the meta belongs to.
	 * @param string $meta_key  Meta key being written.
	 *
	 * @return bool
	 */
	protected function is_restricted_meta_write( $object_id, $meta_key ) {
		$restricted = $this->get_restricted_meta_keys();

		if ( ! isset( $restricted[ $meta_key ] ) ) {
			return false;
		}

		if ( ! function_exists( 'seopress_metabox_role_is_blocked' ) ) {
			return false;
		}

		// The restriction governs the SEO metabox, so it only applies to post
		// types that display it. Several of these keys are shared: each entry
		// of the Redirections manager is a `seopress_404` post storing its
		// settings under the very same `_seopress_redirections_*` keys, and
		// dropping those writes would silently discard bulk actions, quick
		// edits and CSV imports on a feature this restriction never covered.
		$post_types = apply_filters( 'seopress_metaboxe_seo', seopress_get_service( 'WordPressData' )->getPostTypes() );

		if ( ! isset( $post_types[ get_post_type( $object_id ) ] ) ) {
			return false;
		}

		return seopress_metabox_role_is_blocked( $restricted[ $meta_key ] );
	}

	/**
	 * Silently drop a write to a meta the current role may not change.
	 *
	 * Short-circuits `add_metadata()`, `update_metadata()` and
	 * `delete_metadata()` by returning a non-null value, so the stored value is
	 * left alone and the request that carried it still succeeds. That is the
	 * whole point: the role restriction is meant to keep a user out of a
	 * feature, not out of the publish button.
	 *
	 * @param null|bool $check     Short-circuit value, null to proceed.
	 * @param int       $object_id Post ID.
	 * @param string    $meta_key  Meta key being written.
	 *
	 * @return null|bool `true` to report the write as handled, null to proceed.
	 */
	public function discard_restricted_meta_write( $check, $object_id, $meta_key ) {
		if ( null !== $check ) {
			return $check;
		}

		if ( ! $this->is_restricted_meta_write( $object_id, $meta_key ) ) {
			return $check;
		}

		// Reported as handled, so REST does not treat the untouched value as a
		// failed update and turn it into an error.
		return true;
	}

	/**
	 * Same restriction for the meta-id based paths.
	 *
	 * `update_metadata_by_mid()` and `delete_metadata_by_mid()` carry a meta id
	 * rather than a post id, and no meta key at all on the delete side, so both
	 * have to be resolved before the usual check. XML-RPC's
	 * `set_custom_fields()` is the caller that matters here: unlike the Classic
	 * Editor custom fields box it does not consult `is_protected_meta()`.
	 *
	 * @param null|bool $check   Short-circuit value, null to proceed.
	 * @param int       $meta_id Meta row ID.
	 *
	 * @return null|bool `true` to report the write as handled, null to proceed.
	 */
	public function discard_restricted_meta_write_by_mid( $check, $meta_id ) {
		if ( null !== $check ) {
			return $check;
		}

		$meta = get_metadata_by_mid( 'post', $meta_id );

		if ( ! $meta || ! isset( $meta->meta_key, $meta->post_id ) ) {
			return $check;
		}

		if ( ! $this->is_restricted_meta_write( (int) $meta->post_id, $meta->meta_key ) ) {
			return $check;
		}

		return true;
	}
}

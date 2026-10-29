<?php // phpcs:ignore

namespace SEOPress\Actions\Api\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Helpers\Metas\PrimaryTerm;


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
	 * `/wp/v2/<type>/<id>` REST endpoint. The React metabox mirrors its form
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
		$this->register_primary_term_meta( PrimaryTerm::LEGACY_META_KEY );
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

		// One primary term meta key per taxonomy, resolved on `init` because
		// custom taxonomies are not registered yet on `plugins_loaded`.
		//
		// And again on `rest_api_init`, which runs after the whole of `init`:
		// a taxonomy registered at a later priority, or on `registered_post_type`,
		// otherwise had no REST meta, so the Block Editor wrote a key the route
		// silently dropped and the choice was lost on reload.
		add_action( 'init', array( $this, 'register_primary_term_metas' ), 20 );
		add_action( 'rest_api_init', array( $this, 'register_primary_term_metas' ) );

		// A taxonomy without a primary term key of its own on a post reads the
		// legacy value when it holds one of its terms, on every surface.
		add_filter( 'default_post_metadata', array( PrimaryTerm::class, 'inherit_legacy_value' ), 20, 4 );

		// The per-taxonomy keys depend on the site's taxonomies, so they
		// cannot be listed in wpml-config.xml next to the legacy one.
		add_filter( 'wpml_config_array', array( PrimaryTerm::class, 'add_wpml_config' ) );

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

		// Collapse duplicate rows of a single key before it is written, after
		// the role restriction had its say.
		add_filter( 'update_post_metadata', array( $this, 'collapse_duplicate_meta_rows' ), 20, 5 );

		// `show_in_rest` is there for the Block Editor, but core prints the
		// metas in every /wp/v2 response, including the one anyone can read on
		// a published post.
		foreach ( get_post_types() as $post_type ) {
			$this->register_rest_meta_visibility( $post_type );
		}
		add_action( 'registered_post_type', array( $this, 'register_rest_meta_visibility' ) );
	}

	/**
	 * Filter the `meta` field of a post type's REST responses.
	 *
	 * Core has no filter common to all post types, and the metas are
	 * registered for all of them, attachments included. Hooked for every
	 * type rather than the ones in REST today: `show_in_rest` may still be
	 * switched on later, and the filter costs nothing where it never runs.
	 *
	 * @since 10.4.0
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return void
	 */
	public function register_rest_meta_visibility( $post_type ) {
		add_filter( "rest_prepare_{$post_type}", array( $this, 'hide_rest_meta' ), 10, 2 );
	}

	/**
	 * Remove the SEO metas from a /wp/v2 response the user may not see them in.
	 *
	 * A user who may not edit the post gets none of them, whatever the
	 * context: the target keywords, a redirection that is not enabled or the
	 * analysis settings are not public, and what a visitor may see is already
	 * rendered by the head tags and the `seopress/v1/posts` headless route.
	 *
	 * A user who may edit the post, the Block Editor's case, keeps them,
	 * except the keys the Advanced > Security restrictions keep the role from
	 * writing, as the metabox read routes refuse them. The Block Editor does
	 * not send back a key it did not receive, so the post still saves.
	 *
	 * @since 10.4.0
	 *
	 * @param \WP_REST_Response $response The response.
	 * @param \WP_Post          $post     The post.
	 *
	 * @return \WP_REST_Response
	 */
	public function hide_rest_meta( $response, $post ) {
		if ( ! $response instanceof \WP_REST_Response || ! $post instanceof \WP_Post ) {
			return $response;
		}

		$data = $response->get_data();

		if ( empty( $data['meta'] ) || ! is_array( $data['meta'] ) ) {
			return $response;
		}

		$can_edit   = current_user_can( 'edit_post', $post->ID );
		$restricted = $this->get_restricted_meta_keys();
		$public     = array();
		$blocked    = array();

		// A revision or an autosave is restricted as the post it belongs to.
		$post_type = 'revision' === $post->post_type && $post->post_parent ? get_post_type( $post->post_parent ) : $post->post_type;

		if ( ! $can_edit ) {
			/**
			 * SEO meta keys /wp/v2 still shows to users who may not edit the
			 * post, visitors included.
			 *
			 * For a headless front end that read raw metas from the core
			 * routes. The `seopress/v1/posts/<id>` route returns the values
			 * as they are rendered, and needs no such opening.
			 *
			 * @since 10.4.0
			 *
			 * @param string[] $keys Meta keys, none by default.
			 * @param \WP_Post $post The post.
			 */
			$public = (array) apply_filters( 'seopress_rest_public_meta_keys', array(), $post );
		}

		$changed = false;

		foreach ( array_keys( $data['meta'] ) as $meta_key ) {
			$meta_key = (string) $meta_key;

			if ( $can_edit ) {
				// Resolved once per area, not once per key.
				$area = isset( $restricted[ $meta_key ] ) ? $restricted[ $meta_key ] : null;

				if ( null !== $area && ! isset( $blocked[ $area ] ) ) {
					$blocked[ $area ] = self::role_is_blocked_on( $post_type, $area );
				}

				$hide = null !== $area && $blocked[ $area ];
			} else {
				$is_seo_meta = isset( $restricted[ $meta_key ] ) || 0 === strpos( $meta_key, '_seopress_' );
				$hide        = $is_seo_meta && ! in_array( $meta_key, $public, true );
			}

			if ( $hide ) {
				unset( $data['meta'][ $meta_key ] );
				$changed = true;
			}
		}

		if ( $changed ) {
			$response->set_data( $data );
		}

		return $response;
	}

	/**
	 * Expose one `_seopress_robots_primary_term_{taxonomy}` meta per
	 * taxonomy offering a primary term selector, so the Block Editor can
	 * read and write it through the standard `/wp/v2/<type>/<id>` route.
	 *
	 * The legacy `_seopress_robots_primary_cat` key is skipped: it is
	 * already registered above.
	 *
	 * Registering a key twice is a no-op in core, so running on both hooks
	 * costs nothing.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public function register_primary_term_metas() {
		foreach ( PrimaryTerm::get_meta_keys() as $key ) {
			if ( PrimaryTerm::LEGACY_META_KEY === $key ) {
				continue;
			}

			$this->register_primary_term_meta( $key );
		}
	}

	/**
	 * Register a primary term meta key: a term id, or an empty string.
	 *
	 * `sanitize_text_field` let the Block Editor picker, SyncMetaToEditor and
	 * any `/wp/v2` client store `none` or free text, where the robots route
	 * and the Classic Editor store a term id or nothing.
	 *
	 * @since 10.4.0
	 *
	 * @param string $key Meta key.
	 *
	 * @return void
	 */
	protected function register_primary_term_meta( $key ) {
		$this->restricted_meta_keys[ $key ] = 'GLOBAL';

		register_post_meta(
			'',
			$key,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => array( $this, 'meta_auth' ),
				'sanitize_callback' => array( PrimaryTerm::class, 'sanitize_term_id' ),
			)
		);
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
				'sanitize_callback' => '_seopress_titles_desc' === $key ? 'sanitize_textarea_field' : 'sanitize_text_field',
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

		return self::role_is_blocked_on( get_post_type( $object_id ), $restricted[ $meta_key ] );
	}

	/**
	 * Whether the Advanced > Security restrictions keep the current role out
	 * of the given area on the given post type.
	 *
	 * The single rule behind the dropped writes, the metas left out of
	 * /wp/v2 and the editor panels that read them.
	 *
	 * @since 10.4.0
	 *
	 * @param string|false $post_type Post type name.
	 * @param string       $area      `GLOBAL` or `CONTENT_ANALYSIS`.
	 *
	 * @return bool
	 */
	public static function role_is_blocked_on( $post_type, $area ) {
		if ( ! is_string( $post_type ) || ! function_exists( 'seopress_metabox_role_is_blocked' ) ) {
			return false;
		}

		// The restriction governs the SEO metabox, so it only applies to post
		// types that display it. Several of these keys are shared: each entry
		// of the Redirections manager is a `seopress_404` post storing its
		// settings under the very same `_seopress_redirections_*` keys, and
		// dropping those writes would silently discard bulk actions, quick
		// edits and CSV imports on a feature this restriction never covered.
		$post_types = apply_filters( 'seopress_metaboxe_seo', seopress_get_service( 'WordPressData' )->getPostTypes() );

		if ( ! isset( $post_types[ $post_type ] ) ) {
			return false;
		}

		return seopress_metabox_role_is_blocked( $area );
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

	/**
	 * Keep a single row for a single SEOPress key when it is written.
	 *
	 * Every key registered here is `single`, but a migration, an import or a
	 * duplication plugin can leave two rows for the same key. Core's REST
	 * meta handler only skips an unchanged value when one row exists: with
	 * two identical rows it calls update_metadata(), the UPDATE changes
	 * nothing, and the whole Block Editor save fails with "Could not update
	 * the meta value of _seopress_titles_desc in database.". Since
	 * core/editor sends every registered meta on save, a duplicated field
	 * the user never touched was enough to break it.
	 *
	 * The first row is kept, as get_post_meta( $id, $key, true ) reads it,
	 * and the others are deleted. When the kept row already holds the value,
	 * the write is reported as done, so REST does not turn it into an error.
	 *
	 * @since 10.4.0
	 *
	 * @param null|bool $check      Short-circuit value, null to proceed.
	 * @param int       $object_id  Post ID.
	 * @param string    $meta_key   Meta key being written.
	 * @param mixed     $meta_value New value, already unslashed and sanitized.
	 * @param mixed     $prev_value Previous value to match, empty for all rows.
	 *
	 * @return null|bool `true` when the value is already stored, null to proceed.
	 */
	public function collapse_duplicate_meta_rows( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( null !== $check || ! empty( $prev_value ) ) {
			return $check;
		}

		if ( ! isset( $this->restricted_meta_keys[ $meta_key ] ) ) {
			return $check;
		}

		$stored = get_metadata_raw( 'post', $object_id, $meta_key );

		if ( ! is_array( $stored ) || count( $stored ) < 2 ) {
			return $check;
		}

		global $wpdb;

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s ORDER BY meta_id ASC",
				$object_id,
				$meta_key
			)
		);

		if ( count( $rows ) < 2 ) {
			return $check;
		}

		$kept = array_shift( $rows );

		foreach ( $rows as $row ) {
			delete_metadata_by_mid( 'post', (int) $row->meta_id );
		}

		if ( (string) maybe_serialize( $meta_value ) === (string) $kept->meta_value ) {
			return true;
		}

		return $check;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

/**
 * Module metabox
 */
class ModuleMetabox implements ExecuteHooks {

	/**
	 * The ModuleMetabox hooks.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'init', array( $this, 'enqueue' ) );
		add_action( 'add_meta_boxes', array( $this, 'registerClassicOpenerMetabox' ) );
		add_action( 'save_post', array( $this, 'saveClassicEditorMetaFallback' ), 10, 2 );

		if ( current_user_can( seopress_capability( 'edit_posts' ) ) ) { // phpcs:ignore
			// Priority 110 so the beacon survives builders that wipe the
			// script queue mid-flight (Avada Live Builder clears it at 100).
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontend' ), 110 );
		}
	}

	/**
	 * Register the Classic Editor "SEO" metabox. The body provides the
	 * mount node for the React universal metabox so it renders inline
	 * where Classic users expect it, instead of hunting for the floating
	 * beacon or opening a separate overlay. Skipped on Gutenberg (which
	 * has its own sidebar panel) and when the universal metabox can't be
	 * enqueued (legacy classic metabox still handles that case).
	 *
	 * @since 9.9.0
	 *
	 * @return void
	 */
	public function registerClassicOpenerMetabox() {
		if ( ! seopress_get_service( 'EnqueueModuleMetabox' )->canEnqueue() ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && method_exists( $screen, 'is_block_editor' ) && true === $screen->is_block_editor() ) {
			return;
		}

		$post_types = seopress_get_service( 'WordPressData' )->getPostTypes();
		$post_types = apply_filters( 'seopress_metaboxe_seo', $post_types );

		if ( empty( $post_types ) ) {
			return;
		}

		/**
		 * Filter the Classic Editor "SEO" metabox context.
		 *
		 * @since 9.8.0
		 *
		 * @param string $context Metabox context. Default 'normal'. Accepts 'normal', 'side', 'advanced'.
		 */
		$context = apply_filters( 'seopress_metabox_opener_context', 'normal' );

		/**
		 * Filter the Classic Editor "SEO" metabox priority.
		 *
		 * @since 9.8.0
		 *
		 * @param string $priority Metabox priority. Default 'default'. Accepts 'high', 'core', 'default', 'low'.
		 */
		$priority = apply_filters( 'seopress_metabox_opener_priority', 'default' );

		foreach ( array_keys( $post_types ) as $post_type ) {
			add_meta_box(
				'seopress_metabox_opener',
				__( 'SEO', 'wp-seopress' ),
				array( $this, 'renderClassicOpenerMetabox' ),
				$post_type,
				$context,
				$priority
			);
		}
	}

	/**
	 * Render the Classic Editor metabox body — just the mount node
	 * picked up by the React universal metabox to render inline.
	 *
	 * @since 9.9.0
	 *
	 * @return void
	 */
	public function renderClassicOpenerMetabox() {
		?>
		<div id="seopress-js-module-seo-metabox-embed"></div>
		<?php
	}

	/**
	 * Persist SEO meta from the Classic Editor post form when the React
	 * metabox cannot reach the REST API. The React tabs render hidden
	 * inputs (associated with `<form id="post">` via the HTML `form`
	 * attribute) that mirror their Formik state, so a normal post save
	 * still carries the values even if the REST PUTs to `/seopress/v1/...`
	 * are blocked. Skipped during autosave/revision/REST and gated by a
	 * dedicated nonce. The list of meta keys is filterable so the Pro
	 * plugin can register its own.
	 *
	 * @since 9.8.0
	 *
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The post object.
	 *
	 * @return void
	 */
	public function saveClassicEditorMetaFallback( $post_id, $post ) {
		if ( ! isset( $_POST['seopress_metabox_classic_fallback_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['seopress_metabox_classic_fallback_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'seopress_metabox_classic_fallback' ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$meta_keys_map = $this->getClassicEditorFallbackMetaKeys();

		foreach ( $meta_keys_map as $meta_key => $sanitizer ) {
			if ( ! array_key_exists( $meta_key, $_POST ) ) {
				continue;
			}

			$raw   = wp_unslash( $_POST[ $meta_key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$value = $this->sanitizeFallbackValue( $raw, $sanitizer );

			if ( null === $value || '' === $value || ( is_array( $value ) && empty( $value ) ) ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
	}

	/**
	 * Return the meta keys map for the Classic Editor fallback. Each entry
	 * maps a post_meta key to a sanitization spec accepted by
	 * `sanitizeFallbackValue()` — either a callable or one of:
	 * `'text'`, `'textarea'`, `'url'`, `'int'`, or
	 * `array( 'json', $deep_callable )` for JSON-encoded structured fields.
	 * Filterable via `seopress_metabox_classic_fallback_meta_keys` so the
	 * Pro plugin (and integrators) can extend the list.
	 *
	 * @since 9.8.0
	 *
	 * @return array<string, mixed>
	 */
	protected function getClassicEditorFallbackMetaKeys() {
		$keys = array(
			// Title & description tab.
			'_seopress_titles_title'               => 'text',
			'_seopress_titles_desc'                => 'textarea',
			// Advanced tab — robots/canonical/primary category.
			'_seopress_robots_canonical'           => 'url',
			'_seopress_robots_index'               => 'text',
			'_seopress_robots_follow'              => 'text',
			'_seopress_robots_imageindex'          => 'text',
			'_seopress_robots_snippet'             => 'text',
			'_seopress_robots_primary_cat'         => 'text',
			'_seopress_robots_breadcrumbs'         => 'text',
			// Redirections (per-post) tab.
			'_seopress_redirections_value'         => 'url',
			'_seopress_redirections_enabled'       => 'text',
			'_seopress_redirections_enabled_regex' => 'text',
			'_seopress_redirections_logged_status' => 'text',
			'_seopress_redirections_param'         => 'text',
			'_seopress_redirections_type'          => 'int',
			// Content analysis tab — comma-separated string mirroring the
			// REST PUT normalization in
			// SEOPress\Actions\Api\TargetKeywords::processPut().
			'_seopress_analysis_target_kw'         => function ( $raw ) {
				if ( ! is_string( $raw ) ) {
					return '';
				}
				$parts = array_filter(
					array_map( 'trim', explode( ',', $raw ) ),
					static function ( $value ) {
						return '' !== $value;
					}
				);
				return sanitize_text_field( implode( ',', $parts ) );
			},
		);

		// Social tab — per-platform keys, sanitizer derived from the field type.
		if ( class_exists( '\SEOPress\Helpers\Metas\SocialSettings' ) ) {
			$social_fields = array_merge(
				\SEOPress\Helpers\Metas\SocialSettings::getMetaKeysFacebook(),
				\SEOPress\Helpers\Metas\SocialSettings::getMetaKeysTwitter()
			);
			foreach ( $social_fields as $field ) {
				if ( empty( $field['key'] ) ) {
					continue;
				}
				$type = isset( $field['type'] ) ? $field['type'] : 'input';
				switch ( $type ) {
					case 'textarea':
						$keys[ $field['key'] ] = 'textarea';
						break;
					case 'upload':
						$keys[ $field['key'] ] = 'url';
						break;
					default:
						$keys[ $field['key'] ] = 'text';
				}
			}
		}

		/**
		 * Filter the meta keys persisted by the Classic Editor save_post
		 * fallback. Pro / integrators add their own keys here. Each value
		 * is either a string ('text', 'textarea', 'url', 'int'), a callable
		 * applied to the raw $_POST value, or `array( 'json', $deep_cb )`
		 * for JSON-encoded structured fields.
		 *
		 * @since 9.8.0
		 *
		 * @param array<string, mixed> $keys Meta key => sanitizer spec.
		 */
		return apply_filters( 'seopress_metabox_classic_fallback_meta_keys', $keys );
	}

	/**
	 * Apply a sanitizer spec to a raw $_POST value. Returns null when the
	 * value should be skipped (invalid JSON for json-typed entries).
	 *
	 * @since 9.8.0
	 *
	 * @param mixed $raw  Raw value from $_POST (already wp_unslash'd).
	 * @param mixed $spec Sanitizer spec — see getClassicEditorFallbackMetaKeys().
	 *
	 * @return mixed
	 */
	protected function sanitizeFallbackValue( $raw, $spec ) {
		if ( is_string( $spec ) ) {
			switch ( $spec ) {
				case 'textarea':
					return is_scalar( $raw ) ? sanitize_textarea_field( (string) $raw ) : '';
				case 'url':
					return is_scalar( $raw ) ? sanitize_url( (string) $raw ) : '';
				case 'int':
					return is_scalar( $raw ) ? (int) $raw : 0;
				case 'text':
				default:
					return is_scalar( $raw ) ? sanitize_text_field( (string) $raw ) : '';
			}
		}

		// JSON-encoded structured fields: ['json', $deep_callable].
		if ( is_array( $spec ) && isset( $spec[0] ) && 'json' === $spec[0] ) {
			if ( ! is_string( $raw ) || '' === $raw ) {
				return null;
			}
			$decoded = json_decode( $raw, true );
			if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
				return null;
			}
			$callback = isset( $spec[1] ) && is_callable( $spec[1] ) ? $spec[1] : 'sanitize_text_field';
			return is_array( $decoded ) ? map_deep( $decoded, $callback ) : $decoded;
		}

		if ( is_callable( $spec ) ) {
			return call_user_func( $spec, $raw );
		}

		return is_scalar( $raw ) ? sanitize_text_field( (string) $raw ) : '';
	}

	/**
	 * Enqueue module.
	 *
	 * @param array $args_localize The arguments localize.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	protected function enqueueModule( $args_localize = array() ) {
		if ( ! seopress_get_service( 'EnqueueModuleMetabox' )->canEnqueue() ) {
			return;
		}

		// AMP compatibility.
		if ( function_exists( 'amp_is_request' ) && amp_is_request() ) {
			return;
		}

		// Bricks builder compatibility.
		if ( function_exists( 'bricks_is_builder_call' ) && bricks_is_builder_call() === true ) {
			return;
		}

		// Bricks builder compatibility: duplicated tag on homepage.
		if ( isset( $_GET['brickspreview'] ) ) { // phpcs:ignore
			return;
		}

		$is_gutenberg = false;
		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();
			if ( $current_screen && method_exists( $current_screen, 'is_block_editor' ) ) {
				$is_gutenberg = true === get_current_screen()->is_block_editor();
			}
		}

		$dependencies = array( 'react', 'react-dom', 'wp-components' );
		if ( $is_gutenberg ) {
			$dependencies = array_merge( $dependencies, array( 'wp-edit-post', 'wp-plugins' ) );
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-components' );
		wp_enqueue_style( 'seopress-metabox', SEOPRESS_URL_PUBLIC . '/metaboxe.css', array( 'wp-components' ), SEOPRESS_VERSION );
		wp_enqueue_script( 'seopress-metabox', SEOPRESS_URL_PUBLIC . '/metaboxe.js', $dependencies, SEOPRESS_VERSION, true );

		global $post;

		if ( post_type_supports( get_post_type( $post ), 'custom-fields' ) ) {
			wp_enqueue_script( 'seopress-pre-publish-checklist', SEOPRESS_URL_PUBLIC . '/editor/pre-publish-checklist/index.js', array(), SEOPRESS_VERSION, true );
		}
		if ( $is_gutenberg ) {
			// Check if metabox is disabled for this post type.
			if ( '1' === seopress_get_service( 'TitleOption' )->getSingleCptEnable( $post->post_type ) ) {
				return;
			}

			wp_enqueue_script( 'seopress-sidebar-panel', SEOPRESS_URL_PUBLIC . '/editor/sidebar-panel/index.js', array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-i18n' ), SEOPRESS_VERSION, true );

			// Get score data for the current post.
			$score       = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post->ID, array( 'score' ) );
			$score_color = '#94a3b8'; // Default gray.

			if ( ! empty( $score ) && is_array( $score ) ) {
				// Flatten the score array.
				$score_flat = array();
				foreach ( $score as $item ) {
					if ( is_array( $item ) ) {
						$score_flat = array_merge( $score_flat, $item );
					}
				}

				// Both 'high' and 'medium' mean "should be improved" (orange/yellow).
				if ( in_array( 'high', $score_flat, true ) || in_array( 'medium', $score_flat, true ) ) {
					$score_color = '#f59e0b'; // Orange - should be improved.
				} else {
					$score_color = '#16a34a'; // Green - good.
				}
			}

			wp_localize_script(
				'seopress-sidebar-panel',
				'seopressScore',
				array(
					'color'    => $score_color,
					'showText' => apply_filters( 'seopress_toolbar_button_show_text', true ),
				)
			);
		}
		$value = wp_create_nonce( 'seopress_rest' );

		$tags = seopress_get_service( 'TagsToString' )->getTagsAvailable(
			array(
				'without_classes'     => array(
					'\SEOPress\Tags\PostThumbnailUrlHeight',
					'\SEOPress\Tags\PostThumbnailUrlWidth',

				),
				'without_classes_pos' => array( '\SEOPress\Tags\Schema', '\SEOPressPro\Tags\Schema' ),
			)
		);

		$get_locale = get_locale();
		if ( ! empty( $get_locale ) ) {
			$locale       = function_exists( 'locale_get_primary_language' ) ? locale_get_primary_language( get_locale() ) : get_locale();
			$country_code = function_exists( 'locale_get_region' ) ? locale_get_region( get_locale() ) : get_locale();
		} else {
			$locale       = 'en';
			$country_code = 'US';
		}

		$settings_advanced = seopress_get_service( 'AdvancedOption' );
		$user              = wp_get_current_user();
		$roles             = (array) $user->roles;

		// Get post ID - check multiple sources.
		$post_id   = null;
		$post_type = null;

		// In frontend singular context.
		if ( is_singular() ) {
			$post_id = get_the_ID();
		} else {
			// In admin, resolve the edited post from the request first.
			// Page builders such as Elementor Pro Theme Builder can replace
			// the global $post with one of their own templates
			// (elementor_library) while a WooCommerce product / post edit
			// screen is rendered. Relying on the global $post here would
			// leak that template into the SEO preview (wrong title, meta
			// description and ?elementor_library=... URL).
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			if ( $screen && 'post' === $screen->base ) {
				if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} elseif ( isset( $_POST['post_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					$post_id = absint( wp_unslash( $_POST['post_ID'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				}

				// Only trust the request value if it points to a real post.
				if ( $post_id && ! get_post( $post_id ) ) {
					$post_id = null;
				}
			}

			// Fall back to the global $post when the request did not give us
			// a usable ID (new posts, other admin screens, etc.).
			if ( ! $post_id && isset( $post ) && $post instanceof \WP_Post ) {
				$post_id = $post->ID;
			}
		}

		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
		}

		// Compatibility with WooCommerce beta product page.
		if ( isset( $_GET['path'] ) && strpos( $_GET['path'], 'product' ) && isset( $_GET['page'] ) && 'wc-admin' === $_GET['page'] ) { // phpcs:ignore
			$data_path = explode( '/', $_GET['path'] ); // phpcs:ignore
			$post_id   = $data_path[ count( $data_path ) - 1 ];
		}

		$args = array_merge(
			array(
				'SEOPRESS_URL_PUBLIC'       => SEOPRESS_URL_PUBLIC,
				'SEOPRESS_URL_ASSETS'       => SEOPRESS_URL_ASSETS,
				'SEOPRESS_VERSION'          => SEOPRESS_VERSION,
				'SEOPRESS_PRO_VERSION'      => defined( 'SEOPRESS_PRO_VERSION' ) ? SEOPRESS_PRO_VERSION : '0',
				'SEOPRESS_PRO_IS_ACTIVATED' => is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ? true : false,
				'SITENAME'                  => get_bloginfo( 'name' ),
				'SITEURL'                   => site_url(),
				'ADMIN_URL_TITLES'          => admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_single' ),
				'ADMIN_URL_ARCHIVES_TITLES' => admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_archives' ),
				'TAGS'                      => array_values( $tags ),
				'REST_URL'                  => rest_url(),
				'NONCE'                     => wp_create_nonce( 'wp_rest' ),
				// Nonce verified by saveClassicEditorMetaFallback() — lets the
				// Classic Editor post form rescue title/description writes when
				// the REST API is blocked (security plugin, hardening filter,
				// etc.) so the user does not silently lose their input.
				'CLASSIC_FALLBACK_NONCE'    => wp_create_nonce( 'seopress_metabox_classic_fallback' ),
				'POST_ID'                   => $post_id,
				'POST_TYPE'                 => $post_type,
				'POST_URL'                  => $post_id ? get_permalink( $post_id ) : null,
				'IS_GUTENBERG'              => apply_filters( 'seopress_module_metabox_is_gutenberg', $is_gutenberg ),
				// Distinguishes the admin screens (where the Classic Editor
				// "Open SEO editor" metabox button takes over the beacon's
				// role) from frontend contexts where the beacon is still the
				// only entry point into the overlay.
				'IS_ADMIN'                  => is_admin(),
				'SELECTOR_GUTENBERG'        => apply_filters( 'seopress_module_metabox_selector_gutenberg', '.edit-post-header .edit-post-header-toolbar__left' ),
				'TOGGLE_MOBILE_PREVIEW'     => apply_filters( 'seopress_toggle_mobile_preview', 1 ),
				'GOOGLE_SUGGEST'            => array(
					'ACTIVE'       => apply_filters( 'seopress_ui_metabox_google_suggest', false ),
					'LOCALE'       => $locale,
					'COUNTRY_CODE' => $country_code,
				),
				'USER_ROLES'                => array_values( $roles ),
				'ROLES_BLOCKED'             => array(
					'GLOBAL'           => $settings_advanced->getSecurityMetaboxRole(),
					'CONTENT_ANALYSIS' => $settings_advanced->getSecurityMetaboxRoleContentAnalysis(),
				),
				'OPTIONS'                   => array(
					'AI' => seopress_get_service( 'ToggleOption' )->getToggleAi() === '1' ? true : false,
				),
				// Per-user capability flags. Nested so wp_localize_script
				// does not cast their booleans to strings (it only stringifies
				// top-level scalars), which would defeat strict equality in
				// the React side.
				'CAPABILITIES'              => array(
					'IGNORE_ISSUES' => function_exists( 'seopress_capability' )
						? current_user_can( seopress_capability( 'manage_options', 'bot' ) )
						: current_user_can( 'manage_options' ),
				),
				'TABS'                      => array(
					'SCHEMAS' => apply_filters( 'seopress_active_schemas_manual_universal_metabox', false ),
				),
				'SUB_TABS'                  => array(
					'GOOGLE_NEWS'      => apply_filters( 'seopress_active_google_news', false ),
					'VIDEO_SITEMAP'    => apply_filters( 'seopress_active_video_sitemap', false ),
					'INSPECT_URL'      => apply_filters( 'seopress_active_inspect_url', false ),
					'INTERNAL_LINKING' => apply_filters( 'seopress_active_internal_linking', false ),
					'SCHEMA_MANUAL'    => apply_filters( 'seopress_active_schemas', false ),
					'SCHEMA_AUTOMATIC' => apply_filters( 'seopress_active_schemas_automatic_universal_metabox', false ),
				),
				'FAVICON'                   => get_site_icon_url( 32 ),
				'BEACON_SVG'                => apply_filters( 'seopress_beacon_svg', SEOPRESS_URL_ASSETS . '/img/beacon.svg' ),
				'AI_SVG'                    => apply_filters( 'seopress_ai_svg', SEOPRESS_URL_ASSETS . '/img/ai.svg' ),
				'CACHED_CONTENT_ANALYSIS'   => $post_id ? $this->getCachedContentAnalysis( $post_id ) : null,
				// Server-side snapshot of the Titles & Metas form so the
				// React overlay can render without a round trip to the
				// /title-description-metas REST endpoint. SWRConfig wires
				// this into the SWR cache as fallback data.
				'INITIAL_DATA'              => $post_id ? array(
					'titleDescription' => array(
						'title'       => html_entity_decode( (string) get_post_meta( $post_id, '_seopress_titles_title', true ), ENT_QUOTES | ENT_XML1, 'UTF-8' ),
						'description' => html_entity_decode( (string) get_post_meta( $post_id, '_seopress_titles_desc', true ), ENT_QUOTES | ENT_XML1, 'UTF-8' ),
					),
				) : null,
			),
			$args_localize
		);

		wp_localize_script( 'seopress-metabox', 'SEOPRESS_DATA', $args );
		wp_localize_script( 'seopress-metabox', 'SEOPRESS_I18N', seopress_get_service( 'I18nUniversalMetabox' )->getTranslations() );

		// Enqueue metabox promotion banner if available.
		$this->enqueueMetaboxPromo();
	}

	/**
	 * Enqueue metabox promotion banner.
	 *
	 * @since 9.6.0
	 *
	 * @return void
	 */
	protected function enqueueMetaboxPromo() {
		// White-label check.
		if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
			if ( method_exists( seopress_get_service( 'ToggleOption' ), 'getToggleWhiteLabel' )
				&& '1' === seopress_get_service( 'ToggleOption' )->getToggleWhiteLabel() ) {
				return;
			}
		}

		// Check for metabox promotion.
		$promotion = seopress_get_service( 'PromotionService' )->getPromotion( 'metabox' );
		if ( ! $promotion ) {
			return;
		}

		// Enqueue the metabox promo script and styles.
		wp_enqueue_style(
			'seopress-promotions',
			SEOPRESS_URL_ASSETS . '/css/seopress-promotions.css',
			array(),
			SEOPRESS_VERSION
		);

		wp_enqueue_script(
			'seopress-metabox-promo',
			SEOPRESS_URL_ASSETS . '/js/seopress-metabox-promo.js',
			array( 'wp-element', 'seopress-metabox' ),
			SEOPRESS_VERSION,
			true
		);

		// Flatten promotion content for the banner.
		$promo_data = array(
			'id'               => $promotion['id'],
			'title'            => $promotion['content']['title'] ?? '',
			'body'             => $promotion['content']['body'] ?? '',
			'cta_text'         => $promotion['content']['cta_text'] ?? '',
			'cta_url'          => $promotion['content']['cta_url'] ?? '',
			'icon'             => $promotion['content']['icon'] ?? 'star-filled',
			'styling'          => $promotion['styling'] ?? array(),
			'dismissible'      => $promotion['dismissible'] ?? true,
			'dismiss_duration' => $promotion['dismiss_duration_days'] ?? 30,
		);

		wp_localize_script( 'seopress-metabox-promo', 'seopressMetaboxPromo', array( 'promotion' => $promo_data ) );

		// Also localize the dismiss nonce for AJAX dismissal.
		wp_localize_script(
			'seopress-metabox-promo',
			'seopressPromotions',
			array(
				'dismiss_nonce'  => wp_create_nonce( 'seopress_dismiss_promotion_nonce' ),
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'stats_endpoint' => \SEOPress\Constants\Promotions::getApiUrl() . '/stats',
			)
		);
	}

	/**
	 * Enqueue frontend.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function enqueueFrontend() {
		$this->enqueueModule( array( 'POST_ID' => get_the_ID() ) );
	}

	/**
	 * Enqueue.
	 *
	 * @since 5.0.0
	 *
	 * @param string $page The page.
	 *
	 * @return void
	 */
	public function enqueue( $page ) {
		if ( ! in_array( $page, array( 'post.php', 'post-new.php', 'woocommerce_page_wc-admin' ), true ) ) {
			return;
		}
		$this->enqueueModule();
	}

	/**
	 * Enqueue elementor.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function enqueueElementor() {
		$this->enqueueModule();
	}

	/**
	 * Get cached content analysis data formatted for frontend.
	 *
	 * @since 9.5.0
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array|null Cached content analysis data or null if not available.
	 */
	protected function getCachedContentAnalysis( $post_id ) {
		if ( ! $post_id ) {
			return null;
		}

		$cached = seopress_get_service( 'ContentAnalysisDatabase' )->getData( $post_id );

		if ( empty( $cached ) ) {
			return null;
		}

		// Format the data in the structure expected by the frontend.
		// The database stores flat values, but the frontend expects { value: ... } format.
		$data = array();

		// Map database columns to API response format.
		$mappings = array(
			'title'               => 'title',
			'description'         => 'description',
			'og_title'            => 'og:title',
			'og_description'      => 'og:description',
			'og_image'            => 'og:image',
			'og_url'              => 'og:url',
			'og_site_name'        => 'og:site_name',
			'twitter_title'       => 'twitter:title',
			'twitter_description' => 'twitter:description',
			'twitter_image'       => 'twitter:image',
			'twitter_image_src'   => 'twitter:image:src',
			'canonical'           => 'canonical',
			'meta_robots'         => 'meta_robots',
			'meta_google'         => 'meta_google',
		);

		foreach ( $mappings as $db_key => $api_key ) {
			if ( isset( $cached[ $db_key ] ) ) {
				$data[ $api_key ] = array( 'value' => $cached[ $db_key ] );
			}
		}

		// Handle array fields (h1, h2, h3, images, links).
		$array_mappings = array(
			'h1'              => 'h1',
			'h2'              => 'h2',
			'h3'              => 'h3',
			'images'          => 'images',
			'links_no_follow' => 'links_no_follow',
			'outbound_links'  => 'outbound_links',
			'internal_links'  => 'internal_links',
			'json_schemas'    => 'schemas',
		);

		foreach ( $array_mappings as $db_key => $api_key ) {
			if ( isset( $cached[ $db_key ] ) ) {
				$value = $cached[ $db_key ];
				// If stored as JSON string, decode it.
				if ( is_string( $value ) && ! empty( $value ) ) {
					$decoded = json_decode( $value, true );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						$value = $decoded;
					}
				}
				$data[ $api_key ] = array( 'value' => is_array( $value ) ? $value : array() );
			}
		}

		// Add score if available.
		if ( isset( $cached['score'] ) ) {
			$score = $cached['score'];
			if ( is_string( $score ) && ! empty( $score ) ) {
				$decoded = json_decode( $score, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					$score = $decoded;
				}
			}
			$data['score'] = $score;
		}

		// Add permalink/link_preview if available.
		if ( isset( $cached['permalink'] ) ) {
			$data['link_preview'] = $cached['permalink'];
		}

		// Run the same filter the REST endpoint applies so Pro can attach
		// the seopress_seo_issues map (used by the editor ignore controls).
		// Keeps the cached snapshot and a live refresh in lockstep.
		$data = apply_filters( 'seopress_content_analysis_response', $data, $post_id );

		return ! empty( $data ) ? $data : null;
	}
}

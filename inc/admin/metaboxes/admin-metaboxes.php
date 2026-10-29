<?php
/**
 * Metaboxes
 *
 * @package Metaboxes
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

use SEOPress\Actions\Api\Metas\AdvancedSettings;
use SEOPress\Helpers\Metas\PrimaryTerm as PrimaryTermHelper;

/**
 * Display metabox in Custom Post Type
 */
function seopress_display_date_snippet() {
	if ( seopress_get_service( 'TitleOption' )->getSingleCptDate() === '1' ) {
		return '<div class="snippet-date">' . get_the_modified_date( 'M j, Y' ) . ' - </div>';
	}
}

/**
 * Primary term select for one taxonomy.
 *
 * Renders the `<select>` the Classic Editor injector appends to the
 * taxonomy metabox. The field name is derived from the taxonomy's meta
 * key, so `category` and `product_cat` keep the historical
 * `seopress_robots_primary_cat` name while every other taxonomy gets its
 * own `seopress_robots_primary_term_{taxonomy}` field.
 *
 * @since 10.4.0
 *
 * @param WP_Post|null $post             The post being edited.
 * @param string       $taxonomy_name    Taxonomy name.
 * @param bool         $with_description Display the description.
 * @param bool         $print_html       Print the HTML instead of returning it.
 *
 * @return string HTML.
 */
function seopress_primary_term_select( $post, $taxonomy_name, $with_description = true, $print_html = false ) {
	$taxonomy = get_taxonomy( $taxonomy_name );

	if ( ! $taxonomy instanceof WP_Taxonomy ) {
		return '';
	}

	$post_type  = $post instanceof WP_Post ? $post->post_type : '';
	$field_name = PrimaryTermHelper::get_field_name( $taxonomy_name, $post_type );
	$current    = $post instanceof WP_Post ? PrimaryTermHelper::get_raw_value( $post->ID, $taxonomy_name ) : '';

	if ( 'category' === $taxonomy_name ) {
		// Historical behaviour kept for the core category: every category of
		// the site is offered, the browser then narrows the list down to the
		// ones actually checked on the post.
		$terms = get_categories();
	} else {
		$terms = $post instanceof WP_Post ? get_the_terms( $post, $taxonomy_name ) : array();
	}

	if ( in_array( $taxonomy_name, PrimaryTermHelper::get_legacy_taxonomies(), true ) ) {
		$terms = apply_filters( 'seopress_primary_category_list', $terms );
	}

	/**
	 * Filter the terms offered by the primary term selector.
	 *
	 * @since 10.4.0
	 *
	 * @param WP_Term[]|false|WP_Error $terms         The terms.
	 * @param string                   $taxonomy_name Taxonomy name.
	 * @param WP_Post|null             $post          The post being edited.
	 */
	$terms = apply_filters( 'seopress_primary_term_list', $terms, $taxonomy_name, $post );

	$options = sprintf(
		'<option value="none" %s>%s</option>',
		selected( 'none', $current, false ),
		__( 'None (will disable this feature)', 'wp-seopress' )
	);

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$options .= sprintf(
				'<option value="%s" %s>%s</option>',
				(int) $term->term_id,
				selected( $term->term_id, $current, false ),
				esc_html( $term->name )
			);
		}
	}

	$html = sprintf(
		'<p>
				<label for="%1$s" style="display:block; margin-bottom:8px;">%2$s</label>
				%3$s
				<br><select id="%1$s" name="%1$s">%4$s</select>
			</p>',
		esc_attr( $field_name ),
		esc_html( PrimaryTermHelper::get_label( $taxonomy ) ),
		$with_description ? sprintf( '<span class="description">%s</span>', wp_kses_post( PrimaryTermHelper::get_description( $taxonomy ) ) ) : '',
		$options
	);

	if ( $print_html ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Admin markup assembled above, its dynamic parts already escaped.
	}

	return $html;
}

/**
 * Register the primary category post meta.
 *
 * @deprecated 10.4.0 The meta keys are registered per taxonomy by
 *             `SEOPress\Actions\Api\Metas\AdvancedSettings::register_primary_term_metas()`
 *             and by the advanced settings registration beside it. Kept as a
 *             no-op because it was a public global function, and a theme or a
 *             snippet calling it would otherwise fatal.
 *
 * @return void
 */
function seopress_register_primary_category_post_meta() {
	_deprecated_function( __FUNCTION__, '10.4.0' );
}

/**
 * Primary category select.
 *
 * Back-compatible wrapper resolving the taxonomy from the post type being
 * edited, kept because PRO, the legacy metabox form and third parties call
 * it directly.
 *
 * @param bool $print_html Print the HTML instead of returning it.
 * @param bool $with_description Display the description.
 *
 * @return string HTML.
 */
function seopress_primary_category_select( $print_html = true, $with_description = true ) {
	global $post;
	global $typenow;

	$taxonomy = 'product' === $typenow && seopress_get_service( 'WooCommerceActivate' )->isActive() ? 'product_cat' : 'category';

	return seopress_primary_term_select( $post, $taxonomy, $with_description, $print_html );
}

/**
 * Inject the primary category dropdown into the WordPress Categories panel.
 *
 * Restores the inline UI removed alongside the legacy classic SEO metabox
 * in 9.8: in the Block Editor the Gutenberg bundle hooks into the
 * Categories sidebar panel via REST-exposed post meta; in the Classic
 * Editor a small script appends the rendered `<select>` markup (plus a
 * dedicated nonce) into the standard Categories metabox so the value
 * ships with the post form. The data layer (post meta
 * `_seopress_robots_primary_cat`) is unchanged and the React universal
 * SEO metabox keeps its own primary category field as the consolidated
 * power-user surface.
 *
 * @since 9.8.x
 *
 * @return void
 */
function seopress_enqueue_primary_category_injector() {
	global $pagenow, $typenow, $post, $wp_version;

	if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$taxonomies = array_filter(
		PrimaryTermHelper::get_taxonomies( $typenow ),
		static function ( $taxonomy_name ) use ( $typenow ) {
			/**
			 * Filter whether the primary term selector is added to the box of
			 * a taxonomy on the post edit screen.
			 *
			 * Returning false only removes the selector from that box: a
			 * primary term already saved keeps driving the permalink and the
			 * breadcrumbs, and the Advanced tab of the SEO metabox still
			 * offers it.
			 *
			 * @since 10.4.0
			 *
			 * @param bool   $enabled       Whether to add the selector. Default true.
			 * @param string $taxonomy_name Taxonomy name.
			 * @param string $post_type     Post type being edited.
			 */
			return (bool) apply_filters( 'seopress_primary_term_inline_selector', true, $taxonomy_name, $typenow );
		},
		ARRAY_FILTER_USE_KEY
	);

	if ( empty( $taxonomies ) ) {
		return;
	}

	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}

	$screen          = get_current_screen();
	$is_block_editor = $screen && method_exists( $screen, 'is_block_editor' ) && true === $screen->is_block_editor();

	if ( $is_block_editor ) {
		if ( version_compare( $wp_version, '5.8', '<' ) ) {
			return;
		}

		// The panel saves through the post's `meta`, and core only exposes and
		// writes `meta` over REST for post types supporting `custom-fields`.
		// Without it the choice was dropped on save without a word. The
		// universal metabox still offers the selector there, and saves it
		// through its own route.
		if ( ! post_type_supports( $typenow, 'custom-fields' ) ) {
			return;
		}

		// A role kept out of the SEO metabox can neither read the stored
		// choice over REST nor save a new one: the panel would show "None"
		// and silently drop whatever it picks.
		if ( AdvancedSettings::role_is_blocked_on( $typenow, 'GLOBAL' ) ) {
			return;
		}
		wp_enqueue_script(
			'seopress-primary-category',
			SEOPRESS_URL_PUBLIC . '/editor/primary-category-select/index.js',
			array( 'wp-hooks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-data' ),
			SEOPRESS_VERSION,
			true
		);
		wp_set_script_translations( 'seopress-primary-category', 'wp-seopress', WP_LANG_DIR . '/plugins' );

		$meta_keys = array();
		$excluded  = array();
		foreach ( array_keys( $taxonomies ) as $taxonomy_name ) {
			$meta_keys[ $taxonomy_name ] = PrimaryTermHelper::get_meta_key( $taxonomy_name, $typenow );
			$excluded[ $taxonomy_name ]  = $post instanceof WP_Post ? PrimaryTermHelper::get_excluded_term_ids( $post->ID, $taxonomy_name ) : array();
		}

		// PHP stays the single source of truth for the taxonomy / meta key
		// mapping: the Block Editor panel only renders for the taxonomies
		// listed here and writes to the key it is given.
		wp_localize_script(
			'seopress-primary-category',
			'seopressPrimaryTermData',
			array(
				'metaKeys' => $meta_keys,
				'excluded' => $excluded,
			)
		);
		return;
	}

	wp_enqueue_script(
		'seopress-primary-category-classic',
		SEOPRESS_ASSETS_DIR . '/js/seopress-primary-category-classic.js',
		array(),
		SEOPRESS_VERSION,
		true
	);

	$nonce_field = wp_nonce_field( 'seopress_save_primary_category', 'seopress_primary_category_nonce', true, false );

	$payload = array();
	foreach ( array_keys( $taxonomies ) as $taxonomy_name ) {
		$payload[] = array(
			'taxonomy'     => $taxonomy_name,
			'metaboxId'    => PrimaryTermHelper::get_metabox_id( $taxonomy_name ),
			'fieldId'      => PrimaryTermHelper::get_field_name( $taxonomy_name, $typenow ),
			'checkboxName' => PrimaryTermHelper::get_checkbox_name( $taxonomy_name ),
			'selectHTML'   => seopress_primary_term_select( $post, $taxonomy_name, false ),
			'value'        => $post instanceof WP_Post ? PrimaryTermHelper::get_raw_value( $post->ID, $taxonomy_name ) : '',
			'excluded'     => $post instanceof WP_Post ? array_map( 'strval', PrimaryTermHelper::get_excluded_term_ids( $post->ID, $taxonomy_name ) ) : array(),
		);
	}

	wp_localize_script(
		'seopress-primary-category-classic',
		'seopressPrimaryCategorySelectData',
		array(
			'nonceField' => $nonce_field,
			'taxonomies' => $payload,
		)
	);
}
add_action( 'admin_enqueue_scripts', 'seopress_enqueue_primary_category_injector' );

/**
 * Save the primary terms submitted from the taxonomy metaboxes in the
 * Classic Editor. The dedicated nonce + capability check makes this
 * handler self-contained, independent of the legacy SEO metabox save
 * pipeline (which only runs when the universal metabox is suppressed).
 *
 * @since 9.8.x
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function seopress_save_primary_category_from_categories_metabox( $post_id, $post ) {
	if ( ! isset( $_POST['seopress_primary_category_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['seopress_primary_category_nonce'] ) ), 'seopress_save_primary_category' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array_keys( PrimaryTermHelper::get_taxonomies( $post->post_type ) ) as $taxonomy_name ) {
		$field = PrimaryTermHelper::get_field_name( $taxonomy_name, $post->post_type );

		if ( ! isset( $_POST[ $field ] ) ) {
			continue;
		}

		PrimaryTermHelper::save( $post_id, $taxonomy_name, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
	}
}
add_action( 'save_post', 'seopress_save_primary_category_from_categories_metabox', 10, 2 );

/**
 * Metaboxes init
 *
 * @return array Data attributes.
 */
function seopress_metaboxes_init() {
	global $typenow;
	global $pagenow;

	$data_attr             = array();
	$data_attr['data_tax'] = '';
	$data_attr['termId']   = '';

	if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
		$data_attr['current_id'] = get_the_id();
		$data_attr['origin']     = 'post';
		$data_attr['title']      = get_the_title( $data_attr['current_id'] );
	} elseif ( 'term.php' === $pagenow || 'edit-tags.php' === $pagenow ) {
		global $tag;
		$data_attr['current_id'] = $tag->term_id;
		$data_attr['termId']     = $tag->term_id;
		$data_attr['origin']     = 'term';
		$data_attr['data_tax']   = $tag->taxonomy;
		$data_attr['title']      = $tag->name;
	}

	$data_attr['isHomeId'] = get_option( 'page_on_front' );
	if ( '0' === $data_attr['isHomeId'] ) {
		$data_attr['isHomeId'] = '';
	}

	return $data_attr;
}

/**
 * Display SEO metaboxe
 */
function seopress_display_seo_metaboxe() {
	add_action( 'add_meta_boxes', 'seopress_init_metabox' );

	/**
	 * Init metaboxe
	 */
	function seopress_init_metabox() {
		$seopress_get_post_types = seopress_get_service( 'WordPressData' )->getPostTypes();

		$seopress_get_post_types = apply_filters( 'seopress_metaboxe_seo', $seopress_get_post_types );

		if ( ! empty( $seopress_get_post_types ) && ! seopress_get_service( 'EnqueueModuleMetabox' )->canEnqueue() ) {
			foreach ( $seopress_get_post_types as $key => $value ) {
				add_meta_box( 'seopress_cpt', __( 'SEO', 'wp-seopress' ), 'seopress_cpt', $key, 'normal', 'default' );
			}
		}
		add_meta_box( 'seopress_cpt', __( 'SEO', 'wp-seopress' ), 'seopress_cpt', 'seopress_404', 'normal', 'default' );
	}

	/**
	 * Display SEO metaboxe
	 *
	 * @param object $post Post object.
	 */
	function seopress_cpt( $post ) {
		$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_nonce_field( plugin_basename( __FILE__ ), 'seopress_cpt_nonce' );

		// init.
		$disabled = array();

		wp_enqueue_script( 'seopress-cpt-tabs', SEOPRESS_ASSETS_DIR . '/js/seopress-metabox' . $prefix . '.js', [ 'jquery-ui-tabs' ], SEOPRESS_VERSION, true );

		// Note: Classic metabox is now only used for seopress_404 post type.
		// Universal metabox handles all other post types.

		$seopress_titles_title = get_post_meta( $post->ID, '_seopress_titles_title', true );
		$seopress_titles_desc  = get_post_meta( $post->ID, '_seopress_titles_desc', true );

		$disabled['robots_index'] = '';
		if ( seopress_get_service( 'TitleOption' )->getSingleCptNoIndex() || seopress_get_service( 'TitleOption' )->getTitleNoIndex() || true === post_password_required( $post->ID ) ) {
			$seopress_robots_index    = 'yes';
			$disabled['robots_index'] = 'disabled';
		} else {
			$seopress_robots_index = get_post_meta( $post->ID, '_seopress_robots_index', true );
		}

		$disabled['robots_follow'] = '';
		if ( seopress_get_service( 'TitleOption' )->getSingleCptNoFollow() || seopress_get_service( 'TitleOption' )->getTitleNoFollow() ) {
			$seopress_robots_follow    = 'yes';
			$disabled['robots_follow'] = 'disabled';
		} else {
			$seopress_robots_follow = get_post_meta( $post->ID, '_seopress_robots_follow', true );
		}

		$disabled['snippet'] = '';
		if ( seopress_get_service( 'TitleOption' )->getTitleNoSnippet() ) {
			$seopress_robots_snippet = 'yes';
			$disabled['snippet']     = 'disabled';
		} else {
			$seopress_robots_snippet = get_post_meta( $post->ID, '_seopress_robots_snippet', true );
		}

		$disabled['imageindex'] = '';
		if ( seopress_get_service( 'TitleOption' )->getTitleNoImageIndex() ) {
			$seopress_robots_imageindex = 'yes';
			$disabled['imageindex']     = 'disabled';
		} else {
			$seopress_robots_imageindex = get_post_meta( $post->ID, '_seopress_robots_imageindex', true );
		}

		$seopress_robots_canonical                 = get_post_meta( $post->ID, '_seopress_robots_canonical', true );
		$seopress_robots_primary_cat               = get_post_meta( $post->ID, '_seopress_robots_primary_cat', true );
		$seopress_robots_freeze_modified_date      = get_post_meta( $post->ID, '_seopress_robots_freeze_modified_date', true );
		if ( '' === $seopress_robots_freeze_modified_date && '1' === seopress_get_service( 'AdvancedOption' )->getAppearanceFreezeModifiedDate() ) {
			$seopress_robots_freeze_modified_date = 'yes';
		}
		$seopress_robots_custom_modified_date      = get_post_meta( $post->ID, '_seopress_robots_custom_modified_date', true );
		$seopress_social_fb_title                  = get_post_meta( $post->ID, '_seopress_social_fb_title', true );
		$seopress_social_fb_desc                   = get_post_meta( $post->ID, '_seopress_social_fb_desc', true );
		$seopress_social_fb_img                    = get_post_meta( $post->ID, '_seopress_social_fb_img', true );
		$seopress_social_fb_img_attachment_id      = get_post_meta( $post->ID, '_seopress_social_fb_img_attachment_id', true );
		$seopress_social_fb_img_width              = get_post_meta( $post->ID, '_seopress_social_fb_img_width', true );
		$seopress_social_fb_img_height             = get_post_meta( $post->ID, '_seopress_social_fb_img_height', true );
		$seopress_social_twitter_title             = get_post_meta( $post->ID, '_seopress_social_twitter_title', true );
		$seopress_social_twitter_desc              = get_post_meta( $post->ID, '_seopress_social_twitter_desc', true );
		$seopress_social_twitter_img               = get_post_meta( $post->ID, '_seopress_social_twitter_img', true );
		$seopress_social_twitter_img_attachment_id = get_post_meta( $post->ID, '_seopress_social_twitter_img_attachment_id', true );
		$seopress_social_twitter_img_width         = get_post_meta( $post->ID, '_seopress_social_twitter_img_width', true );
		$seopress_social_twitter_img_height        = get_post_meta( $post->ID, '_seopress_social_twitter_img_height', true );
		$seopress_redirections_enabled             = get_post_meta( $post->ID, '_seopress_redirections_enabled', true );
		$seopress_redirections_enabled_regex       = get_post_meta( $post->ID, '_seopress_redirections_enabled_regex', true );
		$seopress_redirections_logged_status       = get_post_meta( $post->ID, '_seopress_redirections_logged_status', true );
		$seopress_redirections_type                = get_post_meta( $post->ID, '_seopress_redirections_type', true );
		$seopress_redirections_value               = get_post_meta( $post->ID, '_seopress_redirections_value', true );
		$seopress_redirections_param               = get_post_meta( $post->ID, '_seopress_redirections_param', true );

		require_once dirname( __DIR__ ) . '/admin-dyn-variables-helper.php'; // Dynamic variables.

		require_once __DIR__ . '/admin-metaboxes-form.php'; // Metaboxe HTML.

		do_action( 'seopress_seo_metabox_init' );
	}

	add_action( 'save_post', 'seopress_save_metabox', 10, 2 );
	/**
	 * Save metabox
	 *
	 * @param int $post_id Post ID.
	 * @param object $post Post object.
	 */
	function seopress_save_metabox( $post_id, $post ) {
		// Nonce.
		if ( ! isset( $_POST['seopress_cpt_nonce'] ) || ! wp_verify_nonce( $_POST['seopress_cpt_nonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check permission.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		if ( 'attachment' !== get_post_type( $post_id ) ) {
			$seo_tabs = isset( $_POST['seo_tabs'] ) ? json_decode( stripslashes( htmlspecialchars_decode( $_POST['seo_tabs'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) ), true ) : array();

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return $post_id;
			}

			if ( in_array( 'title-tab', $seo_tabs, true ) ) {
				if ( isset( $_POST['seopress_titles_title'] ) && '' !== $_POST['seopress_titles_title'] ) {
					update_post_meta( $post_id, '_seopress_titles_title', sanitize_text_field( $_POST['seopress_titles_title'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_titles_title' );
				}
				if ( isset( $_POST['seopress_titles_desc'] ) && '' !== $_POST['seopress_titles_desc'] ) {
					update_post_meta( $post_id, '_seopress_titles_desc', sanitize_textarea_field( $_POST['seopress_titles_desc'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_titles_desc' );
				}
			}
			if ( in_array( 'advanced-tab', $seo_tabs, true ) ) {
				if ( isset( $_POST['seopress_robots_index'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_index', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_index' );
				}
				if ( isset( $_POST['seopress_robots_follow'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_follow', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_follow' );
				}
				if ( isset( $_POST['seopress_robots_imageindex'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_imageindex', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_imageindex' );
				}
				if ( isset( $_POST['seopress_robots_snippet'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_snippet', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_snippet' );
				}
				if ( ! empty( $_POST['seopress_robots_canonical'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_canonical', sanitize_url( $_POST['seopress_robots_canonical'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_canonical' );
				}
				if ( ! empty( $_POST['seopress_robots_primary_cat'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_primary_cat', sanitize_text_field( $_POST['seopress_robots_primary_cat'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_primary_cat' );
				}
				if ( isset( $_POST['seopress_robots_freeze_modified_date'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_freeze_modified_date', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_freeze_modified_date' );
				}
				if ( ! empty( $_POST['seopress_robots_custom_modified_date'] ) ) {
					update_post_meta( $post_id, '_seopress_robots_custom_modified_date', sanitize_text_field( wp_unslash( $_POST['seopress_robots_custom_modified_date'] ) ) );
				} else {
					delete_post_meta( $post_id, '_seopress_robots_custom_modified_date' );
				}
			}
			if ( in_array( 'social-tab', $seo_tabs, true ) ) {
				// Facebook.
				if ( ! empty( $_POST['seopress_social_fb_title'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_title', sanitize_text_field( $_POST['seopress_social_fb_title'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_title' );
				}
				if ( ! empty( $_POST['seopress_social_fb_desc'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_desc', sanitize_textarea_field( $_POST['seopress_social_fb_desc'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_desc' );
				}
				if ( ! empty( $_POST['seopress_social_fb_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_img', sanitize_url( $_POST['seopress_social_fb_img'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_img' );
				}
				if ( ! empty( $_POST['seopress_social_fb_img_attachment_id'] ) && ! empty( $_POST['seopress_social_fb_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_img_attachment_id', sanitize_text_field( $_POST['seopress_social_fb_img_attachment_id'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_img_attachment_id' );
				}
				if ( ! empty( $_POST['seopress_social_fb_img_width'] ) && ! empty( $_POST['seopress_social_fb_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_img_width', sanitize_text_field( $_POST['seopress_social_fb_img_width'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_img_width' );
				}
				if ( ! empty( $_POST['seopress_social_fb_img_height'] ) && ! empty( $_POST['seopress_social_fb_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_fb_img_height', sanitize_text_field( $_POST['seopress_social_fb_img_height'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_fb_img_height' );
				}

				// Twitter.
				if ( ! empty( $_POST['seopress_social_twitter_title'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_title', sanitize_text_field( $_POST['seopress_social_twitter_title'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_title' );
				}
				if ( ! empty( $_POST['seopress_social_twitter_desc'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_desc', sanitize_textarea_field( $_POST['seopress_social_twitter_desc'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_desc' );
				}
				if ( ! empty( $_POST['seopress_social_twitter_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_img', sanitize_url( $_POST['seopress_social_twitter_img'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_img' );
				}
				if ( ! empty( $_POST['seopress_social_twitter_img_attachment_id'] ) && ! empty( $_POST['seopress_social_twitter_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_img_attachment_id', sanitize_text_field( $_POST['seopress_social_twitter_img_attachment_id'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_img_attachment_id' );
				}
				if ( ! empty( $_POST['seopress_social_twitter_img_width'] ) && ! empty( $_POST['seopress_social_twitter_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_img_width', sanitize_text_field( $_POST['seopress_social_twitter_img_width'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_img_width' );
				}
				if ( ! empty( $_POST['seopress_social_twitter_img_height'] ) && ! empty( $_POST['seopress_social_twitter_img'] ) ) {
					update_post_meta( $post_id, '_seopress_social_twitter_img_height', sanitize_text_field( $_POST['seopress_social_twitter_img_height'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_social_twitter_img_height' );
				}
			}
			if ( in_array( 'redirect-tab', $seo_tabs, true ) ) {
				if ( isset( $_POST['seopress_redirections_type'] ) ) {
					$redirection_type = intval( $_POST['seopress_redirections_type'] );

					if ( in_array( $redirection_type, array( 301, 302, 307, 410, 451 ), true ) ) {
						update_post_meta( $post_id, '_seopress_redirections_type', $redirection_type );
					} else {
						delete_post_meta( $post_id, '_seopress_redirections_type' );
					}
				}
				if ( ! empty( $_POST['seopress_redirections_value'] ) ) {
					update_post_meta( $post_id, '_seopress_redirections_value', sanitize_url( $_POST['seopress_redirections_value'] ) );
				} else {
					delete_post_meta( $post_id, '_seopress_redirections_value' );
				}
				if ( isset( $_POST['seopress_redirections_enabled'] ) ) {
					update_post_meta( $post_id, '_seopress_redirections_enabled', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_redirections_enabled', '' );
				}
				if ( isset( $_POST['seopress_redirections_enabled_regex'] ) ) {
					update_post_meta( $post_id, '_seopress_redirections_enabled_regex', 'yes' );
				} else {
					delete_post_meta( $post_id, '_seopress_redirections_enabled_regex' );
				}
				if ( isset( $_POST['seopress_redirections_logged_status'] ) ) {
					$logged_status = sanitize_text_field( $_POST['seopress_redirections_logged_status'] );

					$allowed_options = array( 'both', 'only_logged_in', 'only_not_logged_in' );

					if ( in_array( $logged_status, $allowed_options, true ) ) {
						update_post_meta( $post_id, '_seopress_redirections_logged_status', $logged_status );
					} else {
						delete_post_meta( $post_id, '_seopress_redirections_logged_status' );
					}
				}
				if ( isset( $_POST['seopress_redirections_param'] ) ) {
					$redirections_param = sanitize_text_field( $_POST['seopress_redirections_param'] );

					$allowed_options = array( 'exact_match', 'without_param', 'with_ignored_param' );

					if ( in_array( $redirections_param, $allowed_options, true ) ) {
						update_post_meta( $post_id, '_seopress_redirections_param', $redirections_param );
					} else {
						delete_post_meta( $post_id, '_seopress_redirections_param' );
					}
				}
			}
			do_action( 'seopress_seo_metabox_save', $post_id, $seo_tabs );
		}
	}
}

/**
 * Display CA metaboxe
 */
function seopress_display_ca_metaboxe() {
	add_action( 'add_meta_boxes', 'seopress_init_ca_metabox' );

	/**
	 * Init CA metabox
	 */
	function seopress_init_ca_metabox() {
		$seopress_get_post_types = seopress_get_service( 'WordPressData' )->getPostTypes();

		$seopress_get_post_types = apply_filters( 'seopress_metaboxe_content_analysis', $seopress_get_post_types );

		if ( ! empty( $seopress_get_post_types ) && ! seopress_get_service( 'EnqueueModuleMetabox' )->canEnqueue() ) {
			foreach ( $seopress_get_post_types as $key => $value ) {
				add_meta_box( 'seopress_content_analysis', __( 'Content analysis', 'wp-seopress' ), 'seopress_content_analysis', $key, 'normal', 'default' );
			}
		}
	}

	/**
	 * Display CA metaboxe
	 *
	 * @param object $post Post object.
	 */
	function seopress_content_analysis( $post ) {
		$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_nonce_field( plugin_basename( __FILE__ ), 'seopress_content_analysis_nonce' );

		// Tagify.
		wp_enqueue_script( 'seopress-tagify', SEOPRESS_ASSETS_DIR . '/js/tagify' . $prefix . '.js', array( 'jquery' ), SEOPRESS_VERSION, true );
		wp_register_style( 'seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify' . $prefix . '.css', array(), SEOPRESS_VERSION );
		wp_enqueue_style( 'seopress-tagify' );

		wp_enqueue_script(
			'seopress-cpt-counters',
			SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js',
			array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-autocomplete' ),
			SEOPRESS_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		$seopress_real_preview = array(
			'seopress_nonce'               => wp_create_nonce( 'seopress_real_preview_nonce' ),
			'seopress_real_preview'        => admin_url( 'admin-ajax.php' ),
			'i18n'                         => array( 'progress' => __( 'Analysis in progress...', 'wp-seopress' ) ),
			'ajax_url'                     => admin_url( 'admin-ajax.php' ),
			'get_preview_meta_title'       => wp_create_nonce( 'get_preview_meta_title' ),
			'get_preview_meta_description' => wp_create_nonce( 'get_preview_meta_description' ),
		);
		wp_localize_script( 'seopress-cpt-counters', 'seopressAjaxRealPreview', $seopress_real_preview );

		$seopress_inspect_url = array(
			'seopress_nonce'       => wp_create_nonce( 'seopress_inspect_url_nonce' ),
			'seopress_inspect_url' => admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'seopress-cpt-counters', 'seopressAjaxInspectUrl', $seopress_inspect_url );

		$seopress_analysis_target_kw = get_post_meta( $post->ID, '_seopress_analysis_target_kw', true );
		$seopress_analysis_data      = get_post_meta( $post->ID, '_seopress_analysis_data', true );
		$seopress_titles_title       = get_post_meta( $post->ID, '_seopress_titles_title', true );
		$seopress_titles_desc        = get_post_meta( $post->ID, '_seopress_titles_desc', true );

		if ( seopress_get_service( 'TitleOption' )->getSingleCptNoIndex() || seopress_get_service( 'TitleOption' )->getTitleNoIndex() || true === post_password_required( $post->ID ) ) {
			$seopress_robots_index = 'yes';
		} else {
			$seopress_robots_index = get_post_meta( $post->ID, '_seopress_robots_index', true );
		}

		if ( seopress_get_service( 'TitleOption' )->getSingleCptNoFollow() || seopress_get_service( 'TitleOption' )->getTitleNoFollow() ) {
			$seopress_robots_follow = 'yes';
		} else {
			$seopress_robots_follow = get_post_meta( $post->ID, '_seopress_robots_follow', true );
		}

		if ( seopress_get_service( 'TitleOption' )->getTitleNoSnippet() ) {
			$seopress_robots_snippet = 'yes';
		} else {
			$seopress_robots_snippet = get_post_meta( $post->ID, '_seopress_robots_snippet', true );
		}

		if ( seopress_get_service( 'TitleOption' )->getTitleNoImageIndex() ) {
			$seopress_robots_imageindex = 'yes';
		} else {
			$seopress_robots_imageindex = get_post_meta( $post->ID, '_seopress_robots_imageindex', true );
		}

		require_once __DIR__ . '/admin-metaboxes-content-analysis-form.php'; // Metaboxe HTML.
	}

	add_action( 'save_post', 'seopress_save_ca_metabox', 10, 2 );
	/**
	 * Save CA metabox
	 *
	 * @param int $post_id Post ID.
	 * @param object $post Post object.
	 */
	function seopress_save_ca_metabox( $post_id, $post ) {
		// Nonce.
		if ( ! isset( $_POST['seopress_content_analysis_nonce'] ) || ! wp_verify_nonce( $_POST['seopress_content_analysis_nonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check permission.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		if ( 'attachment' !== get_post_type( $post_id ) ) {
			if ( isset( $_POST['seopress_analysis_target_kw'] ) ) {
				$target_kw = sanitize_text_field( $_POST['seopress_analysis_target_kw'] );
				update_post_meta( $post_id, '_seopress_analysis_target_kw', $target_kw );
			}
		}
	}
}

// Every role of the user counts, as in seopress_metabox_role_is_blocked(): only
// the first one was checked, so a user with a second, restricted role kept a
// metabox the settings hide from them.
if ( is_user_logged_in() && ! seopress_metabox_role_is_blocked( 'GLOBAL' ) ) {
	seopress_display_seo_metaboxe();
}

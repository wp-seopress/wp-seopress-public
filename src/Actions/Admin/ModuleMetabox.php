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

		if ( current_user_can( seopress_capability( 'edit_posts' ) ) ) { // phpcs:ignore
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontend' ) );
		}
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

		$dependencies = array( 'jquery-ui-datepicker' );
		if ( $is_gutenberg ) {
			$dependencies = array_merge( $dependencies, array( 'wp-components', 'wp-edit-post', 'wp-plugins' ) );
		}

		wp_enqueue_media();
		wp_enqueue_script( 'seopress-metabox', SEOPRESS_URL_PUBLIC . '/metaboxe.js', $dependencies, SEOPRESS_VERSION, true );

		global $post;

		if ( post_type_supports( get_post_type( $post ), 'custom-fields' ) ) {
			wp_enqueue_script( 'seopress-pre-publish-checklist', SEOPRESS_URL_PUBLIC . '/editor/pre-publish-checklist/index.js', array(), SEOPRESS_VERSION, true );
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

		$post_id   = is_singular() ? get_the_ID() : null;
		$post_type = null;
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
				'SEOPRESS_PRO_IS_ACTIVATED' => is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ? true : false,
				'SITENAME'                  => get_bloginfo( 'name' ),
				'SITEURL'                   => site_url(),
				'ADMIN_URL_TITLES'          => admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_single' ),
				'ADMIN_URL_ARCHIVES_TITLES' => admin_url( 'admin.php?page=seopress-titles#tab=tab_seopress_titles_archives' ),
				'TAGS'                      => array_values( $tags ),
				'REST_URL'                  => rest_url(),
				'NONCE'                     => wp_create_nonce( 'wp_rest' ),
				'POST_ID'                   => $post_id,
				'POST_TYPE'                 => $post_type,
				'IS_GUTENBERG'              => apply_filters( 'seopress_module_metabox_is_gutenberg', $is_gutenberg ),
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
				'TABS'                      => array(
					'SCHEMAS' => apply_filters( 'seopress_active_schemas_manual_universal_metabox', false ),
				),
				'SUB_TABS'                  => array(
					'GOOGLE_NEWS'      => apply_filters( 'seopress_active_google_news', false ),
					'VIDEO_SITEMAP'    => apply_filters( 'seopress_active_video_sitemap', false ),
					'INSPECT_URL'      => apply_filters( 'seopress_active_inspect_url', false ),
					'INTERNAL_LINKING' => apply_filters( 'seopress_active_internal_linking', false ),
					'SCHEMA_MANUAL'    => apply_filters( 'seopress_active_schemas', false ),
				),
				'FAVICON'                   => get_site_icon_url( 32 ),
				'BEACON_SVG'                => apply_filters( 'seopress_beacon_svg', SEOPRESS_URL_ASSETS . '/img/beacon.svg' ),
				'AI_SVG'                    => apply_filters( 'seopress_ai_svg', SEOPRESS_URL_ASSETS . '/img/ai.svg' ),
			),
			$args_localize
		);

		wp_localize_script( 'seopress-metabox', 'SEOPRESS_DATA', $args );
		wp_localize_script( 'seopress-metabox', 'SEOPRESS_I18N', seopress_get_service( 'I18nUniversalMetabox' )->getTranslations() );
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
		if ( ! in_array( $page, array( 'post.php', 'woocommerce_page_wc-admin' ), true ) ) {
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
}

<?php // phpcs:ignore

namespace SEOPress\Actions\Front\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * Title Meta
 */
class TitleMeta implements ExecuteHooksFrontend {

	/**
	 * The tags to string service.
	 *
	 * @var TagsToString
	 *
	 * @since 4.4.0
	 */
	protected $tags_to_string_service;

	/**
	 * The Title Meta constructor.
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->tags_to_string_service = seopress_get_service( 'TagsToString' );
	}

	/**
	 * The Title Meta hooks.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_head', array( $this, 'preLoad' ), 0 );
	}

	/**
	 * The Title Meta preLoad.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function preLoad() {
		if ( '1' !== seopress_get_toggle_option( 'titles' ) ) {
			return;
		}

		if ( ( function_exists( 'is_wpforo_page' ) && is_wpforo_page() ) || ( class_exists( 'Ecwid_Store_Page' ) && \Ecwid_Store_Page::is_store_page() ) ) {// disable on wpForo pages to avoid conflicts.
			return;
		}

		$priority = apply_filters( 'seopress_titles_the_title_priority', 10 );
		add_filter( 'pre_get_document_title', array( $this, 'render' ), $priority );

		// Avoid TEC rewriting our title tag on Venue and Organizer pages.
		if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
			if (
				function_exists( 'tribe_is_event' ) && tribe_is_event() ||
				function_exists( 'tribe_is_venue' ) && tribe_is_venue() ||
				function_exists( 'tribe_is_organizer' ) && tribe_is_organizer()
				// function_exists('tribe_is_month') && tribe_is_month() && is_tax() ||
				// function_exists('tribe_is_upcoming') && tribe_is_upcoming() && is_tax() ||
				// function_exists('tribe_is_past') && tribe_is_past() && is_tax() ||
				// function_exists('tribe_is_week') && tribe_is_week() && is_tax() ||
				// function_exists('tribe_is_day') && tribe_is_day() && is_tax() ||
				// function_exists('tribe_is_map') && tribe_is_map() && is_tax() ||
				// function_exists('tribe_is_photo') && tribe_is_photo() && is_tax() // phpcs:ignore -- TODO: check if we still need this.
			) {
				add_filter( 'pre_get_document_title', 'seopress_titles_the_title', 20 );
			}
		}

		// Avoid Surecart rewriting our title tag.
		if ( is_plugin_active( 'surecart/surecart.php' ) ) {
			if ( is_singular( 'sc_product' ) ) {
				add_filter( 'pre_get_document_title', 'seopress_titles_the_title', 214748364 );
			}
		}
	}


	/**
	 * The Title Meta render.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	public function render() {
		$default_hook = function_exists( 'seopress_get_service' );

		if ( apply_filters( 'seopress_old_pre_get_document_title', true ) ) {
			return;
		}

		$context = seopress_get_service( 'ContextPage' )->getContext();

		$title = seopress_get_service( 'TitleMeta' )->getValue( $context );

		return $title;
	}
}

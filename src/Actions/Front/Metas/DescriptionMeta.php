<?php // phpcs:ignore

namespace SEOPress\Actions\Front\Metas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

/**
 * Description Meta
 */
class DescriptionMeta implements ExecuteHooksFrontend {

	/**
	 * The tags to string service.
	 *
	 * @var TagsToString
	 *
	 * @since 4.4.0
	 */
	protected $tags_to_string_service;

	/**
	 * The Description Meta constructor.
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->tags_to_string_service = seopress_get_service( 'TagsToString' );
	}

	/**
	 * The Description Meta hooks.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( apply_filters( 'seopress_old_wp_head_description', true ) ) {
			return;
		}
		add_action( 'wp_head', array( $this, 'preLoad' ), 0 );
	}

	/**
	 * The Description Meta preLoad.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function preLoad() {
		if (
			( function_exists( 'is_wpforo_page' ) && is_wpforo_page() )
			|| ( class_exists( 'Ecwid_Store_Page' ) && \Ecwid_Store_Page::is_store_page()
			)
		) {// disable on wpForo pages to avoid conflicts.
			return;
		}

		add_action( 'wp_head', array( $this, 'render' ), 1 );
	}

	/**
	 * The Description Meta render.
	 *
	 * @since 4.4.0
	 *
	 * @return void
	 */
	public function render() {
		$content = $this->getContent();

		if ( empty( $content ) ) {
			return;
		}

		$html  = '<meta name="description" content="' . $content . '">';
		$html .= "\n";
		echo $html; // phpcs:ignore -- TODO: escape properly.
	}

	/**
	 * The Description Meta getContent.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	protected function getContent() {
		$context = seopress_get_service( 'ContextPage' )->getContext();

		$description = seopress_get_service( 'DescriptionMeta' )->getValue( $context );

		return $description;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builder content
 *
 * Reads the text of a page whose layout a page builder keeps in post meta
 * while post_content stays empty, so that %%post_excerpt%% and
 * %%post_content%% still have something to describe the page with.
 *
 * Only the stored data is read. Rendering the builder output instead would run
 * element callbacks, enqueue assets and resolve dynamic data from wp_head and
 * from every row of the posts list.
 *
 * Bricks is supported out of the box. Other builders can supply their text
 * with the seopress_builder_content filter.
 *
 * @since 10.4.0
 */
class BuilderContent {

	/**
	 * Bricks elements holding page text, and the setting that stores it.
	 * Buttons, links, forms and the like are left out: their labels are not
	 * a description of the page.
	 *
	 * @var array
	 */
	const BRICKS_TEXT_ELEMENTS = array(
		'heading'    => 'text',
		'text'       => 'text',
		'text-basic' => 'text',
		'icon-box'   => 'content',
	);

	/**
	 * Get the builder text of a post, as raw HTML.
	 *
	 * Meant for a post whose post_content is empty: callers check that first,
	 * and strip and trim the result the way they do post_content.
	 *
	 * @since 10.4.0
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return string The text, or '' when there is none.
	 */
	public static function getText( $post_id ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return '';
		}

		/**
		 * Filter whether %%post_excerpt%% and %%post_content%% fall back to the
		 * page builder text when post_content is empty.
		 *
		 * @since 10.4.0
		 *
		 * @param bool $fallback Whether to read the builder text. Default true.
		 * @param int  $post_id  The post ID.
		 */
		if ( ! apply_filters( 'seopress_excerpt_builder_fallback', true, $post_id ) ) {
			return '';
		}

		$text = self::getBricksText( $post_id );

		/**
		 * Filter the page builder text of a post whose post_content is empty.
		 *
		 * Lets a builder SEOPress does not read natively supply its text. HTML
		 * and shortcodes are stripped afterwards.
		 *
		 * @since 10.4.0
		 *
		 * @param string $text    The text read from the builder data.
		 * @param int    $post_id The post ID.
		 */
		$text = apply_filters( 'seopress_builder_content', $text, $post_id );

		return is_string( $text ) ? $text : '';
	}

	/**
	 * Read the text of a Bricks page, in the order Bricks renders it.
	 *
	 * @since 10.4.0
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return string
	 */
	private static function getBricksText( $post_id ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		// Without Bricks the page renders post_content, so leftover meta from a
		// previous theme would describe text nobody sees.
		if ( ! defined( 'BRICKS_VERSION' ) && 'bricks' !== get_template() ) {
			return '';
		}

		if ( 'wordpress' === get_post_meta( $post_id, '_bricks_editor_mode', true ) ) {
			return '';
		}

		$elements = get_post_meta( $post_id, '_bricks_page_content_2', true );

		if ( ! is_array( $elements ) || empty( $elements ) ) {
			return '';
		}

		$by_id = array();
		$roots = array();
		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) || ! isset( $element['id'] ) || ! is_scalar( $element['id'] ) ) {
				continue;
			}

			$by_id[ (string) $element['id'] ] = $element;

			if ( empty( $element['parent'] ) ) {
				$roots[] = (string) $element['id'];
			}
		}

		// The array is not in render order once elements have been moved in
		// the builder: walk the tree, depth first, from the root elements.
		$texts   = array();
		$visited = array();
		$stack   = array_reverse( $roots );

		while ( ! empty( $stack ) ) {
			$id = array_pop( $stack );

			if ( isset( $visited[ $id ] ) || ! isset( $by_id[ $id ] ) ) {
				continue;
			}
			$visited[ $id ] = true;

			$element = $by_id[ $id ];
			$name    = isset( $element['name'] ) && is_string( $element['name'] ) ? $element['name'] : '';

			if ( isset( self::BRICKS_TEXT_ELEMENTS[ $name ] ) ) {
				$setting = self::BRICKS_TEXT_ELEMENTS[ $name ];

				if ( isset( $element['settings'][ $setting ] ) && is_string( $element['settings'][ $setting ] ) ) {
					$texts[] = self::removeBricksDynamicTags( $element['settings'][ $setting ] );
				}
			}

			if ( isset( $element['children'] ) && is_array( $element['children'] ) ) {
				foreach ( array_reverse( $element['children'] ) as $child ) {
					if ( is_scalar( $child ) ) {
						$stack[] = (string) $child;
					}
				}
			}
		}

		// One line per element, so a heading does not run into the next
		// paragraph once the tags are stripped.
		return trim( implode( "\n", array_filter( array_map( 'trim', $texts ) ) ) );
	}

	/**
	 * Remove Bricks dynamic data tags such as {post_title} or
	 * {acf_field:format}. They are only resolved when Bricks renders the
	 * element, and the raw tag means nothing in a description.
	 *
	 * @since 10.4.0
	 *
	 * @param string $text The element text.
	 *
	 * @return string
	 */
	private static function removeBricksDynamicTags( $text ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		if ( false === strpos( $text, '{' ) ) {
			return $text;
		}

		$result = preg_replace( '#\{[a-zA-Z_][a-zA-Z0-9_\-]{0,100}(?::[^{}]{0,200})?\}#', '', $text );

		return null === $result ? $text : $result;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes
 *
 * Turns shortcode-laden post content into the readable text an auto-generated
 * meta description needs, without ever executing a shortcode.
 *
 * WordPress core strip_shortcodes() cannot do this job on a page builder site.
 * It only knows the shortcodes registered at that very moment, and for an
 * enclosing shortcode it removes the tag *and* everything it wraps. On a
 * WPBakery, Visual Composer, Avada or Divi page, whose whole content sits
 * inside [vc_row] / [fusion_builder_container] / [et_pb_section], that gives
 * two different wrong answers: raw markup in the admin, where the builder has
 * not registered its shortcodes, and an empty description on the front end,
 * where it has.
 *
 * This helper removes the tags and keeps what they wrap, so both contexts
 * return the same readable text. Executing the shortcodes instead (the other
 * way to get the text back) is deliberately not done here: this code runs in
 * wp_head and in the posts list column, where running arbitrary shortcode
 * callbacks would clobber the global post, start sessions, fire HTTP requests
 * and recurse into SEOPress's own shortcodes.
 *
 * @since 10.3
 */
class Shortcodes {

	/**
	 * Shortcodes whose payload is not readable text: raw HTML/JS blocks,
	 * base64 module payloads, media wrappers. Both the tags and what they
	 * wrap are dropped, otherwise the description fills up with a base64 blob.
	 *
	 * @var array
	 */
	const OPAQUE_TAGS = array(
		'caption',
		'embed',
		'gallery',
		'playlist',
		'audio',
		'video',
		'vc_raw_html',
		'vc_raw_js',
		'et_pb_code',
		'et_pb_fullwidth_code',
		'fusion_code',
		'fusion_syntax_highlighter',
	);

	/**
	 * Longest tag name considered. Shortcode tags are short; a longer run of
	 * word characters between brackets is prose, not markup.
	 *
	 * @var int
	 */
	const MAX_TAG_LENGTH = 60;

	/**
	 * Longest attribute run considered, to keep the patterns bounded on
	 * pathological content.
	 *
	 * @var int
	 */
	const MAX_ATTRIBUTES_LENGTH = 5000;

	/**
	 * Remove shortcode tags from a string, keeping the text they wrap.
	 *
	 * @since 10.3
	 *
	 * @param string $content The raw content.
	 *
	 * @return string The content without shortcode tags.
	 */
	public static function stripTags( $content ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		if ( ! is_string( $content ) || '' === $content ) {
			return '';
		}

		// Fast path: most posts carry no bracket at all, and the passes below
		// are the only reason to walk the string. Cheaper than core
		// strip_shortcodes(), which builds a regex out of every registered tag
		// before it looks at the content.
		if ( false === strpos( $content, '[' ) ) {
			return $content;
		}

		/**
		 * Filter whether shortcode tags are stripped while keeping the text
		 * they wrap.
		 *
		 * Return false to fall back to WordPress core strip_shortcodes(),
		 * which drops an enclosing shortcode together with its content.
		 *
		 * @since 10.3
		 *
		 * @param bool   $strip_tags Whether to strip the tags only. Default true.
		 * @param string $content    The content being processed.
		 */
		if ( ! apply_filters( 'seopress_strip_shortcode_tags', true, $content ) ) {
			return strip_shortcodes( $content );
		}

		$content = self::removeOpaqueShortcodes( $content );

		return self::removeTags( $content );
	}

	/**
	 * Drop the shortcodes whose content is not readable text, payload included.
	 *
	 * @since 10.3
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	private static function removeOpaqueShortcodes( $content ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		/**
		 * Filter the shortcodes removed together with their payload.
		 *
		 * Values are tag names. Anything that is not a bare tag name is
		 * discarded, so a third party cannot smuggle anything through this
		 * filter into the matching below.
		 *
		 * @since 10.3
		 *
		 * @param array $tags Tag names.
		 */
		$tags = apply_filters( 'seopress_strip_shortcodes_opaque_tags', self::OPAQUE_TAGS );

		if ( ! is_array( $tags ) || empty( $tags ) ) {
			return $content;
		}

		$valid = array();
		foreach ( $tags as $tag ) {
			// A tag name and nothing else, whatever the filter returned.
			if ( is_string( $tag ) && 1 === preg_match( '/^[a-zA-Z0-9_\-]{1,' . self::MAX_TAG_LENGTH . '}$/', $tag ) ) {
				$valid[] = $tag;
			}
		}

		if ( empty( $valid ) ) {
			return $content;
		}

		// Which of them the content actually carries, in one scan. Asking
		// strpos() tag by tag walks the whole post once per tag, and on a
		// builder page that probe cost more than every other pass combined.
		if ( 0 === preg_match_all( '#\[(' . implode( '|', $valid ) . ')[\s\]/]#', $content, $matches ) ) {
			return $content;
		}

		foreach ( array_unique( $matches[1] ) as $tag ) {
			$content = self::removeEnclosed( $content, $tag );
		}

		return $content;
	}

	/**
	 * Remove every [tag ...]…[/tag] run, payload included, in a single left to
	 * right sweep.
	 *
	 * Written with strpos() rather than a pattern on purpose. The regex form
	 * of this, however carefully quantified, restarts a scan at every opening
	 * tag that turns out never to close, which costs the square of the content
	 * length: 83ms per call on an 85KB post built to trigger it, seconds on a
	 * large one, on every front-end view and every row of the posts list.
	 * Scanning forward only, and stopping for good once no closing tag is left
	 * to the right, makes the work linear.
	 *
	 * @since 10.3
	 *
	 * @param string $content The content.
	 * @param string $tag     The tag name, already validated.
	 *
	 * @return string
	 */
	private static function removeEnclosed( $content, $tag ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		$opening     = '[' . $tag;
		$opening_len = strlen( $opening );
		$closing     = '[/' . $tag . ']';
		$closing_len = strlen( $closing );

		$result = '';
		$copied = 0;
		$search = 0;

		while ( true ) {
			$open = strpos( $content, $opening, $search );
			if ( false === $open ) {
				break;
			}

			// A tag boundary has to follow the name, so that [video] does not
			// swallow [videopress].
			$boundary = isset( $content[ $open + $opening_len ] ) ? $content[ $open + $opening_len ] : '';
			if ( ']' !== $boundary && '/' !== $boundary && ' ' !== $boundary && "\t" !== $boundary && "\n" !== $boundary && "\r" !== $boundary ) {
				$search = $open + $opening_len;
				continue;
			}

			$open_end = strpos( $content, ']', $open + $opening_len );
			if ( false === $open_end ) {
				break;
			}

			// A self-closing tag owns no payload. Do not pair it with a later wrapper.
			if ( '/' === substr( rtrim( substr( $content, $open, $open_end - $open ) ), -1 ) ) {
				$search = $open_end + 1;
				continue;
			}

			$close = strpos( $content, $closing, $open_end + 1 );
			if ( false === $close ) {
				// Nothing closes this one, so nothing closes any opening tag
				// further right either.
				break;
			}

			$result .= substr( $content, $copied, $open - $copied ) . ' ';
			$copied  = $close + $closing_len;
			$search  = $copied;
		}

		if ( 0 === $copied ) {
			return $content;
		}

		return $result . substr( $content, $copied );
	}

	/**
	 * Replace every shortcode-shaped tag with a space.
	 *
	 * @since 10.3
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	private static function removeTags( $content ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		$tag  = '[a-zA-Z0-9_\-]{1,' . self::MAX_TAG_LENGTH . '}';
		$attr = '[^\[\]]{0,' . self::MAX_ATTRIBUTES_LENGTH . '}';

		// Square brackets are ordinary punctuation ("see [1]", "[voir page
		// 12]"), so a bare word between brackets is left alone. Only these four
		// shapes are markup. One alternation, one pass, no PHP callback: on a
		// builder page carrying a few hundred tags the per-tag callback cost
		// dominated everything else.
		$pattern = '#'
			// [/vc_row] closes something: unambiguous.
			. '\[/' . $tag . '\]'
			// Namespaced tag: vc_row, et_pb_section, fusion_builder_container.
			. '|\[[a-zA-Z0-9]{1,' . self::MAX_TAG_LENGTH . '}[_\-]' . $tag . $attr . '\]'
			// [gallery ids="1,2"]: an attribute assignment is a signature.
			. '|\[' . $tag . '[^\[\]=]{0,' . self::MAX_ATTRIBUTES_LENGTH . '}=' . $attr . '\]'
			// [foo /] is self-closing markup.
			. '|\[' . $tag . $attr . '/\s*\]'
			. '#';

		// A closing tag also identifies an unregistered bare opening tag.
		// Collect these before removing closers, without scanning for each opener.
		preg_match_all( '#\[/([a-zA-Z0-9]{1,' . self::MAX_TAG_LENGTH . '})\]#', $content, $closed );
		$content = self::safeReplace( $pattern, ' ', $content );

		return self::removeRegisteredBareTags( $content, $closed[1] );
	}

	/**
	 * Remove what the shape rules leave behind: a registered shortcode used
	 * bare, with no attribute and no namespace, such as [gallery] or [recipe].
	 *
	 * A closing tag identifies unregistered wrappers too. Otherwise the
	 * alternation uses the registry, the way core strip_shortcodes() does.
	 *
	 * @since 10.3
	 *
	 * @param string $content     The content.
	 * @param array  $closed_tags Bare tag names identified by closing tags.
	 *
	 * @return string
	 */
	private static function removeRegisteredBareTags( $content, $closed_tags = array() ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		global $shortcode_tags;

		$registered = is_array( $shortcode_tags ) ? array_keys( $shortcode_tags ) : array();
		$bare       = array();
		foreach ( array_unique( array_merge( $registered, $closed_tags ) ) as $tag ) {
			// Namespaced and attributed forms are already gone, and rejecting
			// everything else keeps the alternation free of metacharacters.
			if ( is_string( $tag ) && 1 === preg_match( '/^[a-zA-Z0-9]{1,' . self::MAX_TAG_LENGTH . '}$/', $tag ) ) {
				$bare[] = $tag;
			}
		}

		if ( empty( $bare ) ) {
			return $content;
		}

		return self::safeReplace( '#\[(?:' . implode( '|', $bare ) . ')\]#', ' ', $content );
	}

	/**
	 * Run preg_replace(), never returning null.
	 *
	 * @since 10.3
	 *
	 * @param string $pattern     The pattern.
	 * @param string $replacement The replacement.
	 * @param string $subject     The subject.
	 *
	 * @return string
	 */
	private static function safeReplace( $pattern, $replacement, $subject ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- camelCase, consistent with the other helpers in src/.
		$result = preg_replace( $pattern, $replacement, $subject );

		return null === $result ? $subject : $result;
	}
}

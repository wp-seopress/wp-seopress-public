<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Compose\UseTags;

/**
 * TagsToString
 */
class TagsToString {
	use UseTags;

	/**
	 * The REGEX constant.
	 *
	 * @var string
	 */
	const REGEX = '#\%\%(.*?)\%\%#';

	/**
	 * The getExcerptLengthForTags function.
	 *
	 * @since 4.4.0
	 *
	 * @return int
	 */
	public function getExcerptLengthForTags() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return apply_filters( 'seopress_excerpt_length', 50 );
	}

	/**
	 * The getTags function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $string The string.
	 *
	 * @return array
	 */
	public function getTags( $string ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! is_string( $string ) ) {
			return array();
		}

		preg_match_all( self::REGEX, $string, $matches );

		return $matches;
	}

	/**
	 * The getValueFromTag function.
	 *
	 * @since 4.4.0
	 *
	 * @param function $tag The tag.
	 * @param array    $context The context.
	 *
	 * @return mixed
	 */
	public function getValueFromTag( $tag, $context = [] ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		// Resolve the tag to its handler. getTagClass() matches exact tags,
		// aliases and custom-format tags (_cf_<field>, _ct_<slug>), and returns
		// null for anything else, so an arbitrary method name is never dispatched.
		// Calling the resolved handler directly (instead of
		// call_user_func_array( array( $this, $tag ), … )) also removes the
		// user-controlled method invocation entirely. A bare whitelist on
		// getTagsAvailable() keys could not be used here because custom-format
		// tags are registered only under their placeholder name
		// (_cf_your_custom_field_name), never the actual field-based tag.
		$tag_class = $this->getTagClass( $tag );
		if ( null === $tag_class ) {
			return '';
		}

		// Tag classes read the context keys directly, so they must never be
		// handed a partial one. Callers legitimately have nothing to give
		// (PrintLocalBusiness prints its schema outside of any post or term),
		// and this is the single point where a context reaches a tag class.
		$context_service = seopress_get_service( 'ContextPage' );
		if ( is_object( $context_service ) && method_exists( $context_service, 'normalizeContext' ) ) {
			$context = $context_service->normalizeContext( $context );
		}

		// 0 === 'context'
		// 1 === 'tag'
		return $tag_class->getValue(
			array(
				0 => $context,
				1 => $tag,
			)
		);
	}

	/**
	 * The hard ceiling on the number of resolution passes.
	 *
	 * A caller cannot ask for more, whatever it passes in $options['passes'].
	 * Each extra pass widens what gets scanned, so this stays deliberately low.
	 *
	 * @var int
	 */
	const MAX_PASSES = 2;

	/**
	 * The replace function.
	 *
	 * Single pass by default, which is the historical behaviour and the safe
	 * one: the string handed over is ours (a title template), and its tags
	 * resolve to values that are never looked at again.
	 *
	 * A caller can ask for a second pass with $options['passes'], and only the
	 * schema generator does. It is needed there because a schema field does not
	 * reach the JSON as data but as the value of a tag: organization.json holds
	 * [[name]], which becomes %%social_knowledge_name%%, which resolves to what
	 * an administrator typed in the settings — tags included. The second pass is
	 * what resolves those.
	 *
	 * MAX_PASSES stops at two on purpose, and this is a security boundary rather
	 * than a performance one. Pass 1 resolves our own template. Pass 2 resolves
	 * what an administrator typed. A third pass would resolve tags found inside
	 * the values themselves — post titles, custom fields, term names — so anyone
	 * able to write a post title could make a tag resolve, and read post meta
	 * they cannot otherwise see, straight into the title of the page. Content is
	 * data, never a template.
	 *
	 * @since 4.4.0
	 *
	 * @param string $string The string.
	 * @param mixed  $context The context.
	 * @param array  $options The options. 'passes' (int, default 1, capped at MAX_PASSES).
	 *
	 * @return string
	 */
	public function replace( $string, $context = [], $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$passes = isset( $options['passes'] ) ? (int) $options['passes'] : 1;
		$passes = max( 1, min( $passes, self::MAX_PASSES ) );

		// The first pass strips the tags it does not know: what it receives is
		// our own template, so an unrecognised tag there is a mistake to clean.
		$string = $this->replacePass( $string, $context, true );

		for ( $pass = 1; $pass < $passes; $pass++ ) {
			$previous = $string;

			// Later passes run on values, not on our templates. An unknown
			// %%...%% there is text somebody wrote, so it is left alone rather
			// than deleted.
			$string = $this->replacePass( $string, $context, false );

			if ( $string === $previous ) {
				break;
			}
		}

		return $string;
	}

	/**
	 * The replacePass function.
	 *
	 * One resolution pass over the tags found in the string.
	 *
	 * @since 10.1.1
	 *
	 * @param string $string The string.
	 * @param mixed  $context The context.
	 * @param bool   $remove_unknown Whether an unrecognised tag is stripped or left as text.
	 *
	 * @return string
	 */
	protected function replacePass( $string, $context, $remove_unknown ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$tags = $this->getTags( $string );

		if ( ! array_key_exists( 1, $tags ) ) {
			return $string;
		}

		foreach ( $tags[1] as $key => $tag ) {
			if ( ! $remove_unknown && null === $this->getTagClass( $tag ) ) {
				continue;
			}

			$value = $this->getValueFromTag( $tag, $context );
			if ( ! $value ) {
				$string = str_replace( $tags[0][ $key ], '', $string );
			} else {
				$string = str_replace( $tags[0][ $key ], $value, $string );
			}
		}

		return $string;
	}

	/**
	 * The removeDataEmpty function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 *
	 * @return array
	 */
	protected function removeDataEmpty( $data ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return array_filter(
			$data,
			function ( $value ) {
				return '0' === $value || ! empty( $value );
			}
		);
	}

	/**
	 * The replaceDataToString function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 * @param array $context The context.
	 * @param mixed $options The options.
	 *
	 * @return array
	 */
	public function replaceDataToString( $data, $context = array(), $options = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[ $key ] = $this->replaceDataToString( $value, $context, $options );
			} else {
				$data[ $key ] = $this->replace( $value, $context, $options );
			}
		}

		if ( isset( $options['remove_empty'] ) && $options['remove_empty'] ) {
			$data = $this->removeDataEmpty( $data );
		}

		return $data;
	}
}

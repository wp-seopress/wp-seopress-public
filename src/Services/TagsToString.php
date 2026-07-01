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
	 * The replace function.
	 *
	 * @since 4.4.0
	 *
	 * @param string $string The string.
	 * @param mixed  $context The context.
	 *
	 * @return string
	 */
	public function replace( $string, $context = [] ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$tags = $this->getTags( $string );

		if ( ! array_key_exists( 1, $tags ) ) {
			return $string;
		}

		$tags_available = $this->getTagsAvailable();

		foreach ( $tags[1] as $key => $tag ) {
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
				$data[ $key ] = $this->replace( $value, $context );
			}
		}

		if ( isset( $options['remove_empty'] ) && $options['remove_empty'] ) {
			$data = $this->removeDataEmpty( $data );
		}

		return $data;
	}
}

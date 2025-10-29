<?php // phpcs:ignore

namespace SEOPress\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * VariablesToString
 */
class VariablesToString {

	/**
	 * The REGEX constant.
	 *
	 * @var string
	 */
	const REGEX = '#\[\[(.*?)\]\]#';

	/**
	 * The getVariables function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $string The string.
	 *
	 * @return array
	 */
	public function getVariables( $string ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! is_string( $string ) ) {
			return array();
		}

		preg_match_all( self::REGEX, $string, $matches );

		return $matches;
	}

	/**
	 * The getValueFromContext function.
	 *
	 * @since 4.5.0
	 *
	 * @param function $variable The variable.
	 * @param array    $context The context.
	 *
	 * @return string
	 */
	public function getValueFromContext( $variable, $context = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( ! array_key_exists( $variable, $context ) ) {
			return '';
		}

		return $context[ $variable ];
	}

	/**
	 * The replace function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $string The string.
	 * @param mixed  $context The context.
	 *
	 * @return string
	 */
	public function replace( $string, $context = array() ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$variables = $this->getVariables( $string );

		if ( ! array_key_exists( 1, $variables ) ) {
			return $string;
		}

		foreach ( $variables[1] as $key => $variable ) {
			$value = $this->getValueFromContext( $variable, $context );

			$string = str_replace( $variables[0][ $key ], $value, $string );
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

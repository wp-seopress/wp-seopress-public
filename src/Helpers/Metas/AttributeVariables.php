<?php // phpcs:ignore

namespace SEOPress\Helpers\Metas;

defined( 'ABSPATH' ) || exit;

/**
 * Dynamic variables substituted inside an attribute that is already escaped.
 *
 * The Open Graph and Twitter title and description hooks build their tag with
 * the resolved SEO value escaped into the content attribute, then run the
 * legacy replacement arrays over the whole tag again. A variable can still be
 * there at that point: a display name holding %%archive_title%% survives the
 * first pass, because that variable expands to the display name again. Most
 * replacement values are raw (archive title, term names, descriptions), so
 * the second pass could close the attribute and add new ones.
 *
 * @since 10.4.0
 */
abstract class AttributeVariables {

	/**
	 * Replace the dynamic variables of an attribute value or a tag already escaped.
	 *
	 * Each replacement value is escaped for an attribute first. esc_attr() leaves
	 * existing entities alone, so the values the builder escaped already are not
	 * encoded twice.
	 *
	 * @param string[] $variables The variables, e.g. %%archive_title%%.
	 * @param array    $values    Their values, in the same order.
	 * @param string   $subject   The escaped markup to replace them in.
	 *
	 * @return string
	 */
	public static function replace( $variables, $values, $subject ) {
		return str_replace( $variables, self::escape_values( $values ), $subject );
	}

	/**
	 * Escape every replacement value for an attribute.
	 *
	 * @param array $values Replacement values.
	 *
	 * @return string[]
	 */
	public static function escape_values( $values ) {
		if ( ! is_array( $values ) ) {
			return array();
		}

		return array_map(
			static function ( $value ) {
				return is_scalar( $value ) || null === $value ? esc_attr( (string) $value ) : '';
			},
			$values
		);
	}
}

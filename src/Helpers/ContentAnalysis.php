<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ContentAnalysis
 */
abstract class ContentAnalysis {

	/**
	 * Typographic characters WordPress substitutes for the plain ASCII ones a
	 * user types, mapped back onto their ASCII form.
	 *
	 * Dashes are deliberately left out: the hyphen belongs to the word boundary
	 * class used by the keyword regexp, so folding an en or em dash onto it
	 * would change where a keyword is allowed to start and end.
	 *
	 * @var array
	 */
	const TYPOGRAPHIC_CHARS = array(
		// Apostrophes and single quotes.
		"\u{2018}" => "'", // Left single quotation mark.
		"\u{2019}" => "'", // Right single quotation mark, what wptexturize() produces.
		"\u{201A}" => "'", // Single low-9 quotation mark.
		"\u{201B}" => "'", // Single high-reversed-9 quotation mark.
		"\u{2032}" => "'", // Prime.
		"\u{2035}" => "'", // Reversed prime.
		"\u{2039}" => "'", // Single left-pointing angle quotation mark.
		"\u{203A}" => "'", // Single right-pointing angle quotation mark.
		"\u{02B9}" => "'", // Modifier letter prime.
		"\u{02BC}" => "'", // Modifier letter apostrophe.
		"\u{00B4}" => "'", // Acute accent, often typed as an apostrophe.
		'`'        => "'", // Grave accent, same.

		// Double quotes.
		"\u{201C}" => '"', // Left double quotation mark.
		"\u{201D}" => '"', // Right double quotation mark.
		"\u{201E}" => '"', // Double low-9 quotation mark.
		"\u{201F}" => '"', // Double high-reversed-9 quotation mark.
		"\u{2033}" => '"', // Double prime.
		"\u{2036}" => '"', // Reversed double prime.
		"\u{00AB}" => '"', // Left guillemet, what the French locale produces.
		"\u{00BB}" => '"', // Right guillemet, same.

		// Spaces.
		"\u{00A0}" => ' ', // No-break space.
		"\u{202F}" => ' ', // Narrow no-break space, inserted before French punctuation.
		"\u{2007}" => ' ', // Figure space.
		"\u{2009}" => ' ', // Thin space.

		// Ellipsis.
		"\u{2026}" => '...',
	);

	/**
	 * Fold the typographic characters WordPress substitutes at render time back
	 * onto their plain ASCII form, so a target keyword and the content it is
	 * looked for in can be compared literally.
	 *
	 * WordPress rewrites what the user typed on the way out: wptexturize() turns
	 * a straight apostrophe into a right single quotation mark, straight quotes
	 * into curly quotes (or guillemets in French), and the space before some
	 * punctuation into a non-breaking one. The target keyword field goes through
	 * none of this, so the two sides can never compare equal and the analysis
	 * reports a keyword as missing while it is plainly on the page. Folding both
	 * sides makes the apostrophe irrelevant to the comparison, the same way
	 * remove_accents() already makes "é" and "e" equivalent.
	 *
	 * @since 10.2.0
	 *
	 * @param string $text The text to normalize.
	 *
	 * @return string The normalized text, or an empty string when $text is not one.
	 */
	public static function normalizeTypography( $text ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		if ( ! is_string( $text ) || '' === $text ) {
			return '';
		}

		// Resolve entities first (&#8217;, &rsquo;, &nbsp;...) so the map below
		// sees real characters whatever encoding the source text arrived in.
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		return strtr( $text, self::TYPOGRAPHIC_CHARS );
	}

	/**
	 * Lowercase a string without breaking the UTF-8 sequences it carries.
	 *
	 * PHP's strtolower() works one byte at a time and, before PHP 8.2, follows
	 * the LC_CTYPE locale: on a server whose locale maps the high byte range it
	 * turns the 0xC3 lead byte of every Latin-1 supplement character into
	 * 0xE3, so "Müller" comes out as an invalid UTF-8 sequence. remove_accents()
	 * then refuses to touch it (it returns anything that is not valid UTF-8
	 * unchanged), and the mangled keyword can no longer match the content it
	 * was typed for. mb_strtolower() understands the encoding and leaves the
	 * sequence intact.
	 *
	 * @since 10.2.0
	 *
	 * @param string $text The text to lowercase.
	 *
	 * @return string The lowercased text, or an empty string when $text is not one.
	 */
	public static function toLowercase( $text ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		if ( ! is_string( $text ) || '' === $text ) {
			return '';
		}

		if ( function_exists( 'mb_strtolower' ) ) {
			return mb_strtolower( $text, 'UTF-8' );
		}

		// Without mbstring, only fold the ASCII range: byte-wise lowercasing
		// would corrupt the very characters this method exists to protect.
		return strtr( $text, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz' );
	}

	/**
	 * Build the slug readings of a set of target keywords, to be matched
	 * against a permalink whose dashes have already been turned into spaces.
	 *
	 * Two things stand between a keyword and the slug built from it. First,
	 * sanitize_title() joins words with dashes, so a keyword of more than one
	 * word could never line up with a permalink normalized to spaces. Second,
	 * it drops the apostrophe entirely ("l'abeille" becomes "labeille") while
	 * hand-written and imported slugs usually keep the two words apart
	 * ("l-abeille"), so both readings have to be offered.
	 *
	 * Variants identical to the keyword they come from are left out: the
	 * caller has already tried those against the permalink.
	 *
	 * @since 10.2.0
	 *
	 * @param array $keywords The target keywords.
	 *
	 * @return array The slug variants, deduplicated.
	 */
	public static function getSlugVariants( $keywords ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		$variants = array();

		if ( empty( $keywords ) || ! is_array( $keywords ) ) {
			return $variants;
		}

		foreach ( $keywords as $kw ) {
			if ( ! is_string( $kw ) || '' === trim( $kw ) ) {
				continue;
			}

			$readings = array(
				$kw,
				// The apostrophe as a word separator rather than as a character
				// to drop, which is how most slugs carrying one were written.
				str_replace( "'", ' ', self::normalizeTypography( $kw ) ),
			);

			foreach ( $readings as $reading ) {
				$variant = str_replace( '-', ' ', sanitize_title( $reading ) );

				if ( '' !== $variant && $variant !== $kw ) {
					$variants[] = $variant;
				}
			}
		}

		return array_values( array_unique( $variants ) );
	}

	/**
	 * The getData function.
	 *
	 * @return array
	 */
	public static function getData() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = array(
			'all_canonical'      => array(
				'title'  => __( 'Canonical URL', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'schemas'            => array(
				'title'  => __( 'Structured data types', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'old_post'           => array(
				'title'  => __( 'Last modified date', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'keywords_permalink' => array(
				'title'  => __( 'Keywords in permalink', 'wp-seopress' ),
				'impact' => null,
				'desc'   => null,
			),
			'headings'           => array(
				'title'  => __( 'Headings', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'meta_title'         => array(
				'title'  => __( 'Meta title', 'wp-seopress' ),
				'impact' => null,
				'desc'   => null,
			),
			'meta_desc'          => array(
				'title'  => __( 'Meta description', 'wp-seopress' ),
				'impact' => null,
				'desc'   => null,
			),
			'social'             => array(
				'title'  => __( 'Social meta tags', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'robots'             => array(
				'title'  => __( 'Meta robots', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'img_alt'            => array(
				'title'  => __( 'Alternative texts of images', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'nofollow_links'     => array(
				'title'  => __( 'NoFollow Links', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'outbound_links'     => array(
				'title'  => __( 'Outbound Links', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'internal_links'     => array(
				'title'  => __( 'Internal Links', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'content_depth'      => array(
				'title'  => __( 'Content depth', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'heading_hierarchy'  => array(
				'title'  => __( 'Heading structure', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'content_media'      => array(
				'title'  => __( 'Media in content', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
			'content_structure'  => array(
				'title'  => __( 'Content readability', 'wp-seopress' ),
				'impact' => 'good',
				'desc'   => null,
			),
		);

		return apply_filters( 'seopress_get_content_analysis_data', $data );
	}
}

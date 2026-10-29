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
	 * The slug the keyword-in-permalink check reads, dashes turned into spaces.
	 *
	 * The editor and the server both read it from here. The server used to
	 * read the whole URL, so a keyword found in the domain or the category
	 * base passed on the server and failed in the editor.
	 *
	 * @since 10.4.0
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return string The slug.
	 */
	public static function getPermalinkSlug( $post ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		$slug = urldecode( $post->post_name );

		if ( is_plugin_active( 'permalink-manager-pro/permalink-manager.php' ) ) {
			global $permalink_manager_uris;
			if ( ! empty( $permalink_manager_uris ) ) {
				$slug = isset( $permalink_manager_uris[ $post->ID ] ) ? urldecode( $permalink_manager_uris[ $post->ID ] ) : '';
			}
		}

		return str_replace( '-', ' ', $slug );
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
	 * Impacts a check reports when the page satisfies it. 'low' belongs here
	 * because every surface already treats it as a pass: the posts list column
	 * only flips to "Should be improved" on 'medium' or 'high', and the editor
	 * aggregate ignores 'good' and 'low' alike.
	 *
	 * @var array
	 */
	const PASSING_IMPACTS = array( 'good', 'low' );

	/**
	 * Impacts a check reports when the page fails it, using the same partition
	 * as PASSING_IMPACTS so the two lists together describe the whole scale.
	 *
	 * @var array
	 */
	const FAILING_IMPACTS = array( 'medium', 'high' );

	/**
	 * Count how many of the checks that actually ran are satisfied.
	 *
	 * The analysis already produces a verdict per check; the aggregate then
	 * collapses all of them into "Good" or "Should be improved", so a page
	 * failing one check looks exactly like a page failing eight. This counts
	 * the individual verdicts instead, without weighting anything: the figure
	 * is the checklist the user is looking at, not a new scoring model.
	 *
	 * A check only counts when it reported an impact on the known scale. One
	 * left at null was not evaluated (a single-check run, or a third-party
	 * entry added through `seopress_get_content_analysis_data` that nothing
	 * filled in), and counting it either way would be a verdict nobody gave.
	 *
	 * Because the pass/fail partition is the one every surface already uses,
	 * `passed === total` holds exactly when the aggregate verdict is "Good",
	 * so the headline figure can never contradict the list underneath it.
	 *
	 * Checks the user ignored are left out of both counts, the way the editor
	 * leaves them out, so the column and the editor give the same ratio.
	 *
	 * @since 10.4.0
	 *
	 * @param array    $analyzes Either the analyzes array (check key => array
	 *                           with an `impact` entry) or the flat impact map
	 *                           (check key => impact) stored in the score column.
	 * @param string[] $ignored  Check keys to leave out. See getIgnoredChecks().
	 *
	 * @return array{passed: int, failed: int, total: int}
	 */
	public static function getScoreSummary( $analyzes, array $ignored = array() ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		$summary = array(
			'passed' => 0,
			'failed' => 0,
			'total'  => 0,
		);

		if ( ! is_array( $analyzes ) ) {
			return $summary;
		}

		foreach ( $analyzes as $key => $analyze ) {
			// A numeric key means the impacts lost their check identity on the
			// way in (the deduplicated list every version up to 10.3.x stored
			// in the score column), so nothing can be counted per check.
			if ( ! is_string( $key ) ) {
				continue;
			}

			if ( in_array( $key, $ignored, true ) ) {
				continue;
			}

			if ( is_array( $analyze ) ) {
				$impact = self::isEvaluated( $analyze ) && isset( $analyze['impact'] ) ? $analyze['impact'] : null;
			} else {
				$impact = $analyze;
			}

			if ( ! is_string( $impact ) ) {
				continue;
			}

			if ( in_array( $impact, self::PASSING_IMPACTS, true ) ) {
				++$summary['passed'];
				++$summary['total'];
			} elseif ( in_array( $impact, self::FAILING_IMPACTS, true ) ) {
				++$summary['failed'];
				++$summary['total'];
			}
		}

		return $summary;
	}

	/**
	 * The verdict of a stored score, the one the ring and the toolbar show.
	 *
	 * With the per-check detail it is read from the counts, so it cannot
	 * contradict the ratio, and a post whose every check is ignored is good,
	 * as in the editor and the abilities. A row stored by 10.3.x or earlier
	 * only kept its distinct impacts and is read from them.
	 *
	 * @since 10.4.0
	 *
	 * @param array $score   The stored score.
	 * @param array $summary Its getScoreSummary(), ignored checks left out.
	 *
	 * @return bool
	 */
	public static function isGoodVerdict( $score, array $summary ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		if ( ! is_array( $score ) ) {
			return false;
		}

		foreach ( array_keys( $score ) as $key ) {
			if ( is_string( $key ) ) {
				return empty( $summary['failed'] );
			}
		}

		return ! in_array( 'medium', $score, true ) && ! in_array( 'high', $score, true );
	}

	/**
	 * Whether a check of the analyzes array actually ran.
	 *
	 * The content checks are skipped on a page builder page whose post content
	 * is too thin to read: they report 'good' so nothing alerts, and
	 * `evaluated => false` so nobody counts them as passed.
	 *
	 * @since 10.4.0
	 *
	 * @param array $analyze One entry of the analyzes array.
	 *
	 * @return bool
	 */
	public static function isEvaluated( $analyze ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		return ! is_array( $analyze ) || ! isset( $analyze['evaluated'] ) || false !== $analyze['evaluated'];
	}

	/**
	 * The impact of every check, keyed by check, as stored in the score column.
	 *
	 * A check that did not run is stored as null rather than 'good', so the
	 * posts list column leaves it out of its count exactly like the editor
	 * does. The aggregate verdict only looks for 'medium' and 'high', so it
	 * reads the same as before.
	 *
	 * @since 10.4.0
	 *
	 * @param array $analyzes The analyzes array.
	 *
	 * @return array Check key => impact, or null when the check did not run.
	 */
	public static function getImpactMap( $analyzes ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		$map = array();

		foreach ( (array) $analyzes as $key => $analyze ) {
			$map[ $key ] = self::isEvaluated( $analyze ) && is_array( $analyze ) && isset( $analyze['impact'] ) ? $analyze['impact'] : null;
		}

		return $map;
	}

	/**
	 * The issue type a check is stored under in `seopress_seo_issues`.
	 *
	 * Most checks share their name with their issue type; these four do not.
	 * The editor keeps the same table in `ISSUE_TYPE_BY_KEY`.
	 *
	 * @var array
	 */
	const ISSUE_TYPE_BY_CHECK = array(
		'schemas'            => 'json_schemas',
		'keywords_permalink' => 'permalink',
		'meta_title'         => 'title',
		'meta_desc'          => 'description',
	);

	/**
	 * Ignored checks already read in this request, by post.
	 *
	 * @var array
	 */
	protected static $ignored_cache = array();

	/**
	 * Whether the PRO issues table exists, read once per request.
	 *
	 * @var bool|null
	 */
	protected static $issues_table_exists = null;

	/**
	 * Whether PRO, which owns the issues table and the ignore controls, is
	 * loaded. Read once per request; null until then.
	 *
	 * @var bool|null
	 */
	protected static $pro_loaded = null;

	/**
	 * Forget what getIgnoredChecks() read.
	 *
	 * Nothing in the plugin needs it within a request: the ignore state only
	 * changes through PRO's REST routes, and the next request starts empty.
	 * It exists so tests can change the issues table between two reads.
	 *
	 * @since 10.4.0
	 *
	 * @return void
	 */
	public static function flushIgnoredChecks() { // phpcs:ignore -- camelCase is the convention for this namespace.
		self::$ignored_cache       = array();
		self::$issues_table_exists = null;
		self::$pro_loaded          = null;
	}

	/**
	 * The checks of a post that the user ignored entirely.
	 *
	 * A check is ignored when every issue it raised is marked ignored, which is
	 * the editor's rule in `isCheckFullyIgnored()`. The rows live in the
	 * `seopress_seo_issues` table PRO creates, and the editor only learns about
	 * them through PRO, so without PRO loaded nothing is ignored and no query
	 * runs.
	 *
	 * @since 10.4.0
	 *
	 * @param int $post_id The post.
	 *
	 * @return string[] Check keys, as in getData().
	 */
	public static function getIgnoredChecks( $post_id ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		$post_id = (int) $post_id;

		if ( $post_id < 1 ) {
			return array();
		}

		$ignored = self::getIgnoredChecksForPostIds( array( $post_id ) );

		return $ignored[ $post_id ];
	}

	/**
	 * The ignored checks of several posts, read in one query.
	 *
	 * The posts list column and the site-wide ability show many posts at
	 * once; reading them one by one cost one query per row. Posts already read
	 * in this request are not queried again.
	 *
	 * @since 10.4.0
	 *
	 * @param int[] $post_ids The posts.
	 *
	 * @return array Post id => check keys.
	 */
	public static function getIgnoredChecksForPostIds( array $post_ids ) { // phpcs:ignore -- camelCase is the convention for this namespace.
		global $wpdb;

		$post_ids = array_values(
			array_unique(
				array_filter(
					array_map( 'intval', $post_ids ),
					static function ( $id ) {
						return $id > 0;
					}
				)
			)
		);

		$missing = array_values( array_diff( $post_ids, array_keys( self::$ignored_cache ) ) );

		if ( ! empty( $missing ) ) {
			$types_by_post = array_fill_keys( $missing, array() );

			if ( null === self::$pro_loaded ) {
				self::$pro_loaded = function_exists( 'seopress_pro_get_service' );
			}

			$table = $wpdb->prefix . 'seopress_seo_issues';

			if ( self::$pro_loaded && null === self::$issues_table_exists ) {
				// A query that matches nothing rather than SHOW TABLES, which does
				// not list temporary tables: this answers the same on a real site
				// and inside a test transaction.
				$suppressed                = $wpdb->suppress_errors( true );
				self::$issues_table_exists = null !== $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE 1 = 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name built from the prefix.
				$wpdb->suppress_errors( $suppressed );
			}

			if ( self::$pro_loaded && self::$issues_table_exists ) {
				$placeholders = implode( ', ', array_fill( 0, count( $missing ), '%d' ) );

				$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT post_id, issue_type, MIN( issue_ignore ) AS all_ignored FROM {$table} WHERE post_id IN ( {$placeholders} ) GROUP BY post_id, issue_type", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- table name built from the prefix, placeholders built above.
						$missing
					)
				);

				foreach ( (array) $rows as $row ) {
					if ( 1 === (int) $row->all_ignored && isset( $types_by_post[ (int) $row->post_id ] ) ) {
						$types_by_post[ (int) $row->post_id ][] = (string) $row->issue_type;
					}
				}
			}

			$checks = array_keys( self::getData() );

			foreach ( $types_by_post as $post_id => $fully_ignored ) {
				$ignored = array();

				foreach ( $checks as $check ) {
					$type = isset( self::ISSUE_TYPE_BY_CHECK[ $check ] ) ? self::ISSUE_TYPE_BY_CHECK[ $check ] : $check;

					if ( in_array( $type, $fully_ignored, true ) ) {
						$ignored[] = $check;
					}
				}

				/**
				 * Filter the checks of a post left out of the passed/total ratio.
				 *
				 * @since 10.4.0
				 *
				 * @param string[] $ignored Check keys.
				 * @param int      $post_id The post.
				 */
				$ignored = apply_filters( 'seopress_content_analysis_ignored_checks', $ignored, $post_id );

				self::$ignored_cache[ $post_id ] = is_array( $ignored ) ? array_values( $ignored ) : array();
			}
		}

		$result = array();

		foreach ( $post_ids as $post_id ) {
			$result[ $post_id ] = self::$ignored_cache[ $post_id ];
		}

		return $result;
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

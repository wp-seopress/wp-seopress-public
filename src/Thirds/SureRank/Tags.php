<?php // phpcs:ignore

namespace SEOPress\Thirds\SureRank;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Helpers\TagCompose;

/**
 * SureRank tags translator.
 *
 * Maps SureRank %token% syntax to the equivalent SEOPress %%token%% values.
 * Reference: surerank/inc/meta-variables/{site,post,term,user}.php and
 * surerank/inc/functions/variables.php.
 */
class Tags {
	/**
	 * SureRank → SEOPress variable map.
	 *
	 * Empty string means SureRank exposes the variable but SEOPress has no
	 * direct equivalent — the placeholder is stripped from the output.
	 *
	 * @var array
	 */
	protected $variables = array(
		// Site-level.
		'%separator%'          => 'sep',
		'%site_name%'          => 'sitetitle',
		'%site_url%'           => '',
		'%tagline%'            => 'tagline',
		'%page%'               => 'current_pagination',
		'%search_query%'       => 'search_keywords',
		'%currentdate%'        => 'currentdate',
		'%currentday%'         => 'currentday',
		'%currentmonth%'       => 'currentmonth',
		'%currentyear%'        => 'currentyear',
		'%currenttime%'        => 'currenttime',
		'%org_name%'           => '',
		'%org_logo%'           => '',
		'%org_url%'            => '',

		// Post-level.
		'%title%'              => 'post_title',
		'%excerpt%'            => 'post_excerpt',
		'%content%'            => 'post_content',
		'%permalink%'          => 'post_url',
		'%published%'          => 'post_date',
		'%modified%'           => 'post_modified_date',
		'%author_name%'        => 'post_author',
		'%archive_title%'      => 'archive_title',
		'%ID%'                 => '',

		// Term-level.
		'%term_title%'         => 'term_title',
		'%term_description%'   => 'term_description',
		'%slug%'               => '',

		// User-level.
		'%author_description%' => 'author_bio',
		'%author_nicename%'    => 'post_author',
	);

	/**
	 * Overrides applied when the template targets a term.
	 *
	 * SureRank rewrites those tokens at runtime on taxonomy archives
	 * (see Settings::replace_meta_variables), so the import has to do the same
	 * to keep the rendered output identical.
	 *
	 * @var array
	 */
	protected $term_variables = array(
		'%title%'     => 'term_title',
		'%excerpt%'   => 'term_description',
		'%content%'   => 'term_description',
		'%permalink%' => '',
	);

	/**
	 * Replace SureRank tokens with their SEOPress equivalents.
	 *
	 * @param string $input   Raw template string from SureRank.
	 * @param string $context 'post' (default) or 'term'.
	 *
	 * @return string
	 */
	public function replaceTags( $input, $context = 'post' ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! is_string( $input ) || '' === $input ) {
			return $input;
		}

		// SureRank exposes any meta key as %custom_field.the_key%.
		$input = preg_replace(
			'/%custom_field\.([A-Za-z0-9_\-]+)%/',
			TagCompose::getValueWithTag( '_cf_$1' ),
			$input
		);

		$variables = 'term' === $context
			? array_merge( $this->variables, $this->term_variables )
			: $this->variables;

		$replacements = array();
		foreach ( $variables as $key => $value ) {
			$replacements[ $key ] = empty( $value ) ? '' : TagCompose::getValueWithTag( $value );
		}

		// strtr() replaces every token in a single pass: unlike a str_replace()
		// loop it never rescans what it just inserted, so translating %title% to
		// %%term_title%% cannot be picked up again by the %term_title% rule.
		return strtr( $input, $replacements );
	}
}

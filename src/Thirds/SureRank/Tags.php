<?php // phpcs:ignore

namespace SEOPress\Thirds\SureRank;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Helpers\TagCompose;

/**
 * SureRank tags translator.
 *
 * Maps SureRank %token% syntax to the equivalent SEOPress %%token%% values.
 * Reference: surerank/inc/meta-variables/site.php and surerank/inc/functions/variables.php.
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
		'%separator%'      => 'sep',
		'%site_name%'      => 'sitetitle',
		'%tagline%'        => 'tagline',
		'%site_url%'       => '',
		'%page%'           => 'current_pagination',
		'%search_query%'   => 'search_keywords',
		'%currentdate%'    => 'currentdate',
		'%currentday%'     => 'currentday',
		'%currentmonth%'   => 'currentmonth',
		'%currentyear%'    => 'currentyear',
		'%currenttime%'    => 'currenttime',
		'%org_name%'       => '',
		'%org_logo%'       => '',
		'%org_url%'        => '',

		// Post-level.
		'%title%'          => 'post_title',
		'%content%'        => 'post_content',
		'%excerpt%'        => 'post_excerpt',
		'%author%'         => 'post_author',
		'%date%'           => 'post_date',

		// Term-level (`%title%` is reused; mapped above).
		'%description%'    => 'term_description',
		'%parent%'         => '',
	);

	/**
	 * Replace SureRank tokens with their SEOPress equivalents.
	 *
	 * @param string $input Raw template string from SureRank.
	 *
	 * @return string
	 */
	public function replaceTags( $input ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( ! is_string( $input ) || '' === $input ) {
			return $input;
		}

		foreach ( $this->variables as $key => $value ) {
			if ( ! empty( $value ) ) {
				$value = TagCompose::getValueWithTag( $value );
			}

			$input = str_replace( $key, $value, $input );
		}

		return $input;
	}
}

<?php // phpcs:ignore

namespace SEOPress\Thirds\RankMath;

defined( 'ABSPATH' ) || exit( 'Cheatin&#8217; uh?' );

use SEOPress\Helpers\TagCompose;

/**
 * Tags
 */
class Tags {
	/**
	 * Variables
	 *
	 * @var array
	 */
	protected $variables = array(
		'%sep%'                                       => 'sep',
		'%search_query%'                              => 'search_keywords',
		'%count(varname)%'                            => '',
		'%filename%'                                  => '',
		'%sitename%'                                  => 'sitetitle',
		'%sitedesc%'                                  => 'tagline',
		'%currentdate%'                               => 'currentdate',
		'%currentday%'                                => 'currentday',
		'%currentmonth%'                              => 'currentmonth',
		'%currentyear%'                               => 'currentyear',
		'%currenttime%'                               => 'currenttime',
		'%currenttime(F jS, Y)%'                      => 'currenttime',
		'%org_name%'                                  => '',
		'%org_logo%'                                  => '',
		'%title%'                                     => 'post_title',
		'%parent_title%'                              => '',
		'%excerpt%'                                   => 'post_excerpt',
		'%excerpt_only%'                              => 'post_excerpt',
		'%seo_title%'                                 => '',
		'%seo_description%'                           => '',
		'%url%'                                       => 'post_url',
		'%post_thumbnail%'                            => 'post_thumbnail_url',
		'%date%'                                      => 'post_date',
		'%modified%'                                  => 'post_modified_date',
		'%date(F jS, Y)%'                             => 'post_date',
		'%modified(F jS, Y)%'                         => 'post_modified_date',
		'%category%'                                  => 'post_category',
		'%categories%'                                => '',
		'%categories(limit=3&separator= | &exclude=12,23)%' => '',
		'%tag%'                                       => 'post_tag',
		'%tags%'                                      => '',
		'%tags(limit=3&separator= | &exclude=12,23)%' => '',
		'%term%'                                      => 'term_title',
		'%term_description%'                          => 'term_description',
		'%customterm(taxonomy-name)%'                 => '',
		'%customterm_desc(taxonomy-name)%'            => '',
		'%userid%'                                    => '',
		'%name%'                                      => 'post_author',
		'%user_description%'                          => '',
		'%id%'                                        => '',
		'%focuskw%'                                   => 'target_keyword',
		'%customfield(field-name)%'                   => '',
		'%page%'                                      => 'page',
		'%pagenumber%'                                => 'current_pagination',
		'%pagetotal%'                                 => '',
		'%pt_single%'                                 => '',
		'%pt_plural%'                                 => 'cpt_plural',
	);

	/**
	 * Replace tags
	 *
	 * @param string $input Input string.
	 * @return string
	 */
	public function replaceTags( $input ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		foreach ( $this->variables as $key => $value ) {
			if ( ! empty( $value ) ) {
				$value = TagCompose::getValueWithTag( $value );
			}

			$input = str_replace( $key, $value, $input );
		}

		return $input;
	}
}

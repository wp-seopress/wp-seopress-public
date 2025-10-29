<?php // phpcs:ignore

namespace SEOPress\Thirds\AIO;

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
		'#separator_sa'         => 'sep',
		'#site_title'           => 'sitetitle',
		'#tagline'              => 'tagline',
		'#post_title'           => 'post_title',
		'#author_first_name'    => 'author_first_name',
		'#author_last_name'     => 'author_last_name',
		'#author_name'          => 'post_author',
		'#taxonomy_title'       => 'term_title',
		'#current_date'         => 'currentdate',
		'#current_month'        => 'currentmonth',
		'#current_day'          => 'currentday',
		'#current_year'         => 'currentyear',
		'#permalink'            => 'post_url',
		'#post_content'         => 'post_content',
		'#post_excerpt_only'    => 'post_excerpt',
		'#post_excerpt'         => 'post_excerpt',
		'#post_date'            => 'post_date',
		'#post_day'             => '',
		'#post_month'           => '',
		'#post_year'            => '',
		'#custom_field-'        => '_cf_',
		'#tax_name-'            => '_ct_',
		'#categories'           => 'post_category',
		'#taxonomy_description' => 'term_description',
		'#search_term'          => 'search_keywords',
		'#page_number'          => 'current_pagination',
		'#archive_title'        => 'archive_title',
		'#archive_date'         => 'archive_date',
		'#woocommerce_price'    => 'wc_single_price',
		'#woocommerce_sku'      => 'wc_sku',
		'#author_link'          => 'author_website',
		'#author_bio'           => 'author_bio',
		'#site_link'            => 'sitetitle',
		'#post_link'            => 'post_url',
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
				if ( '#custom_field-' === $key ) {
					preg_match_all( '/#custom_field-[^\s]*/', $input, $matches );

					if ( ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $_key => $_value ) {
							$new_tag = str_replace( '#custom_field-', '_cf_', $_value );
							$new_tag = TagCompose::getValueWithTag( $new_tag );
							$input   = str_replace( $_value, $new_tag, $input );
						}
					}
				} elseif ( '#tax_name-' === $key ) {
					preg_match_all( '/#tax_name-[^\s]*/', $input, $matches );

					if ( ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $_key => $_value ) {
							$new_tag = str_replace( '#tax_name-', '_ct_', $_value );
							$new_tag = TagCompose::getValueWithTag( $new_tag );
							$input   = str_replace( $_value, $new_tag, $input );
						}
					}
				} else {
					$value = TagCompose::getValueWithTag( $value );
				}
			}
			if ( '#custom_field-' !== $key && '#tax_name-' !== $key ) {
				$input = str_replace( $key, $value, $input );
			}
		}

		return $input;
	}
}

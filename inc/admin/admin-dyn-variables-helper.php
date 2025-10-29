<?php
/**
 * Dynamic variables helper
 *
 * @package SEOPress
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Get dynamic variables
 *
 * @return array Dynamic variables
 */
function seopress_get_dyn_variables() {
	return apply_filters(
		'seopress_get_dynamic_variables',
		array(
			'%%sep%%'                           => esc_html__( 'Separator', 'wp-seopress' ),
			'%%sitetitle%%'                     => esc_html__( 'Site Title', 'wp-seopress' ),
			'%%tagline%%'                       => esc_html__( 'Tagline', 'wp-seopress' ),
			'%%post_title%%'                    => esc_html__( 'Post Title', 'wp-seopress' ),
			'%%post_excerpt%%'                  => esc_html__( 'Post excerpt', 'wp-seopress' ),
			'%%post_content%%'                  => esc_html__( 'Post content / product description', 'wp-seopress' ),
			'%%post_thumbnail_url%%'            => esc_html__( 'Post thumbnail URL', 'wp-seopress' ),
			'%%post_url%%'                      => esc_html__( 'Post URL', 'wp-seopress' ),
			'%%post_date%%'                     => esc_html__( 'Post date', 'wp-seopress' ),
			'%%post_modified_date%%'            => esc_html__( 'Post modified date', 'wp-seopress' ),
			'%%post_author%%'                   => esc_html__( 'Post author', 'wp-seopress' ),
			'%%post_category%%'                 => esc_html__( 'Post category', 'wp-seopress' ),
			'%%post_tag%%'                      => esc_html__( 'Post tag', 'wp-seopress' ),
			'%%_category_title%%'               => esc_html__( 'Category title', 'wp-seopress' ),
			'%%_category_description%%'         => esc_html__( 'Category description', 'wp-seopress' ),
			'%%tag_title%%'                     => esc_html__( 'Tag title', 'wp-seopress' ),
			'%%tag_description%%'               => esc_html__( 'Tag description', 'wp-seopress' ),
			'%%term_title%%'                    => esc_html__( 'Term title', 'wp-seopress' ),
			'%%term_description%%'              => esc_html__( 'Term description', 'wp-seopress' ),
			'%%search_keywords%%'               => esc_html__( 'Search keywords', 'wp-seopress' ),
			'%%current_pagination%%'            => esc_html__( 'Current number page', 'wp-seopress' ),
			'%%page%%'                          => esc_html__( 'Page number with context', 'wp-seopress' ),
			'%%cpt_plural%%'                    => esc_html__( 'Plural Post Type Archive name', 'wp-seopress' ),
			'%%archive_title%%'                 => esc_html__( 'Archive title', 'wp-seopress' ),
			'%%archive_date%%'                  => esc_html__( 'Archive date', 'wp-seopress' ),
			'%%archive_date_day%%'              => esc_html__( 'Day Archive date', 'wp-seopress' ),
			'%%archive_date_month%%'            => esc_html__( 'Month Archive title', 'wp-seopress' ),
			'%%archive_date_month_name%%'       => esc_html__( 'Month name Archive title', 'wp-seopress' ),
			'%%archive_date_year%%'             => esc_html__( 'Year Archive title', 'wp-seopress' ),
			'%%_cf_your_custom_field_name%%'    => esc_html__( 'Custom fields from post, page, post type and term taxonomy', 'wp-seopress' ),
			'%%_ct_your_custom_taxonomy_slug%%' => esc_html__( 'Custom term taxonomy from post, page or post type', 'wp-seopress' ),
			'%%wc_single_cat%%'                 => esc_html__( 'Single product category', 'wp-seopress' ),
			'%%wc_single_tag%%'                 => esc_html__( 'Single product tag', 'wp-seopress' ),
			'%%wc_single_short_desc%%'          => esc_html__( 'Single product short description', 'wp-seopress' ),
			'%%wc_single_price%%'               => esc_html__( 'Single product price', 'wp-seopress' ),
			'%%wc_single_price_exc_tax%%'       => esc_html__( 'Single product price taxes excluded', 'wp-seopress' ),
			'%%wc_sku%%'                        => esc_html__( 'Single SKU product', 'wp-seopress' ),
			'%%currentday%%'                    => esc_html__( 'Current day', 'wp-seopress' ),
			'%%currentmonth%%'                  => esc_html__( 'Current month', 'wp-seopress' ),
			'%%currentmonth_short%%'            => esc_html__( 'Current month in 3 letters', 'wp-seopress' ),
			'%%currentyear%%'                   => esc_html__( 'Current year', 'wp-seopress' ),
			'%%currentdate%%'                   => esc_html__( 'Current date', 'wp-seopress' ),
			'%%currenttime%%'                   => esc_html__( 'Current time', 'wp-seopress' ),
			'%%author_first_name%%'             => esc_html__( 'Author first name', 'wp-seopress' ),
			'%%author_last_name%%'              => esc_html__( 'Author last name', 'wp-seopress' ),
			'%%author_website%%'                => esc_html__( 'Author website', 'wp-seopress' ),
			'%%author_nickname%%'               => esc_html__( 'Author nickname', 'wp-seopress' ),
			'%%author_bio%%'                    => esc_html__( 'Author biography', 'wp-seopress' ),
			'%%_ucf_your_user_meta%%'           => esc_html__( 'Custom User Meta', 'wp-seopress' ),
			'%%currentmonth_num%%'              => esc_html__( 'Current month in digital format', 'wp-seopress' ),
			'%%target_keyword%%'                => esc_html__( 'Target keyword', 'wp-seopress' ),
		)
	);
}

/**
 * Render dynamic variables
 *
 * @param string $classes Classes.
 *
 * @return string HTML.
 */
function seopress_render_dyn_variables( $classes ) {
	$html = sprintf( '<button type="button" class="' . seopress_btn_secondary_classes() . ' seopress-tag-single-all seopress-tag-dropdown %s"><span class="dashicons dashicons-arrow-down-alt2"></span></button>', $classes );
	if ( ! empty( seopress_get_dyn_variables() ) ) {
		$html .= '<div class="sp-wrap-tag-variables-list">';
		$html .= '<ul class="sp-tag-variables-list">';
		$html .= '<li class="sp-tag-variables-search"><input type="text" class="sp-tag-variables-search-input" placeholder="' . esc_html__( 'Search variables...', 'wp-seopress' ) . '" /></li>';
		foreach ( seopress_get_dyn_variables() as $key => $value ) {
			$html .= '<li data-value=' . $key . ' tabindex="0"><span>' . $value . '</span></li>';
		}
		$html .= '</ul></div>';
	}

	return $html;
}

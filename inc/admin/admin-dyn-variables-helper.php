<?php

function seopress_get_dyn_variables()
{
    return [
        '%%sep%%'                           => 'Separator',
        '%%sitetitle%%'                     => __('Site Title', 'wp-seopress'),
        '%%tagline%%'                       => __('Tagline', 'wp-seopress'),
        '%%post_title%%'                    => __('Post Title', 'wp-seopress'),
        '%%post_excerpt%%'                  => __('Post excerpt', 'wp-seopress'),
        '%%post_content%%'                  => __('Post content / product description', 'wp-seopress'),
        '%%post_thumbnail_url%%'            => __('Post thumbnail URL', 'wp-seopress'),
        '%%post_url%%'                      => __('Post URL', 'wp-seopress'),
        '%%post_date%%'                     => __('Post date', 'wp-seopress'),
        '%%post_modified_date%%'            => __('Post modified date', 'wp-seopress'),
        '%%post_author%%'                   => __('Post author', 'wp-seopress'),
        '%%post_category%%'                 => __('Post category', 'wp-seopress'),
        '%%post_tag%%'                      => __('Post tag', 'wp-seopress'),
        '%%_category_title%%'               => __('Category title', 'wp-seopress'),
        '%%_category_description%%'         => __('Category description', 'wp-seopress'),
        '%%tag_title%%'                     => __('Tag title', 'wp-seopress'),
        '%%tag_description%%'               => __('Tag description', 'wp-seopress'),
        '%%term_title%%'                    => __('Term title', 'wp-seopress'),
        '%%term_description%%'              => __('Term description', 'wp-seopress'),
        '%%search_keywords%%'               => __('Search keywords', 'wp-seopress'),
        '%%current_pagination%%'            => __('Current number page', 'wp-seopress'),
        '%%page%%'                          => __('Page number with context', 'wp-seopress'),
        '%%cpt_plural%%'                    => __('Plural Post Type Archive name', 'wp-seopress'),
        '%%archive_title%%'                 => __('Archive title', 'wp-seopress'),
        '%%archive_date%%'                  => __('Archive date', 'wp-seopress'),
        '%%archive_date_day%%'              => __('Day Archive date', 'wp-seopress'),
        '%%archive_date_month%%'            => __('Month Archive title', 'wp-seopress'),
        '%%archive_date_month_name%%'       => __('Month name Archive title', 'wp-seopress'),
        '%%archive_date_year%%'             => __('Year Archive title', 'wp-seopress'),
        '%%_cf_your_custom_field_name%%'    => __('Custom fields from post, page, post type and term taxonomy', 'wp-seopress'),
        '%%_ct_your_custom_taxonomy_slug%%' => __('Custom term taxonomy from post, page or post type', 'wp-seopress'),
        '%%wc_single_cat%%'                 => __('Single product category', 'wp-seopress'),
        '%%wc_single_tag%%'                 => __('Single product tag', 'wp-seopress'),
        '%%wc_single_short_desc%%'          => __('Single product short description', 'wp-seopress'),
        '%%wc_single_price%%'               => __('Single product price', 'wp-seopress'),
        '%%wc_single_price_exc_tax%%'       => __('Single product price taxes excluded', 'wp-seopress'),
        '%%wc_sku%%'                        => __('Single SKU product', 'wp-seopress'),
        '%%currentday%%'                    => __('Current day', 'wp-seopress'),
        '%%currentmonth%%'                  => __('Current month', 'wp-seopress'),
        '%%currentmonth_short%%'            => __('Current month in 3 letters', 'wp-seopress'),
        '%%currentyear%%'                   => __('Current year', 'wp-seopress'),
        '%%currentdate%%'                   => __('Current date', 'wp-seopress'),
        '%%currenttime%%'                   => __('Current time', 'wp-seopress'),
        '%%author_first_name%%'             => __('Author first name', 'wp-seopress'),
        '%%author_last_name%%'              => __('Author last name', 'wp-seopress'),
        '%%author_website%%'                => __('Author website', 'wp-seopress'),
        '%%author_nickname%%'               => __('Author nickname', 'wp-seopress'),
        '%%author_bio%%'                    => __('Author biography', 'wp-seopress'),
        '%%_ucf_your_user_meta%%'           => __('Custom User Meta', 'wp-seopress'),
        '%%currentmonth_num%%'              => __('Current month in digital format', 'wp-seopress'),
        '%%target_keyword%%'                => __('Target keyword', 'wp-seopress'),
    ];
}

/**
 * @param string $classes
 *
 * @return string
 */
function seopress_render_dyn_variables($classes)
{
    $html = sprintf('<button type="button" class="'.seopress_btn_secondary_classes().' seopress-tag-single-all seopress-tag-dropdown %s"><span class="dashicons dashicons-arrow-down-alt2"></span></button>', $classes);
    if (! empty(seopress_get_dyn_variables())) {
        $html .= '<div class="sp-wrap-tag-variables-list"><ul class="sp-tag-variables-list">';
        foreach (seopress_get_dyn_variables() as $key => $value) {
            $html .= '<li data-value=' . $key . ' tabindex="0"><span>' . $value . '</span></li>';
        }
        $html .= '</ul></div>';
    }

    return $html;
}

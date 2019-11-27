<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Init
global $post;
global $term;

$seopress_titles_title_template ='';
$seopress_titles_description_template ='';
$seopress_paged ='1';
$the_author_meta ='';
$sep = '';
$seopress_excerpt ='';
$post_category ='';
$post_tag ='';
$get_search_query ='';
$woo_single_cat_html ='';
$woo_single_tag_html ='';
$woo_single_price ='';
$woo_single_price_exc_tax ='';
$woo_single_sku = '';
$author_bio ='';

//Excerpt length
$seopress_excerpt_length = 50;
$seopress_excerpt_length = apply_filters('seopress_excerpt_length',$seopress_excerpt_length);

//Remove WordPress Filters
$seopress_array_filters = array('category_description', 'tag_description', 'term_description');
foreach ($seopress_array_filters as $key => $value) {
    remove_filter($value,'wpautop');
}

//Template variables
if (seopress_titles_sep_option()) {
    $sep = seopress_titles_sep_option();
} else {
    $sep = '-';
}

if (!is_404() && $post !='') {
    if (has_excerpt($post->ID)) {
        $seopress_excerpt = get_the_excerpt();
    }
}

if (get_query_var('paged') >'1') {
    $seopress_paged = get_query_var('paged');
    $seopress_paged = apply_filters('seopress_paged', $seopress_paged);
} else {
    $seopress_paged = '';
}

if (is_singular() && isset($post->post_author)){
    $the_author_meta = get_the_author_meta('display_name', $post->post_author);
    $author_bio = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_the_author_meta('description', $post->post_author))))));
}

if (is_author() && NULL !== get_queried_object()) {
    $author = get_queried_object();
    $the_author_meta = $author->display_name;
    $author_bio = esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_the_author_meta('description', $author->ID))))));

}

if (is_single() && has_category()) {
    $post_category_array = get_the_terms(get_the_id(), 'category');
    $post_category = $post_category_array[0]->name;
}

if (is_single() && has_tag()) {
    $post_tag_array = get_the_terms(get_the_id(), 'post_tag');
    $post_tag = $post_tag_array[0]->name;
}

if (get_search_query() !='') {
    $get_search_query = '"'.get_search_query().'"';
} else {
    $get_search_query = esc_attr('" "');
}
$get_search_query = apply_filters('seopress_get_search_query', $get_search_query);

if ($seopress_excerpt !='') {
    $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes($seopress_excerpt), true)))), $seopress_excerpt_length);
} elseif ($post !='') {
    if (get_post_field('post_content', $post->ID) !='') {
        $seopress_get_the_excerpt = wp_trim_words(esc_attr(stripslashes_deep(wp_filter_nohtml_kses(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post->ID), true))))), $seopress_excerpt_length);
    } else {
        $seopress_get_the_excerpt = null;
    }
} else {
    $seopress_get_the_excerpt = null;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' )) {
    if (is_product()) {
        //Woo Cat product
        $woo_single_cats = get_the_terms( $post->ID, 'product_cat' );
                            
        if ( $woo_single_cats && ! is_wp_error( $woo_single_cats ) ) {
            
            $woo_single_cat = array();
            
            foreach ( $woo_single_cats as $term ) {
                $woo_single_cat[] = $term->name;
            }
                            
            $woo_single_cat_html = stripslashes_deep(wp_filter_nohtml_kses(join( ", ", $woo_single_cat )));
        }

        //Woo Tag product
        $woo_single_tags = get_the_terms( $post->ID, 'product_tag' );
                            
        if ( $woo_single_tags && ! is_wp_error( $woo_single_tags ) ) {
            
            $woo_single_tag = array();
            
            foreach ( $woo_single_tags as $term ) {
                $woo_single_tag[] = $term->name;
            }

            $woo_single_tag_html = stripslashes_deep(wp_filter_nohtml_kses(join( ", ", $woo_single_tag )));
        }

        //Woo Price
        $product = wc_get_product($post->ID);
        $woo_single_price = wc_get_price_including_tax( $product );

        //Woo Price tax excluded
        $product = wc_get_product($post->ID);
        $woo_single_price_exc_tax = wc_get_price_excluding_tax( $product );

        //Woo SKU Number
        $product = wc_get_product($post->ID);
        $woo_single_sku = $product->get_sku();
    }
}

$seopress_titles_template_variables_array = array(
    '%%sep%%',
    '%%sitetitle%%',
    '%%sitename%%',
    '%%tagline%%',
    '%%title%%',
    '%%post_title%%',
    '%%post_excerpt%%',
    '%%post_date%%',
    '%%post_modified_date%%',
    '%%post_author%%',
    '%%post_category%%',
    '%%post_tag%%',
    '%%_category_title%%',
    '%%_category_description%%',
    '%%tag_title%%',
    '%%tag_description%%',
    '%%term_title%%',
    '%%term_description%%',
    '%%search_keywords%%',
    '%%current_pagination%%',
    '%%cpt_plural%%',
    '%%archive_title%%',
    '%%archive_date%%',
    '%%archive_date_day%%',
    '%%archive_date_month%%',
    '%%archive_date_year%%',
    '%%wc_single_cat%%',
    '%%wc_single_tag%%',
    '%%wc_single_short_desc%%',
    '%%wc_single_price%%',
    '%%wc_single_price_exc_tax%%',
    '%%wc_sku%%',
    '%%currentday%%',
    '%%currentmonth%%',
    '%%currentyear%%',
    '%%currentdate%%',
    '%%currenttime%%',
    '%%author_bio%%',
);

$seopress_titles_template_variables_array = apply_filters('seopress_titles_template_variables_array',$seopress_titles_template_variables_array);

$seopress_titles_template_replace_array = array(
    $sep,
    get_bloginfo('name'), 
    get_bloginfo('name'), 
    get_bloginfo('description'),
    the_title_attribute('echo=0'),
    the_title_attribute('echo=0'),
    $seopress_get_the_excerpt,
    get_the_date(),
    get_the_modified_date(),
    $the_author_meta,
    $post_category,
    $post_tag,
    single_cat_title('', false),
    wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(category_description())),$seopress_excerpt_length),
    single_tag_title('', false),
    wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(tag_description())),$seopress_excerpt_length),
    single_term_title('', false),
    wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses(term_description())),$seopress_excerpt_length),
    $get_search_query,
    $seopress_paged,
    post_type_archive_title('', false),
    get_the_archive_title(),
    get_query_var('monthnum').' - '.get_query_var('year'),
    get_query_var('day'),
    get_query_var('monthnum'),
    get_query_var('year'),
    $woo_single_cat_html,
    $woo_single_tag_html,
    $seopress_get_the_excerpt,
    $woo_single_price,
    $woo_single_price_exc_tax,
    $woo_single_sku,
    date_i18n('j'),
    date_i18n('F'),
    date('Y'),
    date_i18n( get_option( 'date_format' )),
    current_time(get_option( 'time_format' )),
    $author_bio,
);

$seopress_titles_template_replace_array = apply_filters('seopress_titles_template_replace_array',$seopress_titles_template_replace_array);

$variables = array(
    'post'=> $post,
    'term' => $term,
	'seopress_titles_title_template' => $seopress_titles_title_template,
	'seopress_titles_description_template' => $seopress_titles_description_template,
	'seopress_paged' => $seopress_paged,
	'the_author_meta' => $the_author_meta,
	'sep' => $sep,
	'seopress_excerpt' => $seopress_excerpt,
	'post_category' => $post_category,
	'post_tag' => $post_tag,
    'get_search_query' => $get_search_query,
	'woo_single_cat_html' => $woo_single_cat_html,
	'woo_single_tag_html' => $woo_single_tag_html,
	'woo_single_price' => $woo_single_price,
	'woo_single_price_exc_tax' => $woo_single_price_exc_tax,
	'woo_single_sku' => $woo_single_sku,
	'author_bio' => $author_bio,
	'seopress_get_the_excerpt' => $seopress_get_the_excerpt,
	'seopress_titles_template_variables_array' => $seopress_titles_template_variables_array,
    'seopress_titles_template_replace_array' => $seopress_titles_template_replace_array,
    'seopress_excerpt_length' => $seopress_excerpt_length,
);

return $variables;
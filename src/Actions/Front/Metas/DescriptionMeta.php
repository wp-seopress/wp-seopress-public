<?php

namespace SEOPress\Actions\Front\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class DescriptionMeta implements ExecuteHooksFrontend {

    protected $tagsToStringService;

    public function __construct() {
        $this->tagsToStringService = seopress_get_service('TagsToString');
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks() {
        add_action('wp_head', [$this, 'render'], 1);
    }

    public function render() {
        if (apply_filters('seopress_old_wp_head_description', true)) {
            return;
        }

        $content = $this->getContent();

        if (empty($content)) {
            return;
        }

        $html = '<meta name="description" content="' . $content . '">';
        $html .= "\n";
        echo $html;
    }

    /**
     * @since 4.4.0
     *
     * @return string
     *
     * @param mixed $variablesArray
     * @param mixed $variablesReplace
     */
    protected function getHomeDescriptionTemplate($variablesArray, $variablesReplace) {
        if ( ! function_exists('seopress_get_service')) {
            $descriptionOption = seopress_get_service('TitleOption')->getHomeDescriptionTitle();
            if (empty($descriptionOption)) {
                return '';
            }
            $descriptionOption   = esc_attr($descriptionOption);

            return str_replace($variablesArray, $variablesReplace, $descriptionOption);
        }

        $title   = seopress_get_service('TitleOption')->getHomeDescriptionTitle();
        $context = seopress_get_service('ContextPage')->getContext();

        return $this->tagsToStringService->replace($title, $context);
    }

    protected function getContent() {
        $context = seopress_get_service('ContextPage')->getContext();

        $variables = null;
        $variables = apply_filters('seopress_dyn_variables_fn', $variables);

        $post                                     = $variables['post'];
        $term                                     = $variables['term'];
        $seopress_titles_title_template           = $variables['seopress_titles_title_template'];
        $descriptionTemplate                      = $variables['seopress_titles_description_template'];
        $seopress_paged                           = $variables['seopress_paged'];
        $the_author_meta                          = $variables['the_author_meta'];
        $sep                                      = $variables['sep'];
        $seopress_excerpt                         = $variables['seopress_excerpt'];
        $post_category                            = $variables['post_category'];
        $post_tag                                 = $variables['post_tag'];
        $post_thumbnail_url                       = $variables['post_thumbnail_url'];
        $get_search_query                         = $variables['get_search_query'];
        $woo_single_cat_html                      = $variables['woo_single_cat_html'];
        $woo_single_tag_html                      = $variables['woo_single_tag_html'];
        $woo_single_price                         = $variables['woo_single_price'];
        $woo_single_price_exc_tax                 = $variables['woo_single_price_exc_tax'];
        $woo_single_sku                           = $variables['woo_single_sku'];
        $author_bio                               = $variables['author_bio'];
        $seopress_get_the_excerpt                 = $variables['seopress_get_the_excerpt'];
        $seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
        $seopress_titles_template_replace_array   = $variables['seopress_titles_template_replace_array'];
        $seopress_excerpt_length                  = $variables['seopress_excerpt_length'];
        $page_id                                  = get_option('page_for_posts');

        if (is_front_page() && is_home() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_desc', true)) { //HOMEPAGE
            if ( ! empty(seopress_get_service('TitleOption')->getHomeDescriptionTitle())) {
                $descriptionTemplate = $this->getHomeDescriptionTemplate($seopress_titles_template_variables_array, $seopress_titles_template_replace_array);
            }
        } elseif (is_front_page() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_desc', true)) { //STATIC HOMEPAGE
            if ( ! empty(seopress_get_service('TitleOption')->getHomeDescriptionTitle())) {
                $descriptionTemplate = $this->getHomeDescriptionTemplate($seopress_titles_template_variables_array, $seopress_titles_template_replace_array);
            }
        } elseif (is_home() && '' != get_post_meta($page_id, '_seopress_titles_desc', true)) { //BLOG PAGE
            if (get_post_meta($page_id, '_seopress_titles_desc', true)) {
                $description_meta = esc_html(get_post_meta($page_id, '_seopress_titles_desc', true));
                $description      = $description_meta;

                $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
            }
        } elseif (is_home() && ('posts' == get_option('show_on_front'))) { //YOUR LATEST POSTS
            if ( ! function_exists('seopress_get_service')) {
                if ( ! empty(seopress_get_service('TitleOption')->getHomeDescriptionTitle())) {
                    $description         = esc_attr(seopress_get_service('TitleOption')->getHomeDescriptionTitle());
                    $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
                }
            } else {
                $description         = seopress_get_service('TitleOption')->getHomeDescriptionTitle();
                $descriptionTemplate = $this->tagsToStringService->replace($description, $context);
            }
        } elseif (function_exists('bp_is_group') && bp_is_group()) {
            if ('' !== seopress_get_service('TitleOption')->getBpGroupsDesc()) {
                $description = esc_attr(seopress_get_service('TitleOption')->getBpGroupsDesc());

                $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
            }
        } elseif (is_singular()) { //IS SINGLE
                if (get_post_meta($post->ID, '_seopress_titles_desc', true)) { //IS METABOXE
                    $description = esc_attr(get_post_meta($post->ID, '_seopress_titles_desc', true));

                    preg_match_all('/%%_cf_(.*?)%%/', $description, $matches); //custom fields

                    if ( ! empty($matches)) {
                        $seopress_titles_cf_template_variables_array = [];
                        $seopress_titles_cf_template_replace_array   = [];

                        foreach ($matches['0'] as $key => $value) {
                            $seopress_titles_cf_template_variables_array[] = $value;
                        }

                        foreach ($matches['1'] as $key => $value) {
                            $seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID, $value, true));
                        }
                    }

                    preg_match_all('/%%_ct_(.*?)%%/', $description, $matches2); //custom terms taxonomy

                    if ( ! empty($matches2)) {
                        $seopress_titles_ct_template_variables_array = [];
                        $seopress_titles_ct_template_replace_array   = [];

                        foreach ($matches2['0'] as $key => $value) {
                            $seopress_titles_ct_template_variables_array[] = $value;
                        }

                        foreach ($matches2['1'] as $key => $value) {
                            $term = wp_get_post_terms($post->ID, $value);
                            if ( ! is_wp_error($term)) {
                                $terms                                       = esc_attr($term[0]->name);
                                $seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
                            }
                        }
                    }

                    preg_match_all('/%%_ucf_(.*?)%%/', $description, $matches3); //user meta

                    if ( ! empty($matches3)) {
                        $seopress_titles_ucf_template_variables_array = [];
                        $seopress_titles_ucf_template_replace_array   = [];

                        foreach ($matches3['0'] as $key => $value) {
                            $seopress_titles_ucf_template_variables_array[] = $value;
                        }

                        foreach ($matches3['1'] as $key => $value) {
                            $seopress_titles_ucf_template_replace_array[] = esc_attr(get_user_meta(get_current_user_id(), $value, true));
                        }
                    }

                    //Default
                    $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);

                    //Custom fields
                    if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $descriptionTemplate);
                    }

                    //Custom terms taxonomy
                    if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $descriptionTemplate);
                    }

                    //User meta
                    if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $descriptionTemplate);
                    }
                } elseif ('' !== seopress_get_service('TitleOption')->getSingleCptDesc($post->ID)) { //IS GLOBAL
                    $description = esc_attr(seopress_get_service('TitleOption')->getSingleCptDesc($post->ID));

                    preg_match_all('/%%_cf_(.*?)%%/', $description, $matches); //custom fields

                    if ( ! empty($matches)) {
                        $seopress_titles_cf_template_variables_array = [];
                        $seopress_titles_cf_template_replace_array   = [];

                        foreach ($matches['0'] as $key => $value) {
                            $seopress_titles_cf_template_variables_array[] = $value;
                        }

                        foreach ($matches['1'] as $key => $value) {
                            $seopress_titles_cf_template_replace_array[] = esc_attr(get_post_meta($post->ID, $value, true));
                        }
                    }

                    preg_match_all('/%%_ct_(.*?)%%/', $description, $matches2); //custom terms taxonomy

                    if ( ! empty($matches2)) {
                        $seopress_titles_ct_template_variables_array = [];
                        $seopress_titles_ct_template_replace_array   = [];

                        foreach ($matches2['0'] as $key => $value) {
                            $seopress_titles_ct_template_variables_array[] = $value;
                        }

                        foreach ($matches2['1'] as $key => $value) {
                            $term = wp_get_post_terms($post->ID, $value);
                            if ( ! is_wp_error($term)) {
                                $terms                                       = esc_attr($term[0]->name);
                                $seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
                            }
                        }
                    }

                    preg_match_all('/%%_ucf_(.*?)%%/', $description, $matches3); //user meta

                    if ( ! empty($matches3)) {
                        $seopress_titles_ucf_template_variables_array = [];
                        $seopress_titles_ucf_template_replace_array   = [];

                        foreach ($matches3['0'] as $key => $value) {
                            $seopress_titles_ucf_template_variables_array[] = $value;
                        }

                        foreach ($matches3['1'] as $key => $value) {
                            $seopress_titles_ucf_template_replace_array[] = esc_attr(get_user_meta(get_current_user_id(), $value, true));
                        }
                    }

                    //Default
                    $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);

                    //Custom fields
                    if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $descriptionTemplate);
                    }

                    //Custom terms taxonomy
                    if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $descriptionTemplate);
                    }

                    //User meta
                    if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
                        $descriptionTemplate = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $descriptionTemplate);
                    }
                } else {
                    setup_postdata($post);
                    if ('' != $seopress_get_the_excerpt || '' != get_the_content()) { //DEFAULT EXCERPT OR THE CONTENT
                        $description = wp_trim_words(stripslashes_deep(wp_filter_nohtml_kses($seopress_get_the_excerpt)), $seopress_excerpt_length);

                        $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
                    }
                }
        } elseif (is_post_type_archive() && seopress_get_service('TitleOption')->getArchivesCPTDesc($post->ID)) { //IS POST TYPE ARCHIVE
            $description = esc_attr(seopress_get_service('TitleOption')->getArchivesCPTDesc($post->ID));

            $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
        } elseif ((is_tax() || is_category() || is_tag()) && seopress_get_service('TitleOption')->getTaxDesc()) { //IS TAX
            $description = esc_attr(seopress_get_service('TitleOption')->getTaxDesc());

            if (get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_desc', true)) {
                $descriptionTemplate = esc_attr(get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_desc', true));
                $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $descriptionTemplate);
            } else {
                $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
            }
        } elseif (is_author() && seopress_get_service('TitleOption')->getArchivesAuthorDescription()) { //IS AUTHOR
            $description = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorDescription());

            preg_match_all('/%%_ucf_(.*?)%%/', $description, $matches); //custom fields

            if ( ! empty($matches)) {
                $seopress_titles_cf_template_variables_array = [];
                $seopress_titles_cf_template_replace_array   = [];

                foreach ($matches['0'] as $key => $value) {
                    $seopress_titles_cf_template_variables_array[] = $value;
                }

                foreach ($matches['1'] as $key => $value) {
                    $seopress_titles_cf_template_replace_array[] = esc_attr(get_user_meta(get_current_user_id(), $value, true));
                }
            }

            //Default
            $descriptionTemplate = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorDescription());

            //Custom fields
            if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                $descriptionTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $descriptionTemplate);
            }

            $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $descriptionTemplate);
        } elseif (is_date() && seopress_get_service('TitleOption')->getArchivesDateDesc()) { //IS DATE
            $description = esc_attr(seopress_get_service('TitleOption')->getArchivesDateDesc());

            $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
        } elseif (is_search() && seopress_get_service('TitleOption')->getArchivesSearchDesc()) { //IS SEARCH
            $description = esc_attr(seopress_get_service('TitleOption')->getArchivesSearchDesc());

            $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
        } elseif (is_404() && seopress_get_service('TitleOption')->getArchives404Desc()) { //IS 404
            $description = esc_attr(seopress_get_service('TitleOption')->getArchives404Desc());

            $descriptionTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $description);
        }
        //Hook on meta description - 'seopress_titles_desc'
        if (has_filter('seopress_titles_desc')) {
            $descriptionTemplate = apply_filters('seopress_titles_desc', $descriptionTemplate);
        }

        //Return meta desc tag
        return $descriptionTemplate;
    }
}

<?php

namespace SEOPress\Actions\Front\Metas;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooksFrontend;

class TitleMeta implements ExecuteHooksFrontend {

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
        add_filter('pre_get_document_title', [$this, 'render'], 10);
    }

    /**
     * @since
     *
     * @return string
     *
     * @param mixed $variablesArray
     * @param mixed $variablesReplace
     */
    protected function getHomeTitleTemplate($variablesArray, $variablesReplace) {
        if ( ! function_exists('seopress_get_service')) {
            $titleOption = seopress_get_service('TitleOption')->getHomeSiteTitle();
            if (empty($titleOption)) {
                return '';
            }
            $titleOption   = esc_attr($titleOption);

            return str_replace($variablesArray, $variablesReplace, $titleOption);
        }

        $title   = seopress_get_service('TitleOption')->getHomeSiteTitle();
        $context = seopress_get_service('ContextPage')->getContext();

        return $this->tagsToStringService->replace($title, $context);
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function render() {
        $defaultHook = function_exists('seopress_get_service');

        if (apply_filters('seopress_old_pre_get_document_title', true)) {
            return;
        }

        $context = seopress_get_service('ContextPage')->getContext();

        $variables = apply_filters('seopress_dyn_variables_fn', null);

        $post                                     = $variables['post'];
        $titleTemplate                            = $variables['seopress_titles_title_template'];
        $seopress_titles_template_variables_array = $variables['seopress_titles_template_variables_array'];
        $seopress_titles_template_replace_array   = $variables['seopress_titles_template_replace_array'];
        $page_id                                  = get_option('page_for_posts');

        if (is_front_page() && is_home() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_title', true)) { //HOMEPAGE
            if ( ! empty(seopress_get_service('TitleOption')->getHomeSiteTitle())) {
                $titleTemplate = $this->getHomeTitleTemplate($seopress_titles_template_variables_array, $seopress_titles_template_replace_array);
            }
        } elseif (is_front_page() && isset($post) && '' == get_post_meta($post->ID, '_seopress_titles_title', true)) { //STATIC HOMEPAGE
            if ( ! empty(seopress_get_service('TitleOption')->getHomeSiteTitle())) {
                $titleTemplate = $this->getHomeTitleTemplate($seopress_titles_template_variables_array, $seopress_titles_template_replace_array);
            }
        } elseif (is_home() && '' != get_post_meta($page_id, '_seopress_titles_title', true)) { //BLOG PAGE
            if (get_post_meta($page_id, '_seopress_titles_title', true)) { //IS METABOXE
                $titleOption   = esc_attr(get_post_meta($page_id, '_seopress_titles_title', true));
                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleOption);
            }
        } elseif (is_home() && ('posts' == get_option('show_on_front'))) { //YOUR LATEST POSTS
            if ( ! function_exists('seopress_get_service')) {
                if ( ! empty(seopress_get_service('TitleOption')->getHomeSiteTitle())) {
                    $titleOption = esc_attr(seopress_get_service('TitleOption')->getHomeSiteTitle());

                    $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleOption);
                }
            } else {
                $title         = seopress_get_service('TitleOption')->getHomeSiteTitle();
                $titleTemplate = $this->tagsToStringService->replace($title, $context);
            }
        } elseif (function_exists('bp_is_group') && bp_is_group()) {
            if ('' !== seopress_get_service('TitleOption')->getTitleBpGroups()) {
                $titleOption = esc_attr(seopress_get_service('TitleOption')->getTitleBpGroups());

                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleOption);
            }
        } elseif (is_singular()) { //IS SINGULAR
            // Check Buddypress
            $buddyId = seopress_get_service('BuddyPressGetCurrentId')->getCurrentId();
            if ($buddyId) {
                $post->ID = $buddyId;
            }

            if (get_post_meta($post->ID, '_seopress_titles_title', true)) { //IS METABOXE
                $titleOption = esc_attr(get_post_meta($post->ID, '_seopress_titles_title', true));

                preg_match_all('/%%_cf_(.*?)%%/', $titleOption, $matches); //custom fields

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

                preg_match_all('/%%_ct_(.*?)%%/', $titleOption, $matches2); //custom terms taxonomy

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

                preg_match_all('/%%_ucf_(.*?)%%/', $titleOption, $matches3); //user meta

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
                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleOption);

                //Custom fields
                if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $titleTemplate);
                }

                //Custom terms taxonomy
                if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $titleTemplate);
                }

                //User meta
                if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $titleTemplate);
                }
            } else { //DEFAULT GLOBAL
                $seopress_titles_single_titles_option = esc_attr(seopress_get_service('TitleOption')->getSingleCptTitle($post->ID));

                preg_match_all('/%%_cf_(.*?)%%/', $seopress_titles_single_titles_option, $matches); //custom fields

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

                preg_match_all('/%%_ct_(.*?)%%/', $seopress_titles_single_titles_option, $matches2); //custom terms taxonomy

                if ( ! empty($matches2)) {
                    $seopress_titles_ct_template_variables_array = [];
                    $seopress_titles_ct_template_replace_array   = [];

                    foreach ($matches2['0'] as $key => $value) {
                        $seopress_titles_ct_template_variables_array[] = $value;
                    }

                    foreach ($matches2['1'] as $key => $value) {
                        $term = wp_get_post_terms($post->ID, $value);
                        if ( ! is_wp_error($term) && isset($term[0])) {
                            $terms                                       = esc_attr($term[0]->name);
                            $seopress_titles_ct_template_replace_array[] = apply_filters('seopress_titles_custom_tax', $terms, $value);
                        }
                    }
                }

                preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_single_titles_option, $matches3); //user meta

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
                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_single_titles_option);

                //Custom fields
                if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $titleTemplate);
                }

                //Custom terms taxonomy
                if ( ! empty($matches2) && ! empty($seopress_titles_ct_template_variables_array) && ! empty($seopress_titles_ct_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_ct_template_variables_array, $seopress_titles_ct_template_replace_array, $titleTemplate);
                }

                //User meta
                if ( ! empty($matches3) && ! empty($seopress_titles_ucf_template_variables_array) && ! empty($seopress_titles_ucf_template_replace_array)) {
                    $titleTemplate = str_replace($seopress_titles_ucf_template_variables_array, $seopress_titles_ucf_template_replace_array, $titleTemplate);
                }
            }
        } elseif (is_post_type_archive() && seopress_get_service('TitleOption')->getArchivesCPTTitle()) { //IS POST TYPE ARCHIVE
            $seopress_titles_archive_titles_option = esc_attr(seopress_get_service('TitleOption')->getArchivesCPTTitle());

            $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archive_titles_option);
        } elseif ((is_tax() || is_category() || is_tag()) && seopress_get_service('TitleOption')->getTaxTitle()) { //IS TAX
            $seopress_titles_tax_titles_option = esc_attr(seopress_get_service('TitleOption')->getTaxTitle());

            if (get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_title', true)) {
                $titleTemplate = esc_attr(get_term_meta(get_queried_object()->{'term_id'}, '_seopress_titles_title', true));
                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleTemplate);
            } else {
                $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_tax_titles_option);
            }
        } elseif (is_author() && seopress_get_service('TitleOption')->getArchivesAuthorTitle()) { //IS AUTHOR
            $seopress_titles_archives_author_title_option = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorTitle());

            preg_match_all('/%%_ucf_(.*?)%%/', $seopress_titles_archives_author_title_option, $matches); //custom fields

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
            $titleTemplate = esc_attr(seopress_get_service('TitleOption')->getArchivesAuthorTitle());

            //Custom fields
            if ( ! empty($matches) && ! empty($seopress_titles_cf_template_variables_array) && ! empty($seopress_titles_cf_template_replace_array)) {
                $titleTemplate = str_replace($seopress_titles_cf_template_variables_array, $seopress_titles_cf_template_replace_array, $titleTemplate);
            }

            $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $titleTemplate);
        } elseif (is_date() && seopress_get_service('TitleOption')->getTitleArchivesDate()) { //IS DATE
            $seopress_titles_archives_date_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchivesDate());

            $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_date_title_option);
        } elseif (is_search() && seopress_get_service('TitleOption')->getTitleArchivesSearch()) { //IS SEARCH
            $seopress_titles_archives_search_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchivesSearch());

            $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_search_title_option);
        } elseif (is_404() && seopress_get_service('TitleOption')->getTitleArchives404()) { //IS 404
            $seopress_titles_archives_404_title_option = esc_attr(seopress_get_service('TitleOption')->getTitleArchives404());

            $titleTemplate = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_404_title_option);
        }

        //Hook on Title tag - 'seopress_titles_title'
        if (has_filter('seopress_titles_title')) {
            $titleTemplate = apply_filters('seopress_titles_title', $titleTemplate);
        }

        //Return Title tag
        return $titleTemplate;
    }
}

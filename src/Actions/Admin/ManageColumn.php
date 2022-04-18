<?php

namespace SEOPress\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;

class ManageColumn implements ExecuteHooksBackend
{
    /**
     * @since 4.4.0
     */
    public function __construct()
    {
        $this->tagsToStringService = seopress_get_service('TagsToString');
    }

    /**
     * @since 4.4.0
     *
     * @return void
     */
    public function hooks()
    {
        if ('1' == seopress_get_toggle_option('advanced')) {
            add_action('init', [$this, 'setup']);
        }
    }

    public function setup()
    {
        $listPostTypes = seopress_get_service('WordPressData')->getPostTypes();

        if (empty($listPostTypes)) {
            return;
        }

        foreach ($listPostTypes as $key => $value) {
            add_filter('manage_' . $key . '_posts_columns', [$this, 'addColumn']);
            add_action('manage_' . $key . '_posts_custom_column', [$this, 'displayColumn'], 10, 2);
        }

        add_filter('manage_edit-download_columns', [$this, 'addColumn'], 10, 2);
    }

    public function addColumn($columns)
    {
        if (! function_exists('seopress_advanced_appearance_title_col_option')) {
            require_once SEOPRESS_PLUGIN_DIR_PATH . '/inc/functions/options-advanced-admin.php';
        }
        if (! empty(seopress_advanced_appearance_title_col_option())) {
            $columns['seopress_title'] = __('Title tag', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_meta_desc_col_option())) {
            $columns['seopress_desc'] = __('Meta Desc.', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_redirect_enable_col_option())) {
            $columns['seopress_redirect_enable'] = __('Redirect?', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_redirect_url_col_option())) {
            $columns['seopress_redirect_url'] = __('Redirect URL', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_canonical_option())) {
            $columns['seopress_canonical'] = __('Canonical', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_target_kw_col_option())) {
            $columns['seopress_tkw'] = __('Target Kw', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_noindex_col_option())) {
            $columns['seopress_noindex'] = __('noindex?', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_nofollow_col_option())) {
            $columns['seopress_nofollow'] = __('nofollow?', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_score_col_option())) {
            $columns['seopress_score'] = __('Score', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_words_col_option())) {
            $columns['seopress_words'] = __('Words', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_ps_col_option())) {
            $columns['seopress_ps'] = __('Page Speed', 'wp-seopress');
        }
        if (! empty(seopress_advanced_appearance_insights_col_option())) {
            $columns['seopress_insights'] = __('Insights', 'wp-seopress');
        }

        return $columns;
    }

    /**
     * @since 4.4.0
     * @see manage_' . $postType . '_posts_custom_column
     *
     * @param string $column
     * @param int    $post_id
     *
     * @return void
     */
    public function displayColumn($column, $post_id)
    {
        switch ($column) {
            case 'seopress_title':
                $metaPostTitle   = get_post_meta($post_id, '_seopress_titles_title', true);

                $context = seopress_get_service('ContextPage')->buildContextWithCurrentId($post_id)->getContext();
                $title   = $this->tagsToStringService->replace($metaPostTitle, $context);
                if (empty($title)) {
                    $title = $metaPostTitle;
                }
                printf('<div id="seopress_title-%s">%s</div>', esc_attr($post_id), $title);
                break;

            case 'seopress_desc':
                $metaDescription   = get_post_meta($post_id, '_seopress_titles_desc', true);
                $context           = seopress_get_service('ContextPage')->buildContextWithCurrentId($post_id)->getContext();
                $description       = $this->tagsToStringService->replace($metaDescription, $context);
                if (empty($description)) {
                    $description = $metaDescription;
                }
                printf('<div id="seopress_desc-%s">%s</div>', esc_attr($post_id), $description);
                break;

            case 'seopress_redirect_enable':
                if ('yes' == get_post_meta($post_id, '_seopress_redirections_enabled', true)) {
                    echo '<div id="seopress_redirect_enable-' . esc_attr($post_id) . '"><span class="dashicons dashicons-yes"></span></div>';
                }
                break;
            case 'seopress_redirect_url':
                echo '<div id="seopress_redirect_url-' . esc_attr($post_id) . '">' . esc_html(get_post_meta($post_id, '_seopress_redirections_value', true)) . '</div>';
                break;

            case 'seopress_canonical':
                echo '<div id="seopress_canonical-' . esc_attr($post_id) . '">' . esc_html(get_post_meta($post_id, '_seopress_robots_canonical', true)) . '</div>';
                break;

            case 'seopress_tkw':
                echo '<div id="seopress_tkw-' . esc_attr($post_id) . '">' . esc_html(get_post_meta($post_id, '_seopress_analysis_target_kw', true)) . '</div>';
                break;

            case 'seopress_noindex':
                if ('yes' == get_post_meta($post_id, '_seopress_robots_index', true)) {
                    echo '<span class="dashicons dashicons-hidden"></span><span class="screen-reader-text">' . __('noindex is on!', 'wp-seopress') . '</span>';
                }
                break;

            case 'seopress_nofollow':
                if ('yes' == get_post_meta($post_id, '_seopress_robots_follow', true)) {
                    echo '<span class="dashicons dashicons-yes"></span><span class="screen-reader-text">' . __('nofollow is on!', 'wp-seopress') . '</span>';
                }
                break;

            case 'seopress_words':
                $dataApiAnalysis = get_post_meta($post_id, '_seopress_content_analysis_api', true);
                if (isset($dataApiAnalysis['words_counter']) && $dataApiAnalysis['words_counter'] !== null) {
                    echo $dataApiAnalysis['words_counter'];
                } else {
                    if ('' != get_the_content()) {
                        $seopress_analysis_data['words_counter'] = preg_match_all("/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u", strip_tags(wp_filter_nohtml_kses(get_the_content())), $matches);

                        echo $seopress_analysis_data['words_counter'];
                    }
                }
                break;

            case 'seopress_ps':
                echo '<a href="'.admin_url('admin.php?page=seopress-pro-page&data_permalink='.esc_url(get_the_permalink().'#tab=tab_seopress_page_speed')).'" class="seopress-button" title="' . esc_attr(__('Analyze this page with Google Page Speed', 'wp-seopress')) . '"><span class="dashicons dashicons-dashboard"></span></a>';
                break;

            case 'seopress_score':
                $dataApiAnalysis = get_post_meta($post_id, '_seopress_content_analysis_api', true);
                if (isset($dataApiAnalysis['score']) && $dataApiAnalysis['score'] !== null) {
                    echo '<div class="analysis-score">';
                    if ($dataApiAnalysis['score'] === 'good') {
                        echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="good" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                    </svg><span class="screen-reader-text">' . __('Good', 'wp-seopress') . '</span></p>';
                    } else {
                        echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                        <circle id="bar" class="notgood" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
                    </svg><span class="screen-reader-text">' . __('Should be improved', 'wp-seopress') . '</span></p>';
                    }
                    echo '</div>';
                } else {
                    if (get_post_meta($post_id, '_seopress_analysis_data')) {
                        $ca = get_post_meta($post_id, '_seopress_analysis_data');
                        echo '<div class="analysis-score">';
                        if (isset($ca[0]['score']) && 1 == $ca[0]['score']) {
                            echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
							<circle id="bar" class="good" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
						</svg><span class="screen-reader-text">' . __('Good', 'wp-seopress') . '</span></p>';
                        } elseif (isset($ca[0]['score']) && '' == $ca[0]['score']) {
                            echo '<p><svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
							<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
							<circle id="bar" class="notgood" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
						</svg><span class="screen-reader-text">' . __('Should be improved', 'wp-seopress') . '</span></p>';
                        }
                        echo '</div>';
                    }
                }
                break;

            case 'seopress_insights':
                if (is_plugin_active('wp-seopress-insights/seopress-insights.php')) {
                    foreach (seopress_insights_query_all_rankings() as $key => $value) {
                        if (! empty($value) && $value['url'] == get_the_permalink($post_id)) {
                            $rankings[$value['ts']][] = [
                                'keyword'           => get_the_title(),
                                'p'          		     => $value['p'],
                                'url'               => $value['url'],
                                'search_volume'     => $value['search_volume'],
                                'cpc'               => $value['cpc'],
                                'competition'       => $value['competition'],
                                'date'              => date('Y/m/d', $value['ts']),
                            ];
                        }
                    }

                    if (! empty($rankings)) {
                        foreach ($rankings as $key => $value) {
                            $avg_pos[] 	= $value[0]['p'];
                            $kws[] 		   = $value[0]['keyword'];
                        }

                        echo '<div class="wrap-insights-post">';

                        echo '<p><span class="dashicons dashicons-chart-line"></span>';

                        if (! empty($kws)) {
                            $kws = array_unique($kws);

                            $html = '<ul>';
                            foreach ($kws as $kw) {
                                $html .= '<li><span class="dashicons dashicons-minus"></span>' . $kw . '</li>';
                            }
                            $html .= '</ul>';

                            echo seopress_tooltip(__('Insights from these keywords:', 'wp-seopress-insights'), sprintf('%s', $html), '');
                        }

                        echo '</p>';

                        //Average position
                        echo '<p class="widget-insights-title">' . __('Average position: ', 'wp-seopress-insights') . '</p>';

                        if (! empty($avg_pos)) {
                            echo '<p>';

                            echo '<span>' . round(array_sum($avg_pos) / count($avg_pos), 2) . '</span>';

                            //Variation
                            if (isset($avg_pos[0]) && $avg_pos[1]) {
                                $p_variation = $avg_pos[0] - $avg_pos[1];

                                if ($avg_pos[0] < $avg_pos[1]) {
                                    $p_variation_rel = '<span class="up"><span class="dashicons dashicons-arrow-up-alt"></span> ' . abs($p_variation) . '</span>';
                                } elseif ($avg_pos[0] == $avg_pos[1]) {
                                    $p_variation_rel = '<span class="stable">=</span>';
                                } else {
                                    $p_variation_rel = '<span class="down"><span class="dashicons dashicons-arrow-up-alt"></span> ' . abs($p_variation) . '</span>';
                                }

                                echo $p_variation_rel;
                            }

                            echo '</p>';
                        }

                        //Latest position
                        echo '<p class="widget-insights-title">' . __('Latest position: ', 'wp-seopress-insights') . '</p>';

                        $p = array_key_first($rankings);
                        echo '<p><span>' . $rankings[$p][0]['p'] . '</span></p>';
                        echo '</div>';
                    }
                }
                break;
        }
    }
}

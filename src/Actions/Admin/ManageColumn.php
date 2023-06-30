<?php

namespace SEOPress\Actions\Admin;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Services\TagsToString;

class ManageColumn implements ExecuteHooksBackend
{
    /**
     * @var TagsToString
     */
    protected $tagsToStringService;

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
            add_action('init', [$this, 'setup'], 20); //priority is important for plugins compatibility like Toolset
        }
    }

    public function setup()
    {
        $listPostTypes = seopress_get_service('WordPressData')->getPostTypes();

        if (empty($listPostTypes)) {
            return;
        }

        foreach ($listPostTypes as $key => $value) {
            if (null === seopress_get_service('TitleOption')->getSingleCptEnable($key) && '' != $key) {
                add_filter('manage_' . $key . '_posts_columns', [$this, 'addColumn']);
                add_action('manage_' . $key . '_posts_custom_column', [$this, 'displayColumn'], 10, 2);
                add_filter('manage_edit-' . $key . '_sortable_columns', [$this, 'sortableColumn']);
                add_filter('pre_get_posts', [$this, 'sortColumnsBy']);
            }
        }

        add_filter('manage_edit-download_columns', [$this, 'addColumn'], 10, 2);
    }

    public function addColumn($columns)
    {
        if (seopress_get_service('AdvancedOption')->getAppearanceTitleCol() ==='1') {
            $columns['seopress_title'] = __('Title tag', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceMetaDescriptionCol() ==='1') {
            $columns['seopress_desc'] = __('Meta Desc.', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceRedirectEnableCol() ==='1') {
            $columns['seopress_redirect_enable'] = __('Redirect?', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceRedirectUrlCol() ==='1') {
            $columns['seopress_redirect_url'] = __('Redirect URL', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceCanonical() ==='1') {
            $columns['seopress_canonical'] = __('Canonical', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceTargetKwCol() ==='1') {
            $columns['seopress_tkw'] = __('Target Kw', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceNoIndexCol() ==='1') {
            $columns['seopress_noindex'] = __('noindex?', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceNoFollowCol() ==='1') {
            $columns['seopress_nofollow'] = __('nofollow?', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceScoreCol() ==='1') {
            $columns['seopress_score'] = __('Score', 'wp-seopress');
        }
        if (seopress_get_service('AdvancedOption')->getAppearanceWordsCol() ==='1') {
            $columns['seopress_words'] = __('Words', 'wp-seopress');
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
                printf('<div id="seopress_title_raw-%s" class="hidden">%s</div>', esc_attr($post_id), $metaPostTitle);
                break;

            case 'seopress_desc':
                $metaDescription   = get_post_meta($post_id, '_seopress_titles_desc', true);
                $context           = seopress_get_service('ContextPage')->buildContextWithCurrentId($post_id)->getContext();
                $description       = $this->tagsToStringService->replace($metaDescription, $context);
                if (empty($description)) {
                    $description = $metaDescription;
                }
                printf('<div id="seopress_desc-%s">%s</div>', esc_attr($post_id), $description);
                printf('<div id="seopress_desc_raw-%s" class="hidden">%s</div>', esc_attr($post_id), $metaDescription);
                break;

            case 'seopress_redirect_enable':
                if ('yes' == get_post_meta($post_id, '_seopress_redirections_enabled', true)) {
                    echo '<span class="dashicons dashicons-yes-alt"></span>';
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
        }
    }

    /**
     * @since 6.5.0
     * @see manage_edit' . $postType . '_sortable_columns
     *
     * @param string $columns
     *
     * @return array $columns
     */
    public function sortableColumn($columns) {
        $columns['seopress_noindex']  = 'seopress_noindex';
        $columns['seopress_nofollow'] = 'seopress_nofollow';

        return $columns;
    }

    /**
     * @since 6.5.0
     * @see pre_get_posts
     *
     * @param string $query
     *
     * @return void
     */
    public function sortColumnsBy($query) {
        if (! is_admin()) {
            return;
        }

        $orderby = $query->get('orderby');
        if ('seopress_noindex' == $orderby) {
            $query->set('meta_key', '_seopress_robots_index');
            $query->set('orderby', 'meta_value');
        }
        if ('seopress_nofollow' == $orderby) {
            $query->set('meta_key', '_seopress_robots_follow');
            $query->set('orderby', 'meta_value');
        }
    }
}

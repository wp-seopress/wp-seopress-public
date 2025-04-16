<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Helpers\ContentAnalysis;
use SEOPressPro\Services\Audit;

class GetContent
{
    const NAME_SERVICE = 'GetContentAnalysis';

    private $seo_issues_repository;
    private $seo_issues_database;

    public function __construct()
    {
        $this->seo_issues_repository = function_exists('seopress_pro_get_service') ? seopress_pro_get_service('SEOIssuesRepository') : null;

        $this->seo_issues_database = function_exists('seopress_pro_get_service') ? seopress_pro_get_service('SEOIssuesDatabase') : null;
    }

    public function getMatches($content, $targetKeywords)
    {
        $data = [];

        if(empty($targetKeywords)){
            return null;
        }

        foreach ($targetKeywords as $kw) {
            $kw = remove_accents(wp_specialchars_decode($kw));
            if (preg_match_all('#\b(' . preg_quote($kw, '/') . ')\b#iu', remove_accents($content), $m)) {
                $data[$kw] = $m[0];
            }
        }

        if (empty($data)) {
            return null;
        }

        return $data;
    }


    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeSchemas($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'json_schemas';

        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        if (isset($data['json_schemas']) && is_array($data['json_schemas']) && (!empty($data['json_schemas']) || isset($data['json_schemas']))) {
            $desc = '<p>' . __('We found these schemas in the source code of this page:', 'wp-seopress') . '</p>';

            $desc .= '<ul>';

            $issue_desc = [];

            foreach (array_count_values($data['json_schemas']) as $key => $value) {
                $html = null;
                if ($value > 1) {
                    if ('Review' !== $key) {
                        $html                          = '<span class="impact high">' . __('duplicated schema - x', 'wp-seopress') . $value . '</span>';
                        $analyzes['schemas']['impact'] = 'high';
                    } else {
                        $html                          = ' <span class="impact">' . __('x', 'wp-seopress') . $value . '</span>';
                    }

                    $issue_desc[] = [$key, $value];
                }
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . $key . $html . '</li>';
            }
            $desc .= '</ul>';
            $analyzes['schemas']['desc'] = $desc;

            //If duplicated schema
            if (!empty($issue_desc)) {
                $issue['issue_name'] = 'json_schemas_duplicated';
                $issue['issue_desc'] = $issue_desc;
            }
        } else {
            $docs = seopress_get_docs_links();
            $analyzes['schemas']['impact'] = 'medium';
            $analyzes['schemas']['desc']   = '<p>' . __('No schemas found in the source code of this page. Get rich snippets in Google Search results and improve your visibility by adding structured data types (schemas) to your page.', 'wp-seopress') . '</p>';

            if (!is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
                $analyzes['schemas']['desc']   .= '<p><a class="seopress-help" href="' . esc_url( $docs['schemas']['feature'] ) . '" target="_blank" class="components-button is-link">' . __('Get SEOPress PRO to add schemas now', 'wp-seopress') . '</a></p>';
            } else {
                $analyzes['schemas']['desc']   .= '<p><a class="seopress-help" href="' . esc_url( $docs['schemas']['ebook'] ) . '" target="_blank" class="components-button is-link">' . __('Learn more', 'wp-seopress') . '</a></p>';
            }

            $issue['issue_name'] = 'json_schemas_not_found';
        }

        $issue['issue_priority'] = $analyzes['schemas']['impact'] ? $analyzes['schemas']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeOldPost($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'old_post';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }
        $modified = get_post_datetime($post, 'modified');

        $desc = null;
        if ($modified->getTimestamp() < strtotime('-365 days')) {
            $analyzes['old_post']['impact'] = 'medium';
            $desc                           = '<p><span class="dashicons dashicons-no-alt"></span>' . __('This post is a little old!', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'old_post';
            $issue['issue_desc'] = $modified->getTimestamp();
        } else {
            $desc = '<p><span class="dashicons dashicons-yes"></span>' . __('The last modified date of this article is less than 1 year. Cool!', 'wp-seopress') . '</p>';
        }
        $desc .= '<p>' . __('Search engines love fresh content. Update regularly your articles without entirely rewriting your content and give them a boost in search rankings. SEOPress takes care of the technical part.', 'wp-seopress') . '</p>';
        $analyzes['old_post']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['old_post']['impact'] ? $analyzes['old_post']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeKeywordsPermalink($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'permalink';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        $permalink = !empty($data['permalink']) && is_array($data['permalink']) ? $data['permalink']['value'] : "";
        $permalink = str_replace('-', ' ', $permalink);
        $matches = $this->getMatches($permalink, isset($data['keywords']) ? $data['keywords'] : []);

        if (! empty($matches)) {

            $desc = '<p><span class="dashicons dashicons-yes"></span>' . __('Cool, one of your target keyword is used in your permalink.', 'wp-seopress') . '</p>';
            $desc .= '<ul>';
            foreach ($matches as $key => $value) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . $key . '</li>';
            }

            $desc .= '</ul>';
            $analyzes['keywords_permalink']['desc']   = $desc;
            $analyzes['keywords_permalink']['impact'] = 'good';
        } else {
            if (get_option('page_on_front') == $post->ID) {
                $analyzes['keywords_permalink']['desc']   = '<p><span class="dashicons dashicons-yes"></span>' . __('This is your homepage. This check doesn\'t apply here because there is no slug.', 'wp-seopress') . '</p>';
                $analyzes['keywords_permalink']['impact'] = 'good';
            } else {
                $analyzes['keywords_permalink']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __('You should add one of your target keyword in your permalink.', 'wp-seopress') . '</p>';
                $analyzes['keywords_permalink']['impact'] = 'medium';

                $issue['issue_name'] = 'keywords_permalink';
            }
        }

        $issue['issue_priority'] = $analyzes['keywords_permalink']['impact'] ? $analyzes['keywords_permalink']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeHeadings($analyzes, $data, $post)
    {
        //H1
        $issue = [];
        $issue['issue_type'] = 'headings';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }
        $desc = '<h4>' . __('H1 (Heading 1)', 'wp-seopress') . '</h4>';

        //No headings found
        if(empty($data['h1']) && empty($data['h2']) && empty($data['h3'])){
            $analyzes['headings']['impact'] = 'high';

            $issue['issue_name'] = 'headings_not_found';
        }

        $h1Matches = [];
        if(!empty($data['h1'])){
            foreach ($data['h1'] as $key => $value) {
                $matches = $this->getMatches($value, isset($data['keywords']) ? $data['keywords'] : []);

                if(!$matches){
                    continue;
                }

                foreach ($matches as $keyForKeyword => $value) {
                    $h1Matches[$keyForKeyword] = isset($h1Matches[$keyForKeyword]) ? $h1Matches[$keyForKeyword] + count($value) : count($value);
                }
            }
        }


        if (isset($data['h1']) && is_array($data['h1']) && !empty($h1Matches)) {
            $totalH1 = count($data['h1']);

            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('Target keywords were found in Heading 1 (H1).', 'wp-seopress') . '</p>';

            $desc .= '<ul>';


            foreach ($h1Matches as $key => $matches) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the keyword was found */ sprintf(esc_html__('%1$s was found %2$d times.', 'wp-seopress'), $key, $matches) . '</li>';

            }

            $desc .= '</ul>';
            if ($totalH1 > 1) {
                $issue_desc = [];

                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of headings 1 */ sprintf(esc_html__('We found %d Heading 1 (H1) in your content.', 'wp-seopress'), $totalH1) . '</p>';
                $desc .= '<p>' . __('You should not use more than one H1 heading in your post content. The rule is simple: only one H1 for each web page. It is better for both SEO and accessibility. Below, the list:', 'wp-seopress') . '</p>';
                $analyzes['headings']['impact'] = 'high';

                $desc .= '<ul>';
                foreach ($data['h1'] as $h1) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($h1) . '</li>';

                    $issue_desc[] = sanitize_text_field( $h1 );
                }
                $desc .= '</ul>';

                $issue['issue_name'] = 'headings_h1_duplicated';
                $issue['issue_desc'] = $issue_desc;
            }
        } elseif (isset($data['h1']) && is_array($data['h1']) && count($data['h1']) === 0) {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span><strong>' . __('No Heading 1 (H1) found in your content. This is required for both SEO and Accessibility!', 'wp-seopress') . '</strong></p>';
            $analyzes['headings']['impact'] = 'high';

            $issue['issue_name'] = 'headings_h1_not_found';
        } else {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in Heading 1 (H1).', 'wp-seopress') . '</p>';
            if ('high' != $analyzes['headings']['impact']) {
                $analyzes['headings']['impact'] = 'high';
            }

            $issue['issue_name'] = 'headings_h1_without_target_kw';
        }

        $issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        //H2
        $issue = [];
        $issue['issue_type'] = 'headings';
        $desc .= '<h4>' . __('H2 (Heading 2)', 'wp-seopress') . '</h4>';
        $h2Matches = [];

        if(!empty($data['h2'])){
            foreach ($data['h2'] as $key => $value) {
                $matches = $this->getMatches($value, isset($data['keywords']) ? $data['keywords'] : []);
                if(!$matches){
                    continue;
                }

                foreach ($matches as $keyForKeyword => $value) {
                    $h2Matches[$keyForKeyword] = isset($h2Matches[$keyForKeyword]) ? $h2Matches[$keyForKeyword] + count($value) : count($value);
                }
            }
        }

        if (! empty($h2Matches)) {
            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('Target keywords were found in Heading 2 (H2).', 'wp-seopress') . '</p>';
            $desc .= '<ul>';

            foreach ($h2Matches as $key => $matches) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s heading 2, %2$d number of times the heading 2 was found */ sprintf(esc_html__('%1$s was found %2$d times.', 'wp-seopress'), $key, $matches) . '</li>';
            }
            $desc .= '</ul>';

        } else {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in Heading 2 (H2).', 'wp-seopress') . '</p>';
            if ('high' != $analyzes['headings']['impact']) {
                $analyzes['headings']['impact'] = 'medium';
            }

            $issue['issue_name'] = 'headings_h2_without_target_kw';
        }

        $issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        //H3
        $issue = [];
        $issue['issue_type'] = 'headings';
        $desc .= '<h4>' . __('H3 (Heading 3)', 'wp-seopress') . '</h4>';

        $h3Matches = [];
        if(!empty($data['h3'])){
            foreach ($data['h3'] as $key => $value) {
                $matches = $this->getMatches($value, isset($data['keywords']) ? $data['keywords'] : []);
                if(!$matches){
                    continue;
                }

                foreach ($matches as $keyForKeyword => $value) {
                    $h3Matches[$keyForKeyword] = isset($h3Matches[$keyForKeyword]) ? $h3Matches[$keyForKeyword] + count($value) : count($value);
                }
            }
        }

        if (! empty($h3Matches)) {
            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('Target keywords were found in Heading 3 (H3).', 'wp-seopress') . '</p>';
            $desc .= '<ul>';

            foreach ($h3Matches as $key => $matches) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s heading 3, %2$d number of times the heading 3 was found */ sprintf(esc_html__('%1$s was found %2$d times.', 'wp-seopress'), $key, $matches) . '</li>';
            }
            $desc .= '</ul>';
        } else {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in Heading 3 (H3).', 'wp-seopress') . '</p>';
            if ('high' != $analyzes['headings']['impact'] && 'medium' != $analyzes['headings']['impact']) {
                $analyzes['headings']['impact'] = 'low';
            }

            $issue['issue_name'] = 'headings_h3_without_target_kw';
        }
        $analyzes['headings']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['headings']['impact'] ? $analyzes['headings']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeMetaTitle($analyzes, $data, $post)
    {
        $issues = [];
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, 'title');
        }
        $seopress_titles_title     = !empty($data['title']) ? $data['title'] : get_post_meta($post->ID, '_seopress_titles_title', true);
        $title_length = mb_strlen($seopress_titles_title);

        if (! empty($seopress_titles_title)) {
            $desc = null;

            $matches = $this->getMatches($seopress_titles_title, isset($data['keywords']) ? $data['keywords'] : []);

            if (! empty($matches)) {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('Target keywords were found in the Meta Title.', 'wp-seopress') . '</p>';
                $desc .= '<ul>';
                foreach ($matches as $key => $value) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the target keyword was found */ sprintf(esc_html__('%1$s was found %2$d times.', 'wp-seopress'), $key, count($value)) . '</li>';
                }
                $desc .= '</ul>';
                $analyzes['meta_title']['impact'] = 'good';
            } else {
                $analyzes['meta_title']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in the Meta Title.', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'title_without_target_kw';
                $issues[0]['issue_priority'] = $analyzes['meta_title']['impact'];
            }

            if ($title_length > 60) {
                $analyzes['meta_title']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your custom title is too long.', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'title_too_long';
                $issues[1]['issue_desc'] = $title_length;
                $issues[1]['issue_priority'] = $analyzes['meta_title']['impact'];
            } else {
                if (!empty($analyzes['meta_title']['impact']) && $analyzes['meta_title']['impact'] !== 'medium') {
                    $analyzes['meta_title']['impact'] = 'good';
                }
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('The length of your title is correct', 'wp-seopress') . '</p>';
            }
            $analyzes['meta_title']['desc'] = $desc;
        } else {
            $analyzes['meta_title']['impact'] = 'medium';
            $analyzes['meta_title']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __('No custom title is set for this post. If the global meta title suits you, you can ignore this recommendation.', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'title_not_custom';
            $issues[2]['issue_priority'] = $analyzes['meta_title']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'title';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeMetaDescription($analyzes, $data, $post)
    {
        $issues = [];
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, 'description');
        }
        $seopress_titles_desc                   = !empty($data['description']) ? $data['description'] : get_post_meta($post->ID, '_seopress_titles_desc', true);
        $desc_length = mb_strlen($seopress_titles_desc);

        if (! empty($seopress_titles_desc)) {
            $desc = null;

            $matches = $this->getMatches($seopress_titles_desc, isset($data['keywords']) ? $data['keywords'] : []);
            if (! empty($matches)) {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('Target keywords were found in the Meta description.', 'wp-seopress') . '</p>';
                $desc .= '<ul>';

                foreach ($matches as $key => $value) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . /* translators: %1$s target keyword, %2$d number of times the target keyword was found */ sprintf(esc_html__('%1$s was found %2$d times.', 'wp-seopress'), $key, count($value)) . '</li>';
                }
                $desc .= '</ul>';
                $analyzes['meta_desc']['impact'] = 'good';
            } else {
                $analyzes['meta_desc']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in the Meta description.', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'description_without_target_kw';
                $issues[0]['issue_priority'] = $analyzes['meta_desc']['impact'];
            }

            if ($desc_length > 160) {
                $analyzes['meta_desc']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('You custom meta description is too long.', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'description_too_long';
                $issues[1]['issue_desc'] = $desc_length;
                $issues[1]['issue_priority'] = $analyzes['meta_desc']['impact'];
            } else {
                if (!empty($analyzes['meta_desc']['impact']) && $analyzes['meta_desc']['impact'] !== 'medium') {
                    $analyzes['meta_desc']['impact'] = 'good';
                }
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('The length of your meta description is correct', 'wp-seopress') . '</p>';
            }
            $analyzes['meta_desc']['desc'] = $desc;
        } else {
            $analyzes['meta_desc']['impact'] = 'medium';
            $analyzes['meta_desc']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __('No custom meta description is set for this post. If the global meta description suits you, you can ignore this recommendation.', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'description_not_custom';
            $issues[2]['issue_priority'] = $analyzes['meta_desc']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'description';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeSocialTags($analyzes, $data, $post)
    {
        //og:title
        $issues = [];
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, 'social');
        }
        $desc = null;
        $desc .= '<h4>' . __('Open Graph Title', 'wp-seopress') . '</h4>';

        if (isset($data['og_title']) && is_array($data['og_title']) && !empty($data['og_title'])) {
            $count = count($data['og_title']);

            $all_og_title = $data['og_title'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:TITLE tags */ sprintf(esc_html__('We found %d og:title in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:title in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:title tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'og_title_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_og_title[0])) { //If og:title empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Title tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'og_title_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Title tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_title)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_og_title as $og_title) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_title) . '</li>';

                    $issue_desc[] = sanitize_text_field( $og_title );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Title is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'og_title_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //og:description
        $issues = [];
        $desc .= '<h4>' . __('Open Graph Description', 'wp-seopress') . '</h4>';

        if (isset($data['og_description']) && is_array($data['og_description']) && !empty($data['og_description'])) {
            $count = count($data['og_description']);

            $all_og_desc = $data['og_description'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:DESCRIPTION tags */ sprintf(esc_html__('We found %d og:description in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:description in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:description tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'og_desc_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_og_desc[0])) { //If og:description empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Description tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'og_desc_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Description tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_desc)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_og_desc as $og_desc) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_desc) . '</li>';

                    $issue_desc[] = sanitize_text_field( $og_title );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Description is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'og_desc_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //og:image
        $issue = [];
        $issue['issue_type'] = 'social';
        $desc .= '<h4>' . __('Open Graph Image', 'wp-seopress') . '</h4>';

        if (isset($data['og_image']) && is_array($data['og_image']) && !empty($data['og_image'])) {
            $count = count($data['og_image']);

            $all_og_img = $data['og_image'];

            if ($count > 0 && ! empty($all_og_img[0])) {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . /* translators: %d number of OG:IMAGE tags */ sprintf(esc_html__('We found %d og:image in your content.', 'wp-seopress'), $count) . '</p>';
            }

            //If og:image empty
            if ($count > 0 && empty($all_og_img[0])) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Image tag is empty!', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'og_img_empty';
            }

            if (! empty($all_og_img)) {
                $desc .= '<ul>';
                foreach ($all_og_img as $og_img) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url($og_img) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Image is missing!', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'og_img_missing';
        }

        $issue['issue_priority'] = $analyzes['social']['impact'] ? $analyzes['social']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        //og:url
        $issues = [];
        $desc .= '<h4>' . __('Open Graph URL', 'wp-seopress') . '</h4>';

        if (isset($data['og_url']) && is_array($data['og_url']) && !empty($data['og_url'])) {
            $count = count($data['og_url']);

            $all_og_url = $data['og_url'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:URL tags */ sprintf(esc_html__('We found %d og:url in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:url in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:url tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'og_url_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_og_url[0])) { //If og:url empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph URL tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'og_url_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph URL tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_url)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_og_url as $og_url) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url($og_url) . '</li>';

                    $issue_desc[] = sanitize_url( $og_url );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph URL is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'og_url_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //og:site_name
        $issues = [];
        $desc .= '<h4>' . __('Open Graph Site Name', 'wp-seopress') . '</h4>';

        if (isset($data['og_site_name']) && is_array($data['og_site_name']) && !empty($data['og_site_name'])) {
            $count = count($data['og_site_name']);

            $all_og_site_name = $data['og_site_name'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:SITE_NAME tags */ sprintf(esc_html__('We found %d og:site_name in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:site_name in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:site_name tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'og_sitename_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_og_site_name[0])) { //If og:site_name empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Site Name tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'og_sitename_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Site Name tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_site_name)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_og_site_name as $og_site_name) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_site_name) . '</li>';

                    $issue_desc[] = sanitize_text_field( $og_site_name );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Site Name is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'og_sitename_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //twitter:title
        $issues = [];
        $desc .= '<h4>' . __('X Title', 'wp-seopress') . '</h4>';

        if (isset($data['twitter_title']) && is_array($data['twitter_title']) && !empty($data['twitter_title'])) {
            $count = count($data['twitter_title']);

            $all_tw_title = $data['twitter_title'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of times a twitter:tile tag is found */ sprintf(esc_html__('We found %d twitter:title in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . /* translators: %d number of TWITTER:TITLE tags */ __('You should not use more than one twitter:title in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:title tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'x_title_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_tw_title[0])) { //If twitter:title empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Title tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'x_title_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found a X Title tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_tw_title)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_tw_title as $tw_title) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($tw_title) . '</li>';

                    $issue_desc[] = sanitize_text_field( $tw_title );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Title is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'x_title_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //twitter:description
        $issues = [];
        $desc .= '<h4>' . __('X Description', 'wp-seopress') . '</h4>';

        if (isset($data['twitter_description']) && is_array($data['twitter_description']) && !empty($data['twitter_description'])) {
            $count = count($data['twitter_description']);

            $all_tw_desc = $data['twitter_description'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of TWITTER:DESCRIPTION tags */ sprintf(esc_html__('We found %d twitter:description in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one twitter:description in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:description tag from your source code. Below, the list:', 'wp-seopress') . '</p>';

                $issues[0]['issue_name'] = 'x_desc_duplicated';
                $issues[0]['issue_priority'] = $analyzes['social']['impact'];
            } elseif (empty($all_tw_desc[0])) { //If twitter:description empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Description tag is empty!', 'wp-seopress') . '</p>';

                $issues[1]['issue_name'] = 'x_desc_empty';
                $issues[1]['issue_priority'] = $analyzes['social']['impact'];
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found a X Description tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_tw_desc)) {
                $issue_desc = [];

                $desc .= '<ul>';
                foreach ($all_tw_desc as $tw_desc) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($tw_desc) . '</li>';

                    $issue_desc[] = sanitize_text_field( $tw_desc );
                }
                $desc .= '</ul>';

                $issues[0]['issue_desc'] = $issue_desc;
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Description is missing!', 'wp-seopress') . '</p>';

            $issues[2]['issue_name'] = 'x_desc_missing';
            $issues[2]['issue_priority'] = $analyzes['social']['impact'];
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                $issue['issue_type'] = 'social';
                if (isset($issue['issue_name'])) {
                    if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
                        $this->seo_issues_database->saveData($post->ID, $issue);
                    }
                }
            }
        }

        //twitter:image
        $issue = [];
        $issue['issue_type'] = 'social';
        $desc .= '<h4>' . __('X Image', 'wp-seopress') . '</h4>';

        if (isset($data['twitter_image']) && is_array($data['twitter_image']) && !empty($data['twitter_image'])) {
            $count = count($data['twitter_image']);

            $all_tw_img = $data['twitter_image'];

            if ($count > 0 && ! empty($all_tw_img[0])) {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . /* translators: %d number of TWITTER:IMAGE tags */ sprintf(esc_html__('We found %d twitter:image in your content.', 'wp-seopress'), $count) . '</p>';
            }

            //If twitter:image:src empty
            if ($count > 0 && empty($all_tw_img[0])) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Image tag is empty!', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'x_img_empty';
            }

            if (! empty($all_tw_img)) {
                $desc .= '<ul>';
                foreach ($all_tw_img as $tw_img) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url($tw_img) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Image is missing!', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'x_img_missing';
        }
        $analyzes['social']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['social']['impact'] ? $analyzes['social']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeRobots($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'robots';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }
        $desc = null;
        if (isset($data['meta_robots']) && is_array($data['meta_robots']) && !empty($data['meta_robots'])) {
            $meta_robots = $data['meta_robots'];

            if (count($data['meta_robots']) > 1) {
                $analyzes['robots']['impact'] = 'high';

                $count_meta_robots = count($data['meta_robots']);

                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %s number of meta robots tags */ sprintf(esc_html__('We found %s meta robots in your page. There is probably something wrong with your theme!', 'wp-seopress'), $count_meta_robots) . '</p>';

                $issue['issue_name'] = 'meta_robots_duplicated';
                $issue['issue_desc'] = absint($count_meta_robots);
            }

            $encoded = wp_json_encode($meta_robots);

            if (preg_match('/noindex/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p data-robots="noindex"><span class="dashicons dashicons-no-alt"></span>' . __('<strong>noindex</strong> is on! Search engines can\'t index this page.', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'meta_robots_noindex';
            } else {
                $desc .= '<p data-robots="index"><span class="dashicons dashicons-yes"></span>' . __('<strong>noindex</strong> is off. Search engines will index this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/nofollow/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>nofollow</strong> is on! Search engines can\'t follow your links on this page.', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'meta_robots_nofollow';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nofollow</strong> is off. Search engines will follow links on this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/noimageindex/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>noimageindex</strong> is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'meta_robots_noimageindex';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>noimageindex</strong> is off. Google will index the images on this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/nosnippet/', $encoded)) {
                if ('high' != $analyzes['robots']['impact']) {
                    $analyzes['robots']['impact'] = 'medium';
                }
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>nosnippet</strong> is on! Search engines will not display a snippet of this page in search results.', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'meta_robots_nosnippet';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nosnippet</strong> is off. Search engines will display a snippet of this page in search results.', 'wp-seopress') . '</p>';
            }
        } else {
            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found no meta robots on this page. It means, your page is index,follow. Search engines will index it, and follow links. ', 'wp-seopress') . '</p>';
        }

        $analyzes['robots']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['robots']['impact'] ? $analyzes['robots']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeImgAlt($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'img_alt';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        $withAlt = [];
        $withoutAlt = [];
        if(!empty($data['images'])){
            foreach($data['images'] as $image) {
                if(!empty($image['alt'])) {
                    $withAlt[] = $image['src'];
                } else {
                    $withoutAlt[] = $image['src'];
                }
            }
        }

        if (! empty($withoutAlt)) {

            $desc = '<div class="wrap-analysis-img">';

            if (! empty($withoutAlt)) {
                $issue_desc = [];

                $analyzes['img_alt']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.', 'wp-seopress') . '</p>';

                //Standard images & galleries
                if (! empty($withoutAlt)) {
                    $desc .= '<ul class="attachments">';
                    foreach ($withoutAlt as $img) {
                        $desc .= '<li class="attachment"><figure><img src="' . esc_url($img) . '"/><figcaption style="word-break: break-all;">' . esc_url($img) . '</figcaption></figure></li>';

                        $issue_desc[] = sanitize_url($img);
                    }
                    $desc .= '</ul>';
                }

                $desc .= '<p>' . __('Note that we scan all your source code, it means, some missing alternative texts of images might be located in your header, sidebar or footer.', 'wp-seopress') . '</p>';
            }
            $desc .= '</div>';

            $analyzes['img_alt']['desc'] = $desc;

            $issue['issue_name'] = 'img_alt_missing';
            $issue['issue_desc'] = $issue_desc;
        } elseif(!empty($withAlt) && empty($withoutAlt)) {
            $analyzes['img_alt']['impact'] = 'good';
            $analyzes['img_alt']['desc'] = '<p><span class="dashicons dashicons-yes"></span>' . __('All alternative tags are filled in. Good work!', 'wp-seopress') . '</p>';
        } elseif (empty($withAlt) && empty($withoutAlt)) {
            $analyzes['img_alt']['impact'] = 'medium';
            $analyzes['img_alt']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __('We could not find any image in your content. Content with media is a plus for your SEO.', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'img_alt_no_media';
        }

        $issue['issue_priority'] = $analyzes['img_alt']['impact'] ? $analyzes['img_alt']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeNoFollowLinks($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'nofollow_links';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        if (isset($data['links_no_follow']) && is_array($data['links_no_follow']) && !empty($data['links_no_follow'])) {
            $issue_desc = [];
            $count = count($data['links_no_follow']);

            $desc = '<p>' . /* translators: %d number of nofollow links */ sprintf(esc_html__('We found %d links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:', 'wp-seopress'), $count) . '</p>';
            $desc .= '<ul>';
            foreach ($data['links_no_follow'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank" class="components-button is-link">' . esc_url($link['value']) . '</a><span class="dashicons dashicons-external"></span></li>';

                $issue_desc[] = sanitize_url( $link['url'] );
            }
            $desc .= '</ul>';
            $analyzes['nofollow_links']['impact'] = 'good';
            if ($count > 3) {
                $analyzes['nofollow_links']['impact'] = 'low';
            }
            $analyzes['nofollow_links']['desc']   = $desc;

            $issue['issue_name'] = 'nofollow_links_too_many';
            $issue['issue_desc'] = $issue_desc;
        } else {
            $analyzes['nofollow_links']['desc'] = '<p><span class="dashicons dashicons-yes"></span>' . __('This page doesn\'t have any nofollow links.', 'wp-seopress') . '</p>';
        }

        $issue['issue_priority'] = $analyzes['nofollow_links']['impact'] ? $analyzes['nofollow_links']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeOutboundLinks($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'outbound_links';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        $desc = '<p>' . __('Internet is built on the principle of hyperlink. It is therefore perfectly normal to make links between different websites. However, avoid making links to low quality sites, SPAM... If you are not sure about the quality of a site, add the attribute "nofollow" to your link.', 'wp-seopress') . '</p>';
        if (isset($data['outbound_links']) && is_array($data['outbound_links']) && !empty($data['outbound_links'])) {
            $count = count($data['outbound_links']);

            $desc .= '<p>' . /* translators: %d number of outbound links */ sprintf(__('We found %s outbound links in your page. Below, the list:', 'wp-seopress'), $count) . '</p>';
            $desc .= '<ul>';
            foreach ($data['outbound_links'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank" class="components-button is-link">' . esc_url($link['value']) . '</a><span class="dashicons dashicons-external"></span></li>';
            }
            $desc .= '</ul>';


        } else {
            $analyzes['outbound_links']['impact'] = 'medium';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('This page doesn\'t have any outbound links.', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'outbound_links_missing';
        }
        $analyzes['outbound_links']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['outbound_links']['impact'] ? $analyzes['outbound_links']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeInternalLinks($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'internal_links';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        $desc = '<p>' . __('Internal links are important for SEO and user experience. Always try to link your content together, with quality link anchors.', 'wp-seopress') . '</p>';

        //Bricks compatibility
        $theme = wp_get_theme();
		if (defined('BRICKS_DB_EDITOR_MODE') && ('bricks' == $theme->template || 'Bricks' == $theme->parent_theme)) {
            $analyzes['internal_links']['impact'] = 'good';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Unfortunately, this analysis can‘t work with Bricks Builder because of the way your content is stored in your database.', 'wp-seopress') . '</p>';
        } elseif (isset($data['internal_links']) && is_array($data['internal_links']) && !empty($data['internal_links'])) {
            $count = count($data['internal_links']);

            $desc .= '<p>' . /* translators: %s internal links */ sprintf(__('We found %s internal links to this page.', 'wp-seopress'), $count) . '</p>';

            $desc .= '<ul>';
            foreach ($data['internal_links'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank" class="components-button is-link">' . esc_html($link['value']) . '</a>
                <a class="nounderline" href="' . esc_url(get_edit_post_link($link['id'])) . '" title="' . /* translators: %s link to edit the post */ sprintf(__('edit %s', 'wp-seopress'), esc_html(get_the_title($link['id']))) . '"><span class="dashicons dashicons-edit-large"></span></a></li>';
            }
            $desc .= '</ul>';
        } else {
            $analyzes['internal_links']['impact'] = 'medium';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('This page doesn\'t have any internal links from other content. Links from archive pages are not considered internal links due to lack of context.', 'wp-seopress') . '</p>';

            $issue['issue_name'] = 'internal_links_missing';
        }
        $analyzes['internal_links']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['internal_links']['impact'] ? $analyzes['internal_links']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    /**
     * @param array   $analyzes
     * @param array   $data
     * @param WP_Post $post
     *
     * @return array
     */
    protected function analyzeCanonical($analyzes, $data, $post)
    {
        $issue = [];
        $issue['issue_type'] = 'all_canonical';
        if ($this->seo_issues_repository && method_exists($this->seo_issues_repository, 'deleteSEOIssue')) {
            $this->seo_issues_repository->deleteSEOIssue($post->ID, $issue['issue_type']);
        }

        $desc = '<p>' . __('A canonical URL is required by search engines to handle duplicate content.', 'wp-seopress') . '</p>';

        if (isset($data['canonical']) && is_array($data['canonical']) && !empty($data['canonical'])) {
            $count = count($data['canonical']);

            $desc .= '<p>' . /* translators: %s number of canonical tags */ sprintf(_n('We found %s canonical URL in your source code. Below, the list:', 'We found %s canonical URLs in your source code. Below, the list:', $count, 'wp-seopress'), number_format_i18n($count)) . '</p>';

            $desc .= '<ul>';
            foreach ($data['canonical'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link) . '" target="_blank" class="components-button is-link">' . esc_url($link) . '</a><span class="dashicons dashicons-external"></span></li>';
            }
            $desc .= '</ul>';

            if ($count > 1) {
                $analyzes['all_canonical']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('You must fix this. Canonical URL duplication is bad for SEO.', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'canonical_duplicated';
            }
        } else {
            if ('yes' === get_post_meta($post->ID, '_seopress_robots_index', true)) {
                $analyzes['all_canonical']['impact'] = 'good';
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('This page doesn\'t have any canonical URL because your post is set to <strong>noindex</strong>. This is normal.', 'wp-seopress') . '</p>';
            }
            else  if (seopress_get_service('TitleOption')->getSingleCptNoIndex() || seopress_get_service('TitleOption')->getTitleNoIndex() || true === post_password_required($post->ID)) {
                $analyzes['all_canonical']['impact'] = 'good';
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('This page doesn\'t have any canonical URL because your post is set to <strong>noindex</strong>. This is normal.', 'wp-seopress') . '</p>';
            }
            else {
                $analyzes['all_canonical']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('This page doesn\'t have any canonical URL.', 'wp-seopress') . '</p>';

                $issue['issue_name'] = 'canonical_missing';
            }
        }
        $analyzes['all_canonical']['desc'] = $desc;

        $issue['issue_priority'] = $analyzes['all_canonical']['impact'] ? $analyzes['all_canonical']['impact'] : 0;

        if ($this->seo_issues_database && method_exists($this->seo_issues_database, 'saveData')) {
            if (isset($issue['issue_name'])) {
                $this->seo_issues_database->saveData($post->ID, $issue);
            }
        }

        return $analyzes;
    }

    public function getAnalyzes($post, $type ='')
    {
        if ($post === null) {
            return;
        }

        $data = seopress_get_service('ContentAnalysisDatabase')->getData($post->ID);
        $analyzes = ContentAnalysis::getData();

        switch ($type) {
            case 'schemas':
                $analyzes = $this->analyzeSchemas($analyzes, $data, $post);
                break;
            case 'old_post':
                $analyzes = $this->analyzeOldPost($analyzes, $data, $post);
                break;
            case 'keywords_permalink':
                $analyzes = $this->analyzeKeywordsPermalink($analyzes, $data, $post);
                break;
            case 'headings':
                $analyzes = $this->analyzeHeadings($analyzes, $data, $post);
                break;
            case 'meta_title':
                $analyzes = $this->analyzeMetaTitle($analyzes, $data, $post);
                break;
            case 'meta_description':
                $analyzes = $this->analyzeMetaDescription($analyzes, $data, $post);
                break;
            case 'social_tags':
                $analyzes = $this->analyzeSocialTags($analyzes, $data, $post);
                break;
            case 'robots':
                $analyzes = $this->analyzeRobots($analyzes, $data, $post);
                break;
            case 'img_alt':
                $analyzes = $this->analyzeImgAlt($analyzes, $data, $post);
                break;
            case 'nofollow_links':
                $analyzes = $this->analyzeNoFollowLinks($analyzes, $data, $post);
                break;
            case 'outbound_links':
                $analyzes = $this->analyzeOutboundLinks($analyzes, $data, $post);
                break;
            case 'internal_links':
                $analyzes = $this->analyzeInternalLinks($analyzes, $data, $post);
                break;
            case 'canonical_url':
                $analyzes = $this->analyzeCanonical($analyzes, $data, $post);
                break;
            default:
                $analyzes = $this->analyzeSchemas($analyzes, $data, $post);
                $analyzes = $this->analyzeOldPost($analyzes, $data, $post);
                $analyzes = $this->analyzeKeywordsPermalink($analyzes, $data, $post);
                $analyzes = $this->analyzeHeadings($analyzes, $data, $post);
                $analyzes = $this->analyzeMetaTitle($analyzes, $data, $post);
                $analyzes = $this->analyzeMetaDescription($analyzes, $data, $post);
                $analyzes = $this->analyzeSocialTags($analyzes, $data, $post);
                $analyzes = $this->analyzeRobots($analyzes, $data, $post);
                $analyzes = $this->analyzeImgAlt($analyzes, $data, $post);
                $analyzes = $this->analyzeNoFollowLinks($analyzes, $data, $post);
                $analyzes = $this->analyzeOutboundLinks($analyzes, $data, $post);
                $analyzes = $this->analyzeInternalLinks($analyzes, $data, $post);
                $analyzes = $this->analyzeCanonical($analyzes, $data, $post);
                break;
        }

        return $analyzes;
    }
}

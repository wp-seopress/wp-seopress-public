<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Helpers\ContentAnalysis;

class GetContent
{
    const NAME_SERVICE = 'GetContentAnalysis';

    public function getMatches($content, $targetKeywords)
    {
        $data = [];

        if(empty($targetKeywords)){
            return null;
        }

        foreach ($targetKeywords as $kw) {
            if (preg_match_all('#\b(' . $kw . ')\b#iu', remove_accents($content), $m)) {
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
        if (isset($data['json_schemas']) && is_array($data['json_schemas']) && (!empty($data['json_schemas']) || isset($data['json_schemas']))) {
            $desc = '<p>' . __('We found these schemas in the source code of this page:', 'wp-seopress') . '</p>';

            $desc .= '<ul>';

            foreach (array_count_values($data['json_schemas']) as $key => $value) {
                $html = null;
                if ($value > 1) {
                    if ('Review' !== $key) {
                        $html                          = '<span class="impact high">' . __('duplicated schema - x', 'wp-seopress') . $value . '</span>';
                        $analyzes['schemas']['impact'] = 'high';
                    } else {
                        $html                          = ' <span class="impact">' . __('x', 'wp-seopress') . $value . '</span>';
                    }
                }
                $desc .= '<li><span class="dashicons dashicons-minus"></span>' . $key . $html . '</li>';
            }
            $desc .= '</ul>';
            $analyzes['schemas']['desc'] = $desc;
        } else {
            $analyzes['schemas']['impact'] = 'medium';
            $analyzes['schemas']['desc']   = '<p>' . __('No schemas found in the source code of this page. Get rich snippets in Google Search results and improve your visibility by adding structured data types (schemas) to your page.', 'wp-seopress') . '</p>';
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
        $modified = get_post_datetime($post, 'modified');

        $desc = null;
        if ($modified->getTimestamp() < strtotime('-365 days')) {
            $analyzes['old_post']['impact'] = 'medium';
            $desc                           = '<p><span class="dashicons dashicons-no-alt"></span>' . __('This post is a little old!', 'wp-seopress') . '</p>';
        } else {
            $desc = '<p><span class="dashicons dashicons-yes"></span>' . __('The last modified date of this article is less than 1 year. Cool!', 'wp-seopress') . '</p>';
        }
        $desc .= '<p>' . __('Search engines love fresh content. Update regularly your articles without entirely rewriting your content and give them a boost in search rankings. SEOPress takes care of the technical part.', 'wp-seopress') . '</p>';
        $analyzes['old_post']['desc'] = $desc;

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
        $desc = '<h4>' . __('H1 (Heading 1)', 'wp-seopress') . '</h4>';

        if(empty($data['h1']) && empty($data['h2']) && empty($data['h3'])){
            $analyzes['headings']['impact'] = 'high';
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
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of headings 1 */ sprintf(esc_html__('We found %d Heading 1 (H1) in your content.', 'wp-seopress'), $totalH1) . '</p>';
                $desc .= '<p>' . __('You should not use more than one H1 heading in your post content. The rule is simple: only one H1 for each web page. It is better for both SEO and accessibility. Below, the list:', 'wp-seopress') . '</p>';
                $analyzes['headings']['impact'] = 'high';

                $desc .= '<ul>';
                foreach ($data['h1'] as $h1) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($h1) . '</li>';
                }
                $desc .= '</ul>';
            }
        } elseif (isset($data['h1']) && is_array($data['h1']) && count($data['h1']) === 0) {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span><strong>' . __('No Heading 1 (H1) found in your content. This is required for both SEO and Accessibility!', 'wp-seopress') . '</strong></p>';
            $analyzes['headings']['impact'] = 'high';
        } else {
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('None of your target keywords were found in Heading 1 (H1).', 'wp-seopress') . '</p>';
            if ('high' != $analyzes['headings']['impact']) {
                $analyzes['headings']['impact'] = 'high';
            }
        }

        //H2
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
        }

        //H3
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
        }
        $analyzes['headings']['desc'] = $desc;

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
        $seopress_titles_title     = !empty($data['title']) ? $data['title'] : get_post_meta($post->ID, '_seopress_titles_title', true);

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
            }

            if (mb_strlen($seopress_titles_title) > 65) {
                $analyzes['meta_title']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your custom title is too long.', 'wp-seopress') . '</p>';
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
        $seopress_titles_desc                   = !empty($data['description']) ? $data['description'] : get_post_meta($post->ID, '_seopress_titles_desc', true);

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
            }

            if (mb_strlen($seopress_titles_desc) > 160) {
                $analyzes['meta_desc']['impact'] = 'medium';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('You custom meta description is too long.', 'wp-seopress') . '</p>';
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
        $desc = null;
        $desc .= '<h4>' . __('Open Graph Title', 'wp-seopress') . '</h4>';

        if (isset($data['og_title']) && is_array($data['og_title']) && !empty($data['og_title'])) {
            $count = count($data['og_title']);

            $all_og_title = $data['og_title'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:TITLE tags */ sprintf(esc_html__('We found %d og:title in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:title in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:title tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_og_title[0])) { //If og:title empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Title tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Title tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_title)) {
                $desc .= '<ul>';
                foreach ($all_og_title as $og_title) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_title) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Title is missing!', 'wp-seopress') . '</p>';
        }

        //og:description
        $desc .= '<h4>' . __('Open Graph Description', 'wp-seopress') . '</h4>';

        if (isset($data['og_description']) && is_array($data['og_description']) && !empty($data['og_description'])) {
            $count = count($data['og_description']);

            $all_og_desc = $data['og_description'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:DESCRIPTION tags */ sprintf(esc_html__('We found %d og:description in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:description in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:description tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_og_desc[0])) { //If og:description empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Description tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Description tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_desc)) {
                $desc .= '<ul>';
                foreach ($all_og_desc as $og_desc) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_desc) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Description is missing!', 'wp-seopress') . '</p>';
        }

        //og:image
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
        }

        //og:url
        $desc .= '<h4>' . __('Open Graph URL', 'wp-seopress') . '</h4>';

        if (isset($data['og_url']) && is_array($data['og_url']) && !empty($data['og_url'])) {
            $count = count($data['og_url']);

            $all_og_url = $data['og_url'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:URL tags */ sprintf(esc_html__('We found %d og:url in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:url in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:url tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_og_url[0])) { //If og:url empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph URL tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph URL tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_url)) {
                $desc .= '<ul>';
                foreach ($all_og_url as $og_url) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_url($og_url) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph URL is missing!', 'wp-seopress') . '</p>';
        }

        //og:site_name
        $desc .= '<h4>' . __('Open Graph Site Name', 'wp-seopress') . '</h4>';

        if (isset($data['og_site_name']) && is_array($data['og_site_name']) && !empty($data['og_site_name'])) {
            $count = count($data['og_site_name']);

            $all_og_site_name = $data['og_site_name'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of OG:SITE_NAME tags */ sprintf(esc_html__('We found %d og:site_name in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one og:site_name in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:site_name tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_og_site_name[0])) { //If og:site_name empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Site Name tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found an Open Graph Site Name tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_og_site_name)) {
                $desc .= '<ul>';
                foreach ($all_og_site_name as $og_site_name) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($og_site_name) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your Open Graph Site Name is missing!', 'wp-seopress') . '</p>';
        }

        //twitter:title
        $desc .= '<h4>' . __('X Title', 'wp-seopress') . '</h4>';

        if (isset($data['twitter_title']) && is_array($data['twitter_title']) && !empty($data['twitter_title'])) {
            $count = count($data['twitter_title']);

            $all_tw_title = $data['twitter_title'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of times a twitter:tile tag is found */ sprintf(esc_html__('We found %d twitter:title in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . /* translators: %d number of TWITTER:TITLE tags */ __('You should not use more than one twitter:title in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:title tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_tw_title[0])) { //If twitter:title empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Title tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found a X Title tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_tw_title)) {
                $desc .= '<ul>';
                foreach ($all_tw_title as $tw_title) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($tw_title) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Title is missing!', 'wp-seopress') . '</p>';
        }

        //twitter:description
        $desc .= '<h4>' . __('X Description', 'wp-seopress') . '</h4>';

        if (isset($data['twitter_description']) && is_array($data['twitter_description']) && !empty($data['twitter_description'])) {
            $count = count($data['twitter_description']);

            $all_tw_desc = $data['twitter_description'];

            if ($count > 1) {
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %d number of TWITTER:DESCRIPTION tags */ sprintf(esc_html__('We found %d twitter:description in your content.', 'wp-seopress'), $count) . '</p>';
                $desc .= '<p>' . __('You should not use more than one twitter:description in your post content to avoid conflicts when sharing on social networks. X will take the last twitter:description tag from your source code. Below, the list:', 'wp-seopress') . '</p>';
            } elseif (empty($all_tw_desc[0])) { //If twitter:description empty
                $analyzes['social']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Description tag is empty!', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found a X Description tag in your source code.', 'wp-seopress') . '</p>';
            }

            if (! empty($all_tw_desc)) {
                $desc .= '<ul>';
                foreach ($all_tw_desc as $tw_desc) {
                    $desc .= '<li><span class="dashicons dashicons-minus"></span>' . esc_html($tw_desc) . '</li>';
                }
                $desc .= '</ul>';
            }
        } else {
            $analyzes['social']['impact'] = 'high';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('Your X Description is missing!', 'wp-seopress') . '</p>';
        }

        //twitter:image
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
        }
        $analyzes['social']['desc'] = $desc;

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
        $desc = null;
        if (isset($data['meta_robots']) && is_array($data['meta_robots']) && !empty($data['meta_robots'])) {
            $meta_robots = $data['meta_robots'];

            if (count($data['meta_robots']) > 1) {
                $analyzes['robots']['impact'] = 'high';

                $count_meta_robots = count($data['meta_robots']);

                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . /* translators: %s number of meta robots tags */ sprintf(esc_html__('We found %s meta robots in your page. There is probably something wrong with your theme!', 'wp-seopress'), $count_meta_robots) . '</p>';
            }

            $encoded = wp_json_encode($meta_robots);

            if (preg_match('/noindex/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p data-robots="noindex"><span class="dashicons dashicons-no-alt"></span>' . __('<strong>noindex</strong> is on! Search engines can\'t index this page.', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p data-robots="index"><span class="dashicons dashicons-yes"></span>' . __('<strong>noindex</strong> is off. Search engines will index this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/nofollow/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>nofollow</strong> is on! Search engines can\'t follow your links on this page.', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nofollow</strong> is off. Search engines will follow links on this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/noimageindex/', $encoded)) {
                $analyzes['robots']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>noimageindex</strong> is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>noimageindex</strong> is off. Google will index the images on this page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/noarchive/', $encoded)) {
                if ('high' != $analyzes['robots']['impact']) {
                    $analyzes['robots']['impact'] = 'medium';
                }
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>noarchive</strong> is on! Search engines will not cache your page.', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>noarchive</strong> is off. Search engines will probably cache your page.', 'wp-seopress') . '</p>';
            }

            if (preg_match('/nosnippet/', $encoded)) {
                if ('high' != $analyzes['robots']['impact']) {
                    $analyzes['robots']['impact'] = 'medium';
                }
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>nosnippet</strong> is on! Search engines will not display a snippet of this page in search results.', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nosnippet</strong> is off. Search engines will display a snippet of this page in search results.', 'wp-seopress') . '</p>';
            }
        } else {
            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('We found no meta robots on this page. It means, your page is index,follow. Search engines will index it, and follow links. ', 'wp-seopress') . '</p>';
        }

        //Meta Google
        if (! empty($data['meta_google'])) {
            $meta_google = $data['meta_google'];
            if (preg_match('/nositelinkssearchbox/', wp_json_encode($meta_google))) {
                if ('high' != $analyzes['robots']['impact']) {
                    $analyzes['robots']['impact'] = 'medium';
                }
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('<strong>nositelinkssearchbox</strong> is on! Google will not display a sitelinks searchbox in search results.', 'wp-seopress') . '</p>';
            } else {
                $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nositelinkssearchbox</strong> is off. Google will probably display a sitelinks searchbox in search results.', 'wp-seopress') . '</p>';
            }
        } else {

            $desc .= '<p><span class="dashicons dashicons-yes"></span>' . __('<strong>nositelinkssearchbox</strong> is off. Google will probably display a sitelinks searchbox in search results.', 'wp-seopress') . '</p>';
        }

        $analyzes['robots']['desc'] = $desc;

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
                $analyzes['img_alt']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.', 'wp-seopress') . '</p>';

                //Standard images & galleries
                if (! empty($withoutAlt)) {
                    $desc .= '<ul class="attachments">';
                    foreach ($withoutAlt as $img) {
                        $desc .= '<li class="attachment"><figure><img src="' . esc_url($img) . '"/><figcaption style="word-break: break-all;">'.esc_url($img).'</figcaption></figure></li>';
                    }
                    $desc .= '</ul>';
                }

                $desc .= '<p>' . __('Note that we scan all your source code, it means, some missing alternative texts of images might be located in your header, sidebar or footer.', 'wp-seopress') . '</p>';
            }
            $desc .= '</div>';

            $analyzes['img_alt']['desc'] = $desc;
        } elseif(!empty($withAlt) && empty($withoutAlt)) {
            $analyzes['img_alt']['impact'] = 'good';
            $analyzes['img_alt']['desc'] = '<p><span class="dashicons dashicons-yes"></span>' . __('All alternative tags are filled in. Good work!', 'wp-seopress') . '</p>';
        } elseif (empty($withAlt) && empty($withoutAlt)) {
            $analyzes['img_alt']['impact'] = 'medium';
            $analyzes['img_alt']['desc']   = '<p><span class="dashicons dashicons-no-alt"></span>' . __('We could not find any image in your content. Content with media is a plus for your SEO.', 'wp-seopress') . '</p>';
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
        if (isset($data['links_no_follow']) && is_array($data['links_no_follow']) && !empty($data['links_no_follow'])) {
            $count = count($data['links_no_follow']);

            $desc = '<p>' . /* translators: %d number of nofollow links */ sprintf(esc_html__('We found %d links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:', 'wp-seopress'), $count) . '</p>';
            $desc .= '<ul>';
            foreach ($data['links_no_follow'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_url($link['value']) . '</a><span class="dashicons dashicons-external"></span></li>';
            }
            $desc .= '</ul>';
            $analyzes['nofollow_links']['impact'] = 'good';
            if ($count > 3) {
                $analyzes['nofollow_links']['impact'] = 'low';
            }
            $analyzes['nofollow_links']['desc']   = $desc;
        } else {
            $analyzes['nofollow_links']['desc'] = '<p><span class="dashicons dashicons-yes"></span>' . __('This page doesn\'t have any nofollow links.', 'wp-seopress') . '</p>';
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
        $desc = '<p>' . __('Internet is built on the principle of hyperlink. It is therefore perfectly normal to make links between different websites. However, avoid making links to low quality sites, SPAM... If you are not sure about the quality of a site, add the attribute "nofollow" to your link.') . '</p>';
        if (isset($data['outbound_links']) && is_array($data['outbound_links']) && !empty($data['outbound_links'])) {
            $count = count($data['outbound_links']);

            $desc .= '<p>' . /* translators: %d number of outbound links */ sprintf(__('We found %s outbound links in your page. Below, the list:', 'wp-seopress'), $count) . '</p>';
            $desc .= '<ul>';
            foreach ($data['outbound_links'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_url($link['value']) . '</a><span class="dashicons dashicons-external"></span></li>';
            }
            $desc .= '</ul>';
        } else {
            $analyzes['outbound_links']['impact'] = 'medium';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('This page doesn\'t have any outbound links.', 'wp-seopress') . '</p>';
        }
        $analyzes['outbound_links']['desc'] = $desc;

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
        $desc = '<p>' . __('Internal links are important for SEO and user experience. Always try to link your content together, with quality link anchors.', 'wp-seopress') . '</p>';

        if (isset($data['internal_links']) && is_array($data['internal_links']) && !empty($data['internal_links'])) {
            $count = count($data['internal_links']);

            $desc .= '<p>' . /* translators: %s internal links */ sprintf(__('We found %s internal links to this page.', 'wp-seopress'), $count) . '</p>';

            $desc .= '<ul>';
            foreach ($data['internal_links'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_url($link['value']) . '</a>
                <a class="nounderline" href="' . esc_url(get_edit_post_link($link['id'])) . '" title="' . /* translators: %s link to edit the post */ sprintf(__('edit %s', 'wp-seopress'), esc_html(get_the_title($link['id']))) . '"><span class="dashicons dashicons-edit-large"></span></a></li>';
            }
            $desc .= '</ul>';
        } else {
            $analyzes['internal_links']['impact'] = 'medium';
            $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('This page doesn\'t have any internal links from other content. Links from archive pages are not considered internal links due to lack of context.', 'wp-seopress') . '</p>';
        }
        $analyzes['internal_links']['desc'] = $desc;

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
        $desc = '<p>' . __('A canonical URL is required by search engines to handle duplicate content.') . '</p>';

        if (isset($data['canonical']) && is_array($data['canonical']) && !empty($data['canonical'])) {
            $count = count($data['canonical']);

            $desc .= '<p>' . /* translators: %s number of canonical tags */ sprintf(_n('We found %s canonical URL in your source code. Below, the list:', 'We found %s canonical URLs in your source code. Below, the list:', $count, 'wp-seopress'), number_format_i18n($count)) . '</p>';

            $desc .= '<ul>';
            foreach ($data['canonical'] as $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="' . esc_url($link) . '" target="_blank">' . esc_url($link) . '</a><span class="dashicons dashicons-external"></span></li>';
            }
            $desc .= '</ul>';

            if ($count > 1) {
                $analyzes['all_canonical']['impact'] = 'high';
                $desc .= '<p><span class="dashicons dashicons-no-alt"></span>' . __('You must fix this. Canonical URL duplication is bad for SEO.', 'wp-seopress') . '</p>';
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
            }
        }
        $analyzes['all_canonical']['desc'] = $desc;

        return $analyzes;
    }

    public function getAnalyzes($post)
    {

        $data = seopress_get_service('ContentAnalysisDatabase')->getData($post->ID);

        $analyzes               = ContentAnalysis::getData();

        //Schemas
        $analyzes = $this->analyzeSchemas($analyzes, $data, $post);

        //Old post
        $analyzes = $this->analyzeOldPost($analyzes, $data, $post);

        //Keywords in permalink
        $analyzes = $this->analyzeKeywordsPermalink($analyzes, $data, $post);

        //Headings
        $analyzes = $this->analyzeHeadings($analyzes, $data, $post);

        //Meta Title
        $analyzes = $this->analyzeMetaTitle($analyzes, $data, $post);

        //Meta description
        $analyzes = $this->analyzeMetaDescription($analyzes, $data, $post);

        //Social tags
        $analyzes = $this->analyzeSocialTags($analyzes, $data, $post);

        //Robots
        $analyzes = $this->analyzeRobots($analyzes, $data, $post);

        //Img alt
        $analyzes = $this->analyzeImgAlt($analyzes, $data, $post);

        //Nofollow links
        $analyzes = $this->analyzeNoFollowLinks($analyzes, $data, $post);

        //Outbound links
        $analyzes = $this->analyzeOutboundLinks($analyzes, $data, $post);

        //internal links
        $analyzes = $this->analyzeInternalLinks($analyzes, $data, $post);

        $analyzes = $this->analyzeCanonical($analyzes, $data, $post);

        return $analyzes;
    }
}

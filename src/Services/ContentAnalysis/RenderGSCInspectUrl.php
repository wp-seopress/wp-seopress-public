<?php

namespace SEOPress\Services\ContentAnalysis;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class RenderGSCInspectUrl {
    public function render($id) {
        $data = get_post_meta($id, '_seopress_gsc_inspect_url_data', true);
        if (is_string($data)) {
            $data = json_decode($data);
        }

        //Get Google API Key
        $options            = get_option('seopress_instant_indexing_option_name');
        $google_api_key     = isset($options['seopress_instant_indexing_google_api_key']) ? $options['seopress_instant_indexing_google_api_key'] : '';
        ?>

        <p>
            <?php _e('Inspect the current post URL with Google Search Console and get informations about your indexing, crawling, rich snippets and more.','wp-seopress'); ?>
        </p>

        <button id="seopress_inspect_url" type="button" class="<?php echo seopress_btn_secondary_classes(); ?>"><?php _e('Inspect URL with Google', 'wp-seopress'); ?></button>
        <span class="spinner"></span>

        <?php if (empty($google_api_key)) { ?>

            <p>
                <?php _e('No Google API key found.', 'wp-seopress'); ?>
                <a href="<?php echo admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_inspect_url'); ?>"><?php _e('Fix this!','wp-seopress'); ?></a>
            </p>

        <?php } elseif (empty($data)) {
            ?>
            <p>
                <?php _e('No data found, click Inspect URL button above.', 'wp-seopress'); ?>
            </p>
        <?php } else {
            if (isset($data->error)) {
                if (isset($data->error->message)) {
                ?>
                <p class="seopress-notice is-error"><?php echo '<strong>'.$data->error->code.'</strong>: '.$data->error->message; ?></p>
                <?php } elseif (isset($data->error_description)) { ?>
                    <p class="seopress-notice is-error"><?php echo '<strong>'.$data->error.'</strong>: '.$data->error_description; ?></p>
                <?php }
            } else {
                ?>
                <?php
                //Full report
                $inspectionResultLink = $data->inspectionResult->inspectionResultLink ? $data->inspectionResult->inspectionResultLink : '';
                if (!empty($inspectionResultLink)) {
                    echo '<a href="'.$inspectionResultLink.'" class="'.seopress_btn_secondary_classes().'" target="_blank">'.__('View Full Report','wp-seopress').'</a>';
                }

                //Indexing Verdict
                $verdict = $data->inspectionResult->indexStatusResult->verdict ? $data->inspectionResult->indexStatusResult->verdict : '';
                if (!empty($verdict)) {
                    switch ($verdict) {
                        case 'VERDICT_UNSPECIFIED':
                            $verdict_i18n = '<span class="dashicons dashicons-info"></span>'.__('Unknown verdict', 'wp-seopress');
                            $verdict_i18n_desc = __('The URL has been indexed, can appear in Google Search results, and no problems were found with any enhancements found in the page (structured data, linked AMP pages, and so on).', 'wp-seopress');
                            break;
                        case 'PASS':
                            $verdict_i18n = '<span class="dashicons dashicons-yes-alt"></span>'.__('URL is on Google', 'wp-seopress');
                            $verdict_i18n_desc = __('The URL has been indexed, can appear in Google Search results, and no problems were found with any enhancements found in the page (structured data, linked AMP pages, and so on).', 'wp-seopress');
                            $verdict_class = 'is-success';
                            break;
                        case 'PARTIAL':
                            $verdict_i18n = '<span class="dashicons dashicons-warning"></span>'.__('URL is on Google, but has issues', 'wp-seopress');
                            $verdict_i18n_desc = __('The URL has been indexed and can appear in Google Search results, but there are some problems that might prevent it from appearing with the enhancements that you applied to the page. This might mean a problem with an associated AMP page, or malformed structured data for a rich result (such as a recipe or job posting) on the page.', 'wp-seopress');
                            $verdict_class = 'is-warning';
                            break;
                        case 'FAIL':
                            $verdict_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('URL is not on Google: Indexing errors', 'wp-seopress');
                            $verdict_i18n_desc = __('There was at least one critical error that prevented the URL from being indexed, and it cannot appear in Google Search until those issues are fixed.', 'wp-seopress');
                            $verdict_class = 'is-error';
                            break;
                        case 'NEUTRAL':
                            $verdict_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('URL is not on Google', 'wp-seopress');
                            $verdict_i18n_desc = __('This URL won‘t appear in Google Search results, but we think that was your intention. Common reasons include that the page is password-protected or robots.txt protected, or blocked by a noindex directive.', 'wp-seopress');
                            $verdict_class = 'is-error';
                            break;
                    }
                }

                //Coverage State
                $coverageState = $data->inspectionResult->indexStatusResult->coverageState ? $data->inspectionResult->indexStatusResult->coverageState : '';

                //Indexing State
                $indexingState = $data->inspectionResult->indexStatusResult->indexingState ? $data->inspectionResult->indexStatusResult->indexingState : '';
                if (!empty($indexingState)) {
                    switch ($indexingState) {
                        case 'INDEXING_STATE_UNSPECIFIED':
                            $indexingState_i18n = '<span class="dashicons dashicons-info"></span>'.__('Unknown indexing status.', 'wp-seopress');
                            break;
                        case 'INDEXING_ALLOWED':
                            $indexingState_i18n = '<span class="dashicons dashicons-yes-alt"></span>'.__('Indexing allowed.', 'wp-seopress');
                            break;
                        case 'BLOCKED_BY_META_TAG':
                            $indexingState_i18n = '<span class="dashicons dashicons-warning"></span>'.__('Indexing not allowed, \'noindex\' detected in \'robots\' meta tag.', 'wp-seopress');
                            break;
                        case 'BLOCKED_BY_HTTP_HEADER':
                            $indexingState_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('Indexing not allowed, \'noindex\' detected in \'X-Robots-Tag\' http header.', 'wp-seopress');
                            break;
                        case 'BLOCKED_BY_ROBOTS_TXT':
                            $indexingState_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('Indexing not allowed, blocked to Googlebot with a robots.txt file.', 'wp-seopress');
                            break;
                    }
                }

                //Page Fetch State
                $pageFetchState = $data->inspectionResult->indexStatusResult->pageFetchState ? $data->inspectionResult->indexStatusResult->pageFetchState : '';
                if (!empty($pageFetchState)) {
                    switch ($pageFetchState) {
                        case 'PAGE_FETCH_STATE_UNSPECIFIED':
                            $pageFetchState_i18n = __('Unknown fetch state.', 'wp-seopress');
                            break;
                        case 'SUCCESSFUL':
                            $pageFetchState_i18n = __('Successful fetch.', 'wp-seopress');
                            break;
                        case 'SOFT_404':
                            $pageFetchState_i18n = __('Soft 404.', 'wp-seopress');
                            break;
                        case 'BLOCKED_ROBOTS_TXT':
                            $pageFetchState_i18n = __('Blocked by robots.txt.', 'wp-seopress');
                            break;
                        case 'NOT_FOUND':
                            $pageFetchState_i18n = __('Not found (404).', 'wp-seopress');
                            break;
                        case 'ACCESS_DENIED':
                            $pageFetchState_i18n = __('Blocked due to unauthorized request (401).', 'wp-seopress');
                            break;
                        case 'SERVER_ERROR':
                            $pageFetchState_i18n = __('Server error (5xx).', 'wp-seopress');
                            break;
                        case 'REDIRECT_ERROR':
                            $pageFetchState_i18n = __('Redirection error.', 'wp-seopress');
                            break;
                        case 'ACCESS_FORBIDDEN':
                            $pageFetchState_i18n = __('Blocked due to access forbidden (403).', 'wp-seopress');
                            break;
                        case 'BLOCKED_4XX':
                            $pageFetchState_i18n = __('Blocked due to other 4xx issue (not 403, 404).', 'wp-seopress');
                            break;
                        case 'INTERNAL_CRAWL_ERROR':
                            $pageFetchState_i18n = __('Internal error.', 'wp-seopress');
                            break;
                        case 'INVALID_URL':
                            $pageFetchState_i18n = __('Invalid URL.', 'wp-seopress');
                            break;
                    }
                }

                //Crawl
                $lastCrawlTime = $data->inspectionResult->indexStatusResult->lastCrawlTime ? date("F j, Y - h:i:s A", strtotime($data->inspectionResult->indexStatusResult->lastCrawlTime)) : '';
                $crawledAs = $data->inspectionResult->indexStatusResult->crawledAs ? $data->inspectionResult->indexStatusResult->crawledAs : '';
                if (!empty($crawledAs)) {
                    switch ($crawledAs) {
                        case 'CRAWLING_USER_AGENT_UNSPECIFIED':
                            $crawledAs_i18n = __('Unknown user agent.', 'wp-seopress');
                            break;
                        case 'DESKTOP':
                            $crawledAs_i18n = __('Googlebot desktop', 'wp-seopress');
                            break;
                        case 'MOBILE':
                            $crawledAs_i18n = __('Googlebot smartphone', 'wp-seopress');
                            break;
                    }
                }
                $robotsTxtState = $data->inspectionResult->indexStatusResult->robotsTxtState ? $data->inspectionResult->indexStatusResult->robotsTxtState : '';
                if (!empty($robotsTxtState)) {
                    switch ($robotsTxtState) {
                        case 'ROBOTS_TXT_STATE_UNSPECIFIED':
                            $robotsTxtState = __('Unknown robots.txt state, typically because the page wasn‘t fetched or found, or because robots.txt itself couldn‘t be reached.', 'wp-seopress');
                            break;
                        case 'ALLOWED':
                            $robotsTxtState = __('Yes', 'wp-seopress');
                            break;
                        case 'DISALLOWED':
                            $robotsTxtState = __('Crawl blocked by robots.txt.', 'wp-seopress');
                            break;
                    }
                }


                //Canonical URL
                $userCanonical = $data->inspectionResult->indexStatusResult->userCanonical ? $data->inspectionResult->indexStatusResult->userCanonical : '';
                $googleCanonical = $data->inspectionResult->indexStatusResult->googleCanonical ? $data->inspectionResult->indexStatusResult->googleCanonical : '';

                //Sitemap
                $sitemap = $data->inspectionResult->indexStatusResult->sitemap ? $data->inspectionResult->indexStatusResult->sitemap : __('N/A','wp-seopress');

                //Referring Urls
                $referringUrls = $data->inspectionResult->indexStatusResult->referringUrls ? $data->inspectionResult->indexStatusResult->referringUrls : '';

                //Mobile Verdict
                $verdict_mobile = '';
                if(\property_exists($data, 'inspectionResult') && \property_exists($data->inspectionResult, 'mobileUsabilityResult')) {
                    $verdict_mobile = $data->inspectionResult->mobileUsabilityResult->verdict ? $data->inspectionResult->mobileUsabilityResult->verdict : '';
                }

                $$verdict_mobile_i18n = '';
                $$verdict_mobile_i18n_desc = '';
                if (!empty($verdict_mobile)) {
                    switch ($verdict_mobile) {
                        case 'VERDICT_UNSPECIFIED':
                            $verdict_mobile_i18n = '<span class="dashicons dashicons-info"></span>'.__('No data available', 'wp-seopress');
                            $verdict_mobile_i18n_desc = __('For some reason we couldn‘t retrieve the page or test its mobile-friendliness. Please wait a bit and try again.', 'wp-seopress');
                            break;
                        case 'PASS':
                            $verdict_mobile_i18n = '<span class="dashicons dashicons-yes-alt"></span>'.__('Page is mobile friendly', 'wp-seopress');
                            $verdict_mobile_i18n_desc = __('The page should probably work well on a mobile device.', 'wp-seopress');
                            break;
                        case 'PARTIAL':
                        case 'FAIL':
                        case 'NEUTRAL':
                            $verdict_mobile_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('Page is not mobile friendly', 'wp-seopress');
                            $verdict_mobile_i18n_desc = __('The page won‘t work well on a mobile device because of a few issues.', 'wp-seopress');
                            break;
                    }
                }

                //Rich snippets Verdict
                $detectedItems = __('No detected schemas', 'wp-seopress');
                $verdict_rich_snippets_i18n = __('No data available', 'wp-seopress');
                if (property_exists($data->inspectionResult, 'richResultsResult')) {
                    $verdict_rich_snippets = $data->inspectionResult->richResultsResult->verdict ? $data->inspectionResult->richResultsResult->verdict : '';
                    if (!empty($verdict_rich_snippets)) {
                        switch ($verdict_rich_snippets) {
                            case 'VERDICT_UNSPECIFIED':
                                $verdict_rich_snippets_i18n = '<span class="dashicons dashicons-info"></span>'.__('No data available', 'wp-seopress');
                                break;
                            case 'PASS':
                                $verdict_rich_snippets_i18n = '<span class="dashicons dashicons-yes-alt"></span>'.__('Your Rich Snippets are valid', 'wp-seopress');
                                break;
                            case 'PARTIAL':
                            case 'FAIL':
                            case 'NEUTRAL':
                                $verdict_rich_snippets_i18n = '<span class="dashicons dashicons-dismiss"></span>'.__('Your Rich Snippets are not valid', 'wp-seopress');
                                break;
                        }
                    }
                    $detectedItems = $data->inspectionResult->richResultsResult->detectedItems ? $data->inspectionResult->richResultsResult->detectedItems : '';
                }

                //Render
                $render = [
                    'general' => [
                        'title' => $verdict_i18n,
                        'desc' => $verdict_i18n_desc
                    ],
                    'discovery' => [
                        'title' => __('Discovery','wp-seopress'),
                        'analysis' => [
                            __('Sitemaps','wp-seopress') => $sitemap,
                            __('Referring page','wp-seopress') => $referringUrls,
                        ]
                    ],
                    'crawl' => [
                        'title' => __('Crawl', 'wp-seopress'),
                        'desc' => $coverageState,
                        'analysis' => [
                            __('Last crawl', 'wp-seopress')         => $lastCrawlTime,
                            __('Crawled as', 'wp-seopress')         => $crawledAs_i18n,
                            __('Crawl allowed?', 'wp-seopress')     => $robotsTxtState,
                            __('Page fetch', 'wp-seopress')         => $pageFetchState_i18n,
                            __('Indexing allowed?','wp-seopress')   => $indexingState_i18n,
                        ]
                    ],
                    'indexing' => [
                        'title' => __('Indexing', 'wp-seopress'),
                        'analysis' => [
                            __('User-declared canonical','wp-seopress') => $userCanonical,
                            __('Google-selected canonical','wp-seopress') => $googleCanonical,
                        ]
                    ],
                    'enhancements' => [
                        'title' => __('Enhancements', 'wp-seopress'),
                        'analysis' => [
                            __('Mobile Usability','wp-seopress') => [
                                'verdict' => $verdict_mobile_i18n,
                                'desc' => $verdict_mobile_i18n_desc
                            ],
                            __('Rich Snippets detected','wp-seopress') => [
                                'verdict' => $verdict_rich_snippets_i18n,
                                'schemas' => $detectedItems,
                            ]
                        ]
                    ]
                ];

                if (!empty($render)) {
                    echo '<div class="seopress-gsc-render">';
                    foreach($render as $key_analysis => $analysis) {
                        if ($key_analysis === 'general') { ?>
                            <div class="seopress-gsc-analysis seopress-gsc-summary seopress-notice <?php echo $verdict_class; ?>">
                                <div class="seopress-gsc-verdict"><?php echo $analysis['title']; ?></div>
                                <p><?php echo $analysis['desc']; ?></p>
                            </div>
                        <?php } else {
                            if (!empty($analysis['title'])) { ?>
                                <div class="seopress-gsc-cat"><?php echo $analysis['title']; ?></div>
                            <?php }
                            if (!empty($analysis['desc'])) { ?>
                                <p><?php echo $analysis['desc']; ?></p>
                            <?php }
                            if (!empty($analysis['analysis'])) { ?>
                                <div class="seopress-gsc-analysis">
                                    <?php foreach($analysis['analysis'] as $key => $value) { ?>
                                        <div class="seopress-gsc-item">
                                        <div class="seopress-gsc-item-name"><?php echo $key; ?></div>
                                        <div class="seopress-gsc-item-value">
                                            <?php if (is_array($value)) {
                                                if (!empty($value)) { ?>
                                                    <ul>
                                                    <?php foreach($value as $key_element => $elements) {
                                                        if ($key_element === 'schemas') {
                                                            if (!empty($elements) && is_array($elements)) {
                                                                foreach($elements as $element) {
                                                                    echo '<ul>';
                                                                    if (!empty($element->richResultType)) {
                                                                        echo '<li><strong>' . $element->richResultType.'</strong>';
                                                                    }
                                                                    if (!empty($element->items)) {
                                                                        foreach($element->items as $schemas) {
                                                                            if (!empty($schemas)) {
                                                                                echo '<ul>';
                                                                                foreach($schemas as $schema) {
                                                                                    echo '<li><span class="dashicons dashicons-minus"></span>'.$schema.'</li>';
                                                                                }
                                                                                echo '</ul>';
                                                                            }
                                                                        }
                                                                    }
                                                                    echo '</li></ul>';
                                                                }
                                                            } else {
                                                                echo '<li>'.$elements.'</li>';
                                                            }
                                                        } else {
                                                        ?>
                                                        <li>
                                                            <?php echo $elements; ?>
                                                        </li>
                                                    <?php }
                                                } ?>
                                                </ul>
                                                <?php }
                                            } else {
                                                echo $value;
                                            } ?>
                                        </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php }
                        }
                    }
                    echo '</div>';
                }
            }
        }
    }
}

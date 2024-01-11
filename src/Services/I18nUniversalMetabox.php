<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

class I18nUniversalMetabox
{
    public function getTranslations() {

        return [
            'generic' => [
                'pixels' => __('pixels', 'wp-seopress'),
                'save' => __('Save', 'wp-seopress'),
                'save_settings' => __("Your settings have been saved.", "wp-seopress"),
                'yes' => __("Yes", "wp-seopress"),
                'good' => __("Good", "wp-seopress"),
                'expand' => __("Expand", "wp-seopress"),
                'close' => __("Close", "wp-seopress"),
                'title' => __("Title", "wp-seopress"),
                'twitter' => __("Twitter", "wp-seopress"),
                'maximum_limit' => __("maximum limit", "wp-seopress"),
                'choose_image' => __("Choose an image", "wp-seopress"),
                'opening_hours_morning' => __("Open in the morning?", "wp-seopress"),
                'opening_hours_afternoon' => __("Open in the afternoon?", "wp-seopress"),
                'thumbnail' => __("Thumbnail", "wp-seopress"),
                'x' => __("x", "wp-seopress"),
                'search_tag' => __("Search a tag", "wp-seopress"),
                'loading_data' => __("Loading your data", "wp-seopress")
            ],
            'services' => [
                'social_meta_tags_title' => __("Social meta tags", "wp-seopress"),
                'twitter' => [
                    'title' => __("Twitter Title", "wp-seopress"),
                    'description' => __("Twitter Description", "wp-seopress"),
                    'image' => __("Twitter Image", "wp-seopress"),
                    'missing' => __(
                        /* translators: %s Twitter tag, eg: twitter:title */
                        'Your %s is missing!',
                        "wp-seopress"
                    ),
                    'we_founded' => __("We found", "wp-seopress"),
                    'we_founded_2' => __("in your content.", "wp-seopress"),
                    'help_twitter_title' =>  __(
                        "You should not use more than one twitter:title in your post content to avoid conflicts when sharing on social networks. Twitter will take the last twitter:title tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'help_twitter_description' => __(
                        "You should not use more than one twitter:description in your post content to avoid conflicts when sharing on social networks. Twitter will take the last twitter:description tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'we_founded_tag' => __("We found a", "wp-seopress"),
                    'we_founded_tag_2' => __("tag in your source code.", "wp-seopress"),
                    'tag_empty' => __(
                        /* translators: %s Twitter tag, eg: twitter:title */
                        'Your %s tag is empty!',
                        "wp-seopress"
                    ),

                ],
                'open_graph' => [
                    'title' => __("Open Graph"),
                    'description' => __("Description", "wp-seopress"),
                    'image' => __("Image", "wp-seopress"),
                    'url' => __("URL", "wp-seopress"),
                    'site_name' => __("Site Name", "wp-seopress"),
                    'missing' => __(
                        /* translators: %s Facebook tag, eg: og:title */
                        'Your Open Graph %s is missing!',
                        "wp-seopress"
                    ),
                    'we_founded' => __("We found", "wp-seopress"),
                    'we_founded_2' => __("in your content.", "wp-seopress"),
                    'help_og_title' => __(
                        "You should not use more than one og:title in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:title tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'help_og_description' => __(
                        "You should not use more than one og:description in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:description tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'help_og_url' => __(
                        "You should not use more than one og:url in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:url tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'help_og_site_name' => __(
                        "You should not use more than one og:site_name in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:site_name tag from your source code. Below, the list:",
                        "wp-seopress"
                    ),
                    'we_founded_tag' => __("We found an Open Graph", "wp-seopress"),
                    'we_founded_tag_2' => __("tag in your source code.", "wp-seopress"),
                    'tag_empty' => __(
                        /* translators: %s Facebook tag, eg: og:title */
                        'Your Open Graph %s tag is empty!',
                        "wp-seopress"
                    )
                ],
                'content_analysis' => [
                    'meta_title' => [
                        'title' => __("Meta title", "wp-seopress"),
                        'no_meta_title' => __(
                            "No custom title is set for this post. If the global meta title suits you, you can ignore this recommendation.",
                            "wp-seopress"
                        ),
                        'meta_title_found' => __(
                            "Target keywords were found in the Meta Title.",
                            "wp-seopress"
                        ),
                        'meta_title_found_in' => __(
                            /* translators: %s meta tag found %s times, eg: meta description found 1 time */
                            '%s was found %s times.',
                            "wp-seopress"
                        ),
                        'empty_matches' => __(
                            "None of your target keywords were found in the Meta Title.",
                            "wp-seopress"
                        ),
                        'too_long' => __("Your custom title is too long.", "wp-seopress"),
                        'length' => __("The length of your title is correct.", "wp-seopress")

                    ],
                    'meta_description' => [
                        'title' => __("Meta description", "wp-seopress"),
                        'no_meta_description' => __(
                            "No custom meta description is set for this post. If the global meta description suits you, you can ignore this recommendation.",
                            "wp-seopress"
                        ),
                        'meta_description_found' => __(
                            "Target keywords were found in the Meta description.",
                            "wp-seopress"
                        ),
                        'meta_description_found_in' => __(
                            '%s was found %s times.',
                            "wp-seopress"
                        ),
                        'no_meta_description_found' => __(
                            "None of your target keywords were found in the Meta description.",
                            "wp-seopress"
                        ),
                        'too_long' => __(
                            "You custom meta description is too long.",
                            "wp-seopress"
                        ),
                        'length' => __(
                            "The length of your meta description is correct",
                            "wp-seopress"
                        ),
                    ],
                    'meta_robots' => [
                        'title' => __("Meta robots", "wp-seopress"),
                        'empty_meta_google' => __(
                            "is off. Google will probably display a sitelinks searchbox in search results.",
                            "wp-seopress"
                        ),
                        'empty_metas' => __(
                            "We found no meta robots on this page. It means, your page is index,follow. Search engines will index it, and follow links. ",
                            "wp-seopress"
                        ),
                        'founded_multiple_metas' => __(
                            'We found %s meta robots in your page. There is probably something wrong with your theme!',
                            "wp-seopress"
                        ),
                        'noindex_on' => __(
                            "is on! Search engines can't index this page.",
                            "wp-seopress"
                        ),
                        'noindex_off' => __(
                            "is off. Search engines will index this page.",
                            "wp-seopress"
                        ),
                        'nofollow_on' => __(
                            "is on! Search engines can't follow your links on this page.",
                            "wp-seopress"
                        ),
                        'nofollow_off' => __(
                            "is off. Search engines will follow links on this page.",
                            "wp-seopress"
                        ),
                        'noimageindex_on' => __(
                            "is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).",
                            "wp-seopress"
                        ),
                        'noimageindex_off' => __(
                            "is off. Google will index the images on this page.",
                            "wp-seopress"
                        ),
                        'noarchive_on' => __(
                            "is on! Search engines will not cache your page.",
                            "wp-seopress"
                        ),
                        'noarchive_off' => __(
                            "is off. Search engines will probably cache your page.",
                            "wp-seopress"
                        ),
                        'nosnippet_on' => __(
                            "is on! Search engines will not display a snippet of this page in search results.",
                            "wp-seopress"
                        ),
                        'nosnippet_off' => __(
                            "is off. Search engines will display a snippet of this page in search results.",
                            "wp-seopress"
                        ),
                        'nositelinkssearchbox_on' => __(
                            "is on! Google will not display a sitelinks searchbox in search results.",
                            "wp-seopress"
                        ),
                        'nositelinkssearchbox_off' => __(
                            "is off. Google will probably display a sitelinks searchbox in search results.",
                            "wp-seopress"
                        )
                    ],
                    'outbound_links' => [
                        'title' => __("Outbound Links", "wp-seopress"),
                        'description' => __(
                            'Internet is built on the principle of hyperlink. It is therefore perfectly normal to make links between different websites. However, avoid making links to low quality sites, SPAM... If you are not sure about the quality of a site, add the attribute "nofollow" to your link.'
                        ),
                        'no_outbound_links' => __(
                            "This page doesn't have any outbound links.",
                            "wp-seopress"
                        ),
                        'outbound_links_count' => __(
                            'We found %s outbound links in your page. Below, the list:',
                            "wp-seopress"
                        ),
                    ],
                    'old_post' => [
                        'bad' => __("This post is a little old!", "wp-seopress"),
                        'good' => __(
                            "The last modified date of this article is less than 1 year. Cool!",
                            "wp-seopress"
                        ),
                        'description' => __(
                            "Search engines love fresh content. Update regularly your articles without entirely rewriting your content and give them a boost in search rankings. SEOPress takes care of the technical part.",
                            "wp-seopress"
                        ),
                        'title' => __("Last modified date", "wp-seopress"),
                    ],
                    'headings' => [
                        'head' => __(
                            /* translators: %s heading name, eg: h2, %s heading number, eg: 2 */
                            'Target keywords were found in Heading %s (H%s).',
                            "wp-seopress"
                        ),
                        /* translators: %s heading number, eg: 2 */
                        'heading_hn' => __("Heading H%s", "wp-seopress"),
                        'heading' => __("Heading", "wp-seopress"),
                        'no_heading' => __(
                            'No custom title is set for this post. If the global meta title suits you, you can ignore this recommendation.',
                            "wp-seopress"
                        ),
                        'no_heading_detail' =>__(
                            /* translators: %s heading name, eg: h2, %s heading number, eg: 2 */
                            'No Heading %s (H%s) found in your content. This is required for both SEO and Accessibility!',
                            "wp-seopress"
                        ),
                        'no_target_keywords_detail' => __(
                            /* translators: %s heading name, eg: h2, %s heading number, eg: 2 */
                            'None of your target keywords were found in Heading %s (H%s).',
                            "wp-seopress"
                        ),
                        'match' => __(
                            /* translators: %s heading name found %s times, eg: H2 was found 2 times */
                            '%s was found %s times.',
                            "wp-seopress"
                        ),
                        'count_h1' => __(
                            /* translators: %s number of times a heading is found, eg: 1 */
                            'We found %s Heading 1 (H1) in your content.',
                            "wp-seopress"
                        ),
                        'count_h1_detail' => __(
                            "You should not use more than one H1 heading in your post content. The rule is simple: only one H1 for each web page. It is better for both SEO and accessibility. Below, the list:",
                            "wp-seopress"
                        ),
                        'below_h1' => __("Below the list:", "wp-seopress"),
                        'title' => __("Headings", "wp-seopress"),
                    ],
                    'images' => [
                        'bad' => __(
                            "We could not find any image in your content. Content with media is a plus for your SEO.",
                            "wp-seopress"
                        ),
                        'good' => __(
                            "All alternative tags are filled in. Good work!",
                            "wp-seopress"
                        ),
                        'no_alternative_text' => __(
                            "No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.",
                            "wp-seopress"
                        ),
                        'description_no_alternative_text' => __(
                            "Note that we scan all your source code, it means, some missing alternative texts of images might be located in your header, sidebar or footer.",
                            "wp-seopress"
                        ),
                        'title' => __("Alternative texts of images", "wp-seopress"),
                    ],
                    'internal_links' => [
                        'description' => __(
                            "Internal links are important for SEO and user experience. Always try to link your content together, with quality link anchors."
                        ),
                        'no_internal_links' => __(
                            "This page doesn't have any internal links from other content. Links from archive pages are not considered internal links due to lack of context.",
                            "wp-seopress"
                        ),
                        'internal_links_count' => __(
                            /* translators: %s number of internal links */
                            'We found %s internal links in your page. Below, the list:',
                            "wp-seopress"
                        ),
                        'title' => __("Internal Links", "wp-seopress")
                    ],
                    'kws_permalink' => [
                        'no_apply' => __(
                            "This is your homepage. This check doesn't apply here because there is no slug.",
                            "wp-seopress"
                        ),
                        'bad' => __(
                            "You should add one of your target keyword in your permalink.",
                            "wp-seopress"
                        ),
                        'good' => __(
                            "Cool, one of your target keyword is used in your permalink.",
                            "wp-seopress"
                        ),
                        'description' => __(
                            'Learn more about <a href="https://www.youtube.com/watch?v=Rk4qgQdp2UA" target="_blank">keywords stuffing</a>.',
                            "wp-seopress"
                        ),
                        'title' =>__("Keywords in permalink", "wp-seopress")
                    ],
                    'no_follow_links' => [
                        'founded' => __(
                            /* translators: %s number of times a nofollow link is found, eg: 1 */
                            'We found %s links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:',
                            "wp-seopress"
                        ),
                        'no_founded' => __(
                            "This page doesn't have any nofollow links.",
                            "wp-seopress"
                        ),
                        'title' =>__("NoFollow Links", "wp-seopress")
                    ]

                ],
                'canonical_url' => [
                    'title' => __("Canonical URL", "wp-seopress"),
                    'head' => __(
                        "A canonical URL is required by search engines to handle duplicate content."
                    ,'wp-seopress'),
                    'no_canonical' => __(
                        "This page doesn't have any canonical URL because your post is set to <strong>noindex</strong>. This is normal.",
                        "wp-seopress"
                    ),
                    'no_canonical_no_index' => __(
                        "This page doesn't have any canonical URL.",
                        "wp-seopress"
                    ),
                    /* translators: %d number of times a canonical tag is found, singular form only */
                    'canonicals_found' => __('We found %d canonical URL in your source code. Below, the list:', 'wp-seopress'),
                    /* translators: %d number of times a canonical tag is found, plural form only */
                    'canonicals_found_plural' => __('We found %d canonical URLs in your source code. Below, the list:', 'wp-seopress'),
                    'multiple_canonicals' => __("You must fix this. Canonical URL duplication is bad for SEO.", "wp-seopress"),
                    'duplicated' => __("duplicated schema - x", "wp-seopress"),
                ],
                'schemas' => [
                    'title' => __("Structured Data Types (schemas)", "wp-seopress"),
                    'no_schema' => __("No schemas found in the source code of this page.", "wp-seopress"),
                    'head' => __("We found these schemas in the source code of this page:", "wp-seopress"),
                    'duplicated' => __("duplicated schema - x", "wp-seopress"),

                ]
            ],
            'constants' => [
                'tabs' => [
                    'title_description_meta' => __("Titles & Metas", "wp-seopress"),
                    'content_analysis' => __("Content Analysis", "wp-seopress"),
                    'schemas' => __("Schemas", "wp-seopress")
                ],
                'sub_tabs' => [
                    'title_settings' => __("Title settings", "wp-seopress"),
                    'social' => __("Social", "wp-seopress"),
                    'advanced' => __("Advanced", "wp-seopress"),
                    'redirection' => __("Redirection", "wp-seopress"),
                    'google_news' => __("Google News", "wp-seopress"),
                    'video_sitemap' => __("Video Sitemap", "wp-seopress"),
                    'overview' => __("Overview", "wp-seopress"),
                    'inspect_url' => __("Inspect with Google", "wp-seopress"),
                    'internal_linking' => __("Internal Linking", "wp-seopress"),
                    'schema_manual' => __("Manual", "wp-seopress"),
                ]
            ],
            'seo_bar' => [
                'title' => __("SEO","wp-seopress"),
            ],
            'forms' => [
                'maximum_limit' => __('maximum limit', 'wp-seopress'),
                'maximum_recommended_limit' => __('maximum recommended limit', 'wp-seopress'),
                'meta_title_description' => [
                    'title' => __('Title', 'wp-seopress'),
                    'tooltip_title' => __('Meta Title', 'wp-seopress'),
                    'tooltip_description' => __("Titles are critical to give users a quick insight into the content of a result and why it’s relevant to their query. It's often the primary piece of information used to decide which result to click on, so it's important to use high-quality titles on your web pages.", 'wp-seopress'),
                    'placeholder_title' => __('Enter your title', 'wp-seopress'),
                    'meta_description' => __('Meta description', 'wp-seopress'),
                    'tooltip_description_1' => __( "A meta description tag should generally inform and interest users with a short, relevant summary of what a particular page is about.", 'wp-seopress'),
                    'tooltip_description_2' => __("They are like a pitch that convince the user that the page is exactly what they're looking for.", 'wp-seopress'),
                    'tooltip_description_3' => __("There's no limit on how long a meta description can be, but the search result snippets are truncated as needed, typically to fit the device width.", 'wp-seopress'),
                    'placeholder_description' => __('Enter your description', 'wp-seopress'),
                    'generate_ai' => __('Generate meta with AI', 'wp-seopress'),
                    'generate_ai_title' => __('Generate meta title with AI', 'wp-seopress'),
                    'generate_ai_description' => __('Generate meta description with AI', 'wp-seopress')
                ],
                'repeater_how_to' => [
                    'title_step' => __(
                        "The title of the step (required)",
                        "wp-seopress"
                    ),
                    'description_step' => __(
                        "The text of your step (required)",
                        "wp-seopress"
                    ),
                    'remove_step' => __("Remove step", "wp-seopress"),
                    'add_step' => __("Add step", "wp-seopress")
                ],
                'repeater_negative_notes_review' => [
                    'title' => __(
                        "Your negative statement (required)",
                        "wp-seopress"
                    ),
                    'remove' => __("Remove note", "wp-seopress"),
                    'add' => __("Add a statement", "wp-seopress"),
                ],
                'repeater_positive_notes_review' => [
                    'title' => __(
                        "Your positive statement (required)",
                        "wp-seopress"
                    ),
                    'remove' => __("Remove note", "wp-seopress"),
                    'add' => __("Add a statement", "wp-seopress"),
                ],
            ],
            'google_preview' => [
                'title'  => __('Google Snippet Preview', 'wp-seopress'),
                'tooltip_title' => __('Snippet Preview', 'wp-seopress'),
                'tooltip_description_1' => __('The Google preview is a simulation.', 'wp-seopress'),
                'tooltip_description_2' => __('There is no reliable preview because it depends on the screen resolution, the device used, the expression sought, and Google.', 'wp-seopress'),
                'tooltip_description_3' => __('There is not one snippet for one URL but several.', 'wp-seopress'),
                'tooltip_description_4' => __('All the data in this overview comes directly from your source code.', 'wp-seopress'),
                'tooltip_description_5' => __('This is what the crawlers will see.', 'wp-seopress'),
                'description' => __(
                    "This is what your page will look like in Google search results. You have to publish your post to get the Google Snippet Preview. Note that Google may optionally display an image of your article.",
                    "wp-seopress"
                ),
                'mobile_title' => __("Mobile Preview", "wp-seopress")
            ],
            'components' => [
                'repeated_faq' => [
                    'empty_question' => __(
                        "Empty Question",
                        "wp-seopress"
                    ),
                    'empty_answer' => __(
                        "Empty Answer",
                        "wp-seopress"
                    ),
                    'question' => __(
                        "Question (required)",
                        "wp-seopress"
                    ),
                    'answer' => __(
                        "Answer (required)",
                        "wp-seopress"
                    ),
                    'remove' => __("Remove question", "wp-seopress"),
                    'add' => __("Add question", "wp-seopress")
                ],
            ],
            'layouts' => [
                'meta_robot' => [
                    'title' => __(
                        /* translators: %s documentation URL */
                        "You cannot uncheck a parameter? This is normal, and it's most likely defined in the <a href='%s'>global settings of the plugin.</a>",
                        "wp-seopress"
                    ),
                    'robots_index_description' => __(
                        "Do not display this page in search engine results / XML - HTML sitemaps",
                        "wp-seopress"
                    ),
                    'robots_index_tooltip_title' => __('"noindex" robots meta tag', 'wp-seopress'),
                    'robots_index_tooltip_description_1' => __(
                        'By checking this option, you will add a meta robots tag with the value "noindex".',
                        'wp-seopress'
                    ),
                    'robots_index_tooltip_description_2' => __(
                        'Search engines will not index this URL in the search results.',
                        'wp-seopress'
                    ),
                    'robots_follow_description' => __("Do not follow links for this page", "wp-seopress"),
                    'robots_follow_tooltip_title' => __('"nofollow" robots meta tag', 'wp-seopress'),
                    'robots_follow_tooltip_description_1' => __(
                        'By checking this option, you will add a meta robots tag with the value "nofollow".',
                        'wp-seopress'
                    ),
                    'robots_follow_tooltip_description_2' => __(
                        'Search engines will not follow links from this URL.',
                        'wp-seopress'
                    ),
                    'robots_archive_description' => __(
                        "Do not display a 'Cached' link in the Google search results",
                        "wp-seopress"
                    ),
                    'robots_archive_tooltip_title' => __('"noarchive" robots meta tag', 'wp-seopress'),
                    'robots_archive_tooltip_description_1' => __(
                        'By checking this option, you will add a meta robots tag with the value "noarchive".',
                        'wp-seopress'
                    ),
                    'robots_snippet_description' =>__(
                        "Do not display a description in search results for this page",
                        "wp-seopress"
                    ),
                    'robots_snippet_tooltip_title' => __('"nosnippet" robots meta tag', 'wp-seopress'),
                    'robots_snippet_tooltip_description_1' => __(
                        'By checking this option, you will add a meta robots tag with the value "nosnippet".',
                        'wp-seopress'
                    ),
                    'robots_imageindex_description' => __("Do not index images for this page", "wp-seopress"),
                    'robots_imageindex_tooltip_title' => __('"noimageindex" robots meta tag', 'wp-seopress'),
                    'robots_imageindex_tooltip_description_1' => __(
                        'By checking this option, you will add a meta robots tag with the value "noimageindex".',
                        'wp-seopress'
                    ),
                    'robots_imageindex_tooltip_description_2' => __(
                        'Note that your images can always be indexed if they are linked from other pages.',
                        'wp-seopress'
                    )
                ],
                'inspect_url' => [
                    'description' => __(
                        "Inspect the current post URL with Google Search Console and get informations about your indexing, crawling, rich snippets and more.",
                        "wp-seopress"
                    ),
                    'verdict_unspecified' => [
                        'title' => __("Unknown verdict", "wp-seopress"),
                        'description' =>__(
                            "The URL has been indexed, can appear in Google Search results, and no problems were found with any enhancements found in the page (structured data, linked AMP pages, and so on).",
                            "wp-seopress"
                        )
                    ],
                    'pass' => [
                        'title' => __("URL is on Google", "wp-seopress"),
                        'description' => __(
                            "The URL has been indexed, can appear in Google Search results, and no problems were found with any enhancements found in the page (structured data, linked AMP pages, and so on).",
                            "wp-seopress"
                        )
                    ],
                    'partial' => [
                        'title' => __("URL is on Google, but has issues", "wp-seopress"),
                        'description' => __(
                            "The URL has been indexed and can appear in Google Search results, but there are some problems that might prevent it from appearing with the enhancements that you applied to the page. This might mean a problem with an associated AMP page, or malformed structured data for a rich result (such as a recipe or job posting) on the page.",
                            "wp-seopress"
                        )
                    ],
                    'fail' => [
                        'title' => __(
                            "URL is not on Google: Indexing errors",
                            "wp-seopress"
                        ),
                        'description' => __(
                            "There was at least one critical error that prevented the URL from being indexed, and it cannot appear in Google Search until those issues are fixed.",
                            "wp-seopress"
                        )
                    ],
                    'neutral' => [
                        'title' => __("URL is not on Google", "wp-seopress"),
                        'description' => __(
                            "This URL won‘t appear in Google Search results, but we think that was your intention. Common reasons include that the page is password-protected or robots.txt protected, or blocked by a noindex directive.",
                            "wp-seopress"
                        )
                    ],
                    'indexing_state_unspecified' => __("Unknown indexing status.", "wp-seopress"),
                    'indexing_allowed' => __("Indexing allowed.", "wp-seopress"),
                    'blocked_by_meta_tag' => __(
                        "Indexing not allowed, 'noindex' detected in 'robots' meta tag.",
                        "wp-seopress"
                    ),
                    'blocked_by_http_header' => __(
                        "Indexing not allowed, 'noindex' detected in 'X-Robots-Tag' http header.",
                        "wp-seopress"
                    ),
                    'blocked_by_robots_txt' => __(
                        "Indexing not allowed, blocked to Googlebot with a robots.txt file.",
                        "wp-seopress"
                    ),
                    'page_fetch_state_unspecified' => __("Unknown fetch state.", "wp-seopress"),
                    'successful' => __("Successful fetch.", "wp-seopress"),
                    'soft_404' => __("Soft 404.", "wp-seopress"),
                    'blocked_robots_txt' => __("Blocked by robots.txt.", "wp-seopress"),
                    'not_found' => __("Not found (404).", "wp-seopress"),
                    'access_denied' => __(
                        "Blocked due to unauthorized request (401).",
                        "wp-seopress"
                    ),
                    'server_error' => __("Server error (5xx).", "wp-seopress"),
                    'redirect_error' => __("Redirection error.", "wp-seopress"),
                    'access_forbidden' => __("Blocked due to access forbidden (403).", "wp-seopress"),
                    'blocked_4xx' => __(
                        "Blocked due to other 4xx issue (not 403, 404).",
                        "wp-seopress"
                    ),
                    'internal_crawl_error' => __("Internal error.", "wp-seopress"),
                    'invalid_url' => __("Invalid URL.", "wp-seopress"),
                    'crawling_user_agent_unspecified' => __("Unknown user agent.", "wp-seopress"),
                    'desktop' => __("Googlebot desktop", "wp-seopress"),
                    'mobile' => __("Googlebot smartphone", "wp-seopress"),
                    'robots_txt_state_unspecified' => __(
                        "Unknown robots.txt state, typically because the page wasn‘t fetched or found, or because robots.txt itself couldn‘t be reached.",
                        "wp-seopress"
                    ),
                    'disallowed' => __("Crawl blocked by robots.txt.", "wp-seopress"),
                    'mobile_verdict_unspecified_title' => __("No data available", "wp-seopress"),
                    'mobile_verdict_unspecified_description' => __(
                        "For some reason we couldn't retrieve the page or test its mobile-friendliness. Please wait a bit and try again.",
                        "wp-seopress"
                    ),
                    'mobile_pass_title' => __("Page is mobile friendly", "wp-seopress"),
                    'mobile_pass_description' => __(
                        "The page should probably work well on a mobile device.",
                        "wp-seopress"
                    ),
                    'mobile_fail_title' => __("Page is not mobile friendly", "wp-seopress"),
                    'mobile_fail_description' => __(
                        "The page won‘t work well on a mobile device because of a few issues.",
                        "wp-seopress"
                    ),
                    'rich_snippets_verdict_unspecified'=> __("No data available", "wp-seopress"),
                    'rich_snippets_pass' => __("Your Rich Snippets are valid", "wp-seopress"),
                    'rich_snippets_fail' => __("Your Rich Snippets are not valid", "wp-seopress"),
                    'discovery' => __("Discovery", "wp-seopress"),
                    'discovery_sitemap' => __("Sitemaps", "wp-seopress"),
                    'discovery_referring_urls' => __("Referring page", "wp-seopress"),
                    'crawl' => __("Crawl", "wp-seopress"),
                    'crawl_last_crawl_time' => __("Last crawl", "wp-seopress"),
                    'crawl_crawled_as' => __("Crawled as", "wp-seopress"),
                    'crawl_allowed' => __("Crawl allowed?", "wp-seopress"),
                    'crawl_page_fetch' => __("Page fetch", "wp-seopress"),
                    'crawl_indexing' => __("Indexing allowed?", "wp-seopress"),
                    'indexing_title' => __("Indexing", "wp-seopress"),
                    'indexing_user_canonical' => __("User-declared canonical", "wp-seopress"),
                    'indexing_google_canonical' => __("Google-selected canonical", "wp-seopress"),
                    'enhancements_title' => __("Enhancements", "wp-seopress"),
                    'enhancements_mobile' => __("Mobile Usability", "wp-seopress"),
                    'enhancements_rich_snippets' => __("Rich Snippets detected", "wp-seopress"),
                    'btn_inspect_url' => __("Inspect URL with Google", "wp-seopress"),
                    'notice_empty_api_key' => __(
                        "No data found, click Inspect URL button above.",
                        "wp-seopress"
                    ),
                    'btn_full_report' => __("View Full Report", "wp-seopress")
                ],
                'video_sitemap' => [
                    'btn_remove_video' => __(
                        "Remove video",
                        "wp-seopress"
                    ),
                    'btn_add_video' => __("Add video", "wp-seopress")
                ],
                'internal_linking' => [
                    'matching' => __("Matching word:","wp-seopress"),
                    'description_1' => __(
                        "Internal links are important for SEO and user experience. Always try to link your content together, with quality link anchors.",
                        "wp-seopress"
                    ),
                    'description_2' => __(
                        "Here is a list of articles related to your content, sorted by relevance, that you should link to.",
                        "wp-seopress"
                    ),
                    'no_suggestions' =>  __("No suggestion of internal links.", "wp-seopress"),
                    'copied' => __(
                        "Link copied in the clipboard",
                        "wp-seopress"
                    ),
                    /* translators: %s post title */
                    'copy_link' => __("Copy %s link", "wp-seopress"),
                    'open_link' => __(
                        "Open this link in a new window",
                        "wp-seopress"
                    ),
                    'edit_link' => __(
                        "Edit this link in a new window",
                        "wp-seopress"
                    ),
                    /* translators: %s post title */
                    'edit_link_aria' => __("Edit %s link", "wp-seopress")
                ],
                'content_analysis' => [
                    'description' => __(
                        "Enter a few keywords for analysis to help you write optimized content.",
                        "wp-seopress"
                    ),
                    'description_2' => __(
                        "Writing content for your users is the most important thing! If it doesn‘t feel natural, your visitors will leave your site, Google will know it and your ranking will be affected.",
                        "wp-seopress"
                    ),
                    'title_severity' => __('Degree of severity: %s', 'wp- seopress'),
                    'target_keywords' => __("Target keywords", "wp-seopress"),
                    'target_keywords_tooltip_description' => __(
                        "Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them",
                        "wp-seopress"
                    ),
                    'target_keywords_multiple_usage' => __(
                        'You should avoid using multiple times the same keyword for different pages. Try to consolidate your content into one single page.',
                        "wp-seopress"
                    ),
                    'target_keywords_placeholder' => __(
                        "Enter your target keywords",
                        "wp-seopress"
                    ),
                    'btn_refresh_analysis' => __("Refresh analysis", "wp-seopress"),
                    'help_target_keywords' => __(
                        "To get the most accurate analysis, save your post first. We analyze all of your source code as a search engine would.",
                        "wp-seopress"
                    ),
                    'google_suggestions' => __("Google suggestions", "wp-seopress"),
                    'google_suggestions_tooltip_description' => __(
                        "Enter a keyword, or a phrase, to find the top 10 Google suggestions instantly. This is useful if you want to work with the long tail technique.",
                        "wp-seopress"
                    ),
                    'google_suggestions_placeholder' => __(
                        "Get suggestions from Google",
                        "wp-seopress"
                    ),
                    'get_suggestions' => __("Get suggestions!", "wp-seopress"),
                    'should_be_improved' =>  __("Should be improved", "wp-seopress"),
                    'keyword_singular' => __("The keyword:", "wp-seopress"),
                    'keyword_plural' => __("These keywords:", "wp-seopress"),
                    'already_used_singular' => /* translators: %d number of times a target keyword is used, singular form only */ __("is already used %d time", "wp-seopress"),
                    'already_used_plural' => /* translators: %d number of times a target keyword is used, plural form only */ __("is already used %d times", "wp-seopress"),
                ],
                'schemas_manual' => [
                    'description' => __('It is recommended to enter as many properties as possible to maximize the chances of getting a rich snippet in Google search results.', 'wp-seopress'),
                    'remove' => __("Delete schema", "wp-seopress"),
                    'add' => __("Add a schema", "wp-seopress"),
                ],
                'social' => [
                    'title' => __(
                        "LinkedIn, Instagram, WhatsApp and Pinterest use the same social metadata as Facebook. Twitter does the same if no Twitter cards tags are defined below.",
                        "wp-seopress"
                    ),
                    'facebook_title' => __(
                        "Ask Facebook to update its cache",
                        "wp-seopress"
                    ),
                    'twitter_title' => __(
                        "Preview your Twitter card using the official validator",
                        "wp-seopress"
                    ),
                ],
                'social_preview' => [
                    "facebook" => [
                        "title" => __("Facebook Preview", "wp-seopress"),
                        "description" => __(
                            "This is what your post will look like in Facebook. You have to publish your post to get the Facebook Preview.",
                            "wp-seopress"
                        ),
                        "ratio" => __("Your image ratio is:", "wp-seopress"),
                        "ratio_info" => __("The closer to 1.91 the better.", "wp-seopress"),
                        'img_filesize' => __('Your filesize is: ', 'wp-seopress'),
                        'filesize_is_too_large' => __('This is superior to 300KB. WhatsApp will not use your image.', 'wp-seopress'),
                        "min_size" => __(
                            "Minimun size for Facebook is <strong>200x200px</strong>. Please choose another image.",
                            "wp-seopress"
                        ),
                        "file_support" =>__(
                            "File type not supported by Facebook. Please choose another image.",
                            "wp-seopress"
                        ),
                        "error_image" => __(
                            "File error. Please choose another image.",
                            "wp-seopress"
                        ),
                        "choose_image" =>__("Please choose an image", "wp-seopress"),
                    ],
                    "twitter" => [
                        "title" => __("Twitter Preview", "wp-seopress"),
                        "description" => __(
                            "This is what your post will look like in Twitter. You have to publish your post to get the Twitter Preview.",
                            "wp-seopress"
                        ),
                        "ratio" => __("Your image ratio is:", "wp-seopress"),
                        "ratio_info" =>__(
                            "The closer to 1 the better (with large card, 2 is better).",
                            "wp-seopress"
                        ),
                        "min_size" => __(
                            "Minimun size for Twitter is <strong>144x144px</strong>. Please choose another image.",
                            "wp-seopress"
                        ),
                        "file_support" => __(
                            "File type not supported by Twitter. Please choose another image.",
                            "wp-seopress"
                        ),
                        "error_image" => __(
                            "File error. Please choose another image.",
                            "wp-seopress"
                        ),
                        "choose_image" =>__("Please choose an image", "wp-seopress")

                    ]
                ],
                "advanced" => [
                    'title' => __("Meta robots settings", "wp-seopress"),
                    'tooltip_canonical' => __(
                        "Canonical URL",
                        "wp-seopress"
                    ),
                    'tooltip_canonical_description' => __(
                        "A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site.",
                        "wp-seopress"
                    ),
                    'tooltip_canonical_description_2' => __(
                        "For example, if you have URLs for the same page (for example: example.com?dress=1234 and example.com/dresses/1234), Google chooses one as canonical.",
                        "wp-seopress"
                    ),
                    'tooltip_canonical_description_3' => __(
                        "Note that the pages do not need to be absolutely identical; minor changes in sorting or filtering of list pages do not make the page unique (for example, sorting by price or filtering by item color). The canonical can be in a different domain than a duplicate.",
                        "wp-seopress"
                    )
                ]
            ]
        ];

    }
}


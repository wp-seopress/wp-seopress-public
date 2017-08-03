=== SEOPress ===
Contributors: rainbowgeek
Donate link: https://seopress.org/
Tags: seo, search engine optimization, meta, title, description, keywords, serp, knowledge grah, schema.org, url, redirection, 301, xml sitemap, breadcrumbs, ranking
Requires at least: 4.4+
Tested up to: 4.6
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SEOPress is a simple, fast and powerful SEO plugin for WordPress

== Description ==

SEOPress is a powerful plugin to optimize your SEO, boost your traffic and improve social sharing.<br>

<h3>Features</h3>

<ul>
	<li>Titles</li>
	<li>Meta descriptions</li>
	<li>Open Graph Data</li>
	<li>Google Knowledge Graph</li>
	<li>Twitter Card</li>
	<li>Canonical URL</li> 
	<li>Meta robots (noindex, nofollow, noodp, noimageindex, noarchive, nosnippet)</li>
	<li>Build your custom XML Sitemap to improve search indexing.</li>
	<li>Link your social media accounts to your site.</li>
	<li>Redirections</li>
	<li>Remove stop words (english, french, spanish, german, italian, portuguese)</li>
	<li>Redirect attachment pages to post parent</li>
	<li>Import / Export settings from site to site.</li>
	<li>...</li>
</ul>

<h3>Translation</h3>

<ul>
	<li>English</li>
	<li>French</li>
</ul>

<h3>Subscribe to our newsletter and get a huge discount!</h3>
And get early access to our pro release!
<a href="http://seopress.org/" target="_blank">Subscribe now</a>
== Installation ==

1. Upload 'seopress' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on SEOPress and apply settings.

== Frequently Asked Questions ==
<a href="http://www.seopress.org/support/faq/" target="_blank">Read our FAQ</a>

== Screenshots ==
1. SEOPress Dashboard
2. Titles & Metas
3. XML Sitemap
4. Social
5. Advanced
6. SEOPress Metaboxe: Titles settings
7. SEOPress Metaboxe: Advanced settings
8. SEOPress Metaboxe: Social settings
9. SEOPress Metaboxe: Redirection settings

== Changelog ==
= 0.9 =
* NEW Add OG:URL if Open Graph is enabled
* NEW Add OG:SITE_NAME if Open Graph is enabled
* NEW Add OG:TYPE if Open Graph is enabled
* NEW Add OG:LOCALE if Open Graph is enabled
* NEW Add TWITTER:SITE if Twitter Card is enabled
* NEW Add TWITTER:CREATOR if Twitter Card is enabled
* NEW Add Flush permalinks button in XML Sitemaps settings page
* NEW Add Ping Google manually button for XML Sitemaps
* NEW Add noindex meta robots tag for Author and Date archives
* FIX Display Site Verification meta only on homepage
* FIX Set Search and 404 pages to noindex by default
* FIX Notice Undefined variable: seopress_social_knowledge_img_option
* FIX Notice Undefined variable: seopress_social_accounts_facebook_option
* FIX Notice Undefined variable: seopress_social_accounts_twitter_option
* FIX Notice Undefined variable: seopress_social_accounts_google_option
* FIX Undefined variable: seopress_social_accounts_pinterest_option
* FIX Notice Undefined variable: seopress_social_accounts_instagram_option
* FIX Notice Undefined variable: seopress_social_accounts_youtube_option
* FIX Notice Undefined variable: seopress_social_accounts_linkedin_option 
* FIX Notice Undefined variable: seopress_social_accounts_myspace_option
* FIX Notice Undefined variable: seopress_social_accounts_soundcloud_option
* FIX Notice Undefined variable: seopress_social_accounts_tumblr_option
* FIX Notice: Trying to get property of non-object in inc/functions/options-titles-metas.php on line 253
* FIX Notice: Trying to get property of non-object in inc/functions/options-titles-metas.php on line 355
= 0.8 = 
* NEW Remove stop words in URL (supported languages: EN, FR, ES, DE, IT, PT)
* INFO List last modified posts first in XML sitemaps
* FIX Remove posts marked as noindex in edit post page in XML sitemaps
= 0.7 =
* NEW Redesign SEOPress main page
* NEW Notifications center
* NEW Add XSL in sitemaps for readability
* NEW Add Google site verification option
* NEW Add Bing site verification option
* NEW Add Pinterest site verification option
* NEW Add Yandex site verification option
* INFO Limit items in sitemaps to 1000 for performances
* FIX Notice Undefined variable: seopress_paged
* FIX Website Schema.org in JSON-LD
= 0.6 =
* NEW Add template variable for titles tags and meta descriptions
* INFO Remove screenshots from main directory to assets
* FIX Notice: Trying to get property of non-object in /inc/admin/admin-metaboxes.php on line 11
* FIX Notice: Undefined variable: seopress_titles_the_description
* FIX Notice: Undefined variable: post
* FIX Canonical tag
* FIX Notice: Trying to get property of non-object
= 0.5 =
* NEW Add 301/302 redirections in SEOPress metaboxe for custom post types
* NEW Add Redirect attachment pages to post parent or home if none option
* INFO Use pretty names for Custom Post Types and Custom Taxonomies in all options
= 0.4 =
* NEW Add article:published_time, article:modified_time, og:updated_time metadata
* INFO Add website link
* INFO Improve UI in Google Snippet Preview with live preview
* INFO Improve Canonical URL field
* FIX CSS Google Snippet preview
* FIX Title tag in Google Snipet Preview
= 0.3 =
* INFO Add placeholders
* INFO Improve sanitization
* INFO Set noindex on xml sitemaps
* FIX Text domain for localization
* FIX CSS in Import / Export page
= 0.2 =
* INFO Improve SEOPress Metaboxe UI/UX
* INFO CSS Cleaning
* FIX Titles tag, meta description and meta robots
* FIX Test site visibility in Reading Options before applying Titles & Metas settings
* FIX Security Allowing Direct File Access to plugin files
* FIX No more calling core loading files directly
* FIX Import / Export Tool
= 0.1 =
* NEW Add Title tag for homepage
* NEW Add Meta Description for homepage
* NEW Add Title tag for Single Post Types
* NEW Add Meta Description for Single Post Types
* NEW Add noindex meta robots tag for Single Post Types
* NEW Add nofollow meta robots tag for Single Post Types
* NEW Add Title tag for Taxonomies
* NEW Add Meta Description for Taxonomies
* NEW Add noindex meta robots tag for Taxonomies
* NEW Add nofollow meta robots tag for Taxonomies
* NEW Add Title tag for Author archives
* NEW Add Title tag for Date archives
* NEW Add Title tag for Search archives
* NEW Add Title tag for 404 archives
* NEW Add Title tag for Paged archives
* NEW Add Meta Description for Author archives
* NEW Add Meta Description for Date archives
* NEW Add Meta Description for Search archives
* NEW Add Meta Description for 404 archives
* NEW Add Meta Description for Paged archives
* NEW Add noindex meta robots tag
* NEW Add nofollow meta robots tag
* NEW Add noodp meta robots tag
* NEW Add noimageindex meta google tag
* NEW Add noarchive meta robots tag
* NEW Add nosnippet meta robots tag
* NEW Add Google Knowledge Graph (person, organization, links with social accounts...)
* NEW Add Open Graph Data for Facebook
* NEW Add Twitter Card for Twitter
* NEW Add Sitemap.xml
* NEW Add Import / Export / Reset tool
* Alpha release.
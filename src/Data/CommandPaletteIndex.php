<?php // phpcs:ignore

namespace SEOPress\Data;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command palette index.
 *
 * Single source of truth for every SEOPress setting that should be searchable
 * from the native WP command palette (Cmd/Ctrl+K).
 *
 * Each entry is a plain associative array:
 *   - name      (required) Unique namespaced id ("seopress/…").
 *   - label     (required) Translated primary label shown in the palette.
 *   - keywords  (required) Alternative search terms (array of strings).
 *   - context   (required) Breadcrumb-style hint shown on the right.
 *   - kind      (required) "navigation" | "setting" | "action".
 *   - page      Admin page slug (e.g. "seopress-xml-sitemap").
 *   - tab       Tab hash key (optional).
 *   - field     FieldRow id to scroll/highlight on arrival (optional).
 *   - raw_page  Raw admin path override (used for edit.php?post_type=… links).
 *
 * i18n: labels + keywords + contexts all go through __() so GlotPress picks
 * them up. The index is built at request time, not cached statically, because
 * it depends on the current locale.
 *
 * Extensibility: third-party plugins (starting with wp-seopress-pro) can
 * inject their own commands via the `seopress_command_palette_items` filter.
 *
 * @since 9.8.0
 */
class CommandPaletteIndex {

	/**
	 * Return the full list of commands for the current request.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function all() {
		// Page-level navigation commands are omitted here because WordPress
		// core already generates "Go to: …" commands for every admin menu
		// item (see wp_enqueue_command_palette_assets()).  Adding our own
		// would create visible duplicates in the palette.
		$commands = array_merge(
			self::titles(),
			self::sitemaps(),
			self::social(),
			self::analytics(),
			self::instant_indexing(),
			self::advanced(),
			self::tools()
		);

		/**
		 * Filters the SEOPress command palette index.
		 *
		 * Use this filter to register custom commands from Pro or add-ons.
		 * Each command should follow the shape documented on
		 * {@see \SEOPress\Data\CommandPaletteIndex}.
		 *
		 * @since 9.8.0
		 *
		 * @param array<int,array<string,mixed>> $commands Command list.
		 */
		return apply_filters( 'seopress_command_palette_items', $commands );
	}

	/**
	 * Titles & Metas settings.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function titles() {
		$page = 'seopress-titles';
		return array(
			self::make_setting( 'titles/sep', __( 'Title separator', 'wp-seopress' ), array( 'separator', 'dash', 'pipe', 'title' ), __( 'Titles & Metas › Home', 'wp-seopress' ), $page, 'tab_seopress_titles_home', 'seopress_titles_sep' ),
			self::make_setting( 'titles/home-title', __( 'Homepage site title', 'wp-seopress' ), array( 'home', 'homepage', 'site title' ), __( 'Titles & Metas › Home', 'wp-seopress' ), $page, 'tab_seopress_titles_home', 'seopress_titles_home_site_title' ),
			self::make_setting( 'titles/home-title-alt', __( 'Homepage alternative site title', 'wp-seopress' ), array( 'alternative', 'alt', 'home', 'site title' ), __( 'Titles & Metas › Home', 'wp-seopress' ), $page, 'tab_seopress_titles_home', 'seopress_titles_home_site_title_alt' ),
			self::make_setting( 'titles/home-desc', __( 'Homepage meta description', 'wp-seopress' ), array( 'home', 'description', 'meta' ), __( 'Titles & Metas › Home', 'wp-seopress' ), $page, 'tab_seopress_titles_home', 'seopress_titles_home_site_desc' ),

			// Section-level entries: no field id — these are object values with
			// no single DOM element to highlight; navigating to the tab is enough.
			self::make_setting( 'titles/single', __( 'Post types title & meta templates', 'wp-seopress' ), array( 'post', 'page', 'cpt', 'custom post type', 'template' ), __( 'Titles & Metas › Single', 'wp-seopress' ), $page, 'tab_seopress_titles_single' ),
			self::make_setting( 'titles/tax', __( 'Taxonomies title & meta templates', 'wp-seopress' ), array( 'taxonomy', 'category', 'tag', 'template' ), __( 'Titles & Metas › Taxonomies', 'wp-seopress' ), $page, 'tab_seopress_titles_tax' ),
			self::make_setting( 'titles/archives', __( 'Archive title & meta templates', 'wp-seopress' ), array( 'archive', 'author', 'date', '404', 'search', 'template' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives' ),

			// Archives — known toggles.
			self::make_setting( 'titles/archives-author-disable', __( 'Disable author archives', 'wp-seopress' ), array( 'author', 'archive', 'disable' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_author_disable' ),
			self::make_setting( 'titles/archives-author-noindex', __( 'Noindex author archives', 'wp-seopress' ), array( 'author', 'noindex', 'archive' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_author_noindex' ),
			self::make_setting( 'titles/archives-date-disable', __( 'Disable date archives', 'wp-seopress' ), array( 'date', 'archive', 'disable' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_date_disable' ),
			self::make_setting( 'titles/archives-date-noindex', __( 'Noindex date archives', 'wp-seopress' ), array( 'date', 'noindex', 'archive' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_date_noindex' ),
			self::make_setting( 'titles/archives-search-noindex', __( 'Noindex search results', 'wp-seopress' ), array( 'search', 'noindex' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_search_title_noindex' ),
			self::make_setting( 'titles/archives-404-title', __( '404 page title', 'wp-seopress' ), array( '404', 'error', 'not found' ), __( 'Titles & Metas › Archives', 'wp-seopress' ), $page, 'tab_seopress_titles_archives', 'seopress_titles_archives_404_title' ),

			// Advanced.
			self::make_setting( 'titles/advanced-noindex', __( 'Global noindex', 'wp-seopress' ), array( 'noindex', 'global', 'site' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_noindex' ),
			self::make_setting( 'titles/advanced-nofollow', __( 'Global nofollow', 'wp-seopress' ), array( 'nofollow', 'global', 'links' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_nofollow' ),
			self::make_setting( 'titles/advanced-nosnippet', __( 'Global nosnippet', 'wp-seopress' ), array( 'nosnippet', 'snippet' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_nosnippet' ),
			self::make_setting( 'titles/advanced-noimageindex', __( 'Noimageindex', 'wp-seopress' ), array( 'noimageindex', 'images' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_noimageindex' ),
			self::make_setting( 'titles/advanced-paged-noindex', __( 'Noindex paginated pages', 'wp-seopress' ), array( 'pagination', 'paged', 'noindex' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_paged_noindex' ),
			self::make_setting( 'titles/advanced-paged-rel', __( 'rel prev / rel next on pagination', 'wp-seopress' ), array( 'rel', 'prev', 'next', 'pagination' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_paged_rel' ),
			self::make_setting( 'titles/advanced-no-sitelinks', __( 'Disable sitelinks searchbox', 'wp-seopress' ), array( 'sitelinks', 'searchbox', 'schema' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_nositelinkssearchbox' ),
			self::make_setting( 'titles/advanced-attachments-noindex', __( 'Noindex attachments', 'wp-seopress' ), array( 'attachment', 'noindex', 'media' ), __( 'Titles & Metas › Advanced', 'wp-seopress' ), $page, 'tab_seopress_titles_advanced', 'seopress_titles_attachments_noindex' ),
		);
	}

	/**
	 * XML / HTML Sitemap settings.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function sitemaps() {
		$page = 'seopress-xml-sitemap';
		$gen  = 'tab_seopress_sitemaps_general';
		$ctx  = __( 'XML Sitemap › General', 'wp-seopress' );
		$html = 'tab_seopress_sitemaps_html';
		$hctx = __( 'XML Sitemap › HTML Sitemap', 'wp-seopress' );

		return array(
			self::make_setting( 'sitemap/xml-enable', __( 'Enable XML Sitemap', 'wp-seopress' ), array( 'sitemap', 'xml', 'enable', 'activate' ), $ctx, $page, $gen, 'seopress_xml_sitemap_general_enable' ),
			self::make_setting( 'sitemap/image', __( 'Enable XML Image Sitemap', 'wp-seopress' ), array( 'image', 'sitemap', 'xml', 'photo' ), $ctx, $page, $gen, 'seopress_xml_sitemap_img_enable' ),
			// Video sitemap is a PRO feature — registered by wp-seopress-pro.
			self::make_setting( 'sitemap/author', __( 'Enable Author Sitemap', 'wp-seopress' ), array( 'author', 'sitemap' ), $ctx, $page, $gen, 'seopress_xml_sitemap_author_enable' ),
			self::make_setting( 'sitemap/html-enable', __( 'Enable HTML Sitemap', 'wp-seopress' ), array( 'html', 'sitemap', 'users' ), $ctx, $page, $gen, 'seopress_xml_sitemap_html_enable' ),
			self::make_setting( 'sitemap/post-types', __( 'Post types in sitemap', 'wp-seopress' ), array( 'post types', 'cpt', 'include', 'exclude' ), __( 'XML Sitemap › Post Types', 'wp-seopress' ), $page, 'tab_seopress_sitemaps_post_types', 'seopress_xml_sitemap_post_types_list' ),
			self::make_setting( 'sitemap/taxonomies', __( 'Taxonomies in sitemap', 'wp-seopress' ), array( 'taxonomies', 'categories', 'tags', 'include' ), __( 'XML Sitemap › Taxonomies', 'wp-seopress' ), $page, 'tab_seopress_sitemaps_taxonomies', 'seopress_xml_sitemap_taxonomies_list' ),
			self::make_setting( 'sitemap/html-mapping', __( 'HTML sitemap mapping', 'wp-seopress' ), array( 'html', 'mapping', 'post types', 'taxonomies' ), $hctx, $page, $html, 'seopress_xml_sitemap_html_mapping' ),
			self::make_setting( 'sitemap/html-exclude', __( 'Exclude posts from HTML sitemap', 'wp-seopress' ), array( 'exclude', 'posts', 'ids', 'html' ), $hctx, $page, $html, 'seopress_xml_sitemap_html_exclude' ),
			self::make_setting( 'sitemap/html-orderby', __( 'HTML sitemap order by', 'wp-seopress' ), array( 'order by', 'sort', 'html' ), $hctx, $page, $html, 'seopress_xml_sitemap_html_orderby' ),
			self::make_setting( 'sitemap/html-order', __( 'HTML sitemap order', 'wp-seopress' ), array( 'order', 'asc', 'desc', 'html' ), $hctx, $page, $html, 'seopress_xml_sitemap_html_order' ),
			self::make_setting( 'sitemap/html-date', __( 'Show date in HTML sitemap', 'wp-seopress' ), array( 'date', 'html' ), $hctx, $page, $html, 'seopress_xml_sitemap_html_date' ),
		);
	}

	/**
	 * Social Networks settings.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function social() {
		$page = 'seopress-social';
		return array(
			// Knowledge Graph.
			self::make_setting( 'social/knowledge-type', __( 'Knowledge Graph type', 'wp-seopress' ), array( 'knowledge graph', 'schema', 'person', 'organization', 'type' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_type' ),
			self::make_setting( 'social/knowledge-name', __( 'Knowledge Graph name', 'wp-seopress' ), array( 'knowledge graph', 'name', 'brand' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_name' ),
			self::make_setting( 'social/knowledge-image', __( 'Knowledge Graph image / logo', 'wp-seopress' ), array( 'knowledge graph', 'image', 'logo' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_img' ),
			self::make_setting( 'social/knowledge-phone', __( 'Knowledge Graph phone number', 'wp-seopress' ), array( 'knowledge graph', 'phone', 'contact' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_phone' ),
			self::make_setting( 'social/knowledge-email', __( 'Knowledge Graph email', 'wp-seopress' ), array( 'knowledge graph', 'email', 'contact' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_email' ),
			self::make_setting( 'social/knowledge-tax-id', __( 'Knowledge Graph tax / VAT ID', 'wp-seopress' ), array( 'knowledge graph', 'tax', 'vat', 'siret' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_tax_id' ),
			self::make_setting( 'social/knowledge-legal-name', __( 'Knowledge Graph legal name', 'wp-seopress' ), array( 'knowledge graph', 'legal', 'name', 'company' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_legal_name' ),
			self::make_setting( 'social/knowledge-founding-date', __( 'Knowledge Graph founding date', 'wp-seopress' ), array( 'knowledge graph', 'founding', 'date', 'created' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_founding_date' ),
			self::make_setting( 'social/knowledge-employees', __( 'Knowledge Graph number of employees', 'wp-seopress' ), array( 'knowledge graph', 'employees', 'staff', 'team' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_employees' ),
			self::make_setting( 'social/knowledge-street', __( 'Knowledge Graph street address', 'wp-seopress' ), array( 'knowledge graph', 'address', 'street', 'location' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_street' ),
			self::make_setting( 'social/knowledge-locality', __( 'Knowledge Graph city', 'wp-seopress' ), array( 'knowledge graph', 'address', 'city', 'locality' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_locality' ),
			self::make_setting( 'social/knowledge-region', __( 'Knowledge Graph region', 'wp-seopress' ), array( 'knowledge graph', 'address', 'region', 'state' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_region' ),
			self::make_setting( 'social/knowledge-postal-code', __( 'Knowledge Graph postal code', 'wp-seopress' ), array( 'knowledge graph', 'address', 'postal', 'zip' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_postal_code' ),
			self::make_setting( 'social/knowledge-country', __( 'Knowledge Graph country', 'wp-seopress' ), array( 'knowledge graph', 'address', 'country' ), __( 'Social › Knowledge Graph', 'wp-seopress' ), $page, 'tab_seopress_social_knowledge', 'seopress_social_knowledge_country' ),

			// Social accounts.
			self::make_setting( 'social/accounts-facebook', __( 'Facebook page URL', 'wp-seopress' ), array( 'facebook', 'url', 'account', 'sameas' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_facebook' ),
			self::make_setting( 'social/accounts-twitter', __( 'X / Twitter URL', 'wp-seopress' ), array( 'twitter', 'x', 'url', 'account' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_twitter' ),
			self::make_setting( 'social/accounts-instagram', __( 'Instagram URL', 'wp-seopress' ), array( 'instagram', 'url', 'account' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_instagram' ),
			self::make_setting( 'social/accounts-youtube', __( 'YouTube URL', 'wp-seopress' ), array( 'youtube', 'url', 'account' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_youtube' ),
			self::make_setting( 'social/accounts-linkedin', __( 'LinkedIn URL', 'wp-seopress' ), array( 'linkedin', 'url', 'account' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_linkedin' ),
			self::make_setting( 'social/accounts-pinterest', __( 'Pinterest URL', 'wp-seopress' ), array( 'pinterest', 'url', 'account' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_pinterest' ),
			self::make_setting( 'social/accounts-extra', __( 'Additional accounts (sameAs)', 'wp-seopress' ), array( 'sameas', 'extra', 'additional', 'social' ), __( 'Social › Your accounts', 'wp-seopress' ), $page, 'tab_seopress_social_accounts', 'seopress_social_accounts_extra' ),

			// Facebook / Open Graph.
			self::make_setting( 'social/facebook-og', __( 'Enable Open Graph Data', 'wp-seopress' ), array( 'open graph', 'og', 'facebook', 'social' ), __( 'Social › Facebook', 'wp-seopress' ), $page, 'tab_seopress_social_facebook', 'seopress_social_facebook_og' ),
			self::make_setting( 'social/facebook-image', __( 'Default Open Graph image', 'wp-seopress' ), array( 'facebook', 'og image', 'default' ), __( 'Social › Facebook', 'wp-seopress' ), $page, 'tab_seopress_social_facebook', 'seopress_social_facebook_img' ),
			self::make_setting( 'social/facebook-app-id', __( 'Facebook App ID', 'wp-seopress' ), array( 'facebook', 'app id', 'application' ), __( 'Social › Facebook', 'wp-seopress' ), $page, 'tab_seopress_social_facebook', 'seopress_social_facebook_app_id' ),
			self::make_setting( 'social/facebook-admin-id', __( 'Facebook Admin ID', 'wp-seopress' ), array( 'facebook', 'admin id' ), __( 'Social › Facebook', 'wp-seopress' ), $page, 'tab_seopress_social_facebook', 'seopress_social_facebook_admin_id' ),

			// Twitter / X.
			self::make_setting( 'social/twitter-card', __( 'Enable X Cards', 'wp-seopress' ), array( 'twitter', 'x card', 'summary card', 'social' ), __( 'Social › X / Twitter', 'wp-seopress' ), $page, 'tab_seopress_social_twitter', 'seopress_social_twitter_card' ),
			self::make_setting( 'social/twitter-fallback-og', __( 'Use Open Graph as Twitter fallback', 'wp-seopress' ), array( 'twitter', 'fallback', 'open graph' ), __( 'Social › X / Twitter', 'wp-seopress' ), $page, 'tab_seopress_social_twitter', 'seopress_social_twitter_card_og' ),
			self::make_setting( 'social/twitter-image', __( 'Default X Card image', 'wp-seopress' ), array( 'twitter', 'x card', 'image', 'default' ), __( 'Social › X / Twitter', 'wp-seopress' ), $page, 'tab_seopress_social_twitter', 'seopress_social_twitter_card_img' ),
			self::make_setting( 'social/twitter-image-size', __( 'X Card image size', 'wp-seopress' ), array( 'twitter', 'x card', 'size', 'summary' ), __( 'Social › X / Twitter', 'wp-seopress' ), $page, 'tab_seopress_social_twitter', 'seopress_social_twitter_card_img_size' ),

			// LinkedIn.
			self::make_setting( 'social/linkedin-size', __( 'LinkedIn image size', 'wp-seopress' ), array( 'linkedin', 'size', 'image' ), __( 'Social › LinkedIn', 'wp-seopress' ), $page, 'tab_seopress_social_linkedin', 'seopress_social_li_img_size' ),
		);
	}

	/**
	 * Analytics settings (Google Analytics / Matomo / Clarity / GDPR).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function analytics() {
		$page = 'seopress-google-analytics';
		return array(
			// Google Analytics.
			self::make_setting( 'analytics/ga-enable', __( 'Enable Google Analytics tracking', 'wp-seopress' ), array( 'google analytics', 'ga4', 'tracking', 'gtag' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_enable' ),
			self::make_setting( 'analytics/ga4-id', __( 'Google Analytics measurement ID (GA4)', 'wp-seopress' ), array( 'ga4', 'measurement id', 'tracking id' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_ga4' ),
			self::make_setting( 'analytics/ga-ads', __( 'Google Ads conversion ID', 'wp-seopress' ), array( 'google ads', 'adwords', 'conversion' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_ads' ),

			// Tracking exclusions + options.
			self::make_setting( 'analytics/disable-tracking', __( 'Disable tracking', 'wp-seopress' ), array( 'disable', 'stop', 'tracking' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_disable' ),
			self::make_setting( 'analytics/half-disable', __( 'Disable tracking for logged-in users', 'wp-seopress' ), array( 'logged-in', 'users', 'tracking' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_half_disable' ),
			self::make_setting( 'analytics/roles', __( 'Exclude user roles from tracking', 'wp-seopress' ), array( 'roles', 'exclude', 'tracking' ), __( 'Analytics › Google Analytics', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_roles' ),

			// Custom dimensions.
			self::make_setting( 'analytics/cd-author', __( 'Custom dimension — author', 'wp-seopress' ), array( 'custom dimension', 'author' ), __( 'Analytics › Custom dimensions', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_cd_author' ),
			self::make_setting( 'analytics/cd-category', __( 'Custom dimension — category', 'wp-seopress' ), array( 'custom dimension', 'category' ), __( 'Analytics › Custom dimensions', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_cd_category' ),
			self::make_setting( 'analytics/cd-tag', __( 'Custom dimension — tag', 'wp-seopress' ), array( 'custom dimension', 'tag' ), __( 'Analytics › Custom dimensions', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_cd_tag' ),
			self::make_setting( 'analytics/cd-post-type', __( 'Custom dimension — post type', 'wp-seopress' ), array( 'custom dimension', 'post type' ), __( 'Analytics › Custom dimensions', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_cd_post_type' ),
			self::make_setting( 'analytics/cd-logged-in-user', __( 'Custom dimension — logged-in user', 'wp-seopress' ), array( 'custom dimension', 'logged in', 'user' ), __( 'Analytics › Custom dimensions', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_cd_logged_in_user' ),

			// Event tracking.
			self::make_setting( 'analytics/link-tracking', __( 'External link tracking', 'wp-seopress' ), array( 'link', 'outbound', 'tracking' ), __( 'Analytics › Events', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_link_tracking_enable' ),
			self::make_setting( 'analytics/download-tracking', __( 'Download tracking', 'wp-seopress' ), array( 'download', 'file', 'tracking' ), __( 'Analytics › Events', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_download_tracking_enable' ),
			self::make_setting( 'analytics/affiliate-tracking', __( 'Affiliate link tracking', 'wp-seopress' ), array( 'affiliate', 'tracking' ), __( 'Analytics › Events', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_affiliate_tracking_enable' ),
			self::make_setting( 'analytics/phone-tracking', __( 'Phone number tracking', 'wp-seopress' ), array( 'phone', 'tel', 'tracking' ), __( 'Analytics › Events', 'wp-seopress' ), $page, 'tab_seopress_analytics_google', 'seopress_google_analytics_phone_tracking' ),

			// Matomo.
			self::make_setting( 'analytics/matomo-enable', __( 'Enable Matomo tracking', 'wp-seopress' ), array( 'matomo', 'piwik', 'tracking' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_enable' ),
			self::make_setting( 'analytics/matomo-self-hosted', __( 'Self-hosted Matomo', 'wp-seopress' ), array( 'matomo', 'self-hosted' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_self_hosted' ),
			self::make_setting( 'analytics/matomo-id', __( 'Matomo URL', 'wp-seopress' ), array( 'matomo', 'url', 'domain' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_id' ),
			self::make_setting( 'analytics/matomo-site-id', __( 'Matomo site ID', 'wp-seopress' ), array( 'matomo', 'site id' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_site_id' ),
			self::make_setting( 'analytics/matomo-no-cookies', __( 'Disable Matomo cookies', 'wp-seopress' ), array( 'matomo', 'cookies', 'disable' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_no_cookies' ),
			self::make_setting( 'analytics/matomo-dnt', __( 'Respect Do Not Track (Matomo)', 'wp-seopress' ), array( 'matomo', 'dnt', 'do not track' ), __( 'Analytics › Matomo', 'wp-seopress' ), $page, 'tab_seopress_analytics_matomo', 'seopress_google_analytics_matomo_dnt' ),

			// Clarity.
			self::make_setting( 'analytics/clarity-enable', __( 'Enable Microsoft Clarity', 'wp-seopress' ), array( 'clarity', 'microsoft', 'heatmap', 'tracking' ), __( 'Analytics › Clarity', 'wp-seopress' ), $page, 'tab_seopress_analytics_clarity', 'seopress_google_analytics_clarity_enable' ),
			self::make_setting( 'analytics/clarity-id', __( 'Clarity project ID', 'wp-seopress' ), array( 'clarity', 'project id' ), __( 'Analytics › Clarity', 'wp-seopress' ), $page, 'tab_seopress_analytics_clarity', 'seopress_google_analytics_clarity_project_id' ),

			// GDPR.
			self::make_setting( 'analytics/gdpr-message', __( 'Cookie banner message', 'wp-seopress' ), array( 'gdpr', 'cookie', 'banner', 'consent' ), __( 'Analytics › Cookie banner', 'wp-seopress' ), $page, 'tab_seopress_analytics_gdpr', 'seopress_google_analytics_opt_out_msg' ),
			self::make_setting( 'analytics/gdpr-position', __( 'Cookie banner position', 'wp-seopress' ), array( 'gdpr', 'cookie', 'banner', 'position' ), __( 'Analytics › Cookie banner', 'wp-seopress' ), $page, 'tab_seopress_analytics_gdpr', 'seopress_google_analytics_cb_pos' ),

			// Additional tracking code.
			self::make_setting( 'analytics/other-tracking', __( 'Other tracking code (head)', 'wp-seopress' ), array( 'head', 'tracking', 'pixel', 'custom', 'javascript' ), __( 'Analytics › Tracking code', 'wp-seopress' ), $page, 'tab_seopress_analytics_tracking', 'seopress_google_analytics_other_tracking' ),
			self::make_setting( 'analytics/other-tracking-body', __( 'Other tracking code (body)', 'wp-seopress' ), array( 'body', 'tracking', 'pixel', 'custom' ), __( 'Analytics › Tracking code', 'wp-seopress' ), $page, 'tab_seopress_analytics_tracking', 'seopress_google_analytics_other_tracking_body' ),
			self::make_setting( 'analytics/other-tracking-footer', __( 'Other tracking code (footer)', 'wp-seopress' ), array( 'footer', 'tracking', 'pixel', 'custom' ), __( 'Analytics › Tracking code', 'wp-seopress' ), $page, 'tab_seopress_analytics_tracking', 'seopress_google_analytics_other_tracking_footer' ),
		);
	}

	/**
	 * Instant Indexing settings.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function instant_indexing() {
		$page = 'seopress-instant-indexing';
		return array(
			self::make_setting( 'indexing/automate', __( 'Automate URL submission to search engines', 'wp-seopress' ), array( 'automate', 'submit', 'indexing' ), __( 'Instant Indexing › General', 'wp-seopress' ), $page, 'tab_seopress_instant_indexing_general', 'seopress_instant_indexing_automate_submission' ),
			self::make_setting( 'indexing/google-action', __( 'Google Indexing action type', 'wp-seopress' ), array( 'google', 'action', 'update', 'delete' ), __( 'Instant Indexing › General', 'wp-seopress' ), $page, 'tab_seopress_instant_indexing_general', 'seopress_instant_indexing_google_action' ),
			self::make_setting( 'indexing/manual-batch', __( 'Manual URL batch submission', 'wp-seopress' ), array( 'manual', 'batch', 'submit' ), __( 'Instant Indexing › General', 'wp-seopress' ), $page, 'tab_seopress_instant_indexing_general', 'seopress_instant_indexing_manual_batch' ),
			self::make_setting( 'indexing/google-key', __( 'Google Indexing API key (JSON)', 'wp-seopress' ), array( 'google', 'api key', 'json', 'service account' ), __( 'Instant Indexing › Settings', 'wp-seopress' ), $page, 'tab_seopress_instant_indexing_settings', 'seopress_instant_indexing_google_api_key' ),
			self::make_setting( 'indexing/bing-key', __( 'Bing / IndexNow API key', 'wp-seopress' ), array( 'bing', 'indexnow', 'api key' ), __( 'Instant Indexing › Settings', 'wp-seopress' ), $page, 'tab_seopress_instant_indexing_settings', 'seopress_instant_indexing_bing_api_key' ),
		);
	}

	/**
	 * Advanced settings (image SEO, security, appearance, cleanup…).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function advanced() {
		$page = 'seopress-advanced';
		$img  = 'tab_seopress_advanced_image';
		$sec  = 'tab_seopress_advanced_security';
		$app  = 'tab_seopress_advanced_appearance';
		$adv  = 'tab_seopress_advanced_advanced';

		return array(
			// Image SEO.
			self::make_setting( 'advanced/attachments', __( 'Redirect attachment pages', 'wp-seopress' ), array( 'attachment', 'redirect', 'media' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_attachments' ),
			self::make_setting( 'advanced/attachments-file', __( 'Redirect attachments to file URL', 'wp-seopress' ), array( 'attachment', 'file', 'url' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_attachments_file' ),
			self::make_setting( 'advanced/clean-filename', __( 'Clean media filenames on upload', 'wp-seopress' ), array( 'clean', 'filename', 'accents', 'upload' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_clean_filename' ),
			self::make_setting( 'advanced/image-auto-title', __( 'Auto-fill image title', 'wp-seopress' ), array( 'image', 'title', 'auto' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_title_editor' ),
			self::make_setting( 'advanced/image-auto-alt', __( 'Auto-fill image alt', 'wp-seopress' ), array( 'image', 'alt', 'auto', 'accessibility' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_alt_editor' ),
			self::make_setting( 'advanced/image-auto-alt-kw', __( 'Image alt from target keywords', 'wp-seopress' ), array( 'image', 'alt', 'keyword' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_alt_target_kw' ),
			self::make_setting( 'advanced/image-auto-alt-txt', __( 'Image alt custom text', 'wp-seopress' ), array( 'image', 'alt', 'custom', 'text' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_alt_txt' ),
			self::make_setting( 'advanced/image-auto-caption', __( 'Auto-fill image caption', 'wp-seopress' ), array( 'image', 'caption', 'auto' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_caption_editor' ),
			self::make_setting( 'advanced/image-auto-desc', __( 'Auto-fill image description', 'wp-seopress' ), array( 'image', 'description', 'auto' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_image_auto_desc_editor' ),
			self::make_setting( 'advanced/tax-desc-editor', __( 'Taxonomy description editor', 'wp-seopress' ), array( 'taxonomy', 'description', 'editor', 'rich text' ), __( 'Advanced › Image SEO', 'wp-seopress' ), $page, $img, 'seopress_advanced_advanced_tax_desc_editor' ),

			// Security / roles.
			self::make_setting( 'advanced/metaboxe-role', __( 'SEO metabox permissions', 'wp-seopress' ), array( 'metabox', 'role', 'permission' ), __( 'Advanced › Security', 'wp-seopress' ), $page, $sec, 'seopress_advanced_security_metaboxe_role' ),
			self::make_setting( 'advanced/metaboxe-ca-role', __( 'Content analysis permissions', 'wp-seopress' ), array( 'content analysis', 'role', 'permission' ), __( 'Advanced › Security', 'wp-seopress' ), $page, $sec, 'seopress_advanced_security_metaboxe_ca_role' ),

			// Appearance.
			self::make_setting( 'advanced/adminbar', __( 'Remove SEOPress from admin bar', 'wp-seopress' ), array( 'admin bar', 'toolbar', 'hide' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_adminbar' ),
			self::make_setting( 'advanced/adminbar-counter', __( 'Hide admin bar notifications counter', 'wp-seopress' ), array( 'counter', 'notifications', 'admin bar' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_adminbar_counter' ),
			self::make_setting( 'advanced/universal-metabox', __( 'Universal SEO metabox', 'wp-seopress' ), array( 'universal', 'metabox', 'gutenberg' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_universal_metabox' ),
			self::make_setting( 'advanced/content-analysis-metabox', __( 'Content analysis metabox', 'wp-seopress' ), array( 'content analysis', 'metabox' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_ca_metaboxe' ),
			self::make_setting( 'advanced/title-col', __( 'Show title column in post lists', 'wp-seopress' ), array( 'column', 'title', 'list' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_title_col' ),
			self::make_setting( 'advanced/meta-desc-col', __( 'Show meta description column', 'wp-seopress' ), array( 'column', 'meta description', 'list' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_meta_desc_col' ),
			self::make_setting( 'advanced/score-col', __( 'Show SEO score column', 'wp-seopress' ), array( 'column', 'score', 'content analysis' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_score_col' ),
			self::make_setting( 'advanced/noindex-col', __( 'Show noindex column', 'wp-seopress' ), array( 'column', 'noindex' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_noindex_col' ),
			self::make_setting( 'advanced/nofollow-col', __( 'Show nofollow column', 'wp-seopress' ), array( 'column', 'nofollow' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_nofollow_col' ),
			self::make_setting( 'advanced/canonical-col', __( 'Show canonical column', 'wp-seopress' ), array( 'column', 'canonical' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_canonical' ),
			self::make_setting( 'advanced/redirect-col', __( 'Show redirect column', 'wp-seopress' ), array( 'column', 'redirect' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_redirect_enable_col' ),
			self::make_setting( 'advanced/target-kw-col', __( 'Show target keywords column', 'wp-seopress' ), array( 'column', 'target keyword' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_target_kw_col' ),
			self::make_setting( 'advanced/freeze-modified', __( 'Freeze modified date option', 'wp-seopress' ), array( 'freeze', 'modified date' ), __( 'Advanced › Appearance', 'wp-seopress' ), $page, $app, 'seopress_advanced_appearance_freeze_modified_date' ),

			// Advanced tab — WordPress cleanup.
			self::make_setting( 'advanced/wp-generator', __( 'Remove WP generator meta tag', 'wp-seopress' ), array( 'generator', 'version', 'hide', 'security' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_generator' ),
			self::make_setting( 'advanced/wp-shortlink', __( 'Remove shortlink tag', 'wp-seopress' ), array( 'shortlink', 'head', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_shortlink' ),
			self::make_setting( 'advanced/wp-wlw', __( 'Remove Windows Live Writer tag', 'wp-seopress' ), array( 'wlwmanifest', 'windows', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_wlw' ),
			self::make_setting( 'advanced/wp-rsd', __( 'Remove RSD tag', 'wp-seopress' ), array( 'rsd', 'really simple discovery', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_rsd' ),
			self::make_setting( 'advanced/wp-x-pingback', __( 'Remove X-Pingback header', 'wp-seopress' ), array( 'pingback', 'header', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_x_pingback' ),
			self::make_setting( 'advanced/wp-oembed', __( 'Disable oEmbed', 'wp-seopress' ), array( 'oembed', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_oembed' ),
			self::make_setting( 'advanced/wp-x-powered-by', __( 'Remove X-Powered-By header', 'wp-seopress' ), array( 'powered by', 'header', 'security' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_wp_x_powered_by' ),
			self::make_setting( 'advanced/hentry', __( 'Remove hentry microformat', 'wp-seopress' ), array( 'hentry', 'microformat' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_hentry' ),
			self::make_setting( 'advanced/emoji', __( 'Disable emoji scripts', 'wp-seopress' ), array( 'emoji', 'scripts', 'performance' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_emoji' ),
			self::make_setting( 'advanced/category-url', __( 'Remove /category/ from URLs', 'wp-seopress' ), array( 'category', 'url', 'slug', 'permalink' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_category_url' ),
			self::make_setting( 'advanced/product-cat-url', __( 'Remove /product-category/ from URLs', 'wp-seopress' ), array( 'woocommerce', 'product category', 'url' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_product_cat_url' ),
			self::make_setting( 'advanced/replytocom', __( 'Remove ?replytocom', 'wp-seopress' ), array( 'replytocom', 'comments', 'cleanup' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_replytocom' ),
			self::make_setting( 'advanced/comment-website', __( 'Remove website field from comments', 'wp-seopress' ), array( 'comments', 'website', 'field', 'spam' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_comments_website' ),
			self::make_setting( 'advanced/comment-noreferrer', __( 'Add rel="noreferrer" to comment links', 'wp-seopress' ), array( 'noreferrer', 'comments' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_noreferrer' ),
			self::make_setting( 'advanced/site-verification-google', __( 'Google Search Console verification', 'wp-seopress' ), array( 'google', 'search console', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_google' ),
			self::make_setting( 'advanced/site-verification-bing', __( 'Bing Webmaster verification', 'wp-seopress' ), array( 'bing', 'webmaster', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_bing' ),
			self::make_setting( 'advanced/site-verification-yandex', __( 'Yandex verification', 'wp-seopress' ), array( 'yandex', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_yandex' ),
			self::make_setting( 'advanced/site-verification-baidu', __( 'Baidu verification', 'wp-seopress' ), array( 'baidu', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_baidu' ),
			self::make_setting( 'advanced/site-verification-pinterest', __( 'Pinterest verification', 'wp-seopress' ), array( 'pinterest', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_pinterest' ),
			self::make_setting( 'advanced/site-verification-facebook', __( 'Facebook domain verification', 'wp-seopress' ), array( 'facebook', 'verification', 'domain' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_facebook' ),
			self::make_setting( 'advanced/site-verification-seznam', __( 'Seznam verification', 'wp-seopress' ), array( 'seznam', 'verification' ), __( 'Advanced › Advanced', 'wp-seopress' ), $page, $adv, 'seopress_advanced_advanced_seznam' ),
		);
	}

	/**
	 * Tools page.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function tools() {
		$page = 'seopress-import-export';
		return array(
			self::make_setting( 'tools/settings-export', __( 'Export settings (JSON)', 'wp-seopress' ), array( 'export', 'settings', 'json', 'backup' ), __( 'Tools › Settings', 'wp-seopress' ), $page, 'tab_seopress_tool_settings' ),
			self::make_setting( 'tools/settings-import', __( 'Import settings (JSON)', 'wp-seopress' ), array( 'import', 'settings', 'json', 'restore' ), __( 'Tools › Settings', 'wp-seopress' ), $page, 'tab_seopress_tool_settings' ),
			self::make_setting( 'tools/plugins-import', __( 'Import from Yoast / Rank Math / AIOSEO / SEOFramework', 'wp-seopress' ), array( 'yoast', 'rank math', 'rankmath', 'aioseo', 'migrate', 'import' ), __( 'Tools › Plugins', 'wp-seopress' ), $page, 'tab_seopress_tool_plugins' ),
			self::make_setting( 'tools/reset', __( 'Reset all SEOPress settings', 'wp-seopress' ), array( 'reset', 'clear', 'uninstall', 'wipe' ), __( 'Tools › Reset', 'wp-seopress' ), $page, 'tab_seopress_tool_reset' ),
		);
	}

	/**
	 * Build a setting command (field-level).
	 *
	 * @param string   $id
	 * @param string   $label
	 * @param string[] $keywords
	 * @param string   $context
	 * @param string   $page
	 * @param string   $tab
	 * @param string   $field
	 * @return array<string,mixed>
	 */
	private static function make_setting( $id, $label, array $keywords, $context, $page, $tab, $field = '' ) {
		$entry = array(
			'name'     => 'seopress/setting/' . $id,
			'label'    => $label,
			'keywords' => $keywords,
			'context'  => $context,
			'kind'     => 'setting',
			'page'     => $page,
			'tab'      => $tab,
		);
		if ( '' !== $field ) {
			$entry['field'] = $field;
		}
		return $entry;
	}
}

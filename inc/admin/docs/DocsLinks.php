<?php
/**
 * Docs Links
 *
 * @package SEOPress
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Get the docs links
 *
 * @return array
 */
function seopress_get_docs_links() {
	$docs = array();

	$docs = array(
		'website'              => 'https://www.seopress.org/',
		'pricing'              => 'https://www.seopress.org/pricing/',
		'blog'                 => 'https://www.seopress.org/newsroom/',
		'contact'              => 'https://www.seopress.org/contact-us/',
		'downloads'            => 'https://www.seopress.org/account/my-downloads/',
		'support'              => 'https://www.seopress.org/support/',
		'support-free'         => 'https://www.seopress.org/support/seopress-free/',
		'support-pro'          => 'https://www.seopress.org/support/seopress-pro/',
		'support-insights'     => 'https://www.seopress.org/support/seopress-insights/',
		'support-tickets'      => 'https://www.seopress.org/account/your-tickets/',
		'guides'               => 'https://www.seopress.org/support/guides/',
		'faq'                  => 'https://www.seopress.org/support/faq/',
		'i18n'                 => 'https://www.seopress.org/support/guides/translate-seopress/',
		'get_started'          => array(
			'installation' => array(
				'ico'   => 'ico-starter-guides',
				'title' => __( 'Starter guides', 'wp-seopress' ),
				'desc'  => __( 'Ready to boost your SEO? Get started in minutes with SEOPress.', 'wp-seopress' ),
				'link'  => 'https://www.seopress.org/support/guides/get-started-seopress/',
			),
			'guides'       => array(
				'ico'   => 'ico-guides',
				'title' => __( 'Guides', 'wp-seopress' ),
				'desc'  => __( 'Discover the basics of SEO, how to configure our products to get the most out of them.', 'wp-seopress' ),
				'link'  => 'https://www.seopress.org/support/guides/',
			),
			'ebooks'       => array(
				'ico'   => 'ico-ebooks',
				'title' => __( 'Ebooks', 'wp-seopress' ),
				'desc'  => __( 'Free SEO ebooks to improve your rankings, traffic, conversions and sales.', 'wp-seopress' ),
				'link'  => 'https://www.seopress.org/support/ebooks/',
			),
			'dev'          => array(
				'ico'   => 'ico-hooks',
				'title' => __( 'Developer Docs', 'wp-seopress' ),
				'desc'  => __( 'Unlock the power of SEOPress with our actions and filters.', 'wp-seopress' ),
				'link'  => 'https://www.seopress.org/support/hooks/',
			),
			'sitemaps'     => array(),
		),
		'get_started_insights' => array(
			'installation'  => array( __( 'Activate your SEOPress Insights license', 'wp-seopress' ) => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' ),
			'track_kw'      => array( __( 'Track your keyword rankings in Google with SEOPress Insights', 'wp-seopress' ) => 'https://www.seopress.org/support/guides/track-keyword-rankings-google-seopress-insights/' ),
			'track_bl'      => array( __( 'Monitor and analyse your Backlinks with SEOPress Insights', 'wp-seopress' ) => 'https://www.seopress.org/support/guides/monitor-and-analyse-your-backlinks-with-seopress-insights/' ),
			'find_kw'       => array( __( 'Finding SEO keywords for your WordPress site', 'wp-seopress' ) => 'https://www.seopress.org/support/tutorials/finding-seo-keywords-for-your-blog/' ),
			'optimize_kw'   => array( __( 'Optimize WordPress posts for a keyword', 'wp-seopress' ) => 'https://www.seopress.org/support/tutorials/optimize-wordpress-posts-for-a-keyword/' ),
			'audit_bl'      => array( __( 'Audit the backlinks of your WordPress site (in WordPress)', 'wp-seopress' ) => 'https://www.seopress.org/support/tutorials/audit-the-backlinks-of-your-wordpress-site-in-wordpress/' ),
			'importance_bl' => array( __( 'The importance of backlinks', 'wp-seopress' ) => 'https://www.seopress.org/support/tutorials/the-importance-of-backlinks/' ),
		),
		'universal'            => array(
			'introduction' => 'https://www.seopress.org/features/page-builders-integration/',
		),
		'ai'                   => array(
			'introduction' => 'https://www.seopress.org/features/openai/',
			'errors'       => 'https://www.seopress.org/support/guides/generate-your-seo-metadata-with-openai/',
			'openai'       => array(
				'errors' => 'https://www.seopress.org/support/guides/generate-your-seo-metadata-with-openai/',
			),
			'deepseek'     => array(
				'errors' => 'https://www.seopress.org/support/guides/generate-seo-metadata-with-deepseek-ai/',
			),
		),
		'titles'               => array(
			'thumbnail'         => 'https://support.google.com/programmable-search/answer/1626955?hl=en',
			'wrong_meta'        => 'https://www.seopress.org/support/guides/google-uses-the-wrong-meta-title-meta-description-in-search-results/',
			'alt_title'         => 'https://developers.google.com/search/docs/appearance/site-names#content-guidelines',
			'manage'            => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/',
			'add_theme_support' => 'https://www.seopress.org/support/guides/fixing-missing-add_theme_support-in-your-theme/',
		),
		'sitemaps'             => array(
			'error' => array(
				'blank' => 'https://www.seopress.org/support/guides/xml-sitemap-blank-page/',
				'404'   => 'https://www.seopress.org/support/guides/xml-sitemap-returns-404-error/',
				'html'  => 'https://www.seopress.org/support/guides/how-to-exclude-xml-and-xsl-files-from-caching-plugins/',
			),
			'html'  => 'https://www.seopress.org/support/guides/enable-html-sitemap/',
			'xml'   => 'https://www.seopress.org/support/guides/enable-xml-sitemaps/',
			'image' => 'https://www.seopress.org/support/guides/enable-xml-image-sitemaps/',
			'video' => 'https://www.seopress.org/support/guides/enable-video-xml-sitemap/',
			'hooks' => 'https://www.seopress.org/support/hooks/#topic-77',
		),
		'social'               => array(
			'og' => 'https://www.seopress.org/support/guides/manage-facebook-open-graph-and-twitter-cards-metas/',
		),
		'analytics'            => array(
			'quick_start'     => 'https://www.seopress.org/support/guides/google-analytics/',
			'connect'         => 'https://www.seopress.org/support/guides/connect-wordpress-site-google-analytics/',
			'custom_tracking' => 'https://www.seopress.org/support/hooks/add-custom-tracking-code-with-user-consent/',
			'consent_msg'     => 'https://www.seopress.org/support/hooks/filter-user-consent-message/',
			'gads'            => 'https://www.seopress.org/support/guides/how-to-find-your-google-ads-conversions-id/',
			'gtm'             => 'https://www.seopress.org/support/guides/google-tag-manager-wordpress-seopress/',
			'ecommerce'       => 'https://www.seopress.org/support/guides/how-to-setup-google-enhanced-ecommerce/',
			'events'          => 'https://www.seopress.org/support/guides/how-to-track-file-downloads-affiliates-outbound-and-external-links-with-google-analytics/',
			'ga4_property'    => 'https://www.seopress.org/support/guides/find-my-google-analytics-4-property-id/',
			'api'             => array(
				'analytics' => 'https://console.cloud.google.com/apis/library/analytics.googleapis.com?hl=en',
				'data'      => 'https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com?hl=en',
			),
			'ga_ecommerce'    => array(
				'ev_purchase' => 'https://www.seopress.org/support/hooks/filter-woocommerce-order-status-sent-to-google-analytics-enhanced-ecommerce/',
			),
			'matomo'          => array(
				'on_premise' => 'https://www.seopress.org/support/guides/how-to-use-matomo-on-premise-with-seopress-free/',
				'token'      => 'https://www.seopress.org/support/guides/connect-your-wordpress-site-with-matomo-analytics/',
			),
			'clarity'         => array(
				'project' => 'https://www.seopress.org/support/guides/find-my-microsoft-clarity-project-id/',
			),
		),
		'advanced'             => array(
			'imageseo' => 'https://www.seopress.org/support/guides/optimize-an-image-for-seo/',
		),
		'security'             => array(
			'metaboxe_seo'        => 'https://www.seopress.org/support/hooks/filter-seo-metaboxe-call-by-post-type/',
			'metaboxe_ca'         => 'https://www.seopress.org/support/hooks/filter-content-analysis-metabox-call-by-post-type/',
			'metaboxe_data_types' => 'https://www.seopress.org/support/hooks/filter-structured-data-types-metabox-call-by-post-type/',
			'ga_widget'           => 'https://www.seopress.org/support/hooks/filter-google-analytics-dashboard-widget-capability/',
			'matomo_widget'       => 'https://www.seopress.org/support/hooks/filter-matomo-analytics-dashboard-widget-capability/',
			'caps'                => 'https://www.seopress.org/support/guides/seopress-user-capabilities/',
		),
		'google_preview'       => array(
			'authentification' => 'https://www.seopress.org/support/hooks/filter-google-snippet-preview-remote-request/',
		),
		'bot'                  => 'https://www.seopress.org/support/guides/detect-broken-links/',
		'lb'                   => array(
			'eat'   => 'https://www.seopress.org/newsroom/featured-stories/optimizing-wordpress-sites-for-google-eat/',
			'ebook' => 'https://www.seopress.org/support/ebooks/improving-your-local-seo-using-wordpress/',
		),
		'robots'               => array(
			'file' => 'https://www.seopress.org/support/guides/edit-robots-txt-file/',
		),
		'breadcrumbs'          => array(
			'get_started' => 'https://www.seopress.org/support/guides/enable-breadcrumbs/',
			'sep'         => 'https://www.seopress.org/support/hooks/filter-breadcrumbs-separator/',
			'i18n'        => 'https://www.seopress.org/support/guides/translate-seopress-options-with-wpml-polylang/',
		),
		'redirects'            => array(
			'enable' => 'https://www.seopress.org/support/guides/redirections/',
			'query'  => 'https://www.seopress.org/support/guides/delete-your-404-errors-with-a-mysql-query/',
			'regex'  => 'https://www.seopress.org/support/guides/redirections/#regular-expressions',
		),
		'woocommerce'          => array(
			'ebook' => 'https://www.seopress.org/support/ebooks/woocommerce-seo-ultimate-guide/',
		),
		'schemas'              => array(
			'ebook'         => 'https://www.seopress.org/support/ebooks/master-google-structured-data-types-schemas/',
			'add'           => 'https://www.seopress.org/support/tutorials/how-to-add-schema-to-wordpress-with-seopress-1/',
			'faq_acf'       => 'https://www.seopress.org/support/guides/create-an-automatic-faq-schema-with-acf-repeater-fields/',
			'dynamic'       => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/',
			'variables'     => 'https://www.seopress.org/support/hooks/filter-predefined-dynamic-variables-for-automatic-schemas/',
			'custom_fields' => 'https://www.seopress.org/support/hooks/filter-custom-fields-list-in-schemas/',
			'feature'       => 'https://www.seopress.org/features/structured-data-types/',
		),
		'page_speed'           => array(
			'cwv'    => 'https://www.seopress.org/newsroom/featured-stories/core-web-vitals-and-wordpress-seo/',
			'api'    => 'https://www.seopress.org/support/guides/add-your-google-page-speed-insights-api-key-to-seopress/',
			'google' => 'https://console.cloud.google.com/apis/library/pagespeedonline.googleapis.com?hl=en',
		),
		'indexing_api'         => array(
			'google' => 'https://www.seopress.org/support/guides/google-indexing-api-with-seopress/',
			'api'    => 'https://console.cloud.google.com/apis/library/indexing.googleapis.com?hl=en',
		),
		'inspect_url'          => array(
			'google' => 'https://www.seopress.org/support/guides/how-to-use-google-inspect-url-api-with-seopress-pro/',
		),
		'search_console_api'   => array(
			'google'  => 'https://www.seopress.org/support/guides/google-search-console/',
			'metrics' => 'https://www.seopress.org/support/guides/how-to-use-google-search-console-api-with-seopress-pro/',
			'api'     => 'https://console.cloud.google.com/apis/library/searchconsole.googleapis.com?hl=en',
		),
		'google_news'          => array(
			'get_started' => 'https://www.seopress.org/support/guides/enable-google-news-xml-sitemap/',
		),
		'rss'                  => array(
			'get_started' => 'https://www.seopress.org/support/guides/manage-your-wordpress-rss-feeds/',
		),
		'alerts'               => array(
			'introduction'  => 'https://www.seopress.org/features/seo-alerts/',
			'slack_webhook' => 'https://www.seopress.org/support/guides/how-to-configure-seo-alerts-in-sack-with-seopress-pro/',
		),
		'tools'                => array(
			'csv_import' => 'https://www.seopress.org/support/guides/import-metadata-from-a-csv-file-with-seopress-pro/',
			'csv_export' => 'https://www.seopress.org/support/guides/export-metadata-from-seopress-to-a-csv-file/',
		),
		'license'              => array(
			'account'        => 'https://www.seopress.org/account/',
			'license_errors' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/#i-still-cant-activate-my-license-key',
			'license_define' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/#add-my-license-key-to-wp-config-php',
		),
		'addons'               => array(
			'pro'      => 'https://www.seopress.org/wordpress-seo-plugins/pro/',
			'insights' => 'https://www.seopress.org/wordpress-seo-plugins/insights/',
		),
		'insights'             => array(
			'slack_webhook' => 'https://www.seopress.org/support/guides/how-to-setup-slack-notifications-with-seopress-insights/',
		),
		'integrations'         => array(
			'all'       => 'https://www.seopress.org/integrations/',
			'wpml'      => array(
				'translate' => 'https://www.seopress.org/support/guides/translate-seopress-options-with-wpml-polylang/',
			),
			'litespeed' => array(
				'compatibility' => 'https://www.seopress.org/support/guides/fix-compatibility-issue-with-litespeed-caching-plugin/',
			),
		),
	);

	if ( function_exists( 'seopress_get_locale' ) && 'fr' === seopress_get_locale() ) {
		$docs['website']                             = 'https://www.seopress.org/fr/';
		$docs['pricing']                             = 'https://www.seopress.org/fr/tarifs/';
		$docs['blog']                                = 'https://www.seopress.org/fr/blog/';
		$docs['contact']                             = 'https://www.seopress.org/fr/contact/';
		$docs['downloads']                           = 'https://www.seopress.org/fr/mon-compte/mes-telechargements/';
		$docs['support']                             = 'https://www.seopress.org/fr/support/';
		$docs['support-tickets']                     = 'https://www.seopress.org/fr/mon-compte/vos-tickets/';
		$docs['support-free']                        = 'https://www.seopress.org/fr/support/seopress-free/';
		$docs['support-pro']                         = 'https://www.seopress.org/fr/support/seopress-pro/';
		$docs['support-insights']                    = 'https://www.seopress.org/fr/support/seopress-insights/';
		$docs['guides']                              = 'https://www.seopress.org/fr/support/guides/';
		$docs['faq']                                 = 'https://www.seopress.org/fr/support/faq/';
		$docs['i18n']                                = 'https://www.seopress.org/fr/support/guides/traduire-seopress-dans-votre-langue/';
		$docs['get_started']['installation']['link'] = 'https://www.seopress.org/fr/support/guides/debutez-avec-seopress/';
		$docs['get_started']['guides']['link']       = 'https://www.seopress.org/fr/support/guides/';
		$docs['get_started']['ebooks']['link']       = 'https://www.seopress.org/fr/support/ebooks/';
		$docs['get_started']['dev']['link']          = 'https://www.seopress.org/fr/support/hooks/';
		$docs['get_started']['sitemaps']             = array();

		$docs['get_started_insights']['installation']  = array( 'Activer votre licence SEOPress PRO / Insights' => 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' );
		$docs['get_started_insights']['track_kw']      = array( 'Suivez les positions de vos mots clés dans Google avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/suivez-les-positions-de-vos-mots-cles-dans-google-avec-seopress-insights/' );
		$docs['get_started_insights']['track_bl']      = array( 'Surveiller et analyser vos backlinks avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/surveiller-et-analyser-vos-backlinks-avec-seopress-insights/' );
		$docs['get_started_insights']['find_kw']       = array( 'Trouver les bons mots clés pour le référencement de votre site' => 'https://www.seopress.org/fr/support/tutoriels/trouver-les-bons-mots-cles-pour-le-referencement-de-votre-site/' );
		$docs['get_started_insights']['optimize_kw']   = array( 'Optimisez votre article WordPress pour un mot clé' => 'https://www.seopress.org/fr/support/tutoriels/optimisez-votre-article-wordpress-pour-un-mot-cle/' );
		$docs['get_started_insights']['audit_bl']      = array( 'Auditer les backlinks de votre site WordPress (dans WordPress)' => 'https://www.seopress.org/fr/support/tutoriels/auditer-les-backlinks-de-votre-site-wordpress-dans-wordpress/' );
		$docs['get_started_insights']['importance_bl'] = array( 'L’importance des backlinks' => 'https://www.seopress.org/fr/support/tutoriels/limportance-des-backlinks/' );

		$docs['universal']['introduction'] = 'https://www.seopress.org/fr/fonctionnalites/integration-constructeurs-de-page/';

		$docs['ai']['introduction']       = 'https://www.seopress.org/fr/fonctionnalites/openai/';
		$docs['ai']['errors']             = 'https://www.seopress.org/fr/support/guides/generez-vos-metadonnees-seo-avec-openai/#erreurs-et-solutions';
		$docs['ai']['openai']['errors']   = 'https://www.seopress.org/fr/support/guides/generez-vos-metadonnees-seo-avec-openai/';
		$docs['ai']['deepseek']['errors'] = 'https://www.seopress.org/fr/support/guides/generez-des-metadonnees-seo-avec-deepseek-ai/';

		$docs['titles']['thumbnail']         = 'https://support.google.com/programmable-search/answer/1626955?hl=fr';
		$docs['titles']['wrong_meta']        = 'https://www.seopress.org/fr/support/guides/google-utilise-une-balise-titre-meta-description-incorrecte-dans-les-resultats-de-recherche/';
		$docs['titles']['alt_title']         = 'https://developers.google.com/search/docs/appearance/site-names#content-guidelines';
		$docs['titles']['manage']            = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-et-metas/';
		$docs['titles']['add_theme_support'] = 'https://www.seopress.org/fr/support/guides/resoudre-add_theme_support-manquant-dans-votre-theme/';

		$docs['sitemaps']['error']['blank'] = 'https://www.seopress.org/fr/support/guides/mon-plan-de-site-xml-retourne-une-page-blanche/';
		$docs['sitemaps']['error']['404']   = 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-retourne-erreur-404/';
		$docs['sitemaps']['error']['html']  = 'https://www.seopress.org/fr/support/guides/exclure-les-fichiers-xml-et-xsl-des-extensions-de-cache/';
		$docs['sitemaps']['html']           = 'https://www.seopress.org/fr/support/guides/activer-le-plan-de-site-html/';
		$docs['sitemaps']['xml']            = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml/';
		$docs['sitemaps']['image']          = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-images/';
		$docs['sitemaps']['video']          = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-les-videos/';
		$docs['sitemaps']['hooks']          = 'https://www.seopress.org/fr/support/hooks/#topic-69';

		$docs['social']['og'] = 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/';

		$docs['analytics']['quick_start']                 = 'https://www.seopress.org/fr/support/guides/debutez-avec-google-analytics/';
		$docs['analytics']['connect']                     = 'https://www.seopress.org/fr/support/guides/connectez-votre-site-wordpress-a-google-analytics/';
		$docs['analytics']['custom_tracking']             = 'https://www.seopress.org/fr/support/hooks/ajouter-votre-code-de-suivi-personnalise-avec-le-consentement-utilisateur/';
		$docs['analytics']['consent_msg']                 = 'https://www.seopress.org/fr/support/hooks/filtrer-le-message-du-consentement-utilisateur/';
		$docs['analytics']['gads']                        = 'https://www.seopress.org/fr/support/guides/trouver-votre-id-de-conversion-google-ads/';
		$docs['analytics']['gtm']                         = 'https://www.seopress.org/fr/support/guides/ajouter-google-tag-manager-a-votre-site-wordpress-avec-seopress/';
		$docs['analytics']['ecommerce']                   = 'https://www.seopress.org/fr/support/guides/configurer-le-commerce-electronique-ameliore-pour-google-analytics/';
		$docs['analytics']['events']                      = 'https://www.seopress.org/fr/support/guides/suivre-vos-telechargements-liens-affilies-sortants-et-externes-google-analytics/';
		$docs['analytics']['ga4_property']                = 'https://www.seopress.org/fr/support/guides/trouver-id-de-propriete-google-analytics-4/';
		$docs['analytics']['api']['analytics']            = 'https://console.cloud.google.com/apis/library/analytics.googleapis.com?hl=fr';
		$docs['analytics']['api']['data']                 = 'https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com?hl=fr';
		$docs['analytics']['ga_ecommerce']['ev_purchase'] = 'https://www.seopress.org/fr/support/hooks/filtrer-les-statuts-des-commandes-woocommerce-envoyees-dans-google-analytics-suivi-ecommerce-ameliore/';
		$docs['analytics']['matomo']['on_premise']        = 'https://www.seopress.org/fr/support/guides/comment-utiliser-matomo-en-auto-heberge-avec-seopress-free/';
		$docs['analytics']['matomo']['token']             = 'https://www.seopress.org/fr/support/guides/connectez-votre-site-wordpress-avec-matomo-analytics/';
		$docs['analytics']['clarity']['project']          = 'https://www.seopress.org/fr/support/guides/trouver-mon-id-de-project-microsoft-clarity/';
		$docs['advanced']['imageseo']                     = 'https://www.seopress.org/fr/support/guides/optimiser-une-image-pour-le-referencement/';

		$docs['security']['metaboxe_seo']        = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-seo-par-types-de-contenu/';
		$docs['security']['metaboxe_ca']         = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-danalyse-de-contenu-par-types-de-contenu/';
		$docs['security']['metaboxe_data_types'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-types-de-donnees-structurees-par-types-de-contenu/';
		$docs['security']['ga_widget']           = 'https://www.seopress.org/fr/support/hooks/filtrer-la-capacite-du-widget-google-analytics-du-tableau-de-bord/';
		$docs['security']['matomo_widget']       = 'https://www.seopress.org/fr/support/hooks/filtrer-capacite-widget-matomo-analytics-dashboard/';
		$docs['security']['caps']                = 'https://www.seopress.org/fr/support/guides/capacites-utilisateurs-de-seopress/';

		$docs['google_preview']['authentification'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-requete-distante-google-snippet-preview/';

		$docs['bot']         = 'https://www.seopress.org/fr/support/guides/detecter-les-liens-casses-dans-vos-contenus/';
		$docs['lb']['eat']   = 'https://www.seopress.org/fr/newsroom/reportage/optimiser-votre-site-wordpress-pour-google-eat/';
		$docs['lb']['ebook'] = 'https://www.seopress.org/fr/support/ebooks/ameliorer-le-positionnement-de-votre-site-wordpress-grace-au-seo-local/';

		$docs['robots']['file'] = 'https://www.seopress.org/fr/support/guides/editer-votre-fichier-robots-txt/';

		$docs['breadcrumbs']['get_started'] = 'https://www.seopress.org/fr/support/guides/activer-fil-dariane/';
		$docs['breadcrumbs']['sep']         = 'https://www.seopress.org/fr/support/hooks/filtrer-le-separateur-du-fil-dariane/';
		$docs['breadcrumbs']['i18n']        = 'https://www.seopress.org/fr/support/guides/traduire-les-options-de-seopress-avec-wpml-polylang/';

		$docs['redirects']['enable'] = 'https://www.seopress.org/fr/support/guides/activer-les-redirections-301-et-la-surveillance-des-404/';
		$docs['redirects']['query']  = 'https://www.seopress.org/fr/support/guides/nettoyez-vos-erreurs-404-a-laide-dune-requete-mysql/';
		$docs['redirects']['regex']  = 'https://www.seopress.org/fr/support/guides/activer-les-redirections-301-et-la-surveillance-des-404/#expressions-regulieres';

		$docs['woocommerce']['ebook'] = 'https://www.seopress.org/fr/support/ebooks/woocommerce-seo-guide-ultime/';

		$docs['schemas']['ebook']         = 'https://www.seopress.org/fr/support/ebooks/maitriser-les-types-de-donnees-structurees-de-google-schemas/';
		$docs['schemas']['add']           = 'https://www.seopress.org/fr/support/tutoriels/comment-utiliser-les-schemas-dans-votre-site-wordpress-avec-seopress-pro-1/';
		$docs['schemas']['faq_acf']       = 'https://www.seopress.org/fr/support/guides/creer-un-schema-faq-automatique-avec-les-champs-repeteurs-dacf/';
		$docs['schemas']['dynamic']       = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-et-metas/';
		$docs['schemas']['variables']     = 'https://www.seopress.org/fr/support/hooks/filtrer-la-liste-des-variables-dynamiques-predefinies-pour-les-schemas-automatiques/';
		$docs['schemas']['custom_fields'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-liste-des-champs-personnalises-dans-les-schemas/';
		$docs['schemas']['feature']       = 'https://www.seopress.org/fr/fonctionnalites/types-de-donnees-structurees-google-schemas/';

		$docs['page_speed']['cwv']    = 'https://www.seopress.org/fr/newsroom/reportage/les-core-web-vitals-et-leurs-effets-sur-le-seo-des-sites-wordpress/';
		$docs['page_speed']['api']    = 'https://www.seopress.org/fr/support/guides/ajouter-cle-api-google-page-speed-insights-seopress/';
		$docs['page_speed']['google'] = 'https://console.cloud.google.com/apis/library/pagespeedonline.googleapis.com?hl=fr';

		$docs['indexing_api']['google'] = 'https://www.seopress.org/fr/support/guides/api-google-instant-indexing-avec-seopress/';
		$docs['indexing_api']['api']    = 'https://console.cloud.google.com/apis/library/indexing.googleapis.com?hl=fr';

		$docs['inspect_url']['google'] = 'https://www.seopress.org/fr/support/guides/comment-utiliser-lapi-google-inspection-durl-avec-seopress-pro/';

		$docs['search_console_api']['google']  = 'https://www.seopress.org/fr/support/guides/ajouter-votre-site-a-google-search-console/';
		$docs['search_console_api']['metrics'] = 'https://www.seopress.org/fr/support/guides/comment-utiliser-api-google-search-console-avec-seopress-pro/';
		$docs['search_console_api']['api']     = 'https://console.cloud.google.com/apis/library/searchconsole.googleapis.com?hl=fr';

		$docs['tools']['csv_import'] = 'https://www.seopress.org/fr/support/guides/importer-metadonnees-csv-seopress-pro/';
		$docs['tools']['csv_export'] = 'https://www.seopress.org/fr/support/guides/exporter-vos-metadonnees-vers-un-fichier-csv-avec-seopress-pro/';

		$docs['google_news']['get_started'] = 'https://www.seopress.org/fr/support/guides/activer-le-plan-de-site-xml-google-news/';

		$docs['rss']['get_started'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-flux-rss-wordpress/';

		$docs['license']['account']        = 'https://www.seopress.org/fr/mon-compte/';
		$docs['license']['license_errors'] = 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/#je-ne-peux-toujours-pas-activer-ma-cle-de-licence';
		$docs['license']['license_define'] = 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/#ajouter-ma-cle-de-licence-dans-wp-config-php';

		$docs['addons']['pro']             = 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-pro/';
		$docs['addons']['insights']        = 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-insights/';
		$docs['insights']['slack_webhook'] = 'https://www.seopress.org/fr/support/guides/configurer-les-notifications-slack-avec-seopress-insights/';

		$docs['alerts']['introduction']  = 'https://www.seopress.org/fr/fonctionnalites/alertes-seo/';
		$docs['alerts']['slack_webhook'] = 'https://www.seopress.org/fr/support/guides/configurer-alertes-seo-slack-seopress-pro/';

		$docs['integrations']['all']                        = 'https://www.seopress.org/fr/integrations/';
		$docs['integrations']['wpml']['translate']          = 'https://www.seopress.org/fr/support/guides/traduire-les-options-de-seopress-avec-wpml-polylang/';
		$docs['integrations']['litespeed']['compatibility'] = 'https://www.seopress.org/fr/support/guides/corriger-le-probleme-de-compatibilite-extension-cache-litespeed/';
	}

	$docs['external'] = array(
		'facebook'    => 'https://www.facebook.com/seopresspro/',
		'facebook_gr' => 'https://www.facebook.com/groups/seopress/',
		'youtube'     => 'https://www.youtube.com/seopress',
		'twitter'     => 'https://twitter.com/wp_seopress',
	);

	return $docs;
}

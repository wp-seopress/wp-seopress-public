<?php

if (! defined('ABSPATH')) {
	exit;
}

function seopress_get_docs_links()
{
	$docs  = [];
	$utm   = '?utm_source=plugin&utm_medium=wp-adminutm_campaign=seopress';
	$utm2  = '?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress';
	$utm3  = '?utm_source=plugin&utm_medium=wp-admin-insights&utm_campaign=seopress';

	$docs = [
		'website'          => 'https://www.seopress.org/' . $utm,
		'subscribe'        => 'https://www.seopress.org/subscribe/' . $utm,
		'blog'             => 'https://www.seopress.org/newsroom/' . $utm,
		'downloads'        => 'https://www.seopress.org/account/my-downloads/' . $utm,
		'support'          => 'https://www.seopress.org/support/' . $utm,
		'support-free'     => 'https://www.seopress.org/support/seopress-free/' . $utm,
		'support-pro'      => 'https://www.seopress.org/support/seopress-pro/' . $utm,
		'support-insights' => 'https://www.seopress.org/support/seopress-insights/' . $utm,
		'support-tickets'  => 'https://www.seopress.org/account/your-tickets/' . $utm,
		'guides'           => 'https://www.seopress.org/support/guides/' . $utm,
		'faq'              => 'https://www.seopress.org/support/faq/' . $utm,
		'get_started'      => [
			'installation'        => [
				'ico' => 'ico-starter-guides',
				'title' => __('Starter guides', 'wp-seopress'),
				'desc' => __('Ready to boost your SEO? Get started in minutes with SEOPress.','wp-seopress'),
				'link' => 'https://www.seopress.org/support/guides/get-started-seopress/' . $utm,
			],
			'guides' => [
				'ico' => 'ico-guides',
				'title' => __('Guides', 'wp-seopress'),
				'desc' => __('Discover the basics of SEO, how to configure our products to get the most out of them.','wp-seopress'),
				'link' => 'https://www.seopress.org/support/guides/' . $utm,
			],
			'ebooks' => [
				'ico' => 'ico-ebooks',
				'title' => __('Ebooks', 'wp-seopress'),
				'desc' => __('Free SEO ebooks to improve your rankings, traffic, conversions and sales.','wp-seopress'),
				'link' => 'https://www.seopress.org/support/ebooks/' . $utm,
			],
			'dev' => [
				'ico' => 'ico-hooks',
				'title' => __('Developer Docs', 'wp-seopress'),
				'desc' => __('Unlock the power of SEOPress with our actions and filters.','wp-seopress'),
				'link' => 'https://www.seopress.org/support/hooks/' . $utm,
			],
			'sitemaps' => [],
		],
		'get_started_insights'      => [
			'installation'          => [__('Activate your SEOPress Insights license', 'wp-seopress') => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm3],
			'track_kw'              => [__('Track your keyword rankings in Google with SEOPress Insights', 'wp-seopress') => 'https://www.seopress.org/support/guides/track-keyword-rankings-google-seopress-insights/' . $utm3],
			'track_bl'              => [__('Monitor and analyse your Backlinks with SEOPress Insights', 'wp-seopress') => 'https://www.seopress.org/support/guides/monitor-and-analyse-your-backlinks-with-seopress-insights/' . $utm3],
			'find_kw'               => [__('Finding SEO keywords for your WordPress site', 'wp-seopress') => 'https://www.seopress.org/support/tutorials/finding-seo-keywords-for-your-blog/' . $utm3],
			'optimize_kw'           => [__('Optimize WordPress posts for a keyword', 'wp-seopress') => 'https://www.seopress.org/support/tutorials/optimize-wordpress-posts-for-a-keyword/' . $utm3],
			'audit_bl'              => [__('Audit the backlinks of your WordPress site (in WordPress)', 'wp-seopress') => 'https://www.seopress.org/support/tutorials/audit-the-backlinks-of-your-wordpress-site-in-wordpress/' . $utm3],
			'importance_bl'         => [__('The importance of backlinks', 'wp-seopress') => 'https://www.seopress.org/support/tutorials/the-importance-of-backlinks/' . $utm3],
		],
		'universal' => [
			'introduction' => 'https://www.seopress.org/features/page-builders-integration/' . $utm,
		],
		'ai' => [
			'introduction' => 'https://www.seopress.org/features/openai/' . $utm,
			'errors' => 'https://www.seopress.org/support/guides/generate-your-seo-metadata-with-openai/' . $utm
		],
		'titles' => [
			'thumbnail' => 'https://support.google.com/programmable-search/answer/1626955?hl=en',
			'wrong_meta' => 'https://www.seopress.org/support/guides/google-uses-the-wrong-meta-title-meta-description-in-search-results/' . $utm,
			'alt_title' => 'https://developers.google.com/search/docs/appearance/site-names#content-guidelines' . $utm,
			'manage' => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/' . $utm
		],
		'sitemaps' => [
			'error' => [
				'blank' => 'https://www.seopress.org/support/guides/xml-sitemap-blank-page/' . $utm,
				'404'   => 'https://www.seopress.org/support/guides/xml-sitemap-returns-404-error/' . $utm,
				'html'  => 'https://www.seopress.org/support/guides/how-to-exclude-xml-and-xsl-files-from-caching-plugins/' . $utm,
			],
			'html'  => 'https://www.seopress.org/support/guides/enable-html-sitemap/' . $utm,
			'xml'   => 'https://www.seopress.org/support/guides/enable-xml-sitemaps/' . $utm,
			'image' => 'https://www.seopress.org/support/guides/enable-xml-image-sitemaps/' . $utm,
			'video' => 'https://www.seopress.org/support/guides/enable-video-xml-sitemap/' . $utm,
		],
		'social' => [
			'og' => 'https://www.seopress.org/support/guides/manage-facebook-open-graph-and-twitter-cards-metas/' . $utm,
		],
		'analytics' => [
			'quick_start'       => 'https://www.seopress.org/support/guides/google-analytics/' . $utm,
			'connect'           => 'https://www.seopress.org/support/guides/connect-wordpress-site-google-analytics/' . $utm,
			'custom_dimensions' => 'https://www.seopress.org/support/guides/create-custom-dimension-google-analytics/' . $utm,
			'custom_tracking'   => 'https://www.seopress.org/support/hooks/add-custom-tracking-code-with-user-consent/' . $utm,
			'consent_msg'       => 'https://www.seopress.org/support/hooks/filter-user-consent-message/' . $utm,
			'gads'              => 'https://www.seopress.org/support/guides/how-to-find-your-google-ads-conversions-id/' . $utm,
			'gtm'               => 'https://www.seopress.org/support/guides/google-tag-manager-wordpress-seopress/' . $utm,
			'ecommerce'         => 'https://www.seopress.org/support/guides/how-to-setup-google-enhanced-ecommerce/' . $utm,
			'events'            => 'https://www.seopress.org/support/guides/how-to-track-file-downloads-affiliates-outbound-and-external-links-with-google-analytics/' . $utm,
			'ga4_property'      => 'https://www.seopress.org/support/guides/find-my-google-analytics-4-property-id/' . $utm,
			'api' => [
				'analytics' => 'https://console.cloud.google.com/apis/library/analytics.googleapis.com?hl=en',
				'reporting' => 'https://console.cloud.google.com/apis/library/analyticsreporting.googleapis.com?hl=en',
				'data'      => 'https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com?hl=en'
			],
			'ga_ecommerce' => [
				'ev_purchase' => 'https://www.seopress.org/support/hooks/filter-woocommerce-order-status-sent-to-google-analytics-enhanced-ecommerce/' . $utm,
			],
			'matomo' => [
				'on_premise' => 'https://www.seopress.org/support/guides/how-to-use-matomo-on-premise-with-seopress-free/' . $utm,
				'token' => 'https://www.seopress.org/support/guides/connect-your-wordpress-site-with-matomo-analytics/' . $utm,
			],
			'clarity' => [
				'project' => 'https://www.seopress.org/support/guides/find-my-microsoft-clarity-project-id/' . $utm,
			]
		],
		'advanced' => [
			'imageseo' => 'https://www.seopress.org/support/guides/optimize-an-image-for-seo/' . $utm,
		],
		'compatibility' => [
			'automatic' => 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/' . $utm,
		],
		'security' => [
			'metaboxe_seo'        => 'https://www.seopress.org/support/hooks/filter-seo-metaboxe-call-by-post-type/' . $utm,
			'metaboxe_ca'         => 'https://www.seopress.org/support/hooks/filter-content-analysis-metabox-call-by-post-type/' . $utm,
			'metaboxe_data_types' => 'https://www.seopress.org/support/hooks/filter-structured-data-types-metabox-call-by-post-type/' . $utm,
			'ga_widget'           => 'https://www.seopress.org/support/hooks/filter-google-analytics-dashboard-widget-capability/' . $utm,
			'matomo_widget'       => 'https://www.seopress.org/support/hooks/filter-matomo-analytics-dashboard-widget-capability/' . $utm
		],
		'google_preview' => [
			'authentification' => 'https://www.seopress.org/support/hooks/filter-google-snippet-preview-remote-request/' . $utm,
		],
		'bot' => 'https://www.seopress.org/support/guides/detect-broken-links/' . $utm,
		'lb'  => [
			'eat' => 'https://www.seopress.org/newsroom/featured-stories/optimizing-wordpress-sites-for-google-eat/' . $utm,
			'ebook' => 'https://www.seopress.org/support/ebooks/improving-your-local-seo-using-wordpress/' . $utm,
		],
		'robots' => [
			'file' => 'https://www.seopress.org/support/guides/edit-robots-txt-file/' . $utm,
		],
		'breadcrumbs' => [
			'get_started' => 'https://www.seopress.org/support/guides/enable-breadcrumbs/' . $utm,
			'sep' => 'https://www.seopress.org/support/hooks/filter-breadcrumbs-separator/' . $utm,
			'i18n' => 'https://www.seopress.org/support/guides/translate-seopress-options-with-wpml-polylang/' . $utm,
		],
		'redirects'   => [
			'enable' => 'https://www.seopress.org/support/guides/redirections/' . $utm,
			'query'  => 'https://www.seopress.org/support/guides/delete-your-404-errors-with-a-mysql-query/' . $utm,
			'regex'  => 'https://www.seopress.org/support/guides/redirections/' . $utm . '#regular-expressions',
		],
		'woocommerce' => [
			'ebook' => 'https://www.seopress.org/support/ebooks/woocommerce-seo-ultimate-guide/' . $utm,
		],
		'schemas' => [
			'ebook'   => 'https://www.seopress.org/support/ebooks/master-google-structured-data-types-schemas/' . $utm,
			'add'     => 'https://www.seopress.org/support/tutorials/how-to-add-schema-to-wordpress-with-seopress-1/' . $utm,
			'faq_acf' => 'https://www.seopress.org/support/guides/create-an-automatic-faq-schema-with-acf-repeater-fields/' . $utm,
			'dynamic' => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/' . $utm,
			'variables' => 'https://www.seopress.org/support/hooks/filter-predefined-dynamic-variables-for-automatic-schemas/' . $utm,
			'custom_fields' => 'https://www.seopress.org/support/hooks/filter-custom-fields-list-in-schemas/' . $utm,
		],
		'page_speed' => [
			'cwv' => 'https://www.seopress.org/newsroom/featured-stories/core-web-vitals-and-wordpress-seo/' . $utm,
			'api' => 'https://www.seopress.org/support/guides/add-your-google-page-speed-insights-api-key-to-seopress/' . $utm,
			'google' => 'https://console.cloud.google.com/apis/library/pagespeedonline.googleapis.com?hl=en',
		],
		'indexing_api' => [
			'google' => 'https://www.seopress.org/support/guides/google-indexing-api-with-seopress/' . $utm,
			'api' => 'https://console.cloud.google.com/apis/library/indexing.googleapis.com?hl=en',
		],
		'inspect_url' => [
			'google' => 'https://www.seopress.org/support/guides/how-to-use-google-inspect-url-api-with-seopress-pro/' . $utm,
		],
		'search_console_api' => [
			'google' => 'https://www.seopress.org/support/guides/google-search-console/' . $utm,
			'metrics' => 'https://www.seopress.org/support/guides/how-to-use-google-search-console-api-with-seopress-pro/' . $utm,
			'api' => 'https://console.cloud.google.com/apis/library/searchconsole.googleapis.com?hl=en',
		],
		'google_news' => [
			'get_started' => 'https://www.seopress.org/support/guides/enable-google-news-xml-sitemap/' . $utm,
		],
		'rss' => [
			'get_started' => 'https://www.seopress.org/support/guides/manage-your-wordpress-rss-feeds/'. $utm,
		],
		'tools' => [
			'csv_import' => 'https://www.seopress.org/support/guides/import-metadata-from-a-csv-file-with-seopress-pro/' . $utm,
			'csv_export' => 'https://www.seopress.org/support/guides/export-metadata-from-seopress-to-a-csv-file/' . $utm,
		],
		'license' => [
			'account'        => 'https://www.seopress.org/account/' . $utm,
			'license_errors' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm . '#i-still-cant-activate-my-license-key',
			'license_define' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm . '#add-my-license-key-to-wp-config-php',
		],
		'addons' => [
			'pro' => 'https://www.seopress.org/wordpress-seo-plugins/pro/' . $utm,
			'insights' => 'https://www.seopress.org/wordpress-seo-plugins/insights/' . $utm,
		],
		'insights' => [
			'slack_webhook' => 'https://www.seopress.org/support/guides/how-to-setup-slack-notifications-with-seopress-insights/' . $utm
		]
	];

	if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
		$docs['website']       = 'https://www.seopress.org/fr/' . $utm;
		$docs['subscribe']     = 'https://www.seopress.org/fr/abonnez-vous/' . $utm;
		$docs['blog']          = 'https://www.seopress.org/fr/blog/' . $utm;
		$docs['downloads']     = 'https://www.seopress.org/fr/mon-compte/mes-telechargements/' . $utm;
		$docs['support']       = 'https://www.seopress.org/fr/support/' . $utm;
		$docs['support-tickets']  = 'https://www.seopress.org/fr/mon-compte/vos-tickets/' . $utm;
		$docs['support-free']     = 'https://www.seopress.org/fr/support/seopress-free/' . $utm;
		$docs['support-pro']      = 'https://www.seopress.org/fr/support/seopress-pro/' . $utm;
		$docs['support-insights'] = 'https://www.seopress.org/fr/support/seopress-insights/' . $utm;
		$docs['guides']           = 'https://www.seopress.org/fr/support/guides/' . $utm;
		$docs['faq']              = 'https://www.seopress.org/fr/support/faq/' . $utm;
		$docs['get_started']['installation']['link'] = 'https://www.seopress.org/fr/support/guides/debutez-avec-seopress/' . $utm;
		$docs['get_started']['guides']['link'] = 'https://www.seopress.org/fr/support/guides/' . $utm;
		$docs['get_started']['ebooks']['link'] = 'https://www.seopress.org/fr/support/ebooks/' . $utm;
		$docs['get_started']['dev']['link'] = 'https://www.seopress.org/fr/support/hooks/' . $utm;
		$docs['get_started']['sitemaps'] = [];

		$docs['get_started_insights']['installation'] = ['Activer votre licence SEOPress PRO / Insights' => 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' . $utm3];
		$docs['get_started_insights']['track_kw'] = ['Suivez les positions de vos mots clés dans Google avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/suivez-les-positions-de-vos-mots-cles-dans-google-avec-seopress-insights/' . $utm3];
		$docs['get_started_insights']['track_bl'] = ['Surveiller et analyser vos backlinks avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/surveiller-et-analyser-vos-backlinks-avec-seopress-insights/' . $utm3];
		$docs['get_started_insights']['find_kw'] = ['Trouver les bons mots clés pour le référencement de votre site' => 'https://www.seopress.org/fr/support/tutoriels/trouver-les-bons-mots-cles-pour-le-referencement-de-votre-site/' . $utm3];
		$docs['get_started_insights']['optimize_kw'] = ['Optimisez votre article WordPress pour un mot clé' => 'https://www.seopress.org/fr/support/tutoriels/optimisez-votre-article-wordpress-pour-un-mot-cle/' . $utm3];
		$docs['get_started_insights']['audit_bl'] = ['Auditer les backlinks de votre site WordPress (dans WordPress)' => 'https://www.seopress.org/fr/support/tutoriels/auditer-les-backlinks-de-votre-site-wordpress-dans-wordpress/' . $utm3];
		$docs['get_started_insights']['importance_bl'] = ['L’importance des backlinks' => 'https://www.seopress.org/fr/support/tutoriels/limportance-des-backlinks/' . $utm3];

		$docs['universal']['introduction'] = 'https://www.seopress.org/fr/fonctionnalites/integration-constructeurs-de-page/' . $utm;

		$docs['ai']['introduction'] = 'https://www.seopress.org/fr/fonctionnalites/openai/' . $utm;
		$docs['ai']['errors'] = 'https://www.seopress.org/fr/support/guides/generez-vos-metadonnees-seo-avec-openai/' . $utm;

		$docs['titles']['thumbnail'] = 'https://support.google.com/programmable-search/answer/1626955?hl=fr';
		$docs['titles']['wrong_meta'] = 'https://www.seopress.org/fr/support/guides/google-utilise-une-balise-titre-meta-description-incorrecte-dans-les-resultats-de-recherche/' . $utm;
		$docs['titles']['alt_title'] = 'https://developers.google.com/search/docs/appearance/site-names#content-guidelines' . $utm;
		$docs['titles']['manage'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-et-metas/' . $utm;

		$docs['sitemaps']['error']['blank'] = 'https://www.seopress.org/fr/support/guides/mon-plan-de-site-xml-retourne-une-page-blanche/' . $utm;
		$docs['sitemaps']['error']['404'] = 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-retourne-erreur-404/' . $utm;
		$docs['sitemaps']['error']['html'] = 'https://www.seopress.org/fr/support/guides/exclure-les-fichiers-xml-et-xsl-des-extensions-de-cache/' . $utm;
		$docs['sitemaps']['html'] = 'https://www.seopress.org/fr/support/guides/activer-le-plan-de-site-html/' . $utm;
		$docs['sitemaps']['xml'] = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml/' . $utm;
		$docs['sitemaps']['image'] = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-images/' . $utm;
		$docs['sitemaps']['video'] = 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-les-videos/' . $utm;

		$docs['social']['og'] = 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/' . $utm;

		$docs['analytics']['quick_start'] = 'https://www.seopress.org/fr/support/guides/debutez-avec-google-analytics/' . $utm;
		$docs['analytics']['connect'] = 'https://www.seopress.org/fr/support/guides/connectez-votre-site-wordpress-a-google-analytics/' . $utm;
		$docs['analytics']['custom_dimensions'] = 'https://www.seopress.org/fr/support/guides/creer-des-dimensions-personnalisees-dans-google-analytics/' . $utm;
		$docs['analytics']['custom_tracking'] = 'https://www.seopress.org/fr/support/hooks/ajouter-votre-code-de-suivi-personnalise-avec-le-consentement-utilisateur/' . $utm;
		$docs['analytics']['consent_msg'] = 'https://www.seopress.org/fr/support/hooks/filtrer-le-message-du-consentement-utilisateur/' . $utm;
		$docs['analytics']['gads'] = 'https://www.seopress.org/fr/support/guides/trouver-votre-id-de-conversion-google-ads/' . $utm;
		$docs['analytics']['gtm'] = 'https://www.seopress.org/fr/support/guides/ajouter-google-tag-manager-a-votre-site-wordpress-avec-seopress/' . $utm;
		$docs['analytics']['ecommerce'] = 'https://www.seopress.org/fr/support/guides/configurer-le-commerce-electronique-ameliore-pour-google-analytics/' . $utm;
		$docs['analytics']['events'] = 'https://www.seopress.org/fr/support/guides/suivre-vos-telechargements-liens-affilies-sortants-et-externes-google-analytics/' . $utm;
		$docs['analytics']['ga4_property'] = 'https://www.seopress.org/fr/support/guides/trouver-id-de-propriete-google-analytics-4/' . $utm;
		$docs['analytics']['api']['analytics'] = 'https://console.cloud.google.com/apis/library/analytics.googleapis.com?hl=fr';
		$docs['analytics']['api']['reporting'] = 'https://console.cloud.google.com/apis/library/analyticsreporting.googleapis.com?hl=fr';
		$docs['analytics']['api']['data'] = 'https://console.cloud.google.com/apis/library/analyticsdata.googleapis.com?hl=fr';
		$docs['analytics']['ga_ecommerce']['ev_purchase'] = 'https://www.seopress.org/fr/support/hooks/filtrer-les-statuts-des-commandes-woocommerce-envoyees-dans-google-analytics-suivi-ecommerce-ameliore/' . $utm;
		$docs['analytics']['matomo']['on_premise'] = 'https://www.seopress.org/fr/support/guides/comment-utiliser-matomo-en-auto-heberge-avec-seopress-free/' . $utm;
		$docs['analytics']['matomo']['token'] = 'https://www.seopress.org/fr/support/guides/connectez-votre-site-wordpress-avec-matomo-analytics/' . $utm;
		$docs['analytics']['clarity']['project'] = 'https://www.seopress.org/fr/support/guides/trouver-mon-id-de-project-microsoft-clarity/' . $utm;
		$docs['advanced']['imageseo'] = 'https://www.seopress.org/fr/support/guides/optimiser-une-image-pour-le-referencement/' . $utm;

		$docs['compatibility']['automatic'] = 'https://www.seopress.org/fr/support/guides/generez-automatiquement-les-metas-descriptions-depuis-divi-oxygen-builder-fusion-builder/' . $utm;

		$docs['security']['metaboxe_seo'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-seo-par-types-de-contenu/' . $utm;
		$docs['security']['metaboxe_ca'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-danalyse-de-contenu-par-types-de-contenu/' . $utm;
		$docs['security']['metaboxe_data_types'] = 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-types-de-donnees-structurees-par-types-de-contenu/' . $utm;
		$docs['security']['ga_widget'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-capacite-du-widget-google-analytics-du-tableau-de-bord/' . $utm;
		$docs['security']['matomo_widget'] = 'https://www.seopress.org/fr/support/hooks/filtrer-capacite-widget-matomo-analytics-dashboard/' . $utm;

		$docs['google_preview']['authentification'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-requete-distante-google-snippet-preview/' . $utm;

		$docs['bot'] = 'https://www.seopress.org/fr/support/guides/detecter-les-liens-casses-dans-vos-contenus/' . $utm;
		$docs['lb']['eat'] = 'https://www.seopress.org/fr/newsroom/reportage/optimiser-votre-site-wordpress-pour-google-eat/' . $utm;
		$docs['lb']['ebook'] = 'https://www.seopress.org/fr/support/ebooks/ameliorer-le-positionnement-de-votre-site-wordpress-grace-au-seo-local/' . $utm;

		$docs['robots']['file'] = 'https://www.seopress.org/fr/support/guides/editer-votre-fichier-robots-txt/' . $utm;

		$docs['breadcrumbs']['get_started'] = 'https://www.seopress.org/fr/support/guides/activer-fil-dariane/' . $utm;
		$docs['breadcrumbs']['sep'] = 'https://www.seopress.org/fr/support/hooks/filtrer-le-separateur-du-fil-dariane/' . $utm;
		$docs['breadcrumbs']['i18n'] = 'https://www.seopress.org/fr/support/guides/traduire-les-options-de-seopress-avec-wpml-polylang/' . $utm;

		$docs['redirects']['enable'] = 'https://www.seopress.org/fr/support/guides/activer-les-redirections-301-et-la-surveillance-des-404/' . $utm;
		$docs['redirects']['query'] = 'https://www.seopress.org/fr/support/guides/nettoyez-vos-erreurs-404-a-laide-dune-requete-mysql/' . $utm;
		$docs['redirects']['regex'] = 'https://www.seopress.org/fr/support/guides/activer-les-redirections-301-et-la-surveillance-des-404/' . $utm . '#expressions-regulieres';

		$docs['woocommerce']['ebook'] = 'https://www.seopress.org/fr/support/ebooks/woocommerce-seo-guide-ultime/' . $utm;

		$docs['schemas']['ebook'] = 'https://www.seopress.org/fr/support/ebooks/types-de-donnees-structurees-de-google-schemas/' . $utm;
		$docs['schemas']['add'] = 'https://www.seopress.org/fr/support/tutoriels/comment-utiliser-les-schemas-dans-votre-site-wordpress-avec-seopress-pro-1/' . $utm;
		$docs['schemas']['faq_acf'] = 'https://www.seopress.org/fr/support/guides/creer-un-schema-faq-automatique-avec-les-champs-repeteurs-dacf/' . $utm;
		$docs['schemas']['dynamic'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-et-metas/' . $utm;
		$docs['schemas']['variables'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-liste-des-variables-dynamiques-predefinies-pour-les-schemas-automatiques/' . $utm;
		$docs['schemas']['custom_fields'] = 'https://www.seopress.org/fr/support/hooks/filtrer-la-liste-des-champs-personnalises-dans-les-schemas/' . $utm;

		$docs['page_speed']['cwv'] = 'https://www.seopress.org/fr/newsroom/reportage/les-core-web-vitals-et-leurs-effets-sur-le-seo-des-sites-wordpress/' . $utm;
		$docs['page_speed']['api'] = 'https://www.seopress.org/fr/support/guides/ajouter-cle-api-google-page-speed-insights-seopress/' . $utm;
		$docs['page_speed']['google'] = 'https://console.cloud.google.com/apis/library/pagespeedonline.googleapis.com?hl=fr';

		$docs['indexing_api']['google'] = 'https://www.seopress.org/fr/support/guides/api-google-instant-indexing-avec-seopress/' . $utm;
		$docs['indexing_api']['api'] = 'https://console.cloud.google.com/apis/library/indexing.googleapis.com?hl=fr';

		$docs['inspect_url']['google'] = 'https://www.seopress.org/fr/support/guides/comment-utiliser-lapi-google-inspection-durl-avec-seopress-pro/' . $utm;

		$docs['search_console_api']['google'] = 'https://www.seopress.org/fr/support/guides/ajouter-votre-site-a-google-search-console/' . $utm;
		$docs['search_console_api']['metrics'] = 'https://www.seopress.org/fr/support/guides/comment-utiliser-api-google-search-console-avec-seopress-pro/' . $utm;
		$docs['search_console_api']['api'] = 'https://console.cloud.google.com/apis/library/searchconsole.googleapis.com?hl=fr';

		$docs['tools']['csv_import'] = 'https://www.seopress.org/fr/support/guides/importer-metadonnees-csv-seopress-pro/' . $utm;
		$docs['tools']['csv_export'] = 'https://www.seopress.org/fr/support/guides/exporter-vos-metadonnees-vers-un-fichier-csv-avec-seopress-pro/' . $utm;

		$docs['google_news']['get_started'] = 'https://www.seopress.org/fr/support/guides/activer-le-plan-de-site-xml-google-news/' . $utm;

		$docs['rss']['get_started'] = 'https://www.seopress.org/fr/support/guides/gerez-vos-flux-rss-wordpress/'. $utm;

		$docs['license']['account'] = 'https://www.seopress.org/fr/mon-compte/' . $utm;
		$docs['license']['license_errors'] = 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/'. $utm . '#je-ne-peux-toujours-pas-activer-ma-cle-de-licence';
		$docs['license']['license_define'] = 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' . $utm . '#ajouter-ma-cle-de-licence-dans-wp-config-php';

		$docs['addons']['pro'] = 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-pro/' . $utm;
		$docs['addons']['insights'] = 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-insights/' . $utm;

		$docs['insights']['slack_webhook'] = 'https://www.seopress.org/fr/support/guides/configurer-les-notifications-slack-avec-seopress-insights/' . $utm;
	}

	$docs['external'] = [
		'facebook'      => 'https://www.facebook.com/seopresspro/' . $utm,
		'facebook_gr'   => 'https://www.facebook.com/groups/seopress/' . $utm,
		'youtube'       => 'https://www.youtube.com/seopress' . $utm,
		'twitter'       => 'https://twitter.com/wp_seopress' . $utm,
	];

	return $docs;
}

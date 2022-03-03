<?php

if (! defined('ABSPATH')) {
    exit;
}

function seopress_get_docs_links()
{
    $docs  = [];
    $utm   = '?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress';
    $utm2  = '?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress';
    $utm3  = '?utm_source=plugin&utm_medium=wp-admin-insights&utm_campaign=seopress';

    if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
        $docs = [
            'website'          => 'https://www.seopress.org/fr/' . $utm,
            'blog'             => 'https://www.seopress.org/fr/blog/' . $utm,
            'support'          => 'https://www.seopress.org/fr/support/' . $utm,
            'guides'           => 'https://www.seopress.org/fr/support/guides/' . $utm,
            'faq'              => 'https://www.seopress.org/fr/support/faq/' . $utm,
            'insights'         => 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-insights/' . $utm2,
            'get_started'      => [
                'installation'        => ['Installation de SEOPress' => 'https://www.seopress.org/fr/support/guides/debutez-avec-seopress/' . $utm],
                'license'             => ['Activater votre clé de licence pour recevoir les mises à jours automatiques' => 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' . $utm],
                'wizard'              => ['Configurez SEOPress en 5 minutes' => 'https://youtu.be/uwgS5zTk0j0' . $utm],
                'migration'           => ['Migrer vos métadonnées SEO depuis d\'autres extensions' => 'https://www.seopress.org/fr/solutions/migrer-vers-seopress/' . $utm],
                'sitemaps'            => ['Favoriser l\'exploration de votre site WordPress par les robots des moteurs de recherche' => 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml/' . $utm],
                'content'             => ['Optimiser un contenu de A à Z avec SEOPress' => 'https://www.seopress.org/fr/support/tutoriels/optimisez-votre-article-wordpress-pour-un-mot-cle/' . $utm],
                'analytics'           => ['Mesurez votre trafic avec Google Analytics' => 'https://www.seopress.org/fr/support/guides/debutez-avec-google-analytics/' . $utm],
                'search_console'      => ['Ajouter votre site WordPress à l’index de Google' => 'https://www.seopress.org/fr/support/guides/ajouter-votre-site-a-google-search-console/' . $utm],
                'social'              => ['Optimisez votre taux de clics sur les réseaux sociaux' => 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/' . $utm],
            ],
            'get_started_insights'      => [
                'installation'          => ['Activer votre licence SEOPress PRO / Insights' => 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' . $utm3],
                'track_kw'              => ['Suivez les positions de vos mots clés dans Google avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/suivez-les-positions-de-vos-mots-cles-dans-google-avec-seopress-insights/' . $utm3],
                'track_bl'              => ['Surveiller et analyser vos backlinks avec SEOPress Insights' => 'https://www.seopress.org/fr/support/guides/surveiller-et-analyser-vos-backlinks-avec-seopress-insights/' . $utm3],
                'find_kw'               => ['Trouver les bons mots clés pour le référencement de votre site' => 'https://www.seopress.org/fr/support/tutoriels/trouver-les-bons-mots-cles-pour-le-referencement-de-votre-site/' . $utm3],
                'optimize_kw'           => ['Optimisez votre article WordPress pour un mot clé' => 'https://www.seopress.org/fr/support/tutoriels/optimisez-votre-article-wordpress-pour-un-mot-cle/' . $utm3],
                'audit_bl'              => ['Auditer les backlinks de votre site WordPress (dans WordPress)' => 'https://www.seopress.org/fr/support/tutoriels/auditer-les-backlinks-de-votre-site-wordpress-dans-wordpress/' . $utm3],
                'importance_bl'         => ['L’importance des backlinks' => 'https://www.seopress.org/fr/support/tutoriels/limportance-des-backlinks/' . $utm3],
            ],
            'universal' => [
                'introduction' => 'https://www.seopress.org/fr/fonctionnalites/integration-constructeurs-de-page/' . $utm,
            ],
            'titles' => [
                'thumbnail' => 'https://support.google.com/programmable-search/answer/1626955?hl=fr',
                'wrong_meta' => 'https://www.seopress.org/fr/support/guides/google-utilise-une-balise-titre-meta-description-incorrecte-dans-les-resultats-de-recherche/' . $utm,
            ],
            'sitemaps' => [
                'error' => [
                    'blank' => 'https://www.seopress.org/fr/support/guides/mon-plan-de-site-xml-retourne-une-page-blanche/' . $utm,
                    '404'   => 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-retourne-erreur-404/' . $utm,
                    'html'  => 'https://www.seopress.org/fr/support/guides/exclure-les-fichiers-xml-et-xsl-des-extensions-de-cache/' . $utm,
                ],
                'html'  => 'https://www.seopress.org/fr/support/guides/activer-le-plan-de-site-html/' . $utm,
                'xml'   => 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml/' . $utm,
                'image' => 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-images/' . $utm,
                'video' => 'https://www.seopress.org/fr/support/guides/activer-le-sitemap-xml-pour-les-videos/' . $utm,
            ],
            'social' => [
                'og' => 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/' . $utm,
            ],
            'analytics' => [
                'connect'           => 'https://www.seopress.org/fr/support/guides/connectez-votre-site-wordpress-a-google-analytics/' . $utm,
                'custom_dimensions' => 'https://www.seopress.org/fr/support/guides/creer-des-dimensions-personnalisees-dans-google-analytics/' . $utm,
                'custom_tracking'   => 'https://www.seopress.org/fr/support/hooks/ajouter-votre-code-de-suivi-personnalise-avec-le-consentement-utilisateur/' . $utm,
                'consent_msg'       => 'https://www.seopress.org/fr/support/hooks/filtrer-le-message-du-consentement-utilisateur/' . $utm,
                'gads'              => 'https://www.seopress.org/fr/support/guides/trouver-votre-id-de-conversion-google-ads/' . $utm,
                'gtm'               => 'https://www.seopress.org/fr/support/guides/ajouter-google-tag-manager-a-votre-site-wordpress-avec-seopress/' . $utm,
                'ecommerce'         => 'https://www.seopress.org/fr/support/guides/configurer-le-commerce-electronique-ameliore-pour-google-analytics/' . $utm,
                'events'            => 'https://www.seopress.org/fr/support/guides/suivre-vos-telechargements-liens-affilies-sortants-et-externes-google-analytics/' . $utm,
                'ga4_property'      => 'https://support.google.com/analytics/answer/9744165?hl=en&ref_topic=9303319#analyticsjs',
            ],
            'compatibility' => [
                'automatic' => 'https://www.seopress.org/fr/support/guides/generez-automatiquement-les-metas-descriptions-depuis-divi-oxygen-builder-fusion-builder/' . $utm,
            ],
            'security' => [
                'metaboxe_seo'        => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-seo-par-types-de-contenu/' . $utm,
                'metaboxe_ca'         => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-danalyse-de-contenu-par-types-de-contenu/' . $utm,
                'metaboxe_data_types' => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-types-de-donnees-structurees-par-types-de-contenu/' . $utm,
            ],
            'google_preview' => [
                'authentification' => 'https://www.seopress.org/fr/support/hooks/filtrer-la-requete-distante-google-snippet-preview/' . $utm,
            ],
            'bot' => 'https://www.seopress.org/fr/support/guides/detecter-les-liens-casses-dans-vos-contenus/' . $utm,
            'lb'  => [
                'eat' => 'https://www.seopress.org/fr/newsroom/reportage/optimiser-votre-site-wordpress-pour-google-eat/' . $utm,
            ],
            'robots' => [
                'file' => 'https://www.seopress.org/fr/support/guides/editer-votre-fichier-robots-txt/' . $utm,
            ],
            'breadcrumbs' => [
                'sep' => 'https://www.seopress.org/fr/support/hooks/filtrer-le-separateur-du-fil-dariane/' . $utm,
                'i18n' => 'https://www.seopress.org/fr/support/guides/traduire-les-options-de-seopress-avec-wpml-polylang/' . $utm,
            ],
            'redirects'   => [
                'enable' => 'https://www.seopress.org/fr/support/guides/activer-les-redirections-301-et-la-surveillance-des-404/' . $utm,
                'query'  => 'https://www.seopress.org/fr/support/guides/nettoyez-vos-erreurs-404-a-laide-dune-requete-mysql/' . $utm,
            ],
            'schemas' => [
                'ebook'   => 'https://www.seopress.org/fr/support/ebooks/types-de-donnees-structurees-de-google-schemas/' . $utm,
                'add'     => 'https://www.seopress.org/fr/support/tutoriels/comment-utiliser-les-schemas-dans-votre-site-wordpress-avec-seopress-pro-1/' . $utm,
                'faq_acf' => 'https://www.seopress.org/fr/support/guides/creer-un-schema-faq-automatique-avec-les-champs-repeteurs-dacf/' . $utm,
                'dynamic' => 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-et-metas/' . $utm,
            ],
            'page_speed' => [
                'cwv' => 'https://www.seopress.org/fr/newsroom/reportage/les-core-web-vitals-et-leurs-effets-sur-le-seo-des-sites-wordpress/' . $utm,
                'api' => 'https://www.seopress.org/fr/support/guides/ajouter-cle-api-google-page-speed-insights-seopress/' . $utm,
            ],
            'indexing_api' => [
                'google' => 'https://www.seopress.org/fr/support/guides/api-google-instant-indexing-avec-seopress/' . $utm,
            ],
            'tools' => [
                'csv_import' => 'https://www.seopress.org/fr/support/guides/importer-metadonnees-csv-seopress-pro/' . $utm,
                'csv_export' => 'https://www.seopress.org/fr/support/guides/exporter-vos-metadonnees-vers-un-fichier-csv-avec-seopress-pro/' . $utm,
            ],
            'license' => [
                'account'        => 'https://www.seopress.org/fr/mon-compte/' . $utm,
                'license_errors' => 'https://www.seopress.org/fr/support/guides/activer-votre-licence-seopress-pro-insights/' . $utm,
            ],
            'addons' => [
                'pro' => 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-pro/' . $utm,
                'insights' => 'https://www.seopress.org/fr/extensions-seo-wordpress/seopress-insights/' . $utm,
            ]
        ];
    } else {
        $docs = [
            'website'          => 'https://www.seopress.org/' . $utm,
            'blog'             => 'https://www.seopress.org/newsroom/' . $utm,
            'support'          => 'https://www.seopress.org/support/' . $utm,
            'guides'           => 'https://www.seopress.org/support/guides/' . $utm,
            'faq'              => 'https://www.seopress.org/support/faq/' . $utm,
            'insights'         => 'https://www.seopress.org/wordpress-seo-plugins/insights/' . $utm2,
            'get_started'      => [
                'installation'        => [__('Installation of SEOPress', 'wp-seopress') => 'https://www.seopress.org/support/guides/get-started-seopress/' . $utm],
                'license'             => [__('Activate your license key to receive automatic updates', 'wp-seopress') => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm],
                'wizard'              => [__('Configure SEOPress in 5 minutes', 'wp-seopress') => 'https://youtu.be/uwgS5zTk0j0' . $utm],
                'migration'           => [__('Migrate your SEO metadata from other plugins', 'wp-seopress') => 'https://www.seopress.org/solutions/migrate-from/' . $utm],
                'sitemaps'            => [__('Promote the exploration of your WordPress site by search engine robots', 'wp-seopress') => 'https://www.seopress.org/support/guides/enable-xml-sitemaps/' . $utm],
                'content'             => [__('Optimize content from A to Z with SEOPress', 'wp-seopress') => 'https://www.seopress.org/support/tutorials/optimize-wordpress-posts-for-a-keyword/' . $utm],
                'analytics'           => [__('Measure your traffic with Google Analytics', 'wp-seopress') => 'https://www.seopress.org/support/guides/google-analytics/' . $utm],
                'search_console'      => [__('Add your WordPress site to Google’s index', 'wp-seopress') => 'https://www.seopress.org/support/guides/google-search-console/' . $utm],
                'social'              => [__('Optimize your click-through rate on social networks', 'wp-seopress') => 'https://www.seopress.org/support/guides/manage-facebook-open-graph-and-twitter-cards-metas/' . $utm],
            ],
            'get_started_insights'      => [
                'installation'          => ['Activate your SEOPress Insights license' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm3],
                'track_kw'              => ['Track your keyword rankings in Google with SEOPress Insights' => 'https://www.seopress.org/support/guides/track-keyword-rankings-google-seopress-insights/' . $utm3],
                'track_bl'              => ['Monitor and analyse your Backlinks with SEOPress Insights' => 'https://www.seopress.org/support/guides/monitor-and-analyse-your-backlinks-with-seopress-insights/' . $utm3],
                'find_kw'               => ['Finding SEO keywords for your WordPress site' => 'https://www.seopress.org/support/tutorials/finding-seo-keywords-for-your-blog/' . $utm3],
                'optimize_kw'           => ['Optimize WordPress posts for a keyword' => 'https://www.seopress.org/support/tutorials/optimize-wordpress-posts-for-a-keyword/' . $utm3],
                'audit_bl'              => ['Audit the backlinks of your WordPress site (in WordPress)' => 'https://www.seopress.org/support/tutorials/audit-the-backlinks-of-your-wordpress-site-in-wordpress/' . $utm3],
                'importance_bl'         => ['The importance of backlinks' => 'https://www.seopress.org/support/tutorials/the-importance-of-backlinks/' . $utm3],
            ],
            'universal' => [
                'introduction' => 'https://www.seopress.org/features/page-builders-integration/' . $utm,
            ],
            'titles' => [
                'thumbnail' => 'https://support.google.com/programmable-search/answer/1626955?hl=en',
                'wrong_meta' => 'https://www.seopress.org/support/guides/google-uses-the-wrong-meta-title-meta-description-in-search-results/' . $utm,
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
                'connect'           => 'https://www.seopress.org/support/guides/connect-wordpress-site-google-analytics/' . $utm,
                'custom_dimensions' => 'https://www.seopress.org/support/guides/create-custom-dimension-google-analytics/' . $utm,
                'custom_tracking'   => 'https://www.seopress.org/support/hooks/add-custom-tracking-code-with-user-consent/' . $utm,
                'consent_msg'       => 'https://www.seopress.org/support/hooks/filter-user-consent-message/' . $utm,
                'gads'              => 'https://www.seopress.org/support/guides/how-to-find-your-google-ads-conversions-id/' . $utm,
                'gtm'               => 'https://www.seopress.org/support/guides/google-tag-manager-wordpress-seopress/' . $utm,
                'ecommerce'         => 'https://www.seopress.org/support/guides/how-to-setup-google-enhanced-ecommerce/' . $utm,
                'events'            => 'https://www.seopress.org/support/guides/how-to-track-file-downloads-affiliates-outbound-and-external-links-with-google-analytics/' . $utm,
                'ga4_property'      => 'https://support.google.com/analytics/answer/9744165?hl=en&ref_topic=9303319#analyticsjs',
            ],
            'compatibility' => [
                'automatic' => 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/' . $utm,
            ],
            'security' => [
                'metaboxe_seo'        => 'https://www.seopress.org/support/hooks/filter-seo-metaboxe-call-by-post-type/' . $utm,
                'metaboxe_ca'         => 'https://www.seopress.org/support/hooks/filter-content-analysis-metabox-call-by-post-type/' . $utm,
                'metaboxe_data_types' => 'https://www.seopress.org/support/hooks/filter-structured-data-types-metabox-call-by-post-type/' . $utm,
            ],
            'google_preview' => [
                'authentification' => 'https://www.seopress.org/support/hooks/filter-google-snippet-preview-remote-request/' . $utm,
            ],
            'bot' => 'https://www.seopress.org/support/guides/detect-broken-links/' . $utm,
            'lb'  => [
                'eat' => 'https://www.seopress.org/newsroom/featured-stories/optimizing-wordpress-sites-for-google-eat/' . $utm,
            ],
            'robots' => [
                'file' => 'https://www.seopress.org/support/guides/edit-robots-txt-file/' . $utm,
            ],
            'breadcrumbs' => [
                'sep' => 'https://www.seopress.org/support/hooks/filter-breadcrumbs-separator/' . $utm,
                'i18n' => 'https://www.seopress.org/support/guides/translate-seopress-options-with-wpml-polylang/' . $utm,
            ],
            'redirects'   => [
                'enable' => 'https://www.seopress.org/support/guides/redirections/' . $utm,
                'query'  => 'https://www.seopress.org/support/guides/delete-your-404-errors-with-a-mysql-query/' . $utm,
            ],
            'schemas' => [
                'ebook'   => 'https://www.seopress.org/support/ebooks/master-google-structured-data-types-schemas/' . $utm,
                'add'     => 'https://www.seopress.org/support/tutorials/how-to-add-schema-to-wordpress-with-seopress-1/' . $utm,
                'faq_acf' => 'https://www.seopress.org/support/guides/create-an-automatic-faq-schema-with-acf-repeater-fields/' . $utm,
                'dynamic' => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/' . $utm,
            ],
            'page_speed' => [
                'cwv' => 'https://www.seopress.org/newsroom/featured-stories/core-web-vitals-and-wordpress-seo/' . $utm,
                'api' => 'https://www.seopress.org/support/guides/add-your-google-page-speed-insights-api-key-to-seopress/' . $utm,
            ],
            'indexing_api' => [
                'google' => 'https://www.seopress.org/support/guides/google-indexing-api-with-seopress/' . $utm,
            ],
            'tools' => [
                'csv_import' => 'https://www.seopress.org/support/guides/import-metadata-from-a-csv-file-with-seopress-pro/' . $utm,
                'csv_export' => 'https://www.seopress.org/support/guides/export-metadata-from-seopress-to-a-csv-file/' . $utm,
            ],
            'license' => [
                'account'        => 'https://www.seopress.org/account/' . $utm,
                'license_errors' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm,
            ],
            'addons' => [
                'pro' => 'https://www.seopress.org/wordpress-seo-plugins/pro/' . $utm,
                'insights' => 'https://www.seopress.org/wordpress-seo-plugins/insights/' . $utm,
            ]
        ];
    }

    $docs['external'] = [
        'facebook'      => 'https://www.facebook.com/seopresspro/' . $utm,
        'facebook_gr'   => 'https://www.facebook.com/groups/seopress/' . $utm,
        'youtube'       => 'https://www.youtube.com/seopress' . $utm,
        'twitter'       => 'https://twitter.com/wp_seopress' . $utm,
        'instagram'     => 'https://www.instagram.com/wp_seopress/' . $utm,
    ];

    return $docs;
}

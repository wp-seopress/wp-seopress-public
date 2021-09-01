<?php

if (! defined('ABSPATH')) {
    exit;
}

function seopress_get_docs_links()
{
    $docs  = [];
    $utm   = '?utm_source=plugin&utm_medium=wp-admin-help-tab&utm_campaign=seopress';
    $utm2  = '?utm_source=plugin&utm_medium=wizard&utm_campaign=seopress';

    if (function_exists('seopress_get_locale') && 'fr' == seopress_get_locale()) {
        $docs = [
            'website'          => 'https://www.seopress.org/fr/' . $utm,
            'blog'             => 'https://www.seopress.org/fr/blog/' . $utm,
            'support'          => 'https://www.seopress.org/fr/support/' . $utm,
            'guides'           => 'https://www.seopress.org/fr/support/guides/' . $utm,
            'faq'              => 'https://www.seopress.org/fr/support/faq/' . $utm,
            'insights'         => 'https://www.seopress.org/insights/' . $utm2,
            'get_started'      => [
                'installation'        => ['Installation de SEOPress' => 'https://www.seopress.org/fr/support/guides/debutez-seopress/' . $utm],
                'license'             => ['Activater votre clé de licence pour recevoir les mises à jours automatiques' => 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/' . $utm],
                'wizard'              => ['Configurez SEOPress en 5 minutes' => 'https://youtu.be/uwgS5zTk0j0' . $utm],
                'migration'           => ['Migrer vos métadonnées SEO depuis d\'autres extensions' => 'https://www.seopress.org/fr/migrate-vers-seopress/' . $utm],
                'sitemaps'            => ['Favoriser l\'exploration de votre site WordPress par les robots des moteurs de recherche' => 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/' . $utm],
                'content'             => ['Optimiser un contenu de A à Z avec SEOPress' => 'https://www.seopress.org/fr/blog/optimisez-votre-article-wordpress-pour-un-mot-cle/' . $utm],
                'analytics'           => ['Mesurez votre trafic avec Google Analytics' => 'https://www.seopress.org/fr/support/guides/debutez-google-analytics/' . $utm],
                'search_console'      => ['Ajouter votre site WordPress à l’index de Google' => 'https://www.seopress.org/fr/support/guides/google-search-console/' . $utm],
                'social'              => ['Optimisez votre taux de clics sur les réseaux sociaux' => 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/' . $utm],
            ],
            'titles' => [
                'thumbnail' => 'https://support.google.com/programmable-search/answer/1626955?hl=fr',
                'wrong_meta' => 'https://www.seopress.org/fr/support/guides/meta-titre-description-incorrectes-dans-google/' . $utm,
            ],
            'sitemaps' => [
                'error' => [
                    'blank' => 'https://www.seopress.org/fr/support/guides/xml-sitemap-page-blanche/' . $utm,
                    '404'   => 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-retourne-erreur-404/' . $utm,
                    'html'  => 'https://www.seopress.org/fr/support/guides/exclure-fichiers-xml-xsl-extensions-cache/' . $utm,
                ],
                'html'  => 'https://www.seopress.org/fr/support/guides/activer-plan-de-site-html/' . $utm,
                'xml'   => 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml/' . $utm,
                'image' => 'https://www.seopress.org/fr/support/guides/activer-sitemap-xml-images/' . $utm,
                'video' => 'https://www.seopress.org/fr/support/guides/plan-de-site-xml-video/' . $utm,
            ],
            'social' => [
                'og' => 'https://www.seopress.org/fr/support/guides/gerer-les-metas-facebook-open-graph-et-twitter-cards/' . $utm,
            ],
            'analytics' => [
                'connect'           => 'https://www.seopress.org/fr/support/guides/connectez-site-wordpress-a-google-analytics/' . $utm,
                'custom_dimensions' => 'https://www.seopress.org/fr/support/guides/creer-dimensions-personnalisees-google-analytics/' . $utm,
                'custom_tracking'   => 'https://www.seopress.org/fr/support/hooks/ajouter-votre-code-de-suivi-personnalise-avec-le-consentement-utilisateur/' . $utm,
                'consent_msg'       => 'https://www.seopress.org/fr/support/hooks/filtrer-le-message-du-consentement-utilisateur/' . $utm,
                'gads'              => 'https://www.seopress.org/fr/support/guides/trouver-votre-id-de-conversion-google-ads/' . $utm,
                'gtm'               => 'https://www.seopress.org/fr/support/guides/google-tag-manager-site-wordpress-seopress/' . $utm,
                'ecommerce'         => 'https://www.seopress.org/fr/support/guides/configurer-le-commerce-electronique-ameliore-pour-google-analytics/' . $utm,
            ],
            'compatibility' => [
                'automatic' => 'https://www.seopress.org/fr/support/guides/generez-automatiquement-meta-descriptions-divi-oxygen-builder/' . $utm,
            ],
            'security' => [
                'metaboxe_seo'        => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-seo-par-types-de-contenu/' . $utm,
                'metaboxe_ca'         => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-danalyse-de-contenu-par-types-de-contenu/' . $utm,
                'metaboxe_data_types' => 'https://www.seopress.org/fr/support/hooks/filtrer-lappel-de-la-metaboxe-types-de-donnees-structurees-par-types-de-contenu/' . $utm,
            ],
            'google_preview' => [
                'authentification' => 'https://www.seopress.org/fr/support/hooks/filtrer-la-requete-distante-google-snippet-preview/' . $utm,
            ],
            'bot' => 'https://www.seopress.org/fr/support/guides/detecter-liens-casses/' . $utm,
            'lb'  => [
                'eat' => 'https://www.seopress.org/fr/blog/optimiser-site-wordpress-google-eat/' . $utm,
            ],
            'robots' => [
                'file' => 'https://www.seopress.org/fr/support/guides/editer-fichier-robots-txt/' . $utm,
            ],
            'breadcrumbs' => [
                'sep' => 'https://www.seopress.org/fr/support/hooks/filtrer-le-separateur-du-fil-dariane/' . $utm,
            ],
            'redirects'   => [
                'enable' => 'https://www.seopress.org/fr/support/guides/activer-redirections-301-surveillance-404/' . $utm,
                'query'  => 'https://www.seopress.org/fr/support/guides/nettoyez-vos-erreurs-404-a-laide-dune-requete-mysql/' . $utm,
            ],
            'schemas' => [
                'ebook'   => 'https://www.seopress.org/fr/support/ebooks/types-de-donnees-structurees-de-google-schemas/' . $utm,
                'add'     => 'https://www.seopress.org/fr/blog/comment-utiliser-les-schemas-dans-votre-site-wordpress-avec-seopress-pro-1/' . $utm,
                'faq_acf' => 'https://www.seopress.org/fr/support/guides/schema-faq-automatique-champs-repeteurs-acf/' . $utm,
                'dynamic' => 'https://www.seopress.org/fr/support/guides/gerez-vos-balises-titres-metas/' . $utm,
            ],
            'tools' => [
                'csv_import' => 'https://www.seopress.org/fr/support/guides/importer-metadonnees-csv-seopress-pro/' . $utm,
                'csv_export' => 'https://www.seopress.org/fr/support/guides/exporter-vos-metadonnees-vers-un-fichier-csv-avec-seopress-pro/' . $utm,
            ],
            'license' => [
                'account'        => 'https://www.seopress.org/fr/compte/' . $utm,
                'license_errors' => 'https://www.seopress.org/fr/support/guides/activer-licence-seopress-pro/' . $utm,
            ],
        ];
    } else {
        $docs = [
            'website'          => 'https://www.seopress.org/' . $utm,
            'blog'             => 'https://www.seopress.org/blog/' . $utm,
            'support'          => 'https://www.seopress.org/support/' . $utm,
            'guides'           => 'https://www.seopress.org/support/guides/' . $utm,
            'faq'              => 'https://www.seopress.org/support/faq/' . $utm,
            'insights'         => 'https://www.seopress.org/insights/' . $utm2,
            'get_started'      => [
                'installation'        => [__('Installation of SEOPress') => 'https://www.seopress.org/support/guides/get-started-seopress/' . $utm],
                'license'             => [__('Activate your license key to receive automatic updates') => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm],
                'wizard'              => [__('Configure SEOPress in 5 minutes') => 'https://youtu.be/uwgS5zTk0j0' . $utm],
                'migration'           => [__('Migrate your SEO metadata from other plugins') => 'https://www.seopress.org/migrate-to-seopress/' . $utm],
                'sitemaps'            => [__('Promote the exploration of your WordPress site by search engine robots') => 'https://www.seopress.org/support/guides/enable-xml-sitemaps/' . $utm],
                'content'             => [__('Optimize content from A to Z with SEOPress') => 'https://www.seopress.org/blog/optimize-wordpress-posts-for-a-keyword/' . $utm],
                'analytics'           => [__('Measure your traffic with Google Analytics') => 'https://www.seopress.org/support/guides/google-analytics/' . $utm],
                'search_console'      => [__('Add your WordPress site to Google’s index') => 'https://www.seopress.org/support/guides/google-search-console/' . $utm],
                'social'              => [__('Optimize your click-through rate on social networks') => 'https://www.seopress.org/support/guides/manage-facebook-open-graph-and-twitter-cards-metas/' . $utm],
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
            ],
            'compatibility' => [
                'automatic' => 'https://www.seopress.org/support/guides/generate-automatic-meta-description-from-page-builders/' . $utm,
            ],
            'security' => [
                'metaboxe_seo'        => 'https://www.seopress.org/support/hooks/filter-seo-metaboxe-call-by-post-type/' . $utm,
                'metaboxe_ca'         => 'https://www.seopress.org/support/hooks/filter-content-analysis-metaboxe-call-by-post-type/' . $utm,
                'metaboxe_data_types' => 'https://www.seopress.org/support/hooks/filter-structured-data-types-metaboxe-call-by-post-type/' . $utm,
            ],
            'google_preview' => [
                'authentification' => 'https://www.seopress.org/support/hooks/filter-google-snippet-preview-remote-request/' . $utm,
            ],
            'bot' => 'https://www.seopress.org/support/guides/detect-broken-links/?utm_source=plugin&utm_medium=wp-admin&utm_campaign=seopress' . $utm,
            'lb'  => [
                'eat' => 'https://www.seopress.org/blog/optimizing-wordpress-sites-for-google-eat/' . $utm,
            ],
            'robots' => [
                'file' => 'https://www.seopress.org/support/guides/edit-robots-txt-file/' . $utm,
            ],
            'breadcrumbs' => [
                'sep' => 'https://www.seopress.org/support/hooks/filter-breadcrumbs-separator/' . $utm,
            ],
            'redirects'   => [
                'enable' => 'https://www.seopress.org/support/guides/redirections/' . $utm,
                'query'  => 'https://www.seopress.org/support/guides/delete-your-404-errors-with-a-mysql-query/' . $utm,
            ],
            'schemas' => [
                'ebook'   => 'https://www.seopress.org/support/ebooks/master-google-structured-data-types-schemas/' . $utm,
                'add'     => 'https://www.seopress.org/blog/how-to-add-schema-to-wordpress-with-seopress-1/' . $utm,
                'faq_acf' => 'https://www.seopress.org/support/guides/create-an-automatic-faq-schema-with-acf-repeater-fields/' . $utm,
                'dynamic' => 'https://www.seopress.org/support/guides/manage-titles-meta-descriptions/' . $utm,
            ],
            'tools' => [
                'csv_import' => 'https://www.seopress.org/support/guides/import-metadata-from-a-csv-file-with-seopress-pro/' . $utm,
                'csv_export' => 'https://www.seopress.org/support/guides/export-metadata-from-seopress-to-a-csv-file/' . $utm,
            ],
            'license' => [
                'account'        => 'https://www.seopress.org/account/' . $utm,
                'license_errors' => 'https://www.seopress.org/support/guides/activate-seopress-pro-license/' . $utm,
            ],
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
